emailjs.init("H4KY2BbUPF9nwetTH"); // Tu Public Key

function enviarCodigo(e) {
  e.preventDefault();
  const correo = document.getElementById("correo").value;
  const codigo = Math.floor(10000 + Math.random() * 90000); // Código aleatorio de 5 dígitos

  // Guardar temporalmente en localStorage
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
      window.location.href = "verificar_codigo.php";
    }, (error) => {
      alert("Error al enviar: " + error.text);
    });
}
