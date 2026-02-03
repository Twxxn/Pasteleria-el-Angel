<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'];
    $nuevo_estado = $_POST['nuevo_estado'];

    // 1. Actualizar estado del pedido
    $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id_pedido = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_pedido);
    $stmt->execute();

    if ($nuevo_estado === 'completado') {
        // 2. Verificar si ya existe una venta registrada
        $verificar = $conexion->prepare("SELECT id_venta FROM ventas WHERE id_pedido = ?");
        $verificar->bind_param("i", $id_pedido);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows === 0) {
            // 3. Calcular monto total del pedido dependiendo si es normal o personalizado
            $es_personalizado = false;
            $check = $conexion->prepare("SELECT precio FROM detalle_personalizado WHERE id_pedido = ?");
            $check->bind_param("i", $id_pedido);
            $check->execute();
            $res = $check->get_result();

            if ($row = $res->fetch_assoc()) {
                // Es personalizado
                $es_personalizado = true;
                $total = $row['precio'];
            } else {
                // Es pedido normal, calcular total desde detalle_pedido
                $sql_total = "SELECT SUM(subtotal) AS total FROM detalle_pedido WHERE id_pedido = ?";
                $stmt_total = $conexion->prepare($sql_total);
                $stmt_total->bind_param("i", $id_pedido);
                $stmt_total->execute();
                $resultado_total = $stmt_total->get_result();
                $total = $resultado_total->fetch_assoc()['total'] ?? 0;
                $total = is_numeric($total) ? $total : 0;
            }
            $check->close();

            // 4. Insertar en la tabla ventas
            $stmt_insert = $conexion->prepare("INSERT INTO ventas (id_pedido, monto_total, fecha_venta) VALUES (?, ?, NOW())");
            $stmt_insert->bind_param("id", $id_pedido, $total);
            $stmt_insert->execute();
        }


        // 5. Actualizar stock de productos e insumos utilizados
        $sql_detalle = "SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?";
        $stmt_detalle = $conexion->prepare($sql_detalle);
        $stmt_detalle->bind_param("i", $id_pedido);
        $stmt_detalle->execute();
        $result_detalle = $stmt_detalle->get_result();

        while ($row = $result_detalle->fetch_assoc()) {
            $id_producto = $row['id_producto'];
            $cantidad_pedida = $row['cantidad'];

            // 5.1 Disminuir stock del producto
            $conexion->query("UPDATE productos SET stock = stock - $cantidad_pedida WHERE id_producto = $id_producto");

            // 5.2 Disminuir stock de insumos usados por ese producto
            $sql_insumos = "SELECT id_insumo, cantidad_utilizada FROM producto_insumo WHERE id_producto = ?";
            $stmt_insumos = $conexion->prepare($sql_insumos);
            $stmt_insumos->bind_param("i", $id_producto);
            $stmt_insumos->execute();
            $res_insumos = $stmt_insumos->get_result();

            while ($insumo = $res_insumos->fetch_assoc()) {
                $id_insumo = $insumo['id_insumo'];
                $cantidad_usada_total = $insumo['cantidad_utilizada'] * $cantidad_pedida;

                // 5.3 Disminuir la cantidad disponible del insumo
                $conexion->query("UPDATE insumos SET cantidad_disponible = cantidad_disponible - $cantidad_usada_total WHERE id_insumo = $id_insumo");
            }
        }

        // 6. Verificar y generar alertas por insumos con stock bajo
        $sql_insumos_usados = "
            SELECT pi.id_insumo
            FROM detalle_pedido dp
            JOIN producto_insumo pi ON dp.id_producto = pi.id_producto
            WHERE dp.id_pedido = ?
            GROUP BY pi.id_insumo
        ";

        $stmt_insumos = $conexion->prepare($sql_insumos_usados);
        $stmt_insumos->bind_param("i", $id_pedido);
        $stmt_insumos->execute();
        $resultado_insumos = $stmt_insumos->get_result();

        while ($fila = $resultado_insumos->fetch_assoc()) {
            $id_insumo = $fila['id_insumo'];

            // Obtener cantidad actual y mínima del insumo
            $stmt_cantidades = $conexion->prepare("SELECT cantidad_disponible, cantidad_minima FROM insumos WHERE id_insumo = ?");
            $stmt_cantidades->bind_param("i", $id_insumo);
            $stmt_cantidades->execute();
            $cantidades = $stmt_cantidades->get_result()->fetch_assoc();

            if ($cantidades['cantidad_disponible'] <= $cantidades['cantidad_minima']) {
                // Verificar si ya existe una alerta pendiente
                $stmt_verificar = $conexion->prepare("SELECT id_alerta FROM alertas_stock WHERE id_insumo = ? AND estado = 'pendiente'");
                $stmt_verificar->bind_param("i", $id_insumo);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                if ($stmt_verificar->num_rows === 0) {
                    // Insertar nueva alerta
                    $stmt_alerta = $conexion->prepare("INSERT INTO alertas_stock (id_insumo, fecha_alerta, estado) VALUES (?, NOW(), 'pendiente')");
                    $stmt_alerta->bind_param("i", $id_insumo);
                    $stmt_alerta->execute();
                }

                $stmt_verificar->close();
            }
        }
    }

    // Redirigir después de completar todo el proceso
    header("Location: ver_pedidos.php");
    exit();
}
