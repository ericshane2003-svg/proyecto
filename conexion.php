<?php
// Leemos las variables de entorno de Render, o usamos las de XAMPP/Docker local por defecto
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'inventario_db';
$port = getenv('DB_PORT') ?: 3306;

// Crear la conexión usando la extensión mysqli
$conexion = new mysqli($host, $user, $pass, $db, $port);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la Base de Datos: " . $conexion->connect_error);
}

// Forzar el uso de UTF-8 para que no se rompan los acentos y las ñ
$conexion->set_charset("utf8mb4");
?>