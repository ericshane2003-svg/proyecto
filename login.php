<?php
header('Content-Type: application/json');
error_reporting(0); // Evita etiquetas HTML basura

include 'conexion.php'; // Como todo es plano, no necesitas ../

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
