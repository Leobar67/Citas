<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit;
}

include("../conexion.php");

if (!isset($_POST['id'])) {
    http_response_code(400);
    exit;
}

$id = (int)$_POST['id'];

$stmt = $conexion->prepare("DELETE FROM citas WHERE id = :id");
$stmt->execute([':id' => $id]);

echo "ok";
