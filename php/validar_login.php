<?php
session_start();
include("../conexion.php");

// Recoger datos del formulario
$usuario = $_POST['usuario'];
$password = hash('sha256', $_POST['password']);

try {
    // Preparar consulta con PDO usando parámetros nombrados
    $sql = $conexion->prepare(
        "SELECT id FROM admin WHERE usuario = :usuario AND password = :password"
    );

    // Ejecutar la consulta pasando los valores
    $sql->execute([
        ':usuario' => $usuario,
        ':password' => $password
    ]);

    // Obtener la fila (si existe)
    $admin = $sql->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $_SESSION['admin'] = true;
        header("Location: ../admin.php");
        exit;
    } else {
        echo "Credenciales incorrectas <br><a href='login.php'>Volver</a>";
    }

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
