<?php
// 1. LA MORDAZA: Atrapa cualquier error, <br> o texto basura de PHP
ob_start();

header('Content-Type: application/json');
error_reporting(0); 

try {
    // 2. Incluimos la conexión
    include __DIR__ . '/../Modelo/conexion.php';

    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($usuario) || empty($password)) {
        ob_clean(); // Tiramos la basura atrapada
        echo json_encode(["status" => "error", "mensaje" => "Datos vacíos"]);
        exit;
    }

    // 3. Consultamos el id_rol (como lo tienes en tu BD)
    $stmt = $conexion->prepare("SELECT password, id_rol FROM usuarios WHERE nombre = ?");
    
    if (!$stmt) {
        ob_clean(); // Tiramos la basura
        echo json_encode(["status" => "error", "mensaje" => "Error en la consulta de BD"]);
        exit;
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        if ($password === $fila['password']) {
            session_start();
            $_SESSION['usuario'] = $usuario;
            
            // Convertimos el ID al texto que espera tu panel.php
            $rol_nombre = ($fila['id_rol'] == 1) ? 'administrador' : 'cliente';
            $_SESSION['rol'] = $rol_nombre;
            
            ob_clean(); // Tiramos la basura antes de enviar éxito
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
    ob_clean(); // Si la base de datos explota, tiramos su HTML de error a la basura
    echo json_encode(["status" => "error", "mensaje" => "Error de credenciales de BD."]);
}
?>
