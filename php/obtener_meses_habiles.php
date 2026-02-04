<?php
require_once "../conexion.php";

$meses = [];

$res = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'mes_habil'"
);

while ($m = $res->fetch(PDO::FETCH_ASSOC)) {
    $meses[] = (int)$m['valor'];
}

echo json_encode($meses);
