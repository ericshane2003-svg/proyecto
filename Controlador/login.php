<?php
header('Content-Type: application/json');
include __DIR__ . '/../Modelo/conexion.php';

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// ... (tu consulta SQL de login) ...
if ($res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    
    // IMPORTANTE: Asegúrate de guardar el 'rol' que viene de la BD
    if ($password === $fila['password']) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $fila['rol']; // <--- ESTO ES LO QUE TE FALTA
        echo json_encode(["status" => "success", "mensaje" => "Acceso correcto"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
    }
}
?>
