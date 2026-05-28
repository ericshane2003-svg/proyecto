<?php
// 1. SILENCIAR ERRORES HTML Y FORZAR JSON
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivamos errores en pantalla

function enviarError($msg) {
    echo json_encode(["status" => "error", "mensaje" => $msg]);
    exit;
}

// 2. Incluir conexión con manejo de errores
$ruta = __DIR__ . '/../Modelo/conexion.php';
if (!file_exists($ruta)) enviarError("No se encuentra el archivo de conexión");
include $ruta;

if (!isset($conexion)) enviarError("Error interno: la conexión no se inicializó");

// 3. Validación
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) enviarError("Campos incompletos");

// 4. Consulta
$stmt = $conexion->prepare("SELECT password, rol FROM usuarios WHERE nombre = ?");
if (!$stmt) enviarError("Error en base de datos: " . $conexion->error);

$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    // Comparación directa (si en el futuro usas password_hash, cambia esto a password_verify)
    if ($password === $fila['password']) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $fila['rol'];
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        enviarError("Contraseña incorrecta");
    }
} else {
    enviarError("Usuario no existe");
}
?>
