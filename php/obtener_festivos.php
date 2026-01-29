<?php
include("../conexion.php");

$stmt = $conexion->prepare(
    "SELECT valor FROM configuracion WHERE tipo='festivo'"
);
$stmt->execute();

$festivos = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $festivos[] = $row['valor'];
}

header('Content-Type: application/json');
echo json_encode($festivos);
