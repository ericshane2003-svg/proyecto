<?php
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

session_start();
include '../Modelo/conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    exit("No tienes permisos para ver este reporte.");
}

$fecha_reporte = date('d/m/Y H:i');

$html = "
    <h2 style='text-align:center;'>Bitácora del Sistema (Logs)</h2>
    <p><strong>Generado el:</strong> $fecha_reporte</p>
    <table border='1' width='100%' style='border-collapse: collapse; font-family: sans-serif; text-align: left;'>
        <tr style='background-color: #f2f2f2;'>
            <th style='padding: 8px;'>Usuario</th>
            <th style='padding: 8px;'>Acción Realizada</th>
            <th style='padding: 8px;'>Fecha y Hora</th>
        </tr>
";

$res = $conexion->query("SELECT usuario, accion, fecha_hora FROM logs_sistema ORDER BY fecha_hora DESC");

while ($l = $res->fetch_assoc()) {
    $html .= "<tr>
                <td style='padding: 8px;'>" . htmlspecialchars($l['usuario']) . "</td>
                <td style='padding: 8px;'>" . htmlspecialchars($l['accion']) . "</td>
                <td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($l['fecha_hora'])) . "</td>
              </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Logs_" . time() . ".pdf", ["Attachment" => true]);
?>