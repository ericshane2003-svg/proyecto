<?php
// Configuración estricta para atrapar errores de MySQL en PHP moderno
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

// Limpiamos las variables de entorno de Render
$host = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_HOST')));
$user = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_USER')));
$pass = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_PASS')));
$db   = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_NAME')));

try {
    // Intentamos conectar
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset("utf8mb4");
} catch (Exception $e) {
    // Si la base de datos rechaza la conexión, atrapamos el error y devolvemos JSON
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error", 
        "mensaje" => "Error de credenciales o la BD está apagada."
    ]);
    exit;
}
?>
