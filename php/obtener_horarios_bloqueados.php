<?php
include("../conexion.php");

$result = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='horario_bloqueado'"
);

$horarios = [];
while ($row = $result->fetch_assoc()) {
    $horarios[] = $row['valor']; // ej: 13:00-14:00
}

echo json_encode($horarios);
