<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Procesar el formulario para añadir un producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen']; // Asegúrate de manejar la carga de imágenes correctamente

    $sql = "INSERT INTO productos (nombre, precio, imagen) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sds", $nombre, $precio, $imagen);
        if ($stmt->execute()) {
            $success_message = "Producto añadido con éxito.";
        } else {
            $error_message = "Error al añadir el producto.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Añadir Producto</h1>

    <!-- Mensaje de éxito o error -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="agregar_producto.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="imagen">URL de la Imagen</label>
            <input type="text" class="form-control" id="imagen" name="imagen" required>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Producto</button>
    </form>
    <br><br>
    <a href="admin.php" class="btn btn-secondary">Volver a Admin</a>

</div>

</body>
</html>
