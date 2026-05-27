<?php
session_start();
include '../conexion.php';
include 'registrar_log.php'; // Incluimos la función de logs

header('Content-Type: application/json');

// Validar sesión y rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(["status" => "error", "mensaje" => "No autorizado."]); 
    exit;
}

$accion = $_POST['accion'] ?? '';

// --- ACCIÓN AGREGAR ---
if ($accion === 'agregar') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $nombre, $precio, $stock);
    
    if($stmt->execute()) {
        guardarLog($conexion, $_SESSION['usuario'], "Agregó producto: " . $nombre);
        echo json_encode(["status" => "success", "mensaje" => "Producto agregado."]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al agregar."]);
    }
}

// --- ACCIÓN ELIMINAR ---
if ($accion === 'eliminar') {
    $id = $_POST['id'];
    
    // Obtenemos nombre antes de borrar para el log
    $res = $conexion->query("SELECT nombre FROM productos WHERE id = $id");
    $prod = $res->fetch_assoc();
    $nombre = $prod['nombre'] ?? 'Desconocido';
    
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        guardarLog($conexion, $_SESSION['usuario'], "Eliminó producto: " . $nombre);
        echo json_encode(["status" => "success", "mensaje" => "Producto eliminado."]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al eliminar."]);
    }
}

// --- ACCIÓN EDITAR ---
if ($accion === 'editar') {
    $id = $_POST['id'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    
    $stmt = $conexion->prepare("UPDATE productos SET stock = ?, precio = ? WHERE id = ?");
    $stmt->bind_param("idi", $stock, $precio, $id);
    
    if($stmt->execute()) {
        guardarLog($conexion, $_SESSION['usuario'], "Editó producto ID: " . $id . " (Precio: " . $precio . ", Stock: " . $stock . ")");
        echo json_encode(["status" => "success", "mensaje" => "Inventario actualizado."]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al actualizar."]);
    }
}
?>
