<?php
include("../conexion.php");

$stmt = $conexion->prepare(
    "SELECT valor FROM configuracion WHERE tipo='dia_habil'"
);
$stmt->execute();

$dias = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dias[] = (int)$row['valor'];
}

header('Content-Type: application/json');
echo json_encode($dias);
