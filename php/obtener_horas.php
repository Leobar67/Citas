<?php
include("../conexion.php");

$fecha = $_GET['fecha'];

$sql = $conexion->prepare("SELECT hora FROM citas WHERE fecha = ?");
$sql->bind_param("s", $fecha);
$sql->execute();

$resultado = $sql->get_result();
$horas = [];

while ($fila = $resultado->fetch_assoc()) {
    $horas[] = $fila['hora'];
}

echo json_encode($horas);
