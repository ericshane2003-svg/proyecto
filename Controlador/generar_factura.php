<?php
require_once '../dompdf/autoload.inc.php'; 
use Dompdf\Dompdf;

session_start();
if (!isset($_SESSION['usuario'])) exit("No autorizado");

// Obtenemos los detalles guardados en pagar.php
$items_comprados = $_SESSION['ultima_compra'] ?? [];
$total_pagado = $_SESSION['ultimo_total'] ?? 0;
$fecha = date('d/m/Y H:i');
$usuario = $_SESSION['usuario'];

if(empty($items_comprados)) {
    exit("No hay detalles de la compra reciente para generar la factura.");
}

$html = "
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #198754; padding-bottom: 10px; margin-bottom: 20px; }
        .detalle { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detalle th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .detalle td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .total { text-align: right; margin-top: 20px; font-size: 20px; color: #198754; }
    </style>
    
    <div class='header'>
        <h1>Ticket de Compra Detallado</h1>
        <p><strong>Inventario Gatuno</strong></p>
    </div>
    
    <p><strong>Cliente:</strong> " . htmlspecialchars($usuario) . "</p>
    <p><strong>Fecha de Expedición:</strong> $fecha</p>
    
    <table class='detalle'>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Subtotal</th>
        </tr>
";

// Generamos las filas de cada producto comprado
foreach($items_comprados as $item) {
    $sub = $item['precio'] * $item['cantidad'];
    $html .= "<tr>
                <td>" . htmlspecialchars($item['nombre']) . "</td>
                <td><strong>" . $item['cantidad'] . "</strong></td>
                <td>$" . number_format($item['precio'], 2) . "</td>
                <td>$" . number_format($sub, 2) . "</td>
              </tr>";
}

$html .= "
    </table>
    <div class='total'>
        <strong>TOTAL PAGADO: $" . number_format($total_pagado, 2) . "</strong>
    </div>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Ticket_Compra_" . time() . ".pdf", ["Attachment" => true]);
?>