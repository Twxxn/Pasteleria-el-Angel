<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $sql = "DELETE FROM insumos WHERE id_insumo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: ver_insumos.php");
