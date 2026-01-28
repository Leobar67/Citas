<?php
session_start();

/* PROTECCIÓN */
if (!isset($_SESSION['admin'])) {
    exit("Acceso denegado");
}

include("../conexion.php");

/* FECHA OPCIONAL */
$fecha = $_GET['fecha'] ?? null;

/* CONSULTA */
$sql = "SELECT matricula, nombre, telefono, programa, tramite, fecha, hora, estatus
        FROM citas";

if ($fecha) {
    $sql .= " WHERE fecha = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexion->query($sql);
}

/* HEADERS PARA EXCEL */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=citas.xls");
header("Pragma: no-cache");
header("Expires: 0");

/* BOM UTF-8 (acentos y ñ correctos) */
echo "\xEF\xBB\xBF";

/* TABLA */
echo "<table border='1'>";
echo "<tr>
        <th>Matrícula</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Programa</th>
        <th>Trámite</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Estatus</th>
      </tr>";

$fila = 2; // Excel empieza en fila 2

while ($c = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$c['matricula']}</td>
            <td>{$c['nombre']}</td>
            <td>{$c['telefono']}</td>
            <td>{$c['programa']}</td>
            <td>{$c['tramite']}</td>
            <td>{$c['fecha']}</td>
            <td>{$c['hora']}</td>
            <td>{$c['estatus']}</td>
          </tr>";
    $fila++;
}

echo "</table>";

/* LISTA DESPLEGABLE EN EXCEL (SOLO VISUAL) */
echo "
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Citas</x:Name>
    <x:WorksheetOptions>
     <x:DataValidation>
      <x:Range>H2:H$fila</x:Range>
      <x:Type>List</x:Type>
      <x:Value>\"Pendiente,Se presentó,No se presentó,Reagendado\"</x:Value>
     </x:DataValidation>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
";

exit;
