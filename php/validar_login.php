<?php
session_start();
include("../conexion.php");

$usuario = $_POST['usuario'];
$password = hash('sha256', $_POST['password']);

$stmt = $conexion->prepare("SELECT id FROM admin WHERE usuario = :usuario AND password = :password");
$stmt->execute([
    ':usuario' => $usuario,
    ':password' => $password
]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $_SESSION['admin'] = true;
    header("Location: ../admin.php");
    exit;
} else {
    echo "Credenciales incorrectas <br><a href='login.php'>Volver</a>";
}
