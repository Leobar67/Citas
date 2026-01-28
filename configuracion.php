<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: php/login.php");
    exit;
}
include("conexion.php");

/* ================== GUARDAR CONFIGURACIÓN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* FESTIVOS */
    if (!empty($_POST['festivo'])) {
        $sql = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('festivo', ?)"
        );
        $sql->bind_param("s", $_POST['festivo']);
        $sql->execute();
    }

    /* HORARIOS BLOQUEADOS */
    if (!empty($_POST['bloqueo_inicio']) && !empty($_POST['bloqueo_fin'])) {
        $rango = $_POST['bloqueo_inicio'] . '-' . $_POST['bloqueo_fin'];
        $sql = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('horario_bloqueado', ?)"
        );
        $sql->bind_param("s", $rango);
        $sql->execute();
    }

    /* HORARIO DEL TURNO */
    if (!empty($_POST['turno_inicio']) && !empty($_POST['turno_fin'])) {

        $conexion->query(
            "DELETE FROM configuracion WHERE tipo IN ('turno_inicio','turno_fin')"
        );

        $sql = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES (?, ?)"
        );

        $tipo = 'turno_inicio';
        $sql->bind_param("ss", $tipo, $_POST['turno_inicio']);
        $sql->execute();

        $tipo = 'turno_fin';
        $sql->bind_param("ss", $tipo, $_POST['turno_fin']);
        $sql->execute();
    }

    /* DÍAS HÁBILES */
    if (isset($_POST['dias'])) {

        $conexion->query(
            "DELETE FROM configuracion WHERE tipo='dia_habil'"
        );

        foreach ($_POST['dias'] as $dia) {
            $sql = $conexion->prepare(
                "INSERT INTO configuracion (tipo, valor) VALUES ('dia_habil', ?)"
            );
            $sql->bind_param("s", $dia);
            $sql->execute();
        }
    }

    header("Location: configuracion.php");
    exit;
}

/* ================== ELIMINAR ================== */
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $conexion->query("DELETE FROM configuracion WHERE id=$id");
    header("Location: configuracion.php");
    exit;
}

/* ================== CONSULTAS ================== */
$festivos = $conexion->query(
    "SELECT * FROM configuracion WHERE tipo='festivo'"
);

$bloqueos = $conexion->query(
    "SELECT * FROM configuracion WHERE tipo='horario_bloqueado'"
);

$turnoInicio = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='turno_inicio' LIMIT 1"
)->fetch_assoc()['valor'] ?? '08:00';

$turnoFin = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='turno_fin' LIMIT 1"
)->fetch_assoc()['valor'] ?? '15:30';

$diasHabilitados = [];
$res = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo='dia_habil'"
);
while ($d = $res->fetch_assoc()) {
    $diasHabilitados[] = $d['valor'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/dis.css">
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar bg-utnc px-4">
    <span class="navbar-brand mx-auto text-white fw-bold">
        Configuración del Sistema
    </span>
    <a href="admin.php" class="btn btn-light">Volver</a>
</nav>

<main class="container my-4 flex-grow-1">
<div class="row g-4">

<!-- FESTIVOS -->
<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="text-utnc fw-bold">Días festivos</h5>

<form method="POST" class="d-flex gap-2 mb-3">
    <input type="date" name="festivo" class="form-control" required>
    <button class="btn btn-utnc">Agregar</button>
</form>

<ul class="list-group">
<?php while($f = $festivos->fetch_assoc()): ?>
<li class="list-group-item d-flex justify-content-between">
    <?= $f['valor'] ?>
    <a href="?eliminar=<?= $f['id'] ?>" class="btn btn-sm btn-danger">✖</a>
</li>
<?php endwhile; ?>
</ul>
</div>
</div>
</div>

<!-- HORARIOS BLOQUEADOS -->
<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="text-utnc fw-bold">Horarios restringidos</h5>

<form method="POST" class="row g-2 mb-3">
    <div class="col">
        <input type="time" name="bloqueo_inicio" class="form-control" required>
    </div>
    <div class="col">
        <input type="time" name="bloqueo_fin" class="form-control" required>
    </div>
    <div class="col-12">
        <button class="btn btn-utnc w-100">Agregar</button>
    </div>
</form>

<ul class="list-group">
<?php while($b = $bloqueos->fetch_assoc()): ?>
<li class="list-group-item d-flex justify-content-between">
    <?= $b['valor'] ?>
    <a href="?eliminar=<?= $b['id'] ?>" class="btn btn-sm btn-danger">✖</a>
</li>
<?php endwhile; ?>
</ul>
</div>
</div>
</div>

<!-- HORARIO DEL TURNO -->
<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="text-utnc fw-bold">Horario del turno</h5>

<form method="POST" class="row g-2">
    <div class="col">
        <input type="time" name="turno_inicio" class="form-control"
               value="<?= $turnoInicio ?>" required>
    </div>
    <div class="col">
        <input type="time" name="turno_fin" class="form-control"
               value="<?= $turnoFin ?>" required>
    </div>
    <div class="col-12">
        <button class="btn btn-utnc w-100">Guardar</button>
    </div>
</form>
</div>
</div>
</div>

<!-- DÍAS HÁBILES -->
<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="text-utnc fw-bold">Días habilitados</h5>

<form method="POST">
<?php
$dias = [
    0 => 'Domingo',
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado'
];
foreach ($dias as $num => $nombre):
?>
<div class="form-check">
    <input class="form-check-input"
           type="checkbox"
           name="dias[]"
           value="<?= $num ?>"
           <?= in_array($num, $diasHabilitados) ? 'checked' : '' ?>>
    <label class="form-check-label"><?= $nombre ?></label>
</div>
<?php endforeach; ?>

<button class="btn btn-utnc mt-3 w-100">
    Guardar días habilitados
</button>
</form>
</div>
</div>
</div>

</div>
</main>

<footer class="text-center py-3 bg-utnc text-white">
© 2026 Universidad Tecnológica del Norte de Coahuila
</footer>

</body>
</html>
