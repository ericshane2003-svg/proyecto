<?php
require_once '../dompdf/autoload.inc.php'; // Asegúrate de que tu dompdf esté ahí
use Dompdf\Dompdf;

session_start();
include '../Modelo/conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    exit("No tienes permisos para ver este reporte.");
}

$fecha_reporte = date('d/m/Y H:i');

$html = "
    <h2 style='text-align:center;'>Reporte Histórico de Ventas</h2>
    <p><strong>Generado el:</strong> $fecha_reporte</p>
    <table border='1' width='100%' style='border-collapse: collapse; font-family: sans-serif; text-align: left;'>
        <tr style='background-color: #f2f2f2;'>
            <th style='padding: 8px;'>Producto</th>
            <th style='padding: 8px;'>Cantidad Vendida</th>
            <th style='padding: 8px;'>Fecha y Hora</th>
        </tr>
";

$res = $conexion->query("SELECT p.nombre, v.cantidad, v.fecha FROM ventas v JOIN productos p ON v.producto_id = p.id ORDER BY v.fecha DESC");

while ($v = $res->fetch_assoc()) {
    $html .= "<tr>
                <td style='padding: 8px;'>" . htmlspecialchars($v['nombre']) . "</td>
                <td style='padding: 8px;'>" . $v['cantidad'] . "</td>
                <td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($v['fecha'])) . "</td>
              </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Ventas_" . time() . ".pdf", ["Attachment" => true]);
?>