<?php
header('Content-Type: application/json');
include __DIR__ . '/../Modelo/conexion.php';

// Limpiamos los datos
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// Si recibimos vacío, es porque el formulario no envió nada
if (empty($usuario)) {
    echo json_encode(["status" => "error", "mensaje" => "No se recibieron datos. Revisa el formulario."]);
    exit;
}

$stmt = $conexion->prepare("SELECT password, rol FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $fila = $res->fetch_assoc();
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
