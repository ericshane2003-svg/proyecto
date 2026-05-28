<?php
// Esto fuerza a que CUALQUIER cosa que ocurra se envíe como texto plano, NO como HTML
header('Content-Type: text/plain'); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include __DIR__ . '/../Modelo/conexion.php';
    
    if (!isset($conexion)) {
        die("ERROR: La variable \$conexion no existe después del include.");
    }

    echo "Conexión recibida exitosamente. ";

    $usuario = $_POST['usuario'] ?? 'nada';
    $password = $_POST['password'] ?? 'nada';

    echo "Usuario recibido: " . $usuario;

    // Aquí forzamos una consulta simple
    $stmt = $conexion->prepare("SELECT 1");
    if (!$stmt) {
        die(" ERROR SQL: " . $conexion->error);
    }
    echo " Consulta SQL exitosa.";

} catch (Exception $e) {
    echo " EXCEPCIÓN: " . $e->getMessage();
}
?>
