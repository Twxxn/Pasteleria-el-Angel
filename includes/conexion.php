<?php
// DATOS DE CONEXIÓN DE INFINITYFREE
// ¡No cambies esto por "localhost" o dejará de funcionar!
$host = "sql309.infinityfree.com"; 
$usuario = "if0_40888631";
$contrasena = "zyCIc1bvmK";
$base_datos = "if0_40888631_pasteleria_el_angel";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    // Esto detiene todo si falla y te dice por qué
    die("Fallo la conexión: " . $conexion->connect_error);
}

// Configurar caracteres (para que las ñ y acentos se vean bien)
$conexion->set_charset("utf8");

// Mensaje de prueba (Bórralo o coméntalo cuando ya funcione)
#echo "¡CONEXIÓN EXITOSA! La base de datos está lista.";
?>