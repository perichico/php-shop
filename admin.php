<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Obtener estadísticas de ventas
$sales_sql = "SELECT COUNT(*) AS total_pedidos, SUM(total) AS total_ventas FROM pedidos";
$sales_result = $conn->query($sales_sql);
$sales_data = $sales_result->fetch_assoc();

// Obtener datos de ventas mensuales (o por periodos que desees)
$monthly_sales_sql = "
    SELECT DATE_FORMAT(fecha, '%Y-%m') AS month, SUM(total) AS total
    FROM pedidos
    GROUP BY month
    ORDER BY month ASC";
$monthly_sales_result = $conn->query($monthly_sales_sql);

$months = [];
$sales = [];

while ($row = $monthly_sales_result->fetch_assoc()) {
    $months[] = $row['month'];
    $sales[] = $row['total'];
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">
    <h1>Panel de Administración</h1>
    
    <!-- Estadísticas de Ventas -->
    <div class="mb-4">
        <h2>Estadísticas de Ventas</h2>
        <p>Total de Pedidos: <strong><?php echo $sales_data['total_pedidos']; ?></strong></p>
        <p>Total de Ventas: <strong><?php echo number_format($sales_data['total_ventas'], 2); ?> €</strong></p>
    </div>

    <!-- Gráfico de Ventas Mensuales -->
    <div class="mb-4">
        <h2>Ventas Mensuales</h2>
        <canvas id="monthlySalesChart" width="400" height="200"></canvas>
    </div>

    <!-- Navegación del Panel -->
    <div class="list-group mb-4">
        <a href="agregar_producto.php" class="list-group-item list-group-item-action">Añadir Productos</a>
        <a href="crear_usuario.php" class="list-group-item list-group-item-action">Crear Usuario Administrador</a>
        <a href="ver_productos.php" class="list-group-item list-group-item-action">Ver productos</a>
        <a href="ver_usuarios.php" class="list-group-item list-group-item-action">Ver Usuarios</a>
        <a href="ver_pedidos.php" class="list-group-item list-group-item-action">Ver Pedidos</a>
        <a href="logout.php" class="list-group-item list-group-item-action">Cerrar Sesión</a>
    </div>
</div>

<script>
    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlySalesChart = new Chart(ctx, {
        type: 'line', // Tipo de gráfico (puede ser 'bar', 'line', 'pie', etc.)
        data: {
            labels: <?php echo json_encode($months); ?>, // Meses
            datasets: [{
                label: 'Ventas (€)',
                data: <?php echo json_encode($sales); ?>, // Ventas totales por mes
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
