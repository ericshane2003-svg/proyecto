<?php
session_start();
error_reporting(0); // Callamos los warnings para que la redirección sea limpia
include __DIR__ . '/../Modelo/conexion.php';

// Si no hay carrito, no hacemos nada
if (!isset($_SESSION['usuario']) || empty($_SESSION['carrito'])) {
    die("El carrito está vacío o la sesión expiró.");
}

// 1. Procesamos cada producto del carrito
foreach ($_SESSION['carrito'] as $id_prod => $item) {
    $cantidad = $item['cantidad'];
    
    // Consultamos el precio y stock real a la BD (ya no confiamos en la sesión)
    $stmt = $conexion->prepare("SELECT precio, stock FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id_prod);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($prod = $res->fetch_assoc()) {
        // Calculamos el nuevo stock y evitamos que quede en números negativos
        $nuevo_stock = $prod['stock'] - $cantidad;
        if ($nuevo_stock < 0) $nuevo_stock = 0;
        
        // Actualizamos el inventario en la BD
        $stmt_up = $conexion->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt_up->bind_param("ii", $nuevo_stock, $id_prod);
        $stmt_up->execute();
        
        // Registramos la venta (asumiendo tabla ventas: producto_id, cantidad, fecha)
        $stmt_venta = $conexion->prepare("INSERT INTO ventas (producto_id, cantidad, fecha) VALUES (?, ?, NOW())");
        if ($stmt_venta) {
            $stmt_venta->bind_param("ii", $id_prod, $cantidad);
            $stmt_venta->execute();
        }
    }
}

// 2. IMPORTANTE: No borramos el carrito todavía para que generar_factura.php pueda leer qué compraste
// Simplemente hacemos la redirección transparente para el usuario
header("Location: generar_factura.php");
exit;
?>
