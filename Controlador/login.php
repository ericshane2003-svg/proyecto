<?php
header('Content-Type: application/json');

// 1. Probamos la conexión
$ruta = __DIR__ . '/../Modelo/conexion.php';
include $ruta;

if (!isset($conexion)) {
    die(json_encode(["status" => "error", "mensaje" => "La variable conexion NO se creó"]));
}

// 2. Probamos una consulta simple, sin lógica compleja
$query = "SELECT 1"; 
$resultado = $conexion->query($query);

if ($resultado) {
    echo json_encode(["status" => "success", "mensaje" => "Conexión a BD exitosa, sistema listo"]);
} else {
    echo json_encode(["status" => "error", "mensaje" => "Error al consultar BD: " . $conexion->error]);
}
?>
