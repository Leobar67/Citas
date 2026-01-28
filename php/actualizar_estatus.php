<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

include("../conexion.php");

$id = $_POST['id'];
$estatus = $_POST['estatus'];

$stmt = $conexion->prepare("UPDATE citas SET estatus = :estatus WHERE id = :id");
$stmt->execute([
    ':estatus' => $estatus,
    ':id' => $id
]);

echo "ok";
