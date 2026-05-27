<?php
// Reportar errores pero capturarlos para el JSON
ini_set('display_errors', 0);
header('Content-Type: application/json');

// Incluir conexión con ruta absoluta para evitar problemas
$conexion_path = __DIR__ . '/../Modelo/conexion.php';

if (!file_exists($conexion_path)) {
    echo json_encode(["status" => "error", "mensaje" => "Archivo de conexión no encontrado en: " . $conexion_path]);
    exit;
}

include $conexion_path;

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos vacíos"]);
    exit;
}

$stmt = $conexion->prepare("SELECT u.password, r.nombre as rol FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id WHERE u.nombre = ?");
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