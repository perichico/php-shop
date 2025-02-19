<?php
session_start();
include 'config.php'; // Incluye el archivo de conexión a la base de datos

// Consulta para obtener productos
$sql = "SELECT id, nombre, precio, imagen FROM productos"; // Cambia 'productos' según tu tabla
$result = $conn->query($sql);

$productos = [];

if ($result && $result->num_rows > 0) {
    // Guardar los productos en un array
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
} else {
    echo "No hay productos disponibles.";
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Informática</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            height: 100%; /* Hacer que todas las cartas tengan la misma altura */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Espacio entre el contenido y el botón */
        }
        .card img {
            height: 250px; /* Aumentar la altura de las imágenes */
            object-fit: cover; /* Mantiene la proporción de la imagen */
        }
        .espacio { 
            margin-top: 30px; /* Ajusta este valor para modificar el espacio entre secciones */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">MiTienda</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Catálogo</a></li>
            <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="mis_pedidos.php">Mis pedidos</a></li>
                <li class="nav-item"><a class="nav-link" href="ajustes.php">Ajustes</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<br><br>

<div class="container">
    <div class="row">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 espacio">
                    <div class="card mb-4">
                        <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text"><?php echo number_format($producto['precio'], 2); ?> €</p>
                            <form action="agregar_producto_carrito.php" method="POST">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad:</label>
                                    <input type="number" name="cantidad" id="cantidad" value="1" min="1" class="form-control" required>
                                </div>
                                <button type="submit" name="agregar" class="btn btn-primary">Añadir al carrito</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No hay productos disponibles.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
