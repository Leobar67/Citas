<?php
include("../conexion.php");

$stmt = $conexion->query("SELECT valor FROM configuracion WHERE tipo='horario_bloqueado'");
$horarios = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($horarios);
