<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_producto'];
    
    // Cambia el estado a 'Inactivo'
    $query = "UPDATE productos SET estado = 'inactivo' WHERE id_producto = $id";
    mysqli_query($conexion, $query);

    // Redirige de nuevo a la lista
    header('Location: ver_productos.php');
    exit;
}
?>
