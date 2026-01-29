<?php
include("../conexion.php");

$fecha = $_GET['fecha'] ?? '';

$stmt = $conexion->prepare(
    "SELECT hora FROM citas WHERE fecha = :fecha"
);
$stmt->execute([':fecha' => $fecha]);

$horas = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $horas[] = $row['hora'];
}

header('Content-Type: application/json');
echo json_encode($horas);
