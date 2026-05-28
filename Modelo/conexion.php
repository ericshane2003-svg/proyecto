<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

// Para depurar, si sigue fallando, comenta el mysqli y descomenta el echo:
// die("Host: $host, User: $user, DB: $db");

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("ERROR DE CONEXION: " . $conexion->connect_error);
}
echo "CONEXION EXITOSA";
?>
