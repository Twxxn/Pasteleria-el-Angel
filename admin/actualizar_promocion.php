<?php
require_once '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_producto'])) {
    $id = $_POST['id_producto'];
    $precio_promocion = $_POST['precio_promocion'];

    // Si el campo está vacío o 0, lo ponemos como NULL (sin promoción)
    if ($precio_promocion <= 0 || empty($precio_promocion)) {
        $stmt = $conexion->prepare("UPDATE productos SET precio_promocion = NULL WHERE id_producto = ?");
    } else {
        $stmt = $conexion->prepare("UPDATE productos SET precio_promocion = ? WHERE id_producto = ?");
        $stmt->bind_param("di", $precio_promocion, $id);
    }

    if (isset($stmt)) {
        if ($stmt->param_count === 1) {
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
    }
}

header("Location: ver_productos.php");
exit;
?>
