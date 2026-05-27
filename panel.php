<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../conexion.php';

if (!isset($_SESSION['usuario'])) { 
    header("Location: index.html"); 
    exit; 
}

$usuario_actual = $_SESSION['usuario'];
$rol_actual = $_SESSION['rol'];

// Verificación foto
$stmt_seg = $conexion->prepare("SELECT u.foto, r.nombre as rol_real FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id WHERE u.nombre = ?");
$stmt_seg->bind_param("s", $usuario_actual);
$stmt_seg->execute();
$res_seg = $stmt_seg->get_result();
$fila_seg = $res_seg->fetch_assoc();
$foto_perfil = ($rol_actual === 'administrador') ? 'image.png' : $fila_seg['foto'];

// Consultas
$usuarios_lista = []; $productos_lista = [];
if ($rol_actual === 'administrador') {
    $res = $conexion->query("SELECT u.id, u.nombre, u.id_rol, u.foto, r.nombre as rol_nombre FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id");
    while($row = $res->fetch_assoc()) $usuarios_lista[] = $row;
}
$res_p = $conexion->query("SELECT * FROM productos");
while($rp = $res_p->fetch_assoc()) $productos_lista[] = $rp;

$items_carrito = isset($_SESSION['carrito']) ? array_sum(array_column($_SESSION['carrito'], 'cantidad')) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - <?php echo strtoupper($rol_actual); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .foto-grande { width: 80px; height: 80px; object-fit: cover; }
        .img-mini { width: 40px; height: 40px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0 border-start border-success border-5 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="../Assets/<?php echo $foto_perfil; ?>" class="rounded-circle border border-2 border-success foto-grande" onerror="this.src='../Assets/default.png'">
                <div>
                    <h2 class="mb-0 h4 text-dark"><?php echo htmlspecialchars($usuario_actual); ?></h2>
                    <span class="badge bg-success">ROL: <?php echo strtoupper($rol_actual); ?></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <?php if($rol_actual !== 'administrador'): ?>
                    <a href="ver_carrito.php" class="btn btn-outline-primary">🛒 Mi Carrito (<span id="cart-count"><?php echo $items_carrito; ?></span>)</a>
                <?php endif; ?>
                <a href="../Controlador/cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <?php switch($rol_actual): 
        case 'administrador': ?>
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white"><h5 class="mb-0">👥 Gestión de Usuarios</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light"><tr><th>Foto</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach($usuarios_lista as $u): ?>
                            <tr>
                                <td><img src="../Assets/<?php echo !empty($u['foto']) ? $u['foto'] : 'default.png'; ?>" class="rounded-circle img-mini"></td>
                                <td><strong><?php echo htmlspecialchars($u['nombre']); ?></strong></td>
                                <td><?php echo strtoupper($u['rol_nombre']); ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="editarUser(<?php echo $u['id']; ?>, '<?php echo $u['nombre']; ?>', <?php echo $u['id_rol']; ?>)">Editar</button> 
                                    <button class="btn btn-danger btn-sm" onclick="eliminarUser(<?php echo $u['id']; ?>)">Borrar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="alert alert-success mt-3">
                        <h6>➕ Registrar Nuevo Usuario</h6>
                        <form id="formAdminRegistro" class="row g-2">
                            <div class="col-md-4"><input type="text" name="usuario" class="form-control" placeholder="Nombre" required></div>
                            <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Clave" required></div>
                            <div class="col-md-3">
                                <select name="rol" class="form-select">
                                    <option value="1">Admin</option><option value="2">Cliente</option>
                                    <option value="3">Invitado</option><option value="4">Vendedor</option>
                                </select>
                            </div>
                            <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Crear</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0 border-top border-warning border-4">
                <div class="card-header bg-white"><h5 class="mb-0">📦 Control de Inventario</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-light"><tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach($productos_lista as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                                <td>$<?php echo number_format($p['precio'], 2); ?></td>
                                <td><?php echo $p['stock']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editarProd(<?php echo $p['id']; ?>, <?php echo $p['precio']; ?>, <?php echo $p['stock']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarProd(<?php echo $p['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="alert alert-warning mt-3">
                        <h6>➕ Agregar Nuevo Producto</h6>
                        <form id="formInv" class="row g-2">
                            <div class="col-md-4"><input type="text" name="nombre" class="form-control" placeholder="Nombre del Producto" required></div>
                            <div class="col-md-3"><input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio ($)" required></div>
                            <div class="col-md-3"><input type="number" name="stock" class="form-control" placeholder="Cantidad (Stock)" required></div>
                            <div class="col-md-2"><button type="submit" class="btn btn-warning w-100">Guardar</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4 border-0 border-top border-info border-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-info">📈 Histórico de Ventas</h5>
                            <a href="../Controlador/reporte_ventas.php" target="_blank" class="btn btn-danger btn-sm">📄 Generar PDF</a>
                        </div>
                        <div class="card-body table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>Producto</th><th>Cant.</th><th>Fecha</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $res_v = $conexion->query("SELECT p.nombre, v.cantidad, v.fecha FROM ventas v JOIN productos p ON v.producto_id = p.id ORDER BY v.fecha DESC");
                                    while($v = $res_v->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($v['nombre']); ?></td>
                                        <td><span class="badge bg-info"><?php echo $v['cantidad']; ?></span></td>
                                        <td><?php echo date('d/m H:i', strtotime($v['fecha'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm mb-4 border-0 border-top border-secondary border-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-secondary">📋 Bitácora de Logs</h5>
                            <a href="../Controlador/reporte_logs.php" target="_blank" class="btn btn-danger btn-sm">📄 Generar PDF</a>
                        </div>
                        <div class="card-body table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>Acción</th><th>Fecha</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $res_l = $conexion->query("SELECT * FROM logs_sistema ORDER BY fecha_hora DESC LIMIT 20");
                                    while($l = $res_l->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($l['accion']); ?></td>
                                        <td><?php echo date('d/m H:i', strtotime($l['fecha_hora'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php break; ?>

        <?php case 'cliente': 
              case 'vendedor': ?>
            <div class="card shadow-sm border-0 border-top border-primary border-4">
                <div class="card-header bg-white"><h5>🛍️ Catálogo</h5></div>
                <div class="card-body">
                    <div class="row mb-3 g-2">
                        <div class="col-md-8"><input type="text" id="searchInput" class="form-control" placeholder="Buscar productos..."></div>
                        <div class="col-md-4">
                            <select id="categoryFilter" class="form-select">
                                <option value="todas">Todas las categorías</option>
                                <?php 
                                // Si tu tabla no tiene columna 'categoria', ignora el filtro.
                                $verificar_cat = $conexion->query("SHOW COLUMNS FROM productos LIKE 'categoria'");
                                if($verificar_cat->num_rows > 0) {
                                    $res_cat = $conexion->query("SELECT DISTINCT categoria FROM productos");
                                    while($c = $res_cat->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($c['categoria']); ?>"><?php echo htmlspecialchars($c['categoria']); ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3" id="productContainer">
                        <?php foreach($productos_lista as $p): ?>
                        <div class="col-md-3 product-card" data-name="<?php echo strtolower($p['nombre']); ?>" data-category="<?php echo isset($p['categoria']) ? $p['categoria'] : 'todas'; ?>">
                            <div class="card p-3 text-center h-100">
                                <h6><?php echo htmlspecialchars($p['nombre']); ?></h6>
                                
                                <p class="mb-1 text-muted">Stock actual: <b><?php echo $p['stock']; ?></b></p>
                                <p class="mb-2 fw-bold text-success fs-5">$<?php echo number_format($p['precio'], 2); ?></p>
                                
                                <?php if($p['stock'] > 0): ?>
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Cant.</span>
                                        <input type="number" id="cant_<?php echo $p['id']; ?>" class="form-control text-center" value="1" min="1" max="<?php echo $p['stock']; ?>">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="addCart(<?php echo $p['id']; ?>)">🛒 Añadir al Carrito</button>
                                <?php else: ?>
                                    <div class="alert alert-danger p-1 mb-0"><small>Agotado</small></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        <?php break; ?>
    <?php endswitch; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // FORMULARIOS RESTAURADOS JS
    const formReg = document.getElementById('formAdminRegistro');
    if(formReg) {
        formReg.addEventListener('submit', function(e) { 
            e.preventDefault(); 
            fetch('../Controlador/registrar.php', { method: 'POST', body: new FormData(this) })
            .then(r => r.json()).then(d => { alert(d.mensaje); location.reload(); }); 
        });
    }

    const formInv = document.getElementById('formInv');
    if(formInv) {
        formInv.addEventListener('submit', function(e) { 
            e.preventDefault(); 
            const fd = new FormData(this); fd.append('accion', 'agregar'); 
            fetch('../Controlador/acciones_inventario.php', {method:'POST', body:fd}).then(()=>location.reload()); 
        });
    }

    // Buscador
    document.getElementById('searchInput')?.addEventListener('keyup', filter);
    document.getElementById('categoryFilter')?.addEventListener('change', filter);

    function filter() {
        const s = document.getElementById('searchInput').value.toLowerCase();
        const c = document.getElementById('categoryFilter').value;
        document.querySelectorAll('.product-card').forEach(p => {
            const matchesS = p.getAttribute('data-name').includes(s);
            const matchesC = (c === 'todas' || p.getAttribute('data-category') === c);
            p.style.display = (matchesS && matchesC) ? "" : "none";
        });
    }

    // CARRITO ACTUALIZADO CON CANTIDAD
    function addCart(id) { 
        const cantidad_elegida = document.getElementById('cant_' + id).value; // Extrae cantidad seleccionada
        
        const fd = new FormData(); 
        fd.append('id', id); 
        fd.append('cantidad', cantidad_elegida); // Envía la cantidad
        fd.append('accion', 'agregar'); 
        
        fetch('../Controlador/gestionar_carrito.php', {method:'POST', body:fd})
        .then(r=>r.json()).then(d=>{ 
            if(d.status === 'success') {
                alert("Añadido al carrito: " + cantidad_elegida + " unidades.");
                location.reload(); 
            } else {
                alert(d.mensaje);
            }
        }); 
    }

    function eliminarUser(id) { 
        const fd = new FormData(); fd.append('id', id); fd.append('accion', 'eliminar'); 
        fetch('../Controlador/acciones_usuario.php', {method:'POST', body:fd}).then(()=>location.reload()); 
    }
    
    function editarUser(id, n, r) { 
        const nn = prompt("Nombre:", n); const nr = prompt("Rol ID:", r); 
        if(nn && nr) { 
            const fd = new FormData(); fd.append('id', id); fd.append('nombre', nn); fd.append('id_rol', nr); fd.append('accion', 'editar'); 
            fetch('../Controlador/acciones_usuario.php', {method:'POST', body:fd}).then(()=>location.reload()); 
        } 
    }
    
    function eliminarProd(id) { 
        if(!confirm("¿Eliminar producto?")) return;
        const fd = new FormData(); fd.append('id', id); fd.append('accion', 'eliminar'); 
        fetch('../Controlador/acciones_inventario.php', {method:'POST', body:fd}).then(()=>location.reload()); 
    }

    function editarProd(id, p, s) { 
        const np = prompt("Precio:", p); const ns = prompt("Stock:", s); 
        if(np && ns) { 
            const fd = new FormData(); fd.append('id', id); fd.append('precio', np); fd.append('stock', ns); fd.append('accion', 'editar'); 
            fetch('../Controlador/acciones_inventario.php', {method:'POST', body:fd}).then(()=>location.reload()); 
        } 
    }
</script>
</body>
</html>
