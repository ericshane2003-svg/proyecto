<?php
// Modelo/conexion.php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

$conexion = new mysqli($host, $user, $pass, $db);

// Si falla, enviamos un JSON, NO un error de HTML
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "mensaje" => "Conexión fallida: " . $conexion->connect_error]);
    exit;
}
?>
