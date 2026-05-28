<?php
// Configuración para forzar JSON y evitar errores HTML
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivamos errores en pantalla para no romper JSON

// La ruta desde /Controlador hacia arriba a /Modelo/conexion.php
$ruta_conexion = __DIR__ . '/../Modelo/conexion.php';

if (!file_exists($ruta_conexion)) {
    echo json_encode(["status" => "error", "mensaje" => "Error interno: Archivo de conexión no hallado"]);
    exit;
}

include $ruta_conexion;

// Validar entrada
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos incompletos"]);
    exit;
}

// Consulta SQL
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
