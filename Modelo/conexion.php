<?php
// Apagamos los errores nativos de PHP para que no ensucien la respuesta
error_reporting(0);

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

// Limpiamos la basura de las variables de Render por si acaso
$host = trim(str_replace(['Valor:', 'Value:'], '', $host));
$user = trim(str_replace(['Valor:', 'Value:'], '', $user));
$pass = trim(str_replace(['Valor:', 'Value:'], '', $pass));
$db   = trim(str_replace(['Valor:', 'Value:'], '', $db));

// El @ obliga a PHP a callarse si la base de datos rechaza la conexión
@$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    // Si falla, nosotros controlamos el error y lo enviamos como JSON limpio
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "mensaje" => "Error de credenciales BD"]);
    exit;
}
?>
