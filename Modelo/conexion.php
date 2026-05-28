<?php
// Quitamos el error_reporting(0) para que nos diga la verdad
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("ERROR DE CONEXION: " . $conexion->connect_error);
}

// Si llegamos aquí, la conexión funciona
echo "CONEXION EXITOSA";
?>
