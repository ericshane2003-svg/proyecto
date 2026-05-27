<?php
session_start();
include __DIR__ . '/../Modelo/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "mensaje" => "No autorizado"]);
    exit;
}

$id = intval($_POST['id']);
$cantidad_solicitada = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1; 
$accion = $_POST['accion'] ?? 'agregar';

// --- ACCIÓN: QUITAR PRODUCTO(S) DEL CARRITO ---
if ($accion === 'eliminar_item') {
    if (isset($_SESSION['carrito'][$id]) && $cantidad_solicitada > 0) {
        
        $_SESSION['carrito'][$id]['cantidad'] -= $cantidad_solicitada;
        
        if ($_SESSION['carrito'][$id]['cantidad'] <= 0) {
            unset($_SESSION['carrito'][$id]);
        }
        
        // ESTO ES CLAVE: Fuerza a PHP a guardar la resta antes de hacer cualquier otra cosa
        session_write_close(); 
        
        echo json_encode(["status" => "success", "mensaje" => "Producto(s) retirado(s)."]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "No se pudo actualizar."]);
    }
    exit; 
}

// --- ACCIÓN: AGREGAR AL CARRITO ---
if ($accion === 'agregar') {
    $stmt = $conexion->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();

    if ($producto) {
        if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
        
        $cantidad_actual = isset($_SESSION['carrito'][$id]) ? $_SESSION['carrito'][$id]['cantidad'] : 0;
        $nueva_cantidad_total = $cantidad_actual + $cantidad_solicitada;

        if ($nueva_cantidad_total > $producto['stock']) {
            echo json_encode(["status" => "error", "mensaje" => "⚠️ Stock insuficiente. Hay " . $producto['stock'] . " disponibles."]);
            exit;
        }

        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += $cantidad_solicitada;
        } else {
            $_SESSION['carrito'][$id] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad_solicitada
            ];
        }
        session_write_close();
        echo json_encode(["status" => "success", "mensaje" => "Producto agregado"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Producto no encontrado"]);
    }
}
?>