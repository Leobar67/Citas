<?php
include("../conexion.php");

header('Content-Type: application/json');

$fecha = $_GET['fecha'] ?? '';

if (!$fecha) {
    echo json_encode([
        'horas' => [],
        'total' => 0,
        'limite' => 0
    ]);
    exit;
}

/* 🔹 OBTENER LÍMITE DESDE CONFIGURACIÓN */
$limiteStmt = $conexion->prepare("
    SELECT valor 
    FROM configuracion 
    WHERE tipo = 'limite_citas_diarias'
    LIMIT 1
");
$limiteStmt->execute();
$limite = $limiteStmt->fetchColumn();

if (!$limite) {
    $limite = 20; // valor por defecto si no existe en BD
}

/* 🔹 OBTENER HORAS OCUPADAS (EXCLUYE REAGENDADAS) */
$stmt = $conexion->prepare("
    SELECT hora 
    FROM citas 
    WHERE fecha = :fecha
    AND estatus != 'Reagendado'
");
$stmt->execute([':fecha' => $fecha]);

$horas = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* 🔹 TOTAL DE CITAS ACTIVAS DEL DÍA */
$totalDia = count($horas);

/* 🔹 RESPUESTA COMPLETA */
echo json_encode([
    'horas' => $horas,
    'total' => $totalDia,
    'limite' => (int)$limite
]);
