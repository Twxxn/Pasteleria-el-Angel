<?php
require_once '../includes/conexion.php';

// Obtener todos los productos activos
$productos_query = "SELECT * FROM productos WHERE estado = 'activo'";
$productos_result = $conexion->query($productos_query);

// Obtener todas las secciones
$secciones_query = "SELECT * FROM secciones";
$secciones_result = $conexion->query($secciones_query);
$secciones = [];
while ($row = $secciones_result->fetch_assoc()) {
    $secciones[] = $row;
}

// Procesar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['secciones'])) {
    foreach ($_POST['secciones'] as $id_producto => $ids_secciones) {
        $conexion->query("DELETE FROM producto_seccion WHERE id_producto = $id_producto");

        $stmt = $conexion->prepare("INSERT INTO producto_seccion (id_producto, id_seccion) VALUES (?, ?)");
        foreach ($ids_secciones as $id_seccion) {
            $stmt->bind_param("ii", $id_producto, $id_seccion);
            $stmt->execute();
        }
    }

    echo "<p class='mensaje-exito'>✅ Secciones actualizadas correctamente.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Secciones de Productos</title>
    <link rel="stylesheet" href="../css/secciones.css">
</head>
<body>
    <a href="index.php" class="btn-volver">← Volver al inicio</a>
    <h2>Asignar Secciones a Productos</h2>
    <form method="POST">
        <table>
            <tr>
                <th>Producto</th>
                <?php foreach ($secciones as $sec): ?>
                    <th><?= ucfirst($sec['nombre']) ?></th>
                <?php endforeach; ?>
            </tr>

            <?php while ($prod = $productos_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['nombre']) ?></td>
                    <?php
                    $secciones_actuales = [];
                    $res = $conexion->query("SELECT id_seccion FROM producto_seccion WHERE id_producto = " . $prod['id_producto']);
                    while ($s = $res->fetch_assoc()) {
                        $secciones_actuales[] = $s['id_seccion'];
                    }
                    ?>
                    <?php foreach ($secciones as $sec): ?>
                        <td>
                            <input type="checkbox"
                                   name="secciones[<?= $prod['id_producto'] ?>][]"
                                   value="<?= $sec['id_seccion'] ?>"
                                   <?= in_array($sec['id_seccion'], $secciones_actuales) ? 'checked' : '' ?>>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </table>
        <button class="guardar-btn" type="submit">Guardar cambios</button>
    </form>


</body>
</html>
