<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $codigo_ingresado = $_POST['codigo'];
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $codigo_real = $_POST['codigo_real'];

    if ($codigo_ingresado == $codigo_real) {
        // OJO: Esto guarda la contraseña sin hash (sólo para ejemplo)
        $stmt = $conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE correo = ?");
        $stmt->bind_param("ss", $nueva_contrasena, $correo);
        if ($stmt->execute()) {
            echo "<script>alert('Contraseña actualizada.'); window.location.href='login.php';</script>";
        } else {
            echo "Error al actualizar.";
        }
    } else {
        echo "Código incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificar código</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <form action="verificar_codigo.php" method="POST" class="form-login">
    <h2>Verificar código</h2>
    <input type="email" name="correo" required placeholder="Tu correo">
    <input type="text" name="codigo" required placeholder="Código recibido">
    <input type="password" name="nueva_contrasena" required placeholder="Nueva contraseña">
    <input type="hidden" name="codigo_real" id="codigo_real">
    <button type="submit">Actualizar contraseña</button>
  </form>

  <script>
    // Recuperar el código desde localStorage
    document.getElementById("codigo_real").value = localStorage.getItem("codigo_recuperacion");
  </script>
</body>
</html>
