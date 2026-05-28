<?php
require_once('../vendor/autoload.php');
use Dompdf\Dompdf;

session_start();
error_reporting(0);
include __DIR__ . '/../Modelo/conexion.php';

// Seguridad: Solo el admin puede ver esto
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    exit("Acceso denegado. Solo administradores.");
}

$html = "
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0dcaf0; padding-bottom: 10px; margin-bottom: 20px; }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .tabla td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
    
    <div class='header'>
        <h2>📈 Histórico de Ventas Globales</h2>
        <p>Reporte generado el: " . date('d/m/Y H:i') . "</p>
    </div>
    
    <table class='tabla'>
        <tr>
            <th>Producto</th>
            <th>Cantidad Vendida</th>
            <th>Fecha de Venta</th>
        </tr>
";

// Consultamos las ventas cruzando con el nombre del producto
$res_v = $conexion->query("SELECT p.nombre, v.cantidad, v.fecha FROM ventas v JOIN productos p ON v.producto_id = p.id ORDER BY v.fecha DESC");

while($v = $res_v->fetch_assoc()) {
    $html .= "<tr>
                <td>" . htmlspecialchars($v['nombre']) . "</td>
                <td style='text-align: center;'><strong>" . $v['cantidad'] . "</strong></td>
                <td>" . date('d/m/Y H:i', strtotime($v['fecha'])) . "</td>
              </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Ventas_" . date('Ymd_Hi') . ".pdf", ["Attachment" => true]);
?>
