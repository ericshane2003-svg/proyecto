<?php
// 1. FUNDAMENTAL: Iniciar sesión para saber quién está comprando
session_start();
header('Content-Type: application/json');

// 2. Validar que el usuario haya pasado por el login
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "mensaje" => "No autorizado. La sesión caducó."]);
    exit;
}

// 3. Opcional: Si no quieres que el 'administrador' compre, descomenta esto:
/*
if ($_SESSION['rol'] === 'administrador') {
    echo json_encode(["status" => "error", "mensaje" => "Los administradores no pueden usar el carrito."]);
    exit;
}
*/

// 4. Recibir los datos del formulario (enviados por el JS)
$accion = $_POST['accion'] ?? '';
$id_producto = $_POST['id'] ?? '';
$cantidad = (int)($_POST['cantidad'] ?? 1);

// Si no existe el carrito en la sesión, lo creamos
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// 5. Procesar la acción
if ($accion === 'agregar' && !empty($id_producto)) {
    
    // Si el producto ya está en el carrito, le sumamos la cantidad
    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
    } else {
        // Si es nuevo, lo registramos
        $_SESSION['carrito'][$id_producto] = [
            'id' => $id_producto,
            'cantidad' => $cantidad
        ];
    }
    
    echo json_encode(["status" => "success", "mensaje" => "Agregado correctamente"]);
    exit;
}

// Si llega hasta aquí sin entrar al 'if', es que algo faltó
echo json_encode(["status" => "error", "mensaje" => "Acción no reconocida o faltan datos."]);
?>
