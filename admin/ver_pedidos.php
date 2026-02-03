<?php
session_start();
include '../includes/conexion.php';

// Validación: solo puede entrar un administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Obtener todos los pedidos con datos del usuario, ordenados por fecha y hora de recolección
// Obtener el filtro desde GET
$filtro = isset($_GET['estado']) ? $_GET['estado'] : 'todos';

$sql_base = "SELECT p.*, u.nombre AS nombre_usuario 
             FROM pedidos p
             JOIN usuarios u ON p.id_usuario = u.id_usuario";

// Solo filtra si el estado es válido
$estados_validos = ['pendiente', 'completado', 'cancelado'];
if (in_array($filtro, $estados_validos)) {
    $sql_base .= " WHERE p.estado = '$filtro'";
}

$sql_base .= " ORDER BY p.fecha_recogida ASC, p.hora_recogida ASC";


$resultado = $conexion->query($sql_base);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedidos - Admin</title>
    <link rel="stylesheet" href="../css/admin_pedidos.css">
</head>

<body>
    <a href="index.php" class="btn-volver">← Volver al inicio</a>
    <h1>Gestión de Pedidos</h1>

    <div class="filtros-pedidos" style="text-align: center; margin-bottom: 20px;">
        <a href="ver_pedidos.php?estado=pendiente" class="btn-filtro">Pendientes</a>
        <a href="ver_pedidos.php?estado=completado" class="btn-filtro">Completados</a>
        <a href="ver_pedidos.php?estado=cancelado" class="btn-filtro">Cancelados</a>
        <a href="ver_pedidos.php" class="btn-filtro">Todos</a>
    </div>


    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Fecha Pedido</th>
                <th>Fecha Recolección</th>
                <th>Hora</th>
                <th>Método Pago</th>
                <th>Estado</th>
                <th>Observaciones</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($pedido = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($pedido['nombre_usuario']) ?></td>
                    <td><?= date('Y-m-d', strtotime($pedido['fecha_pedido'])) ?></td>
                    <td><?= $pedido['fecha_recogida'] ?></td>
                    <td><?= $pedido['hora_recogida'] ?></td>
                    <td><?= $pedido['metodo_pago'] ?></td>
                    <td><?= ucfirst($pedido['estado']) ?></td>
                    <td><?= htmlspecialchars($pedido['observaciones']) ?></td>
                    <td>
                        <form action="actualizar_estado_pedido.php" method="POST" onsubmit="return confirmarCambioEstado();">
                            <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                            <select name="nuevo_estado">
                                <option value="pendiente" <?= $pedido['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="completado" <?= $pedido['estado'] == 'completado' ? 'selected' : '' ?>>Completado</option>
                                <option value="cancelado" <?= $pedido['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                            <button type="submit">Cambiar</button>
                        </form>
                    </td>
                </tr>

                <!-- Subtabla con detalle del pedido -->
                <tr>
                    <td colspan="8" style="padding: 0;">
                        <table style="width: 95%; margin: 10px auto; background-color: #fff0f5; border-radius: 10px;">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_pedido_detalle = $pedido['id_pedido'];
                                $sql_detalle = "SELECT d.*, p.nombre AS nombre_producto 
                FROM detalle_pedido d 
                JOIN productos p ON d.id_producto = p.id_producto 
                WHERE d.id_pedido = ?";
                                $stmt_detalle = $conexion->prepare($sql_detalle);
                                $stmt_detalle->bind_param("i", $id_pedido_detalle);
                                $stmt_detalle->execute();
                                $res_detalle = $stmt_detalle->get_result();
                                $total = 0;

                                if ($res_detalle->num_rows > 0) {
                                    while ($prod = $res_detalle->fetch_assoc()):
                                        $total += $prod['subtotal'];
                                ?>
                                        <tr>
                                            <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                                            <td><?= $prod['cantidad'] ?></td>
                                            <td>$<?= number_format($prod['subtotal'], 2) ?></td>
                                        </tr>
                                    <?php
                                    endwhile;
                                } else {
                                    // Intentar cargar como pedido personalizado
                                    $sql_personalizado = "SELECT precio FROM detalle_personalizado WHERE id_pedido = ?";
                                    $stmt_pers = $conexion->prepare($sql_personalizado);
                                    $stmt_pers->bind_param("i", $id_pedido_detalle);
                                    $stmt_pers->execute();
                                    $res_pers = $stmt_pers->get_result();

                                    if ($row = $res_pers->fetch_assoc()) {
                                        $total = $row['precio'];
                                    ?>
                                        <tr>
                                            <td><em>Pastel personalizado</em></td>
                                            <td>1</td>
                                            <td>$<?= number_format($total, 2) ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>

                                <tr>
                                    <td colspan="2" style="text-align: right; font-weight: bold;">Total del pedido:</td>
                                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br><br>

    <script>
        function confirmarCambioEstado() {
            return confirm("¿Estás seguro de que deseas cambiar el estado del pedido?");
        }
    </script>
</body>

</html>