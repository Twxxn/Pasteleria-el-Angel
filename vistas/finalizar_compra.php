<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    echo "<p style='text-align:center;'>Tu carrito está vacío.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    
    <link rel="stylesheet" href="../css/finalizar_compra.css">
    
    <script>
    function toggleTransferenciaInfo() {
        const metodo = document.getElementById("metodo_pago").value;
        document.getElementById("info_transferencia").style.display = metodo === "transferencia" ? "block" : "none";
    }

    function validarFormulario(e) {
        const fecha = document.getElementById("fecha_recogida").value;
        const hora = document.getElementById("hora_recogida").value;
        const fechaActual = new Date().toISOString().split("T")[0];

        if (!fecha) {
            alert("Por favor selecciona una fecha de recogida.");
            e.preventDefault();
            return false;
        }

        if (fecha < fechaActual) {
            alert("No puedes seleccionar una fecha pasada.");
            e.preventDefault();
            return false;
        }

        const horaInt = parseInt(hora.split(":")[0]);
        if (horaInt < 9 || horaInt >= 18) {
            alert("La hora de recogida debe ser entre las 9:00 y 18:00.");
            e.preventDefault();
            return false;
        }

        return confirm("¿Estás seguro de confirmar tu pedido?");
    }
    </script>
</head>
<body>
    <h2 style="text-align:center;">Resumen de tu pedido</h2>

    <table style="margin:auto;">
        <thead> <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; foreach ($carrito as $item): ?>
            <tr>
                <td data-label="Producto"><?= htmlspecialchars($item['nombre']) ?></td>
                <td data-label="Precio">$<?= number_format($item['precio'], 2) ?></td>
                <td data-label="Cantidad"><?= $item['cantidad'] ?></td>
                <td data-label="Total">$<?= number_format($item['total'], 2) ?></td>
            </tr>
            <?php $total += $item['total']; endforeach; ?>
            
            <tr>
                <td colspan="3" style="text-align:right; border:none;"><strong>Total a Pagar:</strong></td>
                <td data-label="Total Final"><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">Completa los siguientes datos</h3>
    
    <form action="procesar_pedido.php" method="POST" onsubmit="return validarFormulario(event)" style="max-width: 450px; margin:auto;">
        <label for="metodo_pago">Método de pago:</label>
        <select name="metodo_pago" id="metodo_pago" onchange="toggleTransferenciaInfo()" required>
            <option value="efectivo">Efectivo al recoger</option>
            <option value="transferencia">Transferencia</option>
        </select><br><br>

        <div id="info_transferencia" style="display:none; background:#f1f1f1; padding:10px; margin-bottom:10px;">
            <strong>Transferencia:</strong><br>
            Banco: BBVA<br>
            Cuenta: 4152 5648 9012 3456<br>
            A nombre de: Pastelería El Ángel<br>
            <em>Recuerda mostrar tu comprobante al recoger.</em>
        </div>

        <label for="fecha_recogida">Fecha de recolección:</label>
        <input type="date" name="fecha_recogida" id="fecha_recogida" required><br><br>

        <label for="hora_recogida">Hora de recolección:</label>
        <input type="time" name="hora_recogida" id="hora_recogida" min="09:00" max="18:00" required><br><br>

        <label for="observaciones">Observaciones:</label><br>
        <textarea name="observaciones" rows="3" cols="40" placeholder="Opcional..."></textarea><br><br>

        <button type="submit">Confirmar pedido</button>
    </form>

    <p style="text-align:center; margin-top:20px;">
        <a href="carrito.php">← Volver al carrito</a>
    </p>
</body>
</html>