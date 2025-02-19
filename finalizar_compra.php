<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p>Debes estar <a href='login.php'>iniciado sesión</a> para realizar un pedido.</p>";
    exit;
}

// Verificar si el carrito está vacío
if (empty($_SESSION['carrito'])) {
    echo "<p>Tu carrito está vacío.</p>";
    exit;
}

// Calcular el total del carrito
$total = 0;
foreach ($_SESSION['carrito'] as $producto) {
    if (is_array($producto) && isset($producto['precio'], $producto['cantidad'])) {
        $total += $producto['precio'] * $producto['cantidad'];
    }
}

// Si se ha enviado el formulario de finalizar compra
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $usuario_id = $_SESSION['usuario_id']; // Obtener el ID del usuario de la sesión
    $pago = $_POST['pago'];

    // Guardar la información del pedido en la base de datos
    $sql = "INSERT INTO pedidos (usuario_id, fecha, total, estado) VALUES (?, NOW(), ?, 'pendiente')";
    
    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular parámetros
        $stmt->bind_param("sd", $usuario_id, $total);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<p>Gracias por tu compra!</p>";
            echo "<p>Tu pedido se ha realizado con éxito.</p>";

            // Vaciar el carrito
            $_SESSION['carrito'] = array();
        } else {
            echo "<p>Error al realizar el pedido: " . $conn->error . "</p>";
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "<p>Error al preparar la consulta: " . $conn->error . "</p>";
    }

    // Cerrar la conexión
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <h1 class="my-4">Finalizar Compra</h1>

    <h3>Resumen de tu pedido</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['carrito'] as $producto): ?>
                <?php if (is_array($producto) && isset($producto['nombre'], $producto['cantidad'], $producto['precio'])): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                        <td><?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?> €</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Error al cargar el producto.</td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Total: <?php echo number_format($total, 2); ?> €</h4>

    <h3 class="my-4">Método de pago</h3>
    <form action="finalizar_compra.php" method="POST">
        <div class="form-group">
            <label for="pago">Selecciona un método de pago</label>
            <select class="form-control" id="pago" name="pago" required>
                <option value="tarjeta">Tarjeta de crédito</option>
                <option value="paypal">PayPal</option>
                <option value="transferencia">Transferencia bancaria</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success btn-block">Confirmar pedido</button>
    </form>
</div>
</body>
</html>
