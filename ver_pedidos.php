<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Consulta para obtener todos los pedidos
$sql = "SELECT p.id, p.usuario_id, p.fecha, p.total, p.estado, u.nombre AS nombre_usuario 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha DESC"; // Ordenar por fecha, más reciente primero
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pedidos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="my-4">Lista de Pedidos</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['fecha']); ?></td>
                        <td><?php echo number_format($pedido['total'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay pedidos disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="admin.php" class="btn btn-secondary">Volver a Admin</a>
</div>
</body>
</html>
