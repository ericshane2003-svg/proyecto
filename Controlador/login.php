<?php
// Forzar que el servidor hable, incluso si hay errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Esto es para probar si el archivo siquiera se ejecuta
die(json_encode(["status" => "debug", "mensaje" => "El archivo PHP se ejecutó correctamente"]));

/* // --- CÓDIGO ACTUAL COMENTADO PARA NO INTERFERIR ---
header('Content-Type: application/json');
include __DIR__ . '/../Modelo/conexion.php';
// ... el resto de tu lógica ...
*/
?>
