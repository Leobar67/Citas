<?php
require_once "../conexion.php";

$limite = $conexion->query(
    "SELECT valor FROM configuracion 
     WHERE tipo = 'limite_citas_diarias' 
     LIMIT 1"
)->fetchColumn() ?: 20;

echo json_encode(['limite' => (int)$limite]);
