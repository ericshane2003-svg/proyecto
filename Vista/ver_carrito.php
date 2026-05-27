<?php
session_start();

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
    <title>Mi Carrito - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🛒 Mi Carrito de Compras</h5>
            <a href="panel.php" class="btn btn-sm btn-secondary">Regresar al Panel</a>
        </div>
        <div class="card-body">
            <?php 
            if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])): ?>
                <div class="alert alert-info">Tu carrito está vacío. Agrega productos desde el catálogo.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['carrito'] as $id => $item): 
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                <td><span class="badge bg-primary fs-6"><?php echo $item['cantidad']; ?></span></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 140px;">
                                        <input type="number" id="quitar_<?php echo $id; ?>" class="form-control text-center" value="1" min="1" max="<?php echo $item['cantidad']; ?>">
                                        <button class="btn btn-danger" onclick="eliminarDelCarrito(<?php echo $id; ?>)">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5">TOTAL A PAGAR:</td>
                                <td class="fw-bold fs-5 text-success">$<?php echo number_format($total, 2); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-success btn-lg" onclick="procesarPago()">
                        💰 Finalizar Compra y Generar Factura PDF
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function eliminarDelCarrito(id) {
        // Aseguramos matemáticamente que el valor sea un entero válido
        const inputValor = document.getElementById('quitar_' + id).value;
        const cantidad_quitar = parseInt(inputValor);

        if(isNaN(cantidad_quitar) || cantidad_quitar <= 0) {
            alert("Por favor, ingresa una cantidad válida a quitar.");
            return;
        }

        if(!confirm(`¿Seguro que deseas quitar ${cantidad_quitar} unidad(es) de este producto?`)) return;

        const fd = new FormData();
        fd.append('id', id);
        fd.append('cantidad', cantidad_quitar);
        fd.append('accion', 'eliminar_item');

        fetch('../Controlador/gestionar_carrito.php', {
            method: 'POST',
            body: fd
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload(); 
            } else {
                alert(data.mensaje);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function procesarPago() {
        if(!confirm("¿Confirmar compra y generar ticket?")) return;

        fetch('../Controlador/pagar.php', { 
            method: 'POST' 
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.mensaje);
                window.open(data.url_factura, '_blank');
                setTimeout(() => { window.location.href = 'panel.php'; }, 1000);
            } else {
                alert(data.mensaje);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error de conexión con el servidor.");
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>