<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$cantidad = $_POST['cantidad_disponible'];
$unidad = $_POST['unidad_medida'];
$minimo = $_POST['cantidad_minima'];
$vencimiento = $_POST['fecha_vencimiento'];
$precio = $_POST['precio_compra'];

// 1. Actualizar el insumo
$sql = "UPDATE insumos 
        SET nombre=?, cantidad_disponible=?, unidad_medida=?, cantidad_minima=?, fecha_vencimiento=?, precio_compra=? 
        WHERE id_insumo=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sdssssi", $nombre, $cantidad, $unidad, $minimo, $vencimiento, $precio, $id);

if ($stmt->execute()) {

    // 2. Verificar si el insumo ahora está por ENCIMA del mínimo
    $sql_verificar = "SELECT cantidad_disponible, cantidad_minima FROM insumos WHERE id_insumo = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->bind_param("i", $id);
    $stmt_verificar->execute();
    $datos = $stmt_verificar->get_result()->fetch_assoc();

    if ($datos['cantidad_disponible'] > $datos['cantidad_minima']) {
        // 3. Marcar alerta como atendida SOLO si hay una alerta pendiente activa
        $sql_alerta = "UPDATE alertas_stock 
                       SET estado = 'atendida' 
                       WHERE id_insumo = ? 
                       AND estado = 'pendiente'";
        $stmt_alerta = $conexion->prepare($sql_alerta);
        $stmt_alerta->bind_param("i", $id);
        $stmt_alerta->execute();
    }

    header("Location: ver_insumos.php?msg=ok");
    exit;
} else {
    echo "❌ Error al actualizar el insumo.";
}
