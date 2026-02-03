<?php

session_start();

include 'includes/conexion.php';


function obtenerProductosPorSeccion($conexion, $nombre_seccion)

{

  // Nota: He cambiado 'producto_Seccion' y 'Secciones' a minúsculas

  // para evitar errores en el servidor Linux.

  $stmt = $conexion->prepare("

        SELECT p.* FROM productos p

        INNER JOIN producto_seccion ps ON p.id_producto = ps.id_producto

        INNER JOIN secciones s ON ps.id_seccion = s.id_seccion

        WHERE s.nombre = ? AND p.estado = 'activo' AND p.stock > 0

    ");

  $stmt->bind_param("s", $nombre_seccion);

  $stmt->execute();

  $resultado = $stmt->get_result();

  return $resultado->fetch_all(MYSQLI_ASSOC);

}

?>



<!DOCTYPE html>

<html lang="es">



<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Pastelería El Ángel</title>

  <link rel="stylesheet" href="css/style.css?v=2.0">

  <script src="js/carousel.js" defer></script>

  <link rel="icon" type="image/png" href="img/logo.jpeg">

</head>



<body>



  <header class="navbar">

    <img src="img/logo.jpeg" alt="Logo El Ángel" class="logo">



    <div class="nav-container">

      <nav>

        <ul class="nav-links">

          <li><a href="#menu">PRODUCTOS</a></li>

          <li><a href="#sucursal">SUCURSAL</a></li>

          <li><a href="#promociones">PROMOCIONES</a></li>

          <li><a href="#contacto">CONTACTO</a></li>

        </ul>

      </nav>

    </div>



    <div class="nicono-navbar">

      <?php if (isset($_SESSION['nombre'])): ?>

        <div class="usuario-nombre">

          <span><?= htmlspecialchars($_SESSION['nombre']) ?></span>

          <a href="vistas/perfil_cliente.php" title="Mi cuenta">

            <img src="img/usuario.png" alt="Perfil" class="icono-navbar">

          </a>

        </div>

      <?php else: ?>

        <a href="vistas/login.php" title="Iniciar sesión">

          <img src="img/usuario.png" alt="Iniciar sesión" class="icono-navbar">

        </a>

      <?php endif; ?>





      <a href="vistas/carrito.php" title="Carrito">

        <img src="img/carrito.png" alt="Carrito" class="icono-navbar">

      </a>



      <a href="vistas/mis_pedidos.php" title="Pedidos">

        <img src="img/pedido.png" alt="Pedidos" class="icono-navbar">

      </a>

    </div>



    <div class="boton-sesion">

      <?php if (isset($_SESSION['nombre'])): ?>

        <a href="includes/logout.php" class="btn-sesion">Cerrar sesión</a>

      <?php else: ?>

        <a href="vistas/login.php" class="btn-sesion">Iniciar sesión</a>

      <?php endif; ?>

    </div>

  </header>



  <section class="banner">

    <div class="carousel">

      <img src="img/banner1.jpg" alt="Banner 1" class="slide">

      <img src="img/banner2.jpg" alt="Banner 2" class="slide">

      <img src="img/banner3.jpg" alt="Banner 3" class="slide">

      <button id="prev">&#10094;</button>

      <button id="next">&#10095;</button>

    </div>

  </section>



  <div style="text-align: center;">

    <a href="vistas/personalizar_pedido.php" class="btn-pedido">PERSONALIZA TU PASTEL</a>

  </div>



  <section class="promociones" id="promociones">

    <h2 class="titulo-promociones">PROMOCIONES ESPECIALES</h2>

    <div class="promo-container">

      <?php

      $productos_menu = obtenerProductosPorSeccion($conexion, 'promociones');

      if($productos_menu) { // Verificamos si hay productos

        foreach ($productos_menu as $row) {

          // CONVERSIÓN DE IMAGEN BLOB A BASE64

          $imgData = base64_encode($row['imagen']);

          $src = 'data:image/jpeg;base64,'.$imgData;

      ?>

        <div class="promo-card">

          <img src="<?= $src ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="promo-img">

          

          <div class="promo-descuento">PROMO</div>

          <p class="promo-nombre"><?= htmlspecialchars($row['nombre']) ?></p>

          <p class="promo-detalle"><?= htmlspecialchars($row['descripcion']) ?></p>



          <?php if (!empty($row['precio_promocion'])): ?>

            <p>

              <span style="color: gray; text-decoration: line-through;">

                $<?= number_format($row['precio'], 2) ?>

              </span>

              <span style="color: #e53935; font-weight: bold;">

                $<?= number_format($row['precio_promocion'], 2) ?>

              </span>

            </p>

          <?php else: ?>

            <p class="precio">$<?= number_format($row['precio'], 2) ?></p>

          <?php endif; ?>



          <form action="vistas/agregar_al_carrito.php" method="POST">

            <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">

            <button type="submit" class="btn-comprar">Agregar al carrito</button>

          </form>

        </div>

      <?php 

        } 

      } else {

         echo "<p>No hay promociones activas.</p>";

      }

      ?>

    </div>

  </section>



  <section class="menu" id="menu">

    <h2 class="titulo-menu">MENÚ</h2>

    <div class="productos-container">

      <?php

      $productos_menu = obtenerProductosPorSeccion($conexion, 'menú');

      if($productos_menu) {

        foreach ($productos_menu as $row) {

          // CONVERSIÓN DE IMAGEN BLOB A BASE64

          $imgData = base64_encode($row['imagen']);

          $src = 'data:image/jpeg;base64,'.$imgData;

      ?>

        <div class="producto-card">

          <img src="<?= $src ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="producto-img">

          

          <?php if (!empty($row['precio_promocion'])): ?>

            <div class="precio">

              <span style="color: gray; text-decoration: line-through;">

                $<?= number_format($row['precio'], 2) ?>

              </span>

              <span style="color:rgb(255, 255, 255); font-weight: bold;">

                $<?= number_format($row['precio_promocion'], 2) ?>

              </span>

            </div>

          <?php else: ?>

            <div class="precio">$<?= number_format($row['precio'], 2) ?></div>

          <?php endif; ?>



          <p><strong><?= htmlspecialchars($row['nombre']) ?></strong></p>

          <p class="descripcion"><?= htmlspecialchars($row['descripcion']) ?></p>



          <form action="vistas/agregar_al_carrito.php" method="POST">

            <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">

            <button type="submit" class="btn-comprar">Agregar al carrito</button>

          </form>

        </div>

      <?php 

        } 

      }

      ?>

    </div>

  </section>



  <section class="mas-vendido">

    <h2 class="titulo-vendido">LO MÁS VENDIDO DEL MES</h2>

    <div class="vendido-container">

      <?php

      $productos_menu = obtenerProductosPorSeccion($conexion, 'más vendidos');

      if($productos_menu) {

        foreach ($productos_menu as $row) {

          // CONVERSIÓN DE IMAGEN BLOB A BASE64

          $imgData = base64_encode($row['imagen']);

          $src = 'data:image/jpeg;base64,'.$imgData;

      ?>

        <div class="vendido-card">

          <div class="badge">⭐ Más vendido</div>

          <img src="<?= $src ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="vendido-img">

          

          <h3 class="vendido-nombre"><?= htmlspecialchars($row['nombre']) ?></h3>

          <p class="vendido-detalle"><?= htmlspecialchars($row['descripcion']) ?></p>

          <?php if (!empty($row['precio_promocion'])): ?>

            <div class="precio">

              <span style="color: gray; text-decoration: line-through;">

                $<?= number_format($row['precio'], 2) ?>

              </span>

              <span style="color:rgb(255, 255, 255); font-weight: bold;">

                $<?= number_format($row['precio_promocion'], 2) ?>

              </span>

            </div>

          <?php else: ?>

            <div class="precio">$<?= number_format($row['precio'], 2) ?></div>

          <?php endif; ?>

          <form action="vistas/agregar_al_carrito.php" method="POST">

            <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">

            <button type="submit" class="btn-comprar">Agregar al carrito</button>

          </form>



        </div>

      <?php 

        } 

      }

      ?>

    </div>

  </section>



  <?php include('includes/footer.php'); ?>

</body>

</html> 