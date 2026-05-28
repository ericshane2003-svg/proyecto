<?php
// Modelo/conexion.php
$host = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_HOST')));
$user = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_USER')));
$pass = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_PASS')));
$db   = trim(str_replace(['Valor:', 'Value:'], '', getenv('DB_NAME')));

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    // Si llegamos aquí, todo bien
} catch (mysqli_sql_exception $e) {
    // Si falla, enviamos un error limpio
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "mensaje" => "Error de conexión BD"]);
    exit;
}
?>
