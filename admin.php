<?php 
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: php/login.php");
    exit;
}

include("conexion.php");

/* FILTROS */
$filtroFecha = $_GET['fecha'] ?? '';
$vista = $_GET['vista'] ?? 'cards';

/* CONSULTA */
$sql = "SELECT * FROM citas";
if ($filtroFecha) {
    $sql .= " WHERE fecha = '$filtroFecha'";
}
$sql .= " ORDER BY fecha, hora";

$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dis.css">
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar bg-utnc px-4 position-relative">
    <span class="navbar-brand mx-auto fw-bold text-white">
        Panel Administrador
    </span>

    <div class="position-absolute end-0 me-4 d-flex gap-2">
        <a href="configuracion.php" class="btn btn-light">Configuración</a>
        <a href="php/logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
</nav>

<main class="container my-4 flex-grow-1">

    <!-- FILTROS -->
    <div class="row mb-4 g-3 align-items-end">

        <div class="col-md-4">
            <input type="text" id="buscador" class="form-control"
                   placeholder="Buscar por matrícula, nombre, teléfono o trámite">
        </div>

        <div class="col-md-3">
            <form method="GET" id="formFecha">
                <input type="hidden" name="vista" value="<?= $vista ?>">
                <input type="date" name="fecha" class="form-control"
                       value="<?= htmlspecialchars($filtroFecha) ?>"
                       onchange="this.form.submit()">
            </form>
        </div>

        <div class="col-md-3">
            <div class="btn-group w-100">
                <a href="?vista=cards<?= $filtroFecha ? '&fecha='.$filtroFecha : '' ?>"
                   class="btn <?= $vista=='cards'?'btn-utnc':'btn-outline-secondary' ?>">
                   Tarjetas
                </a>
                <a href="?vista=tabla<?= $filtroFecha ? '&fecha='.$filtroFecha : '' ?>"
                   class="btn <?= $vista=='tabla'?'btn-utnc':'btn-outline-secondary' ?>">
                   Tabla
                </a>
            </div>
        </div>

        <div class="col-md-2">
            <a href="admin.php" class="btn btn-secondary w-100">Ver todas</a>
        </div>
        <div class="row mb-4 g-3">
    <div class="col-md-6">
        <a href="php/exportar_excel.php"
           class="btn btn-success w-100">
            📥 Descargar TODAS las citas
        </a>
    </div>

    <div class="col-md-6">
        <?php if ($filtroFecha): ?>
        <a href="php/exportar_excel.php?fecha=<?= $filtroFecha ?>"
           class="btn btn-utnc w-100">
            📅 Descargar citas del <?= $filtroFecha ?>
        </a>
        <?php else: ?>
        <button class="btn btn-secondary w-100" disabled>
            📅 Selecciona una fecha para descargar
        </button>
        <?php endif; ?>
    </div>
</div>


    </div>

    <!-- ================== VISTA TARJETAS ================== -->
    <?php if ($vista === 'cards'): ?>
    <div class="row">
        <?php while($c = $resultado->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4 mb-4 cita">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5 class="text-utnc fw-bold"><?= $c['nombre'] ?></h5>

                    <p class="mb-1"><strong>Matrícula:</strong> <?= $c['matricula'] ?></p>
                    <p class="mb-1"><strong>Teléfono:</strong> <?= $c['telefono'] ?></p>
                    <p class="mb-1"><strong>Programa:</strong> <?= $c['programa'] ?></p>
                    <p class="mb-1"><strong>Trámite:</strong> <?= $c['tramite'] ?></p>
                    <p class="mb-1"><strong>Fecha:</strong> <?= $c['fecha'] ?></p>
                    <p class="mb-3"><strong>Hora:</strong> <?= $c['hora'] ?></p>

                    <div class="d-flex gap-2">
                        <select class="form-select"
                                onchange="actualizarEstatus(<?= $c['id'] ?>, this.value)">
                            <option <?= $c['estatus']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                            <option <?= $c['estatus']=='Se presentó'?'selected':'' ?>>Se presentó</option>
                            <option <?= $c['estatus']=='No se presentó'?'selected':'' ?>>No se presentó</option>
                            <option <?= $c['estatus']=='Reagendado'?'selected':'' ?>>Reagendado</option>
                        </select>

                        <button class="btn btn-danger"
                                onclick="eliminarCita(<?= $c['id'] ?>)">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- ================== VISTA TABLA ================== -->
    <?php if ($vista === 'tabla'): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-utnc">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Programa</th>
                    <th>Trámite</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $resultado->fetch_assoc()): ?>
                <tr class="cita">
                    <td><?= $c['matricula'] ?></td>
                    <td><?= $c['nombre'] ?></td>
                    <td><?= $c['telefono'] ?></td>
                    <td><?= $c['programa'] ?></td>
                    <td><?= $c['tramite'] ?></td>
                    <td><?= $c['fecha'] ?></td>
                    <td><?= $c['hora'] ?></td>
                    <td>
                        <select class="form-select form-select-sm"
                                onchange="actualizarEstatus(<?= $c['id'] ?>, this.value)">
                            <option <?= $c['estatus']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                            <option <?= $c['estatus']=='Se presentó'?'selected':'' ?>>Se presentó</option>
                            <option <?= $c['estatus']=='No se presentó'?'selected':'' ?>>No se presentó</option>
                            <option <?= $c['estatus']=='Reagendado'?'selected':'' ?>>Reagendado</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm"
                                onclick="eliminarCita(<?= $c['id'] ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</main>

<footer class="text-center py-3 bg-utnc text-white">
    © 2026 Universidad Tecnológica del Norte de Coahuila
</footer>

<script>
/* BUSCADOR */
document.getElementById("buscador").addEventListener("keyup", function () {
    const texto = this.value.toLowerCase();
    document.querySelectorAll(".cita").forEach(cita => {
        cita.style.display = cita.innerText.toLowerCase().includes(texto) ? "" : "none";
    });
});

/* ACTUALIZAR ESTATUS */
function actualizarEstatus(id, estatus) {
    fetch("php/actualizar_estatus.php", {
        method: "POST",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&estatus=${estatus}`
    });
}

/* ELIMINAR CITA */
function eliminarCita(id) {
    if (!confirm("¿Seguro que deseas eliminar esta cita?\nEsta acción no se puede deshacer.")) {
        return;
    }

    fetch("php/eliminar_cita.php", {
        method: "POST",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
    })
    .then(r => r.text())
    .then(res => {
        if (res === "ok") {
            location.reload();
        } else {
            alert("Error al eliminar la cita");
        }
    });
}
</script>

</body>
</html>
