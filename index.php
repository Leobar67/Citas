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
    <script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>
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
<input id="telefono" name="telefono" type="text" class="form-control form-control-lg" maxlength="12" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
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
    fetch('php/obtener_meses_habiles.php').then(r=>r.json()),
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
    .then(data=>{

        const ocupadas = data.horas;   // ← ahora viene dentro del objeto
        const totalDia = data.total;   // ← total de citas activas
        const limite = data.limite;    // ← límite dinámico desde BD

        horaSelect.innerHTML = '<option value="">Seleccione una hora</option>';

        // 🚨 BLOQUEAR COMPLETAMENTE EL DÍA SI LLEGÓ AL LÍMITE
        if (totalDia >= limite) {
            horaSelect.innerHTML = '<option value="">Cupo lleno este día</option>';
            horaSelect.disabled = true;
            return;
        } else {
            horaSelect.disabled = false;
        }

        const [hi,mi] = turnoInicio.split(':').map(Number);
        const [hf,mf] = turnoFin.split(':').map(Number);

        let inicio = hi*60 + mi;
        let fin = hf*60 + mf;

        let totalMinutos = fin - inicio;

        // 👇 INTERVALO AUTOMÁTICO BASADO EN EL LÍMITE REAL
        let intervalo = Math.floor(totalMinutos / limite);

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

        // 👇 Obtener datos antes de resetear
        const nombre = document.getElementById('nombre').value;
        const matricula = document.getElementById('matricula').value;
        const telefono = document.getElementById('telefono').value;
        const programa = document.getElementById('programa').value;
        const tramite = document.getElementById('tramite').selectedOptions[0].text;
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        // 👇 Llenar comprobante
        document.getElementById('c_nombre').textContent = nombre;
        document.getElementById('c_matricula').textContent = matricula;
        document.getElementById('c_telefono').textContent = telefono;
        document.getElementById('c_programa').textContent = programa;
        document.getElementById('c_tramite').textContent = tramite;
        document.getElementById('c_fecha').textContent = fecha;
        document.getElementById('c_hora').textContent = hora;

        const comprobante = document.getElementById('comprobante');
        comprobante.style.display = 'block';

        // 👇 Generar imagen
        html2canvas(comprobante).then(canvas => {

            let link = document.createElement('a');
            link.download = 'cita.png';
            link.href = canvas.toDataURL();
            link.click();

            comprobante.style.display = 'none';
        });

        form.reset();
        horaSelect.innerHTML='<option value="">Seleccione una hora</option>';
    }
}
</script>

<div id="comprobante" style="width:400px; padding:20px; background:#fff; border-radius:12px; display:none; font-family:Arial;">

    <h2 style="text-align:center; color:#198754;">Cita Agendada</h2>
    <hr>

    <p><strong>Nombre:</strong> <span id="c_nombre"></span></p>
    <p><strong>Matrícula:</strong> <span id="c_matricula"></span></p>
    <p><strong>Teléfono:</strong> <span id="c_telefono"></span></p>
    <p><strong>Programa:</strong> <span id="c_programa"></span></p>
    <p><strong>Trámite:</strong> <span id="c_tramite"></span></p>
    <p><strong>Fecha:</strong> <span id="c_fecha"></span></p>
    <p><strong>Hora:</strong> <span id="c_hora"></span></p>

    <hr>

    <p style="text-align:center; font-size:12px;">
        Presentarse 10 minutos antes <br>
        Universidad Tecnológica del Norte de Coahuila
    </p>

</div>
    


</body>
</html>








