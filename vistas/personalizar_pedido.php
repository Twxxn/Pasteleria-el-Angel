<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personaliza tu pastel</title>
    <link rel="stylesheet" href="../css/personalizado.css">
    <script>
        function toggleTransferenciaInfo() {
            const metodo = document.getElementById("metodo_pago").value;
            document.getElementById("info_transferencia").style.display = metodo === "transferencia" ? "block" : "none";
        }
    </script>
</head>

<body>
    <div class="main-container">
        <a href="../index.php" class="btn-volver-inicio">← Volver al inicio</a>
        
        <h2>Personaliza tu pastel</h2>
        
        <form action="../procesar_personalizado.php" method="POST" id="formPersonalizado">
            
            <div class="form-group">
                <label>Tamaño:</label>
                <select name="tamano" required onchange="actualizarPrecio()">
                    <option value="Chico">Chico (8 porciones)</option>
                    <option value="Mediano">Mediano (12 porciones)</option>
                    <option value="Grande">Grande (16+ porciones)</option>
                    <option value="Extra Grande">Extra Grande (25+ porciones)</option>
                </select>
                <p class="precio-display"><strong>Total estimado:</strong> <span id="precio_pastel">$300</span></p>
            </div>

            <div class="form-group">
                <label>Sabor:</label>
                <select name="sabor" required>
                    <option value="Chocolate">Chocolate</option>
                    <option value="Vainilla">Vainilla</option>
                    <option value="Tres Leches">Tres Leches</option>
                    <option value="Red Velvet">Red Velvet</option>
                    <option value="Zanahoria">Zanahoria</option>
                    <option value="Choco-Vainilla">Choco-Vainilla</option>
                    <option value="Moka">Moka</option>
                    <option value="Limón">Limón</option>
                    <option value="Marble">Marble</option>
                    <option value="Coco">Coco</option>
                    <option value="Queso Crema">Queso Crema</option>
                </select>
            </div>

            <div class="form-group">
                <label>Relleno:</label>
                <select name="relleno" required>
                    <option value="Fresa">Fresa</option>
                    <option value="Cajeta">Cajeta</option>
                    <option value="Nutella">Nutella</option>
                    <option value="Durazno">Durazno</option>
                    <option value="Zarzamora">Zarzamora</option>
                    <option value="Queso Crema">Queso Crema</option>
                    <option value="Piña">Piña</option>
                    <option value="Frutas Mixtas">Frutas Mixtas</option>
                    <option value="Merengue">Merengue</option>
                    <option value="Chantilly">Chantilly</option>
                    <option value="Ganache de Chocolate">Ganache de Chocolate</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tipo de cobertura:</label>
                <select name="cobertura" required>
                    <option value="Fondant">Fondant</option>
                    <option value="Chantilly">Chantilly</option>
                    <option value="Ganache">Ganache</option>
                    <option value="Buttercream">Buttercream</option>
                    <option value="Glaseado Simple">Glaseado Simple</option>
                </select>
            </div>

            <div class="form-group">
                <label>Decoración/Toppings:</label>
                <select name="topping" required>
                    <option value="Frutas naturales">Frutas naturales</option>
                    <option value="Chispas de chocolate">Chispas de chocolate</option>
                    <option value="Confites">Confites</option>
                    <option value="Nueces">Nueces</option>
                    <option value="Flores comestibles">Flores comestibles</option>
                    <option value="Figuras temáticas">Figuras temáticas</option>
                </select>
            </div>

            <div class="form-group">
                <label>Forma del pastel:</label>
                <select name="forma" required>
                    <option value="Redondo">Redondo</option>
                    <option value="Cuadrado">Cuadrado</option>
                    <option value="Corazón">Corazón</option>
                    <option value="Rectangular">Rectangular</option>
                    <option value="Forma personalizada">Forma personalizada</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tema o color:</label>
                <input type="text" name="tema" placeholder="Ej. Unicornio, Rosa pastel" required>
            </div>

            <div class="form-group">
                <label>Dedicatoria:</label>
                <input type="text" name="dedicatoria" maxlength="100" placeholder="Feliz cumpleaños, Ana" required>
            </div>

            <div class="row-fechas">
                <div class="form-group half">
                    <label>Fecha de entrega:</label>
                    <input type="date" name="fecha_entrega" required>
                </div>

                <div class="form-group half">
                    <label>Hora de entrega:</label>
                    <input type="time" name="hora_entrega" min="09:00" max="18:00" required>
                </div>
            </div>

            <div class="form-group">
                <label>Método de pago:</label>
                <select name="metodo_pago" id="metodo_pago" onchange="toggleTransferenciaInfo()" required>
                    <option value="efectivo">Efectivo al recoger</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>

            <div id="info_transferencia" class="info-box" style="display:none;">
                <strong>Datos para Transferencia:</strong><br>
                Banco: BBVA<br>
                Cuenta: 4152 5678 9012 3456<br>
                A nombre de: Pastelería El Ángel<br>
                <em>Recuerda mostrar tu comprobante al recoger.</em>
            </div>

            <div class="form-group">
                <label>Observaciones:</label>
                <textarea name="observaciones" rows="3" placeholder="Detalles adicionales..."></textarea>
            </div>

            <button type="button" class="btn-confirmar" onclick="abrirModal()">Confirmar pedido personalizado</button>
            
            <input type="hidden" name="precio" id="precio_input" value="300">
        </form>

        <p class="mensaje-contacto">
            ¿Requieres un diseño más específico o temático?<br>
            Comunícate con nosotros al <strong>477 589 4366</strong> para atención personalizada.
        </p>
    </div>

    <div id="modalConfirmacion" class="modal">
        <div class="modal-content">
            <h3>¿Confirmar pedido personalizado?</h3>
            <p>Una vez confirmado, se registrará en el sistema y comenzaremos a prepararlo.</p>
            <div class="modal-buttons">
                <button onclick="document.getElementById('formPersonalizado').submit()" class="btn-si">Sí, confirmar</button>
                <button onclick="cerrarModal()" class="btn-no">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById("modalConfirmacion").style.display = "flex"; // Flex para centrar
        }

        function cerrarModal() {
            document.getElementById("modalConfirmacion").style.display = "none";
        }

        function actualizarPrecio() {
            const tamaño = document.querySelector('select[name="tamano"]').value;
            let precio = 300;

            switch (tamaño) {
                case 'Mediano': precio = 450; break;
                case 'Grande': precio = 550; break;
                case 'Extra Grande': precio = 650; break;
                default: precio = 300;
            }

            document.getElementById("precio_pastel").textContent = "$" + precio;
            document.getElementById("precio_input").value = precio;
        }

        // Cerrar modal si tocan fuera
        window.onclick = function(event) {
            const modal = document.getElementById("modalConfirmacion");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>