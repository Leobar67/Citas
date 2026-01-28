<?php
include("../conexion.php");

$stmt = $conexion->query("SELECT valor FROM configuracion WHERE tipo='festivo'");
$festivos = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($festivos);
