<?php
// Forzar que el servidor nunca envíe HTML, solo JSON
header('Content-Type: application/json');

// Manejador de errores personalizado
set_error_handler(function($errno, $errstr) {
    echo json_encode(["status" => "error", "mensaje" => "Error interno: $errstr"]);
    exit;
});

include __DIR__ . '/../Modelo/conexion.php';

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Datos vacíos"]);
    exit;
}

$stmt = $conexion->prepare("SELECT password, rol FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($fila = $res->fetch_assoc()) {
    if ($password === $fila['password']) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $fila['rol'];
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
}
?>
