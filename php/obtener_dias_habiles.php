<?php
include("../conexion.php");

$dias = [];

$stmt = $conexion->query("SELECT valor FROM configuracion WHERE tipo='dia_habil'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dias[] = (int)$row['valor'];
}

header('Content-Type: application/json');
echo json_encode($dias);
