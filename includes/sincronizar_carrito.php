<?php
session_start();
include('includes/conexion.php');

// Verificamos que sea un cliente
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener o crear el carrito en la BD
$sql_carrito = "SELECT id_carrito FROM carritos WHERE id_usuario = ?";
$stmt_carrito = $conexion->prepare($sql_carrito);
$stmt_carrito->bind_param("i", $id_usuario);
$stmt_carrito->execute();
$result = $stmt_carrito->get_result();

if ($result->num_rows > 0) {
    $id_carrito = $result->fetch_assoc()['id_carrito'];
} else {
    $stmt_insert = $conexion->prepare("INSERT INTO carritos (id_usuario) VALUES (?)");
    $stmt_insert->bind_param("i", $id_usuario);
    $stmt_insert->execute();
    $id_carrito = $conexion->insert_id;
}

// Obtener productos del carrito desde BD
$sql_productos = "
    SELECT cp.id_producto, cp.cantidad, p.nombre, 
           COALESCE(p.precio_promocion, p.precio) AS precio
    FROM carrito_productos cp
    INNER JOIN productos p ON p.id_producto = cp.id_producto
    WHERE cp.id_carrito = ?
";
$stmt_prod = $conexion->prepare($sql_productos);
$stmt_prod->bind_param("i", $id_carrito);
$stmt_prod->execute();
$res = $stmt_prod->get_result();

// Cargar productos a $_SESSION['carrito']
$_SESSION['carrito'] = [];

while ($row = $res->fetch_assoc()) {
    $id = $row['id_producto'];
    $cantidad = $row['cantidad'];
    $precio = $row['precio'];

    $_SESSION['carrito'][$id] = [
        'nombre' => $row['nombre'],
        'precio' => $precio,
        'cantidad' => $cantidad,
        'total' => $cantidad * $precio
    ];
}
?>
