<?php
error_reporting(0); // Silencia cualquier error de PHP para no romper el JSON

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';
$port = getenv('DB_PORT') ?: 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);

if ($conexion->connect_error) {
    // Si falla, enviamos JSON, no HTML
    echo json_encode(["status" => "error", "mensaje" => "Fallo de conexión: " . $conexion->connect_error]);
    exit;
}
$conexion->set_charset("utf8mb4");
?>
