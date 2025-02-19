<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Verificar si se envió un ID de usuario
if (!isset($_GET['id'])) {
    header("Location: ver_usuarios.php");
    exit;
}

// Obtener el usuario por ID
$user_id = $_GET['id'];
$sql = "SELECT nombre, email, tipo_usuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nombre, $email, $tipo_usuario);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Usuario</h1>
    <form action="actualizar_usuario.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $user_id; ?>">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="tipo_usuario">Tipo de Usuario</label>
            <select class="form-control" id="tipo_usuario" name="tipo_usuario">
                <option value="usuario" <?php echo ($tipo_usuario == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                <option value="admin" <?php echo ($tipo_usuario == 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
    </form>
    <a href="ver_usuarios.php" class="btn btn-secondary mt-2">Cancelar</a>
</div>
</body>
</html>
