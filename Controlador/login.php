<?php
header('Content-Type: application/json');
session_start();
include '../Modelo/conexion.php';

// Validar que llegaron datos
if (!isset($_POST['usuario']) || !isset($_POST['password'])) {
    echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
    exit;
}

$user = $_POST['usuario'];
$pass = $_POST['password'];

try {
    $stmt = $conexion->prepare("SELECT u.id, u.password, r.nombre as rol FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id WHERE u.nombre = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        
        // Verificación de contraseña (ajusta esto si usas password_verify o texto plano)
        if ($pass === $fila['password']) {
            $_SESSION['usuario'] = $user;
            $_SESSION['rol'] = $fila['rol'];
            echo json_encode(["status" => "success", "mensaje" => "Bienvenido", "rol" => $fila['rol']]);
        } else {
            echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "mensaje" => "Error del servidor: " . $e->getMessage()]);
}
?>
