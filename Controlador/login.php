<?php
error_reporting(0);
header('Content-Type: application/json');

// La ruta exacta para subir desde Controlador hasta Modelo
$ruta_conexion = __DIR__ . '/../Modelo/conexion.php';

if (!file_exists($ruta_conexion)) {
    echo json_encode(["status" => "error", "mensaje" => "No encuentro conexion.php en: " . $ruta_conexion]);
    exit;
}

include $ruta_conexion;

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos vacíos"]);
    exit;
}

// ... aquí tu consulta SQL (la que ya tienes) ...
$stmt = $conexion->prepare("SELECT u.password, r.nombre as rol FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id WHERE u.nombre = ?");
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
