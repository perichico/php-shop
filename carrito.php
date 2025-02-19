<?php
session_start();
include 'config.php';

// Inicializamos el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Si se ha eliminado un producto del carrito
if (isset($_GET['accion']) && $_GET['accion'] == 'remove' && isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    // Recorrer el carrito para encontrar y eliminar el producto
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] == $id_producto) {
            unset($_SESSION['carrito'][$key]); // Eliminar el producto del carrito
            break; // Salir del bucle una vez que se elimina
        }
    }

    header('Location: carrito.php'); // Redirigir al carrito después de eliminar
    exit();
}

// Obtener los productos del carrito
$productos_carrito = array();
$total = 0;

if (!empty($_SESSION['carrito'])) {
    // Obtenemos los IDs del carrito
    $ids = implode(",", array_column($_SESSION['carrito'], 'id'));
    $sql = "SELECT * FROM Productos WHERE id IN ($ids)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];

        // Buscar el producto en la sesión para obtener la cantidad
        foreach ($_SESSION['carrito'] as $item) {
            if ($item['id'] == $id) {
                $cantidad = (int)$item['cantidad']; // Obtenemos la cantidad directamente del item
                break;
            }
        }

        $subtotal = $row['precio'] * $cantidad; // Calcular subtotal
        $total += $subtotal; // Sumar al total

        $productos_carrito[] = array(
            'id' => $id,
            'nombre' => $row['nombre'],
            'precio' => (float)$row['precio'], // Asegurar que el precio sea un número de punto flotante
            'cantidad' => $cantidad, // Guardar la cantidad correcta
            'subtotal' => $subtotal
        );
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
<div class="container mt-5">
    <h1>Carrito de Compras</h1>

    <?php if (!empty($productos_carrito)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_carrito as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                        <td><?php echo (int)$producto['cantidad']; ?></td> <!-- Mostrar la cantidad -->
                        <td><?php echo number_format($producto['subtotal'], 2); ?> €</td>
                        <td><a href="carrito.php?accion=remove&id=<?php echo $producto['id']; ?>" class="btn btn-danger">Eliminar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total: <?php echo number_format($total, 2); ?> €</h3>
        <a href="finalizar_compra.php" class="btn btn-success">Finalizar Compra</a>
    <?php else: ?>
        <p>No hay productos en el carrito.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
