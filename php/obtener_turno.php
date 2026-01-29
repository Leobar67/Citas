<?php
include("../conexion.php");

$response = [
    "inicio" => "08:00",
    "fin"    => "15:30"
];

$stmt = $conexion->prepare(
    "SELECT tipo, valor FROM configuracion 
     WHERE tipo IN ('turno_inicio','turno_fin')"
);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['tipo'] === 'turno_inicio') {
        $response['inicio'] = $row['valor'];
    }
    if ($row['tipo'] === 'turno_fin') {
        $response['fin'] = $row['valor'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
