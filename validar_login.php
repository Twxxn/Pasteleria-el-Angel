<?php
// --- MODO DETECTIVE ACTIVADO ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------

session_start();

// INTENTO 1: Buscar conexión en la carpeta includes
if (file_exists('includes/conexion.php')) {
    include('includes/conexion.php');
} 
// INTENTO 2: Buscar conexión subiendo un nivel (por si el archivo está en una carpeta)
elseif (file_exists('../includes/conexion.php')) {
    include('../includes/conexion.php');
} else {
    die("ERROR CRÍTICO: No se encuentra el archivo conexion.php. Revisa la ruta.");
}

$correo = $_POST['correo'];
$contrasena_ingresada = $_POST['contrasena'];

// OJO AQUÍ: Puse la tabla 'usuarios' en minúsculas (es lo estándar en InfinityFree)
// Si tu tabla empieza con mayúscula en la base de datos, cámbialo aquí.
$sql = "SELECT * FROM usuarios WHERE correo = ?";

if ($stmt = mysqli_prepare($conexion, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($usuario = mysqli_fetch_assoc($res)) {
        
        // Verificamos la contraseña
        // ASEGÚRATE: ¿Tu columna en la BD se llama 'contraseña', 'contrasena' o 'pass'?
        // Aquí estoy asumiendo que se llama 'contraseña' (con ñ) como en tu código anterior.
        // Si se llama diferente, cambia $usuario['contraseña'] por el nombre real.
        if (password_verify($contrasena_ingresada, $usuario['contraseña'])) {
            
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['tipo'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] === 'administrador') {
                header("Location: admin/index.php");
            } else {
                // Intento de incluir sincronizar_carrito (con manejo de errores)
                if(file_exists('includes/sincronizar_carrito.php')) {
                    include('includes/sincronizar_carrito.php');
                } elseif(file_exists('../includes/sincronizar_carrito.php')) {
                    include('../includes/sincronizar_carrito.php');
                }
                
                header("Location: index.php");
            }
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Correo no registrado.";
    }
    mysqli_stmt_close($stmt);
} else {
    // Si falla la preparación de la consulta, muestra por qué
    echo "Error en la consulta SQL: " . mysqli_error($conexion);
}
?>