<?php
session_start();
include __DIR__ . '/../Modelo/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(["status" => "error", "mensaje" => "No autorizado."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];

    if ($accion === 'eliminar') {
        $id = $_POST['id'];
        // Evitar suicidio de cuenta admin
        $verificar = $conexion->query("SELECT nombre FROM usuarios WHERE id = $id");
        $u = $verificar->fetch_assoc();
        if($u['nombre'] === $_SESSION['usuario']) {
            echo json_encode(["status" => "error", "mensaje" => "❌ No puedes eliminarte a ti mismo."]);
            exit;
        }

        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()) echo json_encode(["status" => "success", "mensaje" => "✅ Usuario eliminado."]);
    }

    if ($accion === 'editar') {
        $id = $_POST['id'];
        $nuevo_nombre = $_POST['nombre'];
        $nuevo_rol = $_POST['id_rol'];

        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, id_rol = ? WHERE id = ?");
        $stmt->bind_param("sii", $nuevo_nombre, $nuevo_rol, $id);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "mensaje" => "✅ Usuario actualizado (Nombre y Rol)."]);
        } else {
            echo json_encode(["status" => "error", "mensaje" => "❌ Error al actualizar."]);
        }
    }
}
?>