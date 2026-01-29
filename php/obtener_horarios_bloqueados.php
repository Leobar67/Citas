<?php
include("../conexion.php");

$stmt = $conexion->prepare(
    "SELECT valor FROM configuracion WHERE tipo='horario_bloqueado'"
);
$stmt->execute();

$horarios = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $horarios[] = $row['valor']; // ej: 13:00-14:00
}

header('Content-Type: application/json');
echo json_encode($horarios);
