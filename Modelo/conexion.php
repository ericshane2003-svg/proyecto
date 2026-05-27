<?php
// Usamos variables de entorno para Render
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';
$port = getenv('DB_PORT') ?: 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);

if ($conexion->connect_error) {
    // Si falla la conexión, mandamos un JSON, NO un texto plano
    echo json_encode(["status" => "error", "mensaje" => "Error DB: " . $conexion->connect_error]);
    exit;
}
$conexion->set_charset("utf8mb4");
// NO pongas nada después de esto, ni siquiera ?>
