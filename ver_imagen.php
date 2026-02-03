<?php
include('includes/conexion.php');
$id = $_GET['id'];

$sql = "SELECT imagen FROM Productos WHERE id_producto = $id";
$resultado = mysqli_query($conexion, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    header("Content-Type: image/jpeg"); // Cambiar a png si es necesario
    echo $fila['imagen'];
} else {
    // Imagen por defecto si no se encuentra
    readfile('img/default.jpg');
}
?>
