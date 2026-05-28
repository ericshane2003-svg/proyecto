<?php
header('Content-Type: application/json');
error_reporting(E_ALL); // Cambiamos a E_ALL para ver errores si hay problemas

// Buscamos la conexión subiendo un nivel y entrando a la carpeta Modelo
// Si esto falla, el problema es que la carpeta 'Modelo' no está donde crees
$ruta_conexion = __DIR__ . '/../Modelo/conexion.php';

if (file_exists($ruta_conexion)) {
    include $ruta_conexion;
} else {
    // Si no encuentra el archivo, enviamos este JSON para saber dónde está buscando
    echo json_encode([
        "status" => "error", 
        "mensaje" => "Archivo no encontrado", 
        "ruta_buscada" => $ruta_conexion,
        "directorio_actual" => __DIR__
    ]);
    exit;
}

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(["status" => "error", "mensaje" => "Campos vacíos"]);
    exit;
}

// Aseguramos que la variable $conexion exista
if (!isset($conexion)) {
    echo json_encode(["status" => "error", "mensaje" => "La conexión a la BD no se inicializó"]);
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
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
}
?>
