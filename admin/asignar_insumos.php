<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$id_producto = $_GET['id'] ?? null;
if (!$id_producto) {
    echo "Producto no especificado.";
    exit;
}

$producto = $conexion->query("SELECT nombre FROM productos WHERE id_producto = $id_producto")->fetch_assoc();
$insumos = $conexion->query("SELECT * FROM insumos");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Asignar Insumos</title>
    <link rel="stylesheet" href="../css/admin_asignar_insumos.css">
    <script>
        function actualizarUnidad(selectElement, index) {
            const unidad = selectElement.selectedOptions[0].dataset.unidad;
            document.getElementById("unidad_" + index).innerText = unidad;
        }

        function agregarInsumo() {
            const index = document.querySelectorAll('.grupo-insumo').length;
            const contenedor = document.getElementById("contenedor-insumos");

            const div = document.createElement("div");
            div.className = "grupo-insumo";
            div.innerHTML = `
            <label>Insumo:</label>
            <select name="insumos[]" onchange="actualizarUnidad(this, ${index})">
                <option value="">-- Seleccionar --</option>
                <?php $insumos->data_seek(0);
                while ($insumo = $insumos->fetch_assoc()): ?>
                    <option value="<?= $insumo['id_insumo'] ?>" data-unidad="<?= $insumo['unidad_medida'] ?>">
                        <?= $insumo['nombre'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Cantidad:</label>
            <input type="number" step="0.01" name="cantidades[]" required>
            <span id="unidad_${index}" class="unidad-medida"> </span>
        `;
            contenedor.appendChild(div);
        }
    </script>
</head>

<body>
    <h2 class="titulo-admin">Asignar Insumos a: <?= htmlspecialchars($producto['nombre']) ?></h2>

    <form action="guardar_insumos_producto.php" method="POST" class="formulario-insumos-multiple" onsubmit="return confirmarAsignacion();">
        <input type="hidden" name="id_producto" value="<?= $id_producto ?>">

        <div id="contenedor-insumos">
            <div class="grupo-insumo">
                <label>Insumo:</label>
                <select name="insumos[]" onchange="actualizarUnidad(this, 0)">
                    <option value="">-- Seleccionar --</option>
                    <?php $insumos->data_seek(0);
                    while ($insumo = $insumos->fetch_assoc()): ?>
                        <option value="<?= $insumo['id_insumo'] ?>" data-unidad="<?= $insumo['unidad_medida'] ?>">
                            <?= $insumo['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Cantidad:</label>
                <input type="number" step="0.01" name="cantidades[]" required>
                <span id="unidad_0" class="unidad-medida"> </span>
            </div>
        </div>

        <button type="button" onclick="agregarInsumo()">+ Agregar otro insumo</button>
        <br><br>
        <button type="submit">Guardar insumos</button>
    </form>

    <div class="centrar-boton">
        <a href="seleccionar_producto_insumos.php" class="btn-volver">← Volver a elegir producto</a>
    </div>
    <script>
        function confirmarAsignacion() {
            return confirm("¿Estás seguro de que deseas asignar estos insumos al producto?");
        }
    </script>

</body>

</html>