<?php
require_once "conexion.php";

$tramitesHabilitados = $conexion->query(
    "SELECT valor FROM configuracion WHERE tipo = 'tramite_habilitado'"
)->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

    <link rel="stylesheet" href="css/dis.css">
</head>

<body class="d-flex flex-column min-vh-100">

<header class="header-utnc position-relative text-center">
    <h1 class="m-0 text-white">Agenda de Citas</h1>

    <a href="php/login.php"
       class="btn btn-light btn-sm position-absolute end-0 top-50 translate-middle-y me-4">
        Inicio de sesión
    </a>
</header>

<main class="container-fluid py-5 flex-grow-1">
<div class="row justify-content-center">
<div class="col-12 col-md-11 col-lg-10 col-xl-8">

<div id="mensaje"></div>

<form id="formCita" class="card shadow-lg p-5">

<h3 class="text-center mb-4 text-success">Agendar Cita</h3>

<div class="row">
<div class="col-md-6 mb-3">
<label for="matricula" class="form-label">Matrícula</label>
<input id="matricula" name="matricula" type="text" class="form-control form-control-lg" required>
</div>

<div class="col-md-6 mb-3">
<label for="telefono" class="form-label">Teléfono</label>
<input id="telefono" name="telefono" type="tel" class="form-control form-control-lg" required>
</div>
</div>

<div class="mb-3">
<label for="nombre" class="form-label">Nombre completo</label>
<input id="nombre" name="nombre" type="text" class="form-control form-control-lg" required>
</div>

<div class="mb-3">
<label for="programa" class="form-label">Programa educativo</label>
<input id="programa" name="programa" type="text" class="form-control form-control-lg" required>
</div>

<div class="mb-3">
<label for="tramite" class="form-label">Trámite</label>
<select id="tramite" name="tramite" class="form-select form-select-lg" required>
<option value="">Seleccione</option>

<?php
$listaTramites = [
    1 => 'Recoger título Técnico Superior Universitario',
    2 => 'Recoger título Ingeniería',
    3 => 'Recoger título Licenciatura',
    4 => 'Tramitar título Técnico Superior Universitario',
    5 => 'Tramitar título Ingeniería',
    6 => 'Tramitar título Licenciatura',
    7 => 'Recoger papelería'
];

foreach ($listaTramites as $id => $nombre):
    if (in_array((string)$id, $tramitesHabilitados, true)):
?>
<option value="<?= $id ?>"><?= $nombre ?></option>
<?php
    endif;
endforeach;
?>
</select>
</div>


    
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Fecha</label>
<input type="text" name="fecha" id="fecha"
class="form-control form-control-lg"
placeholder="Seleccione una fecha" required>
</div>

<div class="col-md-6 mb-4">
<label class="form-label">Hora</label>
<select name="hora" id="hora" class="form-select form-select-lg" required>
<option value="">Seleccione una hora</option>
</select>
</div>
</div>

<button class="btn btn-success btn-lg w-100">
Agendar cita
</button>

</form>
</div>
</div>
</main>

<footer class="text-center py-3 bg-utnc text-white">
© 2026 Universidad Tecnológica del Norte de Coahuila
</footer>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const horaSelect = document.getElementById('hora');
const fechaInput = document.getElementById('fecha');
const form = document.getElementById('formCita');
const mensaje = document.getElementById('mensaje');

let festivos = [];
let bloqueos = [];
let diasHabiles = [];
let turnoInicio = "08:00";
let turnoFin = "15:30";
let mesesHabiles = [];
let limiteCitas = 20;    

/* CARGAR CONFIGURACIÓN */
Promise.all([
    fetch('php/obtener_festivos.php').then(r=>r.json()),
    fetch('php/obtener_horarios_bloqueados.php').then(r=>r.json()),
    fetch('php/obtener_dias_habiles.php').then(r=>r.json()),
    fetch('php/obtener_turno.php').then(r=>r.json()),
    fetch('php/obtener_meses_habiles.php').then(r=>r.json())
    fetch('php/obtener_limite.php').then(r=>r.json())
]).then(([f,b,d,t,m,l])=>{
    festivos = f;
    bloqueos = b;
    diasHabiles = d.map(Number);
    turnoInicio = t.inicio;
    turnoFin = t.fin;
    mesesHabiles = m;
    limiteCitas = l.limite;

    iniciarCalendario();
});

/* CALENDARIO */
function iniciarCalendario() {
    flatpickr("#fecha", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: [
            // ❌ Bloquear días no habilitados
            d => !diasHabiles.includes(d.getDay()),

            // ❌ Bloquear festivos
            d => festivos.includes(d.toISOString().split('T')[0]),

            // ❌ Bloquear meses NO habilitados
            d => !mesesHabiles.includes(d.getMonth() + 1)
        ],
        onDayCreate(_,__,___,day){
            if (!day.classList.contains("flatpickr-disabled")) {
                day.classList.add("dia-habil");
            }
        },
        onChange: cargarHoras
    });
}

/* HORAS */
function cargarHoras() {

    const fecha = fechaInput.value;

    fetch(`php/obtener_horas.php?fecha=${fecha}`)
    .then(r=>r.json())
    .then(ocupadas=>{

        horaSelect.innerHTML = '<option value="">Seleccione una hora</option>';

        const [hi,mi] = turnoInicio.split(':').map(Number);
        const [hf,mf] = turnoFin.split(':').map(Number);

        let inicio = hi*60 + mi;
        let fin = hf*60 + mf;

        let totalMinutos = fin - inicio;

        // 👇 INTERVALO AUTOMÁTICO
        let intervalo = Math.floor(totalMinutos / limiteCitas);

        // seguridad mínima (evita división rara)
        if (intervalo < 1) intervalo = 1;

        for (let min = inicio; min <= fin; min += intervalo) {

            let h = Math.floor(min/60);
            let m = String(min%60).padStart(2,'0');
            let valor = `${String(h).padStart(2,'0')}:${m}:00`;

            let bloqueado = bloqueos.some(r=>{
                let [i,f]=r.split('-');
                return valor>=i && valor<f;
            });

            if (bloqueado) continue;

            let op = document.createElement('option');
            op.value = valor;
            op.textContent = `${h%12||12}:${m} ${h>=12?'PM':'AM'}`;

            if (ocupadas.includes(valor)) {
                op.disabled = true;
                op.textContent += ' (No disponible)';
            }

            horaSelect.appendChild(op);
        }
    });
}

/* ENVÍO */
form.addEventListener('submit', e=>{
    e.preventDefault();
    const data=new FormData(form);

    fetch('php/procesar.php',{method:'POST',body:data})
    .then(r=>r.json())
    .then(res=>{
        if(res.preguntarReagendar){
            if(confirm(res.mensaje)){
                data.append('reagendar','1');
                fetch('php/procesar.php',{method:'POST',body:data})
                .then(r=>r.json())
                .then(fin=>mostrar(fin));
            }
        } else mostrar(res);
    });
});

function mostrar(r){
    mensaje.innerHTML=`
    <div class="alert alert-${r.tipo} alert-dismissible fade show">
        ${r.mensaje}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    if(r.tipo==='success'){
        form.reset();
        horaSelect.innerHTML='<option value="">Seleccione una hora</option>';
    }
}
</script>

</body>
</html>






