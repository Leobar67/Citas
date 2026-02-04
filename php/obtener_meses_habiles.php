<?php
include("../conexion.php");

$meses = [];

$res = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='mes_habil'"
);

while ($m = $res->fetch_assoc()) {
    $meses[] = (int)$m['valor']; // 1 a 12
}

echo json_encode($meses);
