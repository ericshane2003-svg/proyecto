<?php
session_start();
include __DIR__ . '/../Modelo/conexion.php';
include 'registrar_log.php';

header('Content-Type: application/json');

// Validar que el usuario sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(["status" => "error", "mensaje" => "❌ No tienes permisos."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password_plana = $_POST['password'];
    $id_rol_pedido = $_POST['rol'];

    // 1. Verificación de existencia
    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ?");
    $verificar->bind_param("s", $usuario);
    $verificar->execute();
    if ($verificar->get_result()->num_rows > 0) {
        echo json_encode(["status" => "error", "mensaje" => "⚠️ El usuario ya existe."]);
        exit;
    }

    // 2. Encriptación segura (Password Hashing)
    $password_segura = password_hash($password_plana, PASSWORD_DEFAULT);
    $foto_nueva = "default.png";

    // 3. Inserción
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, password, id_rol, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $usuario, $password_segura, $id_rol_pedido, $foto_nueva);

    if ($stmt->execute()) {
        guardarLog($conexion, $_SESSION['usuario'], "Registró al usuario: " . $usuario);
        echo json_encode(["status" => "success", "mensaje" => "✅ Usuario creado con seguridad."]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "❌ Error: " . $conexion->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "mensaje" => "Método inválido."]);
}
?>