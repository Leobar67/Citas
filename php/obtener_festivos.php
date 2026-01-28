<?php
include("../conexion.php");

$result = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='festivo'"
);

$festivos = [];
while ($row = $result->fetch_assoc()) {
    $festivos[] = $row['valor']; // YYYY-MM-DD
}

echo json_encode($festivos);
