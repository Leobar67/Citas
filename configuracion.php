<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: php/login.php");
    exit;
}

require_once "conexion.php";

/* ================== GUARDAR CONFIGURACIÓN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* FESTIVOS */
    if (!empty($_POST['festivo'])) {
        $stmt = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('festivo', :valor)"
        );
        $stmt->execute([':valor' => $_POST['festivo']]);
    }

    /* HORARIOS BLOQUEADOS */
    if (!empty($_POST['bloqueo_inicio']) && !empty($_POST['bloqueo_fin'])) {
        $rango = $_POST['bloqueo_inicio'] . '-' . $_POST['bloqueo_fin'];
        $stmt = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('horario_bloqueado', :valor)"
        );
        $stmt->execute([':valor' => $rango]);
    }

    /* HORARIO DEL TURNO */
    if (!empty($_POST['turno_inicio']) && !empty($_POST['turno_fin'])) {

        $conexion->exec(
            "DELETE FROM configuracion WHERE tipo IN ('turno_inicio','turno_fin')"
        );

        $stmt = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES (:tipo, :valor)"
        );

        $stmt->execute([
            ':tipo'  => 'turno_inicio',
            ':valor' => $_POST['turno_inicio']
        ]);

        $stmt->execute([
            ':tipo'  => 'turno_fin',
            ':valor' => $_POST['turno_fin']
        ]);
    }

    /* DÍAS HÁBILES */
    if (isset($_POST['dias'])) {

        $conexion->exec(
            "DELETE FROM configuracion WHERE tipo = 'dia_habil'"
        );

        $stmt = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('dia_habil', :valor)"
        );

        foreach ($_POST['dias'] as $dia) {
            $stmt->execute([':valor' => $dia]);
        }
    }

    /* MESES HÁBILES */
    if (isset($_POST['guardar_meses'])) {

        $conexion->exec(
            "DELETE FROM configuracion WHERE tipo = 'mes_habil'"
        );

        if (!empty($_POST['meses'])) {
            $stmt = $conexion->prepare(
                "INSERT INTO configuracion (tipo, valor) VALUES ('mes_habil', :valor)"
            );

            foreach ($_POST['meses'] as $mes) {
                $stmt->execute([':valor' => $mes]);
            }
        }
    }
    /* TRÁMITES HABILITADOS */
if (isset($_POST['guardar_tramites'])) {

    $conexion->exec(
        "DELETE FROM configuracion WHERE tipo = 'tramite_habilitado'"
    );

    if (!empty($_POST['tramites'])) {
        $stmt = $conexion->prepare(
            "INSERT INTO configuracion (tipo, valor) VALUES ('tramite_habilitado', :valor)"
        );

        foreach ($_POST['tramites'] as $tramite) {
            $stmt->execute([':valor' => $tramite]);
        }
    }
}

    header("Location: configuracion.php");
    exit;
}

/* ================== ELIMINAR ================== */
if (isset($_GET['eliminar'])) {
    $stmt = $conexion->prepare(
        "DELETE FROM configuracion WHERE id = :id"
    );
    $stmt->execute([':id' => (int)$_GET['eliminar']]);

    header("Location: configuracion.php");
    exit;
}

/* ================== CONSULTAS ================== */
$festivos = $conexion->query(
    "SELECT id, valor FROM configuracion WHERE tipo = 'festivo' ORDER BY valor"
);

$bloqueos = $conexion->query(
    "SELECT id, valor FROM configuracion WHERE tipo = 'horario_bloqueado' ORDER BY valor"
);

$turnoInicio = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'turno_inicio' LIMIT 1"
)->fetchColumn() ?: '08:00';

$turnoFin = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'turno_fin' LIMIT 1"
)->fetchColumn() ?: '15:30';

$diasHabilitados = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'dia_habil'"
)->fetchAll(PDO::FETCH_COLUMN);

$mesesHabilitados = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'mes_habil'"
)->fetchAll(PDO::FETCH_COLUMN);

$tramitesHabilitados = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'tramite_habilitado'"
)->fetchAll(PDO::FETCH_COLUMN);
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

<!-- ================= FILA 1 ================= -->
<div class="row g-4 mb-4">

    <!-- FESTIVOS -->
    <div class="col-md-6">
        <div class="card shadow h-100 card-config">
            <div class="card-body">
                <h5 class="text-utnc fw-bold">Días festivos</h5>

                <form method="POST" class="d-flex gap-2 mb-3">
                    <input type="date" name="festivo" class="form-control" required>
                    <button class="btn btn-utnc">Agregar</button>
                </form>

                <ul class="list-group">
                <?php while ($f = $festivos->fetch(PDO::FETCH_ASSOC)): ?>
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
        <div class="card shadow h-100 card-config">
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
                <?php while ($b = $bloqueos->fetch(PDO::FETCH_ASSOC)): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <?= $b['valor'] ?>
                        <a href="?eliminar=<?= $b['id'] ?>" class="btn btn-sm btn-danger">✖</a>
                    </li>
                <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- ================= FILA 2 ================= -->
<div class="row g-4 mb-4">

    <!-- HORARIO DEL TURNO -->
    <div class="col-md-6">
        <div class="card shadow h-100 card-config">
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
        <div class="card shadow h-100 card-config">
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

<!-- ================= FILA 3 ================= -->
<div class="row g-4 justify-content-center">
    
    <!-- MESES HABILITADOS -->
    <div class="col-md-8 col-lg-6">
        <div class="card shadow h-100 card-config">
            <div class="card-body">
                <h5 class="text-utnc fw-bold text-center">Meses habilitados</h5>

                <form method="POST">
                <input type="hidden" name="guardar_meses" value="1">

                <?php
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];

                foreach ($meses as $num => $nombre):
                ?>
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="meses[]"
                               value="<?= $num ?>"
                               <?= in_array((string)$num, $mesesHabilitados, true) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $nombre ?></label>
                    </div>
                <?php endforeach; ?>

                <button class="btn btn-utnc mt-3 w-100">
                    Guardar meses habilitados
                </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TRÁMITES HABILITADOS -->
<div class="col-md-8 col-lg-6 mt-4">
    <div class="card shadow h-100 card-config">
        <div class="card-body">
            <h5 class="text-primary fw-bold text-center">
                Trámites habilitados
            </h5>

            <form method="POST">
            <input type="hidden" name="guardar_tramites" value="1">

            <?php
            $listaTramites = [
                1 => 'Recoger título TSU',
                2 => 'Recoger título Ingeniería',
                3 => 'Recoger título Licenciatura',
                4 => 'Tramitar título TSU',
                5 => 'Tramitar título Ingeniería',
                6 => 'Tramitar título Licenciatura',
                7 => 'Recoger papelería'
            ];

            foreach ($listaTramites as $id => $nombre):
            ?>
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="tramites[]"
                           value="<?= $id ?>"
                           <?= in_array((string)$id, $tramitesHabilitados, true) ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        <?= $nombre ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <button class="btn btn-utnc mt-3 w-100">
                Guardar trámites habilitados
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








