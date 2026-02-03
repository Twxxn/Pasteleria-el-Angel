<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../vistas/login.php');
    exit;
}

include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id_producto = (int) $_POST['id_producto'];
    $id_usuario = $_SESSION['id_usuario'];

    // Obtener datos del producto
    $query = mysqli_query($conexion, "SELECT * FROM productos WHERE id_producto = $id_producto AND estado = 'activo'");
    $producto = mysqli_fetch_assoc($query);

    if (!$producto) {
        $_SESSION['mensaje_carrito'] = "Producto no válido.";
        header("Location: carrito.php");
        exit;
    }

    // Determinar precio final (usar precio_promocion si existe)
    $precio_final = (isset($producto['precio_promocion']) && $producto['precio_promocion'] > 0)
        ? $producto['precio_promocion']
        : $producto['precio'];

    // Inicializar carrito en sesión si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar o actualizar en la sesión
    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad'] += 1;
        $_SESSION['carrito'][$id_producto]['total'] = $_SESSION['carrito'][$id_producto]['cantidad'] * $precio_final;
    } else {
        $_SESSION['carrito'][$id_producto] = [
            'nombre' => $producto['nombre'],
            'precio' => $precio_final,
            'cantidad' => 1,
            'total' => $precio_final
        ];
    }

    // --- GUARDAR EN LA BASE DE DATOS ---
    // 1. Buscar o crear carrito para el usuario
    $res_carrito = mysqli_query($conexion, "SELECT id_carrito FROM carritos WHERE id_usuario = $id_usuario");
    if (mysqli_num_rows($res_carrito) > 0) {
        $row = mysqli_fetch_assoc($res_carrito);
        $id_carrito = $row['id_carrito'];
    } else {
        mysqli_query($conexion, "INSERT INTO carritos (id_usuario) VALUES ($id_usuario)");
        $id_carrito = mysqli_insert_id($conexion);
    }

    // 2. Verificar si el producto ya está en carrito_productos
    $res_existente = mysqli_query($conexion, "SELECT * FROM carrito_productos WHERE id_carrito = $id_carrito AND id_producto = $id_producto");

    if (mysqli_num_rows($res_existente) > 0) {
        mysqli_query($conexion, "UPDATE carrito_productos SET cantidad = cantidad + 1 WHERE id_carrito = $id_carrito AND id_producto = $id_producto");
    } else {
        mysqli_query($conexion, "INSERT INTO carrito_productos (id_carrito, id_producto, cantidad) VALUES ($id_carrito, $id_producto, 1)");
    }

    $_SESSION['mensaje_carrito'] = "Producto agregado al carrito";
    header("Location: ../vistas/carrito.php");
    exit;
}
