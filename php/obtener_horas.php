<?php
include("../conexion.php");

$fecha = $_GET['fecha'];

$stmt = $conexion->prepare("SELECT hora FROM citas WHERE fecha = :fecha");
$stmt->execute([':fecha' => $fecha]);
$horas = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($horas);
