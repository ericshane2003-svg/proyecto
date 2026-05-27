<?php
// Controlador/registrar_log.php
// Esta función es una herramienta que llamaremos desde otros archivos.

function guardarLog($conexion, $usuario, $accion) {
    // Usamos sentencia preparada para máxima seguridad
    $stmt = $conexion->prepare("INSERT INTO logs_sistema (usuario, accion) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $usuario, $accion);
        $stmt->execute();
        $stmt->close();
    }
}
?>