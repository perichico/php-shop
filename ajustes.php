<?php
session_start();
include 'config.php'; // Incluye el archivo de conexión a la base de datos

// Verifica si el usuario está conectado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener los datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nombre, email, direccion FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
} else {
    echo "Error al cargar los datos del usuario.";
    exit;
}

// Actualizar los datos del usuario si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    // Actualizar datos en la base de datos
    $sql_update = "UPDATE usuarios SET nombre = ?, email = ?, direccion = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nombre, $email, $direccion, $usuario_id);
    $stmt_update->execute();

    // Confirmación de actualización
    echo "<p>Datos actualizados correctamente.</p>";
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes</title>
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
    <h1 class="my-4">Ajustes de Cuenta</h1>
    <form action="ajustes.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Datos</button>
    </form>

    <form action="logout.php" method="POST" class="mt-3">
        <button type="submit" class="btn btn-danger">Cerrar sesión</button>
    </form>
</div>
</body>
</html>

