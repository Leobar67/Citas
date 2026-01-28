<?php
include("../conexion.php");

$response = [
    "inicio" => "08:00",
    "fin"    => "15:30"
];

$inicio = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='turno_inicio' LIMIT 1"
);

$fin = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='turno_fin' LIMIT 1"
);

if ($inicio && $inicio->num_rows > 0) {
    $response['inicio'] = $inicio->fetch_assoc()['valor'];
}

if ($fin && $fin->num_rows > 0) {
    $response['fin'] = $fin->fetch_assoc()['valor'];
}

header('Content-Type: application/json');
echo json_encode($response);
