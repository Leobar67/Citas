<?php
session_start();
include("../conexion.php");

$usuario = $_POST['usuario'];
$password = hash('sha256', $_POST['password']);

$sql = $conexion->prepare(
    "SELECT id FROM admin WHERE usuario = ? AND password = ?"
);
$sql->bind_param("ss", $usuario, $password);
$sql->execute();
$sql->store_result();

if ($sql->num_rows === 1) {
    $_SESSION['admin'] = true;
    header("Location: ../admin.php");
} else {
    echo "Credenciales incorrectas <br><a href='login.php'>Volver</a>";
}
