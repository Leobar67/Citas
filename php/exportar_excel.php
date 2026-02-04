<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

include("../conexion.php");

$tramites = [
    1 => 'Rec T TSU',
    2 => 'Rec T Ingeniería',
    3 => 'Rec T Licenciatura',
    4 => 'Tra T TSU',
    5 => 'Tra T Ingeniería',
    6 => 'Tra T Licenciatura',
    7 => 'Rec papelería'
];

$fecha = $_GET['fecha'] ?? null;

$sql = "SELECT matricula, nombre, telefono, programa, tramite, fecha, hora, estatus FROM citas";
if ($fecha) {
    $stmt = $conexion->prepare($sql . " WHERE fecha = :fecha");
    $stmt->execute([':fecha' => $fecha]);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $conexion->query($sql);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Headers para Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=citas.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo "\xEF\xBB\xBF"; // BOM UTF-8

echo "<table border='1'>
<tr>
<th>Matrícula</th><th>Nombre</th><th>Teléfono</th><th>Programa</th>
<th>Trámite</th><th>Fecha</th><th>Hora</th><th>Estatus</th>
</tr>";

foreach ($resultado as $c) {
    echo "<tr>
            <td>{$c['matricula']}</td>
            <td>{$c['nombre']}</td>
            <td>{$c['telefono']}</td>
            <td>{$c['programa']}</td>
            <td>{$tramites[$c['tramite']]}</td>
            <td>{$c['fecha']}</td>
            <td>{$c['hora']}</td>
            <td>{$c['estatus']}</td>
          </tr>";
}

echo "</table>";
exit;



