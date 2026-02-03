<?php include('../includes/conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="../css/login.css?v=2.0">
</head>
<body>

  <form action="../validar_login.php" method="POST" class="form-login">
    <h2>BIENVENIDO</h2>
    <?php session_start(); ?>
    <?php if (isset($_SESSION['registro_exitoso'])): ?>
      <div style="color: green; text-align: center; margin-bottom: 10px;">
        <?= $_SESSION['registro_exitoso'] ?>
      </div>
      <?php unset($_SESSION['registro_exitoso']); ?>
    <?php endif; ?>

    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>

    <button type="submit" name="login">INICIAR SESIÓN</button>

    <p><a href="registro.php">Crear una cuenta</a> | <a href="recuperar.php">¿Olvidaste tu contraseña?</a></p>
    
    <p style="text-align:center;">
      <a href="../index.php" class="btn-volver">← Volver al inicio</a>
    </p>

  </form>
</body>
</html>