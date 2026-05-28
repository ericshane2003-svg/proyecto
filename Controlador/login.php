<?php
header('Content-Type: application/json');

// Incluir conexión
include __DIR__ . '/../Modelo/conexion.php';

// Validar entrada
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos vacíos"]);
    exit;
}

$stmt = $conexion->prepare("SELECT password FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    if ($password === $fila['password']) {
        session_start();
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
}
?>
