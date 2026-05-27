<?php
error_reporting(0); // Esto evita que cualquier aviso de PHP rompa tu JSON

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';
$port = getenv('DB_PORT') ?: 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);

if ($conexion->connect_error) {
    // IMPORTANTE: Si falla, mandamos un JSON, NO un texto plano
    echo json_encode(["status" => "error", "mensaje" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

$conexion->set_charset("utf8mb4");
// NO pongas nada después de esto
