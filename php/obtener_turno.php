<?php
include("../conexion.php");

$response = ["inicio" => "08:00", "fin" => "15:30"];

$inicio = $conexion->query("SELECT valor FROM configuracion WHERE tipo='turno_inicio' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$fin    = $conexion->query("SELECT valor FROM configuracion WHERE tipo='turno_fin' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($inicio) $response['inicio'] = $inicio['valor'];
if ($fin)    $response['fin'] = $fin['valor'];

header('Content-Type: application/json');
echo json_encode($response);
