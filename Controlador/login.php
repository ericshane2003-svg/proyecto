<?php
header('Content-Type: application/json');

// 1. Incluimos con una ruta de seguridad absoluta
$ruta = __DIR__ . '/../Modelo/conexion.php';
if (!file_exists($ruta)) {
    die(json_encode(["status" => "error", "mensaje" => "No existe archivo conexion.php"]));
}
include $ruta;

// 2. Blindaje: Verificamos si la variable $conexion fue creada exitosamente
if (!isset($conexion) || !($conexion instanceof mysqli)) {
    die(json_encode(["status" => "error", "mensaje" => "La variable conexion no se inicializó correctamente"]));
}

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// 3. Blindaje de consulta
$stmt = $conexion->prepare("SELECT password, nombre FROM usuarios WHERE nombre = ?");
if (!$stmt) {
    die(json_encode(["status" => "error", "mensaje" => "Error en prepare: " . $conexion->error]));
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    if ($password === $fila['password']) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
}
?>
