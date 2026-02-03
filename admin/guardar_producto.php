<?php 
require_once '../includes/conexion.php';

// Recolectar datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$categoria = $_POST['categoria'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];

// Verifica que la imagen exista y toma su contenido binario
if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
    $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
} else {
    $imagen = null; // O puedes poner un BLOB vacío
}

// Nuevo campo: precio promocional
$precio_promocion = !empty($_POST['precio_promocion']) ? $_POST['precio_promocion'] : null;

// Preparar la consulta con un placeholder NULL para la imagen
$sql = "INSERT INTO Productos (nombre, descripcion, categoria, imagen, precio, stock, tipo, estado, precio_promocion)
        VALUES (?, ?, ?, ?, ?, ?, 'normal', 'activo', ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

// Bind param: 
// 's' - string, 'd' - double, 'i' - integer, 'b' - blob
// Ponemos NULL en la posición de la imagen, y la enviamos después con send_long_data
$null = null;
$stmt->bind_param("sssbdid", $nombre, $descripcion, $categoria, $null, $precio, $stock, $precio_promocion);

// Enviar la imagen como dato largo (blob) si existe
if ($imagen !== null) {
    $stmt->send_long_data(3, $imagen); // El índice 3 es la posición de la imagen (empezando desde 0)
}

if ($stmt->execute()) {
    $producto_id = $conexion->insert_id;

    // Insertar en Producto_Seccion
    if (isset($_POST['secciones']) && is_array($_POST['secciones'])) {
        $stmt_seccion = $conexion->prepare("INSERT INTO Producto_Seccion (id_producto, id_seccion) VALUES (?, ?)");
        foreach ($_POST['secciones'] as $id_seccion) {
            $stmt_seccion->bind_param("ii", $producto_id, $id_seccion);
            $stmt_seccion->execute();
        }
    }

    // Mostrar mensaje bonito con botones estilizados
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Producto guardado</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                padding: 40px;
                text-align: center;
            }
            .mensaje {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
                padding: 20px;
                border-radius: 10px;
                display: inline-block;
                font-size: 18px;
                margin-bottom: 20px;
            }
            .botones a {
                display: inline-block;
                margin: 10px;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            }
            .botones a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="mensaje">✅ Producto guardado correctamente.</div>
        <div class="botones">
            <a href="agregar_producto.php">Agregar otro</a>
            <a href="index.php">Volver al inicio</a>
        </div>
    </body>
    </html>';
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}

