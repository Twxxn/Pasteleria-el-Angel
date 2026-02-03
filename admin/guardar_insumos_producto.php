<?php
session_start();
include '../includes/conexion.php';

$id_producto = $_POST['id_producto'];
$insumos = $_POST['insumos'];
$cantidades = $_POST['cantidades'];

for ($i = 0; $i < count($insumos); $i++) {
    $id_insumo = $insumos[$i];
    $cantidad = $cantidades[$i];

    if (!empty($id_insumo) && $cantidad > 0) {
        $query = "INSERT INTO producto_insumo (id_producto, id_insumo, cantidad_utilizada)
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE cantidad_utilizada = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("iidd", $id_producto, $id_insumo, $cantidad, $cantidad);
        $stmt->execute();
    }
}

header("Location: seleccionar_producto_insumos.php?msg=ok");
exit;
