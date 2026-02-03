<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $correo = $_POST['correo'];
  $nueva = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);

  $stmt = $conexion->prepare("UPDATE usuarios SET contrasena = ? WHERE correo = ?");
  $stmt->bind_param("ss", $nueva, $correo);
  $stmt->execute();

  $_SESSION['registro_exitoso'] = "Contraseña actualizada correctamente. Inicia sesión.";
  header("Location: login.php");
  exit();
}

$correo_recuperado = "<script>document.write(localStorage.getItem('correo_recuperacion'));</script>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva Contraseña</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <form method="POST" class="form-login">
    <h2>Restablecer contraseña</h2>
    <input type="hidden" name="correo" id="correo_hidden">
    <input type="password" name="nueva_contrasena" required placeholder="Nueva contraseña">
    <button type="submit">Guardar</button>
  </form>

  <script>
    document.getElementById("correo_hidden").value = localStorage.getItem("correo_recuperacion");
  </script>
</body>
</html>
