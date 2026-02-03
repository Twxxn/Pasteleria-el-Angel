<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
        header("Location: ../index.php");
        exit();
    }

    $id_producto = $_POST['id_producto'];
    $nuevo_precio = $_POST['precio'];
    $nuevo_stock = $_POST['stock'];

    // Determinar el estado automáticamente
    $estado = ($nuevo_stock <= 0) ? 'inactivo' : 'activo';

    $sql = "UPDATE productos SET precio = ?, stock = ?, estado = ? WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("disi", $nuevo_precio, $nuevo_stock, $estado, $id_producto);
    $stmt->execute();

    header("Location: ver_productos.php");
    exit();
}
?>
