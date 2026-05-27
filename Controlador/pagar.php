<?php
session_start();
include __DIR__ . '/../Modelo/conexion.php';
include 'registrar_log.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || empty($_SESSION['carrito'])) {
    echo json_encode(["status" => "error", "mensaje" => "Carrito vacío o no logueado."]);
    exit;
}

$conexion->begin_transaction();

try {
    $total = 0;
    // Respaldamos la compra exacta para imprimirla en la factura
    $_SESSION['ultima_compra'] = $_SESSION['carrito'];
    
    foreach ($_SESSION['carrito'] as $id => $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        
        // Descontar stock
        $stmt = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $item['cantidad'], $id);
        $stmt->execute();

        // Registrar en histórico de ventas
        $stmt_venta = $conexion->prepare("INSERT INTO ventas (producto_id, cantidad) VALUES (?, ?)");
        $stmt_venta->bind_param("ii", $id, $item['cantidad']);
        $stmt_venta->execute();
    }

    $_SESSION['ultimo_total'] = $total;
    guardarLog($conexion, $_SESSION['usuario'], "Compra realizada por $" . $total);
    
    $conexion->commit();
    
    // Vaciamos el carrito de forma segura
    $_SESSION['carrito'] = [];
    session_write_close();

    echo json_encode([
        "status" => "success", 
        "mensaje" => "Compra exitosa. Generando ticket de compra...", 
        "url_factura" => "../Controlador/generar_factura.php"
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(["status" => "error", "mensaje" => "Error al procesar: " . $e->getMessage()]);
}
?>