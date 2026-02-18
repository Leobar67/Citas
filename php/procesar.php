<?php
include("../conexion.php");
header('Content-Type: application/json');

function respuesta($tipo, $mensaje, $extra = []) {
    echo json_encode(array_merge([
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ], $extra));
    exit;
}

/* ================== DATOS ================== */
$matricula = trim($_POST['matricula'] ?? '');
$nombre    = trim($_POST['nombre'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$programa  = trim($_POST['programa'] ?? '');
$tramite   = trim($_POST['tramite'] ?? '');
$fecha     = $_POST['fecha'] ?? '';
$hora      = $_POST['hora'] ?? '';
$reagendar = isset($_POST['reagendar']) && $_POST['reagendar'] === '1';

/* ================== VALIDAR ================== */
if (!$matricula || !$nombre || !$telefono || !$programa || !$tramite || !$fecha || !$hora) {
    respuesta('danger', 'Todos los campos son obligatorios');
}

/* ================== DÍA HÁBIL ================== */
$diaSemana = date('w', strtotime($fecha));

$stmt = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='dia_habil'"
);
$diasHabiles = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

if (!in_array($diaSemana, $diasHabiles)) {
    respuesta('danger', 'El día seleccionado no está habilitado');
}

/* ================== HORARIO COMIDA ================== */
$horaSel = strtotime($hora);
if ($horaSel >= strtotime("13:00") && $horaSel < strtotime("14:00")) {
    respuesta('danger', 'Horario de comida (13:00 a 14:00)');
}

/* ================== CITA EXISTENTE ================== */
$stmt = $conexion->prepare(
    "SELECT id, fecha, hora FROM citas
     WHERE matricula = :matricula AND estatus = 'Pendiente'
     
);
$stmt->execute([':matricula' => $matricula]);
$citaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

/* ================== HORA OCUPADA ================== */
/* 👇 IGNORAR cita anterior SI es reagendar */
$sqlHora = "
    SELECT id FROM citas
    WHERE fecha = :fecha AND hora = :hora
";
$params = [
    ':fecha' => $fecha,
    ':hora'  => $hora
];

if ($citaExistente && $reagendar) {
    $sqlHora .= " AND id != :id";
    $params[':id'] = $citaExistente['id'];
}

$stmt = $conexion->prepare($sqlHora);
$stmt->execute($params);

if ($stmt->fetch()) {
    respuesta('warning', 'Esa hora ya no está disponible, seleccione otra');
}

/* ================== PREGUNTAR REAGENDAR ================== */
if ($citaExistente && !$reagendar) {
    respuesta(
        'warning',
        "Ya existe una cita para esta matrícula el día {$citaExistente['fecha']} a las {$citaExistente['hora']}. ¿Desea reagendar?",
        ['preguntarReagendar' => true]
    );
}

/* ================== REAGENDAR ================== */
if ($citaExistente && $reagendar) {
    $stmt = $conexion->prepare(
        "UPDATE citas SET estatus = 'Reagendado' WHERE id = :id"
    );
    $stmt->execute([':id' => $citaExistente['id']]);
}

/* ================== INSERTAR NUEVA ================== */
$stmt = $conexion->prepare("
    INSERT INTO citas
    (matricula, nombre, telefono, programa, tramite, fecha, hora, estatus)
    VALUES
    (:matricula, :nombre, :telefono, :programa, :tramite, :fecha, :hora, 'Pendiente')
");

$stmt->execute([
    ':matricula' => $matricula,
    ':nombre'    => $nombre,
    ':telefono'  => $telefono,
    ':programa'  => $programa,
    ':tramite'   => $tramite,
    ':fecha'     => $fecha,
    ':hora'      => $hora
]);

if ($reagendar) {
    respuesta('success', '🔁 Cita reagendada correctamente');
} else {
    respuesta('success', '📅 Cita agendada correctamente');
}
--------


