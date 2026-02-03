<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID inválido.";
    exit;
}

$sql = "SELECT * FROM insumos WHERE id_insumo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "Insumo no encontrado.";
    exit;
}

$insumo = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Insumo</title>
    <link rel="stylesheet" href="../css/admin_insumos.css">
</head>

<body>
    <h2 class="titulo-admin">Editar Insumo</h2>

    <form class="formulario-admin" method="POST" action="guardar_edicion_insumo.php" onsubmit="return confirmarEdicion();">
        <input type="hidden" name="id" value="<?= $insumo['id_insumo'] ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($insumo['nombre']) ?>" required>

        <label>Cantidad disponible:</label>
        <input type="number" step="0.01" name="cantidad_disponible" value="<?= $insumo['cantidad_disponible'] ?>" required>

        <label>Unidad de medida:</label>
        <select name="unidad_medida" required>
            <option value="kg" <?= $insumo['unidad_medida'] == 'kg' ? 'selected' : '' ?>>kg</option>
            <option value="g" <?= $insumo['unidad_medida'] == 'g' ? 'selected' : '' ?>>g</option>
            <option value="L" <?= $insumo['unidad_medida'] == 'L' ? 'selected' : '' ?>>L</option>
            <option value="unidades" <?= $insumo['unidad_medida'] == 'unidades' ? 'selected' : '' ?>>unidades</option>
        </select>

        <label>Cantidad mínima:</label>
        <input type="number" step="0.01" name="cantidad_minima" value="<?= $insumo['cantidad_minima'] ?>" required>

        <label>Fecha de vencimiento:</label>
        <input type="date" name="fecha_vencimiento" value="<?= $insumo['fecha_vencimiento'] ?>">

        <label>Precio de compra:</label>
        <input type="number" step="0.01" name="precio_compra" value="<?= $insumo['precio_compra'] ?>" required>

        <button type="submit">Guardar cambios</button>
    </form>

    <div class="centrar-boton">
        <a href="ver_insumos.php" class="btn-volver">← Volver</a>
    </div>
    <script>
        function confirmarEdicion() {
            return confirm("¿Estás seguro de que deseas guardar los cambios en este insumo?");
        }
    </script>

</body>

</html>