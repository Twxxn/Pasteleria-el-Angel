<?php
session_start();
// Activar reporte de errores por si algo falla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
  header("Location: ../index.php");
  exit;
}
include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ver Productos</title>
  <link rel="stylesheet" href="../css/admin_ver_productos.css?v=2.0">
  <link rel="stylesheet" href="../css/style.css?v=2.0">

  <style>
      /* Un poco de CSS de emergencia por si el archivo externo falla */
      .contenedor-productos-admin {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
          justify-content: center;
          padding: 20px;
      }
      .tarjeta-admin {
          border: 1px solid #ddd;
          border-radius: 10px;
          padding: 15px;
          width: 100%;
          max-width: 300px; /* Tamaño tarjeta */
          background: white;
          box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      }
      .img-admin {
          width: 100%;
          height: 200px;
          object-fit: cover;
          border-radius: 8px;
      }
      input[type="number"] {
          padding: 5px;
          border-radius: 5px;
          border: 1px solid #ccc;
          width: 80%;
      }
      button {
          cursor: pointer;
          padding: 8px;
          border-radius: 5px;
          border: none;
          color: white;
          background: #f08080;
      }
  </style>
</head>

<body>
  <a href="index.php" class="btn-volver" style="display:inline-block; margin: 20px;">← Volver al inicio</a>
  <h2 class="titulo-admin" style="text-align:center;">Gestión de Productos</h2>

  <div class="contenedor-productos-admin">
    <?php
    // CORRECCIÓN: 'productos' en minúsculas
    $sql = "SELECT * FROM productos"; 
    $resultado = mysqli_query($conexion, $sql);

    // Verificación de error en la consulta
    if (!$resultado) {
        die("<p style='color:red; text-align:center;'>Error en la consulta: " . mysqli_error($conexion) . "</p>");
    }

    if (mysqli_num_rows($resultado) > 0) {
        while ($producto = mysqli_fetch_assoc($resultado)) {
            // FIX IMAGEN: Convertir BLOB a Base64
            $imgData = base64_encode($producto['imagen']);
            $src = 'data:image/jpeg;base64,' . $imgData;
    ?>
      <div class="tarjeta-admin">
        <img src="<?= $src ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-admin">
        
        <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
        <p style="font-size: 0.9em; color: #666;"><?= htmlspecialchars($producto['descripcion']) ?></p>
        
        <p>Precio Actual: <strong>$<?= number_format($producto['precio'], 2) ?></strong></p>

        <?php if (!empty($producto['precio_promocion'])): ?>
          <p>
            <span style="color: gray; text-decoration: line-through;">$<?= number_format($producto['precio'], 2) ?></span>
            <span style="color: #e53935; font-weight: bold;">$<?= number_format($producto['precio_promocion'], 2) ?></span>
          </p>
        <?php endif; ?>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">

        <form action="actualizar_promocion.php" method="POST" onsubmit="return confirmarCambioProducto();">
          <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
          <label style="font-size: 0.8em;">Oferta:</label>
          <input type="number" step="0.01" name="precio_promocion" placeholder="Precio Promo" value="<?= $producto['precio_promocion'] ?>">
          <button type="submit" style="background: #ffb74d; width: 100%; margin-top: 5px;">Actualizar Promo</button>
        </form>

        <form action="actualizar_producto.php" method="POST" style="margin-top: 15px;" onsubmit="return confirmarCambioProducto();">
          <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

          <div style="margin-bottom: 5px;">
            <label for="precio" style="font-size: 0.8em;">Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
          </div>

          <div style="margin-bottom: 5px;">
            <label for="stock" style="font-size: 0.8em;">Stock:</label>
            <input type="number" name="stock" value="<?= $producto['stock'] ?>" required>
          </div>

          <button type="submit" style="width: 100%;">Guardar Cambios</button>
        </form>

        <p style="margin-top: 10px; font-size: 0.9em;">
            <strong>Estado:</strong> 
            <span style="color: <?= $producto['estado'] === 'activo' ? 'green' : 'red' ?>;">
                <?= ucfirst($producto['estado']) ?>
            </span>
        </p>


        <?php if ($producto['estado'] === 'activo'): ?>
          <form action="inactivar_producto.php" method="POST">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            <button class="btn-inactivar" style="background: #e53935; width: 100%;" onclick="return confirm('¿Estás seguro de inactivar este producto?')">Inactivar</button>
          </form>
        <?php else: ?>
          <form method="POST" action="activar_producto.php">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            <button class="btn-activar" style="background: #43a047; width: 100%;" onclick="return confirm('¿Deseas volver a activar este producto?')">Activar</button>
          </form>
        <?php endif; ?>

      </div>

    <?php 
        } // Fin del while
    } else {
        echo "<p style='text-align:center; width: 100%;'>No hay productos registrados aún.</p>";
    }
    ?>
  </div>
  <br><br>
  <script>
    function confirmarCambioProducto() {
      return confirm("¿Estás seguro de que deseas aplicar estos cambios?");
    }
  </script>

</body>
</html>