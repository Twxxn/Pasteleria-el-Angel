<?php
session_start();
include '../includes/conexion.php';

// Validar acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener datos del cliente
$stmt = $conexion->prepare("SELECT nombre, correo FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();

// Obtener número de pedidos
$pedidos = $conexion->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE id_usuario = ?");
$pedidos->bind_param("i", $id_usuario);
$pedidos->execute();
$total_pedidos = $pedidos->get_result()->fetch_assoc()['total'] ?? 0;

// Obtener gasto total
$total = $conexion->prepare("
    SELECT COALESCE(SUM(v.monto_total), 0) AS total
    FROM ventas v
    JOIN pedidos p ON v.id_pedido = p.id_pedido
    WHERE p.id_usuario = ?
");
$total->bind_param("i", $id_usuario);
$total->execute();
$total_gastado = $total->get_result()->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Mi Perfil</h1>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($resultado['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($resultado['correo']) ?></p>
        <p><strong>Pedidos realizados:</strong> <?= $total_pedidos ?></p>
        <p><strong>Total gastado:</strong> $<?= number_format($total_gastado, 2) ?></p>
        <a href="../index.php" class="btn-volver">← Volver al inicio</a>
    </div>
</body>
</html>
