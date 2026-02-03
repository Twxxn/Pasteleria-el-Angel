<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Cuenta | Pastelería El Ángel</title>
  <link rel="stylesheet" href="../css/registro.css">
</head>
<body>

  <form class="form-registro" action="../validar_registro.php" method="POST">
    <h2>CREAR CUENTA</h2>

    <?php session_start(); ?>
<?php if (isset($_SESSION['error_registro'])): ?>
  <div style="color: red; text-align: center; margin-bottom: 10px;">
    <?= $_SESSION['error_registro'] ?>
  </div>
  <?php unset($_SESSION['error_registro']); ?>
<?php endif; ?>

    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>

    <button type="submit">REGISTRARSE</button>

    <p style="margin-top: 10px;">
      ¿Ya tienes cuenta?
      <a href="login.php">Inicia sesión</a>
    </p>
    <p style="text-align:center;">
  <a href="../index.php" class="btn-volver">← Volver al inicio</a>
</p>
  </form>

</body>
</html>
