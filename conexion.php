<?php
$host = "db"; // 👈 nombre del servicio MySQL
$usuario = "root";
$password = "root";
$bd = "agenda_citas";

$conexion = new mysqli($host, $usuario, $password, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
