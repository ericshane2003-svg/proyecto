<?php
session_start();
error_reporting(0);
include __DIR__ . '/../Modelo/conexion.php';

if (!isset($_SESSION['usuario']) || empty($_SESSION['carrito'])) {
    die("El carrito está vacío o la sesión expiró.");
}

// Aquí vamos a guardar el "paquete" para el PDF
$ultima_compra = [];
$ultimo_total = 0;

foreach ($_SESSION['carrito'] as $id_prod => $item) {
    $cantidad = $item['cantidad'];
    
    // Sacamos TODOS los datos reales de la BD
    $stmt = $conexion->prepare("SELECT nombre, precio, stock FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id_prod);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($prod = $res->fetch_assoc()) {
        
        // 1. Armamos los datos para la factura
        $ultima_compra[] = [
            'nombre' => $prod['nombre'],
            'precio' => $prod['precio'],
            'cantidad' => $cantidad
        ];
        $ultimo_total += ($prod['precio'] * $cantidad);
        
        // 2. Descontamos el inventario
        $nuevo_stock = $prod['stock'] - $cantidad;
        if ($nuevo_stock < 0) $nuevo_stock = 0;
        
        $stmt_up = $conexion->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt_up->bind_param("ii", $nuevo_stock, $id_prod);
        $stmt_up->execute();
        
        // 3. Registramos la venta (si la tabla existe)
        $stmt_venta = $conexion->prepare("INSERT INTO ventas (producto_id, cantidad, fecha) VALUES (?, ?, NOW())");
        if ($stmt_venta) {
            $stmt_venta->bind_param("ii", $id_prod, $cantidad);
            $stmt_venta->execute();
        }
    }
}

// Guardamos el paquete en la sesión para que generar_factura.php lo pueda leer
$_SESSION['ultima_compra'] = $ultima_compra;
$_SESSION['ultimo_total'] = $ultimo_total;

// Vaciamos el carrito (¡la compra ya se hizo!)
unset($_SESSION['carrito']);

// Disparamos la generación del PDF transparente para el usuario
header("Location: generar_factura.php");
exit;
?>
