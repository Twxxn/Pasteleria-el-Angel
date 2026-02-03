<?php
session_start();
include '../includes/conexion.php';

// Validar solo administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $cantidad = $_POST['cantidad_disponible'];
    $unidad = $_POST['unidad_medida'];
    $minima = $_POST['cantidad_minima'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $precio = $_POST['precio_compra'];

    // SQL SIN proveedor
    $sql = "INSERT INTO insumos (nombre, cantidad_disponible, unidad_medida, cantidad_minima, fecha_vencimiento, precio_compra)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sdssds", $nombre, $cantidad, $unidad, $minima, $fecha_vencimiento, $precio);

    if ($stmt->execute()) {
        $mensaje = "Insumo agregado correctamente.";
    } else {
        $mensaje = "Error al guardar el insumo.";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Insumo</title>
    <link rel="stylesheet" href="../css/admin_insumos.css">
</head>
<body>
    <a href="index.php" class="btn-volver">← Volver al inicio</a>
    <h2 class="titulo-admin">Agregar Nuevo Insumo</h2>

    <?php if (isset($mensaje)): ?>
        <p style="text-align: center; color: #bf4079;"><strong><?= $mensaje ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="agregar_insumo.php" class="formulario-admin">
        <label>Nombre del insumo:</label>
        <input type="text" name="nombre" required>

        <label>Cantidad disponible:</label>
        <input type="number" name="cantidad_disponible" step="0.01" required>

        <label>Unidad de medida:</label>
        <select name="unidad_medida" required>
            <option value="kg">kg</option>
            <option value="g">g</option>
            <option value="L">L</option>
            <option value="unidades">unidades</option>
        </select>

        <label>Cantidad mínima permitida:</label>
        <input type="number" name="cantidad_minima" step="0.01" required>

        <label>Fecha de vencimiento:</label>
        <input type="date" name="fecha_vencimiento" required>

        <label>Precio de compra:</label>
        <input type="number" name="precio_compra" step="0.01" required>

        <button type="submit">Guardar Insumo</button>
    </form>
</body>
</html>
