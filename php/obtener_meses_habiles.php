<?php
require_once "../conexion.php";

$meses = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'mes_habil'"
)->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(array_map('intval', $meses));
