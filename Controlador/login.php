<?php
// Esto es para depurar. Si esto sale en el Response, ya sabemos que el archivo SÍ abre.
// die("El archivo login.php sí se está ejecutando"); 

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ruta absoluta blindada
$ruta_conexion = __DIR__ . '/../Modelo/conexion.php';

if (!file_exists($ruta_conexion)) {
    echo json_encode(["status" => "error", "mensaje" => "No existe: " . $ruta_conexion]);
    exit;
}

include $ruta_conexion;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "mensaje" => "No es POST"]);
    exit;
}

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos vacíos"]);
    exit;
}

$stmt = $conexion->prepare("SELECT u.password, r.nombre as rol FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id WHERE u.nombre = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "mensaje" => "Error SQL: " . $conexion->error]);
    exit;
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
