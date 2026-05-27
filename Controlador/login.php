<?php
session_start();
include '../Modelo/conexion.php';
include 'registrar_log.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password_input = $_POST['password'];

    // Encriptamos la entrada con MD5 para que coincida con el formato original
    $password_md5 = md5($password_input);

    // Consulta directa usando MD5
    $stmt = $conexion->prepare("SELECT u.id, r.nombre AS rol_nombre 
                                FROM usuarios u 
                                INNER JOIN roles r ON u.id_rol = r.id 
                                WHERE u.nombre = ? AND u.password = ?");
    $stmt->bind_param("ss", $usuario, $password_md5);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user_data = $res->fetch_assoc();
        
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $user_data['rol_nombre'];
        
        guardarLog($conexion, $usuario, "Login exitoso (MD5).");
        echo json_encode(["status" => "success", "mensaje" => "Bienvenido"]);
    } else {
        guardarLog($conexion, $usuario, "Intento fallido: usuario o pass incorrecto.");
        echo json_encode(["status" => "error", "mensaje" => "Usuario o contraseña incorrectos."]);
    }
    $stmt->close();
}
?>