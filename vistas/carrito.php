<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrito de Compras</title>
  
  <link rel="stylesheet" href="../css/style.css?v=2.0">
  <link rel="stylesheet" href="../css/carrito.css?v=2.0">
</head>
<body>
  
  <div class="contenedor-carrito-responsivo">
      <h2 style="text-align: center; margin-top: 20px;">🛒 Mi Carrito</h2>

      <?php if (isset($_SESSION['mensaje_carrito'])): ?>
        <p style="color: green; text-align: center;"><?= $_SESSION['mensaje_carrito'] ?></p>
        <?php unset($_SESSION['mensaje_carrito']); ?>
      <?php endif; ?>

      <?php if (empty($_SESSION['carrito'])): ?>
        <div class="carrito-vacio" style="text-align: center; padding: 50px;">
          <i class="fas fa-shopping-cart" style="font-size: 50px; color: #ccc;"></i>
          <p>Tu carrito está vacío.</p>
          <a href="../index.php" class="btn-volver">← Volver al inicio</a>
        </div>

      <?php else: ?>
        <table class="tabla-carrito">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Total</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = 0;
            foreach ($_SESSION['carrito'] as $id => $item):
              $subtotal = $item['precio'] * $item['cantidad'];
              $total += $subtotal;
            ?>
            <tr>
              <td data-label="Producto" class="nombre-producto"><?= htmlspecialchars($item['nombre']) ?></td>
              <td data-label="Precio">$<?= number_format($item['precio'], 2) ?></td>
              <td data-label="Cantidad">
                <form action="actualizar_carrito.php" method="POST" class="form-cantidad">
                  <input type="hidden" name="id_producto" value="<?= $id ?>">
                  <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" max="99">
                  <button type="submit" class="btn-actualizar">↻</button>
                </form>
              </td>
              <td data-label="Total" class="total-fila">$<?= number_format($subtotal, 2) ?></td>
              <td data-label="Acción">
                <form action="eliminar_del_carrito.php" method="POST">
                  <input type="hidden" name="id_producto" value="<?= $id ?>">
                  <button type="submit" class="btn-eliminar">Eliminar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="resumen-carrito">
          <h3>Total a Pagar: $<?= number_format($total, 2) ?></h3>
          <div class="botones-finales">
              <a href="finalizar_compra.php" class="btn-finalizar">Finalizar compra</a>
              <a href="../index.php" class="btn-volver">Seguir comprando</a>
          </div>
        </div>

      <?php endif; ?>
  </div>

</body>
</html>