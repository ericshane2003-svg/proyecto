<?php
error_reporting(0);
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';
$port = getenv('DB_PORT') ?: 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);
if ($conexion->connect_error) { 
    echo json_encode(["status" => "error", "mensaje" => "Error DB"]); 
    exit; 
}
$conexion->set_charset("utf8mb4");
?>