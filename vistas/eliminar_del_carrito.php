<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id = (int) $_POST['id_producto'];

    // Eliminar del carrito de sesión
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }

    // Eliminar también de la base de datos si es cliente
    if (isset($_SESSION['id_usuario']) && $_SESSION['tipo'] === 'cliente') {
        include '../includes/conexion.php';

        $id_usuario = $_SESSION['id_usuario'];

        // Obtener el ID del carrito de este usuario
        $res_carrito = mysqli_query($conexion, "SELECT id_carrito FROM carritos WHERE id_usuario = $id_usuario LIMIT 1");

        if ($row_carrito = mysqli_fetch_assoc($res_carrito)) {
            $id_carrito = $row_carrito['id_carrito'];

            // Eliminar el producto específico del carrito
            mysqli_query($conexion, "DELETE FROM carrito_productos WHERE id_carrito = $id_carrito AND id_producto = $id");
        }
    }
}

header('Location: carrito.php', true, 303);
exit;
?>
