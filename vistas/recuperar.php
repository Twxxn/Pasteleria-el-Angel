<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="../css/login.css">
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <script src="../js/enviar_codigo.js" defer></script> <!-- JS externo -->
  <script>
    emailjs.init("H4KY2BbUPF9nwetTH"); // ← Tu User ID

    function enviarCodigo(e) {
      e.preventDefault();
      const correo = document.getElementById("correo").value;
      const codigo = Math.floor(10000 + Math.random() * 90000);

      // Guardar el código y el correo para verificar después
      localStorage.setItem("codigo_recuperacion", codigo);
      localStorage.setItem("correo_recuperacion", correo);

      const templateParams = {
        to_email: correo,
        message: "Tu código de recuperación es: " + codigo,
        codigo: codigo
      };

      emailjs.send("service_rzut079", "template_5umsl3p", templateParams)
        .then(() => {
          alert("Código enviado. Revisa tu correo.");
          window.location.href = "verificar_codigo.php"; // ← siguiente paso
        }, (error) => {
          alert("Error al enviar: " + error.text);
        });
    }
  </script>
</head>
<body>
  <form onsubmit="enviarCodigo(event)" class="form-login">
    <h2>Recuperar contraseña</h2>
    <input type="email" id="correo" required placeholder="Tu correo">
    <button type="submit">Enviar código</button>
    <p><a href="login.php">← Volver al login</a></p>
  </form>
</body>
</html>
