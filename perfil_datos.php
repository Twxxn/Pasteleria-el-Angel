<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    echo "<p>No has iniciado sesión.</p>";
    exit;
}

$id = $_SESSION['id_usuario'];
$tipo = $_SESSION['tipo'];

if ($tipo === 'cliente') {
    $sql = "SELECT nombre, correo FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    // Obtener pedidos
    $pedidos = $conexion->query("SELECT COUNT(*) AS total FROM pedidos WHERE id_usuario = $id")->fetch_assoc()['total'];
    $ventas = $conexion->query("SELECT SUM(monto_total) AS total FROM ventas v JOIN pedidos p ON v.id_pedido = p.id_pedido WHERE p.id_usuario = $id")->fetch_assoc()['total'] ?? 0;

    echo "<p><strong>Nombre:</strong> {$res['nombre']}</p>";
    echo "<p><strong>Correo:</strong> {$res['correo']}</p>";
    echo "<p><strong>Pedidos realizados:</strong> $pedidos</p>";
    echo "<p><strong>Total gastado:</strong> $" . number_format($ventas, 2) . "</p>";

} elseif ($tipo === 'administrador') {
    $sql = "SELECT nombre, correo FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    echo "<p><strong>Administrador:</strong> {$res['nombre']}</p>";
    echo "<p><strong>Correo:</strong> {$res['correo']}</p>";
    echo "<p><strong>Pedidos gestionados:</strong> ";
    $gestionados = $conexion->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'];
    echo "$gestionados</p>";
}
?>
