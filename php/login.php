<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
           <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

    <link rel="stylesheet" href="../css/dis.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh">

<form action="validar_login.php" method="POST" class="card p-4 shadow" style="width:350px">
    <h4 class="text-center mb-3">Acceso Administrador</h4>

    <input type="text" name="usuario" class="form-control mb-3" placeholder="Usuario" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>

    <button class="btn btn-utnc w-100">Ingresar</button>

</form>

</body>
</html>
