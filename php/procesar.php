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

$matricula = trim($_POST['matricula']);
$nombre    = trim($_POST['nombre']);
$telefono  = trim($_POST['telefono']);
$programa  = trim($_POST['programa']);
$tramite   = trim($_POST['tramite']);
$fecha     = $_POST['fecha'];
$hora      = $_POST['hora'];
$reagendar = isset($_POST['reagendar']) ? (bool)$_POST['reagendar'] : false;

/* VALIDAR */
if (!$matricula || !$nombre || !$telefono || !$programa || !$tramite || !$fecha || !$hora) {
    respuesta('danger', 'Todos los campos son obligatorios');
}

/* FIN DE SEMANA
$dia = date('w', strtotime($fecha));
if ($dia == 0 || $dia == 6) {
    respuesta('danger', 'No se permiten citas en fin de semana');
}
*/

// VALIDAR DÍA HÁBIL DESDE CONFIGURACIÓN
$diaSemana = date('w', strtotime($fecha));

$res = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='dia_habil'"
);

$diasHabiles = [];
while ($d = $res->fetch_assoc()) {
    $diasHabiles[] = (int)$d['valor'];
}

if (!in_array($diaSemana, $diasHabiles)) {
    respuesta('danger', 'El día seleccionado no está habilitado');
}


/* HORARIO */
$horaMin = strtotime("08:00");
$horaMax = strtotime("15:30");
$horaSel = strtotime($hora);

/* COMIDA */
if ($horaSel >= strtotime("13:00") && $horaSel < strtotime("14:00")) {
    respuesta('danger', 'Horario de comida (13:00 a 14:00)');
}

/* VALIDAR HORA OCUPADA */
$verificarHora = $conexion->prepare(
    "SELECT id FROM citas WHERE fecha = ? AND hora = ?"
);
$verificarHora->bind_param("ss", $fecha, $hora);
$verificarHora->execute();
$verificarHora->store_result();
if ($verificarHora->num_rows > 0) {
    respuesta('warning', 'Esa hora ya no está disponible, seleccione otra');
}

/* VALIDAR SI YA EXISTE CITA DE ESA MATRÍCULA */
$verificarMatricula = $conexion->prepare(
    "SELECT id, fecha, hora FROM citas WHERE matricula = ? AND estatus != 'Reagendado'"
);
$verificarMatricula->bind_param("s", $matricula);
$verificarMatricula->execute();
$resultMatricula = $verificarMatricula->get_result();

if ($resultMatricula->num_rows > 0 && !$reagendar) {
    $cita = $resultMatricula->fetch_assoc();
    respuesta('warning', 
        "Ya existe una cita para esta matrícula el día {$cita['fecha']} a las {$cita['hora']}. ¿Desea reagendar?", 
        ['preguntarReagendar' => true, 'citaExistente' => $cita]
    );
}

/* SI ES REAGENDAR */
if ($resultMatricula->num_rows > 0 && $reagendar) {
    $cita = $resultMatricula->fetch_assoc();
    // Actualizar cita anterior a "Reagendado"
    $update = $conexion->prepare("UPDATE citas SET estatus='Reagendado' WHERE id=?");
    $update->bind_param("i", $cita['id']);
    $update->execute();
}

/* INSERTAR NUEVA CITA */
$sql = $conexion->prepare("
    INSERT INTO citas 
    (matricula, nombre, telefono, programa, tramite, fecha, hora, estatus)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente')
");
$sql->bind_param(
    "sssssss",
    $matricula,
    $nombre,
    $telefono,
    $programa,
    $tramite,
    $fecha,
    $hora
);
$sql->execute();

respuesta('success', '✅ Cita agendada correctamente');
