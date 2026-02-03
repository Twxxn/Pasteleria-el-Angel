<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

$id_admin = $_SESSION['id_usuario'];

// Obtener datos del admin
$stmt = $conexion->prepare("SELECT nombre, correo FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Total de pedidos gestionados
$pedidos = $conexion->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'] ?? 0;

// Total de ventas confirmadas
$ventas = $conexion->query("SELECT COUNT(*) AS total FROM ventas")->fetch_assoc()['total'] ?? 0;

// Total vendido
$monto_total = $conexion->query("SELECT COALESCE(SUM(monto_total), 0) AS total FROM ventas")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Administrador</title>
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Mi Perfil (Administrador)</h1>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($admin['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($admin['correo']) ?></p>
        <p><strong>Pedidos gestionados:</strong> <?= $pedidos ?></p>
        <p><strong>Ventas registradas:</strong> <?= $ventas ?></p>
        <p><strong>Total de ingresos:</strong> $<?= number_format($monto_total, 2) ?></p>
        <a href="index.php" class="btn-volver">← Volver al inicio</a>
    </div>
</body>
</html>
