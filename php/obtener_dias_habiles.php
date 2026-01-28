<?php
include("../conexion.php");

$dias = [];

$resultado = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='dia_habil'"
);

while ($row = $resultado->fetch_assoc()) {
    $dias[] = (int)$row['valor'];
}

header('Content-Type: application/json');
echo json_encode($dias);
