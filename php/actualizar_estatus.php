<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

include("../conexion.php");

$id = $_POST['id'];
$estatus = $_POST['estatus'];

$sql = $conexion->prepare(
    "UPDATE citas SET estatus = ? WHERE id = ?"
);
$sql->bind_param("si", $estatus, $id);
$sql->execute();
