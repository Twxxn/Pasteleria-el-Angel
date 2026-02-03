<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include("includes/conexion.php"); // Ajusta según tu estructura

// Recoger datos del usuario
$id_usuario = $_SESSION['id_usuario'];
$fecha_pedido = date("Y-m-d H:i:s");

// Recoger datos del formulario
$tamano       = $_POST['tamano'];
$sabor        = $_POST['sabor'];
$relleno      = $_POST['relleno'];
$cobertura    = $_POST['cobertura'];
$topping      = $_POST['topping'];
$forma        = $_POST['forma'];
$tema         = $_POST['tema'];
$dedicatoria  = $_POST['dedicatoria'];
$fecha_entrega = $_POST['fecha_entrega'];
$hora_recogida = $_POST['hora_entrega'];
$metodo_pago   = $_POST['metodo_pago'];
$observaciones = !empty($_POST['observaciones']) ? $_POST['observaciones'] : "Pedido personalizado";

// Insertar en PEDIDOS
$sql_pedido = "INSERT INTO pedidos (id_usuario, fecha_pedido, estado, metodo_pago, fecha_recogida, hora_recogida, observaciones)
               VALUES (?, ?, 'pendiente', ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_pedido);
$stmt->bind_param("isssss", $id_usuario, $fecha_pedido, $metodo_pago, $fecha_entrega, $hora_recogida, $observaciones);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $id_pedido = $conexion->insert_id;

    // Insertar en DETALLE_PERSONALIZADO
    $precio = $_POST['precio'];

    $sql_detalle = "INSERT INTO detalle_personalizado 
    (id_pedido, tamaño, sabor, relleno, cobertura, topping, forma, tema, dedicatoria, fecha_entrega, precio)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conexion->prepare($sql_detalle);
    $stmt2->bind_param("isssssssssd", $id_pedido, $tamano, $sabor, $relleno, $cobertura, $topping, $forma, $tema, $dedicatoria, $fecha_entrega, $precio);

    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        echo "<script>
            alert('¡Tu pedido personalizado se ha registrado correctamente!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Hubo un error al guardar los detalles personalizados.');
            window.location.href = 'personalizar_pedido.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Hubo un error al registrar el pedido.');
        window.location.href = 'personalizar_pedido.php';
    </script>";
}
