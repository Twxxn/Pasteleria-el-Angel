<?php
include "config.php";

if ($conexion) {
    echo "✅ Conectado correctamente a la base de datos";
} else {
    echo "❌ No conectado";
}
?>
