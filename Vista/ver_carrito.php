<?php
session_start();
include '../Modelo/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-secondary">🛒 Mi Carrito de Compras</h5>
            <a href="panel.php" class="btn btn-secondary btn-sm">Regresar al Panel</a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th>Precio Unitario</th>
                            <th class="text-center">Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_pagar = 0;
                        
                        // Verificamos si hay algo en el carrito
                        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                            // Recorremos el carrito
                            foreach ($_SESSION['carrito'] as $id_prod => $item) {
                                $cant = $item['cantidad'];

                                // Consultamos a la BD para sacar el nombre y precio real
                                $stmt = $conexion->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
                                $stmt->bind_param("i", $id_prod);
                                $stmt->execute();
                                $res = $stmt->get_result();

                                if ($prod = $res->fetch_assoc()) {
                                    $subtotal = $prod['precio'] * $cant;
                                    $total_pagar += $subtotal;
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                        <td>$<?php echo number_format($prod['precio'], 2); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6 px-3 py-2 rounded-2"><?php echo $cant; ?></span>
                                        </td>
                                        <td class="fw-bold">$<?php echo number_format($subtotal, 2); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm">🗑️</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>Tu carrito está vacío 🥺</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-white border-top d-flex justify-content-end align-items-center">
                <h4 class="mb-0 text-dark me-3">TOTAL A PAGAR:</h4>
                <h4 class="mb-0 text-success fw-bold">$<?php echo number_format($total_pagar, 2); ?></h4>
            </div>
        </div>
        
        <div class="card-footer bg-white border-0 p-3">
            <a href="../Controlador/generar_factura.php" class="btn btn-success w-100 fs-5 py-3 fw-bold shadow-sm">
                💰 Finalizar Compra y Generar Factura PDF
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
