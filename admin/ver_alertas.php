<?php
session_start();
include '../includes/conexion.php';

// Solo acceso para administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$sql = "SELECT a.id_alerta, i.nombre AS nombre_insumo, i.cantidad_disponible, i.cantidad_minima, a.fecha_alerta, a.estado
        FROM alertas_stock a
        JOIN insumos i ON a.id_insumo = i.id_insumo
        ORDER BY a.fecha_alerta DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alertas de Stock</title>
    <link rel="stylesheet" href="../css/admin_alertas.css">
</head>
<body>
    <a href="index.php" class="btn-volver">← Volver al inicio</a>
    <h2 class="titulo-admin">Alertas de Stock Bajo</h2>

    <table>
        <thead>
            <tr>
                <th>Insumo</th>
                <th>Cantidad Actual</th>
                <th>Stock Mínimo</th>
                <th>Fecha Alerta</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
            <tr class="<?= $fila['estado'] === 'pendiente' ? 'alerta-pendiente' : 'alerta-atendida' ?>">
                <td><?= htmlspecialchars($fila['nombre_insumo']) ?></td>
                <td><?= $fila['cantidad_disponible'] ?></td>
                <td><?= $fila['cantidad_minima'] ?></td>
                <td><?= $fila['fecha_alerta'] ?></td>
                <td><strong><?= ucfirst($fila['estado']) ?></strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
