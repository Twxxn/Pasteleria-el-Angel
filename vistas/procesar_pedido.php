<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
$fecha_recogida = $_POST['fecha_recogida'] ?? null;
$hora_recogida = $_POST['hora_recogida'] ?? null;
$observaciones = $_POST['observaciones'] ?? '';
$carrito = $_SESSION['carrito'] ?? [];

$fecha_pedido = date('Y-m-d');
$estado = 'pendiente';

if (empty($fecha_recogida) || empty($carrito)) {
    $_SESSION['error_pedido'] = "Faltan datos o el carrito está vacío.";
    header("Location: finalizar_compra.php");
    exit;
}

// Validar hora segura
if ($hora_recogida < '09:00' || $hora_recogida > '18:00') {
    $_SESSION['error_pedido'] = "La hora debe ser entre 09:00 y 18:00.";
    header("Location: finalizar_compra.php");
    exit;
}

// 1. Insertar en pedidos
$stmt = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha_pedido, estado, metodo_pago, fecha_recogida, hora_recogida, observaciones)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $id_usuario, $fecha_pedido, $estado, $metodo_pago, $fecha_recogida, $hora_recogida, $observaciones);

if ($stmt->execute()) {
    $id_pedido = $stmt->insert_id;

    // 2. Insertar detalles de pedido
    foreach ($carrito as $id_producto => $producto) {
        $cantidad = $producto['cantidad'];
        $subtotal = $producto['precio'] * $cantidad;

        $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal)
                                            VALUES (?, ?, ?, ?)");
        $stmt_detalle->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $subtotal);
        $stmt_detalle->execute();
    }

    // 3. Limpiar carrito
    $conexion->query("DELETE FROM carrito_productos WHERE id_carrito = (SELECT id_carrito FROM carritos WHERE id_usuario = $id_usuario)");
    unset($_SESSION['carrito']);

    $_SESSION['mensaje_pedido'] = "¡Pedido realizado con éxito!";
    header("Location: ../index.php");
    exit;
} else {
    echo "Error al registrar pedido: " . $stmt->error;
}
?>
