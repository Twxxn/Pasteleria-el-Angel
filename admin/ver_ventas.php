<?php
session_start();
include '../includes/conexion.php';

// Verificar sesión de administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Consultar las ventas con nombre del cliente
$sql = "SELECT v.*, u.nombre AS nombre_cliente
        FROM ventas v
        JOIN pedidos p ON v.id_pedido = p.id_pedido
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        ORDER BY v.fecha_venta DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas Registradas</title>
    <link rel="stylesheet" href="../css/admin_pedidos.css">
</head>
<body>
    <a href="index.php" class="btn-volver">← Volver al inicio</a>
    <h1>Ventas Realizadas</h1>

    <table>
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Monto Total</th>
                <th>Fecha de Venta</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($venta = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $venta['id_venta'] ?></td>
                <td><?= $venta['id_pedido'] ?></td>
                <td><?= htmlspecialchars($venta['nombre_cliente']) ?></td>
                <td>$<?= number_format($venta['monto_total'], 2) ?></td>
                <td><?= $venta['fecha_venta'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <br>

</body>
</html>
