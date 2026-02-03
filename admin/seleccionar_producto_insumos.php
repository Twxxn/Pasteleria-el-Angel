<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$resultado = $conexion->query("SELECT id_producto, nombre FROM productos");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Producto</title>
    <link rel="stylesheet" href="../css/admin_asignar_insumos.css">
</head>

<body>
    <h2 class="titulo-admin">Seleccionar Producto para Asignar Insumos</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ok'): ?>
        <div style="text-align:center; color: green; font-weight: bold; margin-top: 10px;">
            ✅ Insumos guardados correctamente.
        </div>
    <?php endif; ?>


    <form action="asignar_insumos.php" method="GET" class="formulario-insumos-multiple">
        <label for="id">Selecciona un producto:</label>
        <select name="id" required>
            <option value="">-- Seleccionar producto --</option>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <option value="<?= $row['id_producto'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <button type="submit">Asignar insumos</button>
    </form>

    <div class="centrar-boton">
        <a href="index.php" class="btn-volver">← Volver al inicio</a>
    </div>
</body>

</html>