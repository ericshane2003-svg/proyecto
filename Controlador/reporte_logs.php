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
        .header { text-align: center; border-bottom: 2px solid #6c757d; padding-bottom: 10px; margin-bottom: 20px; }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .tabla td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
    
    <div class='header'>
        <h2>📋 Bitácora de Auditoría (Logs)</h2>
        <p>Reporte generado el: " . date('d/m/Y H:i') . "</p>
    </div>
    
    <table class='tabla'>
        <tr>
            <th>Acción Registrada</th>
            <th>Fecha y Hora</th>
        </tr>
";

// Consultamos la bitácora de logs
$res_l = $conexion->query("SELECT accion, fecha_hora FROM logs_sistema ORDER BY fecha_hora DESC");

while($l = $res_l->fetch_assoc()) {
    $html .= "<tr>
                <td>" . htmlspecialchars($l['accion']) . "</td>
                <td>" . date('d/m/Y H:i', strtotime($l['fecha_hora'])) . "</td>
              </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Logs_" . date('Ymd_Hi') . ".pdf", ["Attachment" => true]);
?>
