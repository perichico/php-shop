<?php
session_start();
include 'config.php'; // Incluye el archivo de conexión a la base de datos

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redirigir al login si no está logueado
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener todos los pedidos del usuario
$sql = "SELECT id, fecha, total, estado FROM pedidos WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$pedidos = [];
if ($result && $result->num_rows > 0) {
    // Guardar los pedidos en un array
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }
} else {
    $mensaje = "No has realizado ningún pedido.";
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
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
    <h2>Mis Pedidos</h2>
    <?php if (!empty($pedidos)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID del Pedido</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['fecha']); ?></td>
                        <td><?php echo number_format($pedido['total'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning"><?php echo $mensaje; ?></div>
    <?php endif; ?>
</div>
</body>
</html>
