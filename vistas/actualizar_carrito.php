<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];

    if (isset($_SESSION['carrito'][$id])) {
        include '../includes/conexion.php';

        // Validar stock actual en BD
        $consulta = mysqli_query($conexion, "SELECT stock FROM Productos WHERE id_producto = $id AND estado = 'activo'");
        $producto = mysqli_fetch_assoc($consulta);

        if ($producto && $cantidad > 0 && $cantidad <= $producto['stock']) {
            // Actualizar la sesión
            $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
            $_SESSION['carrito'][$id]['total'] = $_SESSION['carrito'][$id]['precio'] * $cantidad;

            // Si es cliente, también sincronizar en base de datos
            if (isset($_SESSION['id_usuario']) && $_SESSION['tipo'] === 'cliente') {
                $id_usuario = $_SESSION['id_usuario'];

                // Obtener el ID del carrito en la base
                $res_carrito = mysqli_query($conexion, "SELECT id_carrito FROM carritos WHERE id_usuario = $id_usuario LIMIT 1");
                if ($row_carrito = mysqli_fetch_assoc($res_carrito)) {
                    $id_carrito = $row_carrito['id_carrito'];

                    // Verificar si ya existe el producto en el carrito
                    $existe = mysqli_query($conexion, "SELECT * FROM carrito_productos WHERE id_carrito = $id_carrito AND id_producto = $id");

                    if (mysqli_num_rows($existe) > 0) {
                        // Actualizar cantidad
                        mysqli_query($conexion, "UPDATE carrito_productos SET cantidad = $cantidad WHERE id_carrito = $id_carrito AND id_producto = $id");
                    } else {
                        // Insertar si no existía
                        mysqli_query($conexion, "INSERT INTO carrito_productos (id_carrito, id_producto, cantidad) VALUES ($id_carrito, $id, $cantidad)");
                    }
                }
            }
        } else {
            $_SESSION['error_carrito'] = "Cantidad no válida o fuera de stock.";
        }
    }
}

header('Location: carrito.php', true, 303);
exit;
?>
