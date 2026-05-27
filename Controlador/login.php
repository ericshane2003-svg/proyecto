<?php
error_reporting(0); // Silencia advertencias
ob_start(); // Inicia un buffer para limpiar salidas accidentales
header('Content-Type: application/json');

include '../Modelo/conexion.php';

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    ob_clean();
    echo json_encode(["status" => "error", "mensaje" => "Datos vacíos"]);
    exit;
}

try {
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
            
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
        } else {
            ob_clean();
            echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
        }
    } else {
        ob_clean();
        echo json_encode(["status" => "error", "mensaje" => "Usuario no existe"]);
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode(["status" => "error", "mensaje" => "Error de servidor: " . $e->getMessage()]);
}
?>
