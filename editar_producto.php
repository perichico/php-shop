<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Verificar si se envió un ID de producto
if (!isset($_GET['id'])) {
    header("Location: ver_productos.php");
    exit;
}

// Obtener el producto por ID
$product_id = $_GET['id'];
$sql = "SELECT nombre, precio, imagen FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nombre, $precio, $imagen);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Producto</h1>
    <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product_id; ?>">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        </div>
        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($precio); ?>" required>
        </div>
        <div class="form-group">
            <label for="imagen">Imagen (URL)</label>
            <input type="text" class="form-control" id="imagen" name="imagen" value="<?php echo htmlspecialchars($imagen); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
    </form>
    <a href="ver_productos.php" class="btn btn-secondary mt-2">Cancelar</a>
</div>
</body>
</html>
