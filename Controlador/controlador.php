<?php
session_start();
include __DIR__ . '/../Modelo/conexion.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password_encriptada = md5($_POST['password']);

    $sql = "SELECT u.nombre, u.foto, r.nombre AS rol 
            FROM usuarios u 
            INNER JOIN roles r ON u.id_rol = r.id 
            WHERE u.nombre = ? AND u.password = ?";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usuario, $password_encriptada);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $fila['nombre'];
        $_SESSION['rol'] = $fila['rol']; 
        $_SESSION['foto'] = $fila['foto']; // Guardamos la foto específica
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Datos incorrectos."]);
    }
}
?>