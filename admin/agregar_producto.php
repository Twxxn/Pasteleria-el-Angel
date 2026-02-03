<?php
session_start();
// 1. SEGURIDAD: Verificar si es admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

include '../includes/conexion.php';

// 2. LÓGICA PARA GUARDAR (Todo en el mismo archivo para evitar errores)
if (isset($_POST['guardar_producto'])) {
    // Recibir datos
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $tipo = $_POST['tipo']; // Nuevo campo tipo
    $precio_promocion = !empty($_POST['precio_promocion']) ? $_POST['precio_promocion'] : NULL;

    // Procesar Imagen a BLOB
    $imagenBinaria = null;
    if (isset($_FILES['imagen']['tmp_name']) && !empty($_FILES['imagen']['tmp_name'])) {
        $imagenBinaria = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
    }

    // Insertar en tabla 'productos' (MINÚSCULAS)
    $sql = "INSERT INTO productos (nombre, descripcion, categoria, precio, stock, tipo, precio_promocion, imagen, estado) 
            VALUES ('$nombre', '$descripcion', '$categoria', '$precio', '$stock', '$tipo', " . ($precio_promocion ? "'$precio_promocion'" : "NULL") . ", '$imagenBinaria', 'activo')";

    if ($conexion->query($sql)) {
        $id_nuevo = $conexion->insert_id;

        // Guardar Secciones (Checkboxes)
        if (isset($_POST['secciones'])) {
            foreach ($_POST['secciones'] as $id_seccion) {
                // Tabla 'producto_seccion' (MINÚSCULAS)
                $conexion->query("INSERT INTO producto_seccion (id_producto, id_seccion) VALUES ('$id_nuevo', '$id_seccion')");
            }
        }
        echo "<script>alert('¡Producto agregado correctamente!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al guardar: " . $conexion->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Producto</title>
  <link rel="stylesheet" href="../css/admin_productos.css?v=2.0">
  <link rel="stylesheet" href="../css/style.css?v=2.0">
  
  <style>
      /* Estilos rápidos por si falla el CSS externo */
      body { font-family: sans-serif; background-color: #f9f9f9; padding: 20px; }
      .form-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
      input, textarea, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
      button { background: #f08080; color: white; padding: 10px 20px; border: none; border-radius: 5px; width: 100%; font-size: 16px; cursor: pointer; }
      button:hover { background: #d95f6a; }
      .checkbox-group label { display: block; margin-bottom: 5px; cursor: pointer; }
      .checkbox-group input { width: auto; margin-right: 10px; }
  </style>
</head>

<body>
  <a href="index.php" style="text-decoration: none; color: #333; font-weight: bold; display: block; margin-bottom: 15px;">← Volver al inicio</a>
  
  <div class="form-container">
    <h2 style="text-align:center; color: #d95f6a;">Agregar Nuevo Producto</h2>

    <form action="" method="POST" enctype="multipart/form-data">
      
      <label>Nombre del producto:</label>
      <input type="text" name="nombre" placeholder="Ej. Pastel de Chocolate" required>

      <label>Descripción:</label>
      <textarea name="descripcion" placeholder="Ingredientes y detalles..." rows="3" required></textarea>

      <label>Categoría:</label>
      <input type="text" name="categoria" placeholder="Ej. Pasteles, Gelatinas..." required>

      <div style="display: flex; gap: 10px;">
          <div style="flex: 1;">
            <label>Precio ($):</label>
            <input type="number" step="0.01" name="precio" placeholder="0.00" required>
          </div>
          <div style="flex: 1;">
            <label>Precio Promo (Opcional):</label>
            <input type="number" step="0.01" name="precio_promocion" placeholder="0.00">
          </div>
      </div>

      <div style="display: flex; gap: 10px;">
          <div style="flex: 1;">
            <label>Stock:</label>
            <input type="number" name="stock" placeholder="Cantidad" required>
          </div>
          <div style="flex: 1;">
             <label>Tipo de producto:</label>
            <select name="tipo">
                <option value="normal">Normal</option>
                <option value="especial">Especial</option>
                <option value="temporada">De Temporada</option>
            </select>
          </div>
      </div>

      <p style="font-weight: bold; margin-bottom: 10px;">Secciones donde aparecerá:</p>
      <div class="checkbox-group">
      <?php
      // CONSULTA CORREGIDA A MINÚSCULAS
      $resultado = $conexion->query("SELECT * FROM secciones");
      
      if($resultado) {
          while ($seccion = $resultado->fetch_assoc()) {
            echo '<label><input type="checkbox" name="secciones[]" value="' . $seccion['id_seccion'] . '"> ' . ucfirst($seccion['nombre']) . '</label>';
          }
      } else {
          echo "<p style='color:red'>No se encontraron secciones. Verifica la tabla 'secciones'.</p>";
      }
      ?>
      </div>

      <label style="font-weight: bold; display:block; margin-top: 15px;">Imagen del producto:</label>
      <input type="file" name="imagen" accept="image/*" required onchange="mostrarVistaPrevia(event)">
      
      <div style="text-align: center;">
        <img id="preview-img" src="#" alt="Vista previa" style="display:none; max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin: 10px auto; border: 2px solid #ddd;">
      </div>

      <button type="submit" name="guardar_producto">GUARDAR PRODUCTO</button>

    </form>
  </div>

  <script>
    function mostrarVistaPrevia(event) {
      const reader = new FileReader();
      reader.onload = function() {
        const output = document.getElementById('preview-img');
        output.src = reader.result;
        output.style.display = 'block';
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>

</body>
</html>