<?php
session_start();
include '../includes/conexion.php';

// Validar acceso administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$sql = "SELECT * FROM insumos ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Insumos</title>
    <link rel="stylesheet" href="../css/admin_insumos.css">
</head>

<body>
    <h2 class="titulo-admin">Gestión de Insumos</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ok'): ?>
        <div style="text-align:center; margin: 20px; padding: 10px; background-color: #e7f9e7; color: #2d7c2d; border: 1px solid #b2d8b2; border-radius: 8px;">
            ✅ Insumo actualizado correctamente.
        </div>
    <?php endif; ?>


    <table class="tabla-insumos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Stock Mínimo</th>
                <th>Vencimiento</th>
                <th>Precio Compra</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($insumo = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?= htmlspecialchars($insumo['nombre']) ?></td>
                    <td><?= $insumo['cantidad_disponible'] ?></td>
                    <td><?= $insumo['unidad_medida'] ?></td>
                    <td><?= $insumo['cantidad_minima'] ?></td>
                    <td><?= $insumo['fecha_vencimiento'] ?></td>
                    <td>$<?= number_format($insumo['precio_compra'], 2) ?></td>
                    <td>
                        <a href="editar_insumo.php?id=<?= $insumo['id_insumo'] ?>">Editar</a> |
                        <a href="eliminar_insumo.php?id=<?= $insumo['id_insumo'] ?>" onclick="return confirm('¿Estás seguro de eliminar este insumo?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="centrar-boton">
        <a href="index.php" class="btn-volver">← Volver al inicio</a>
    </div>
</body>

</html>