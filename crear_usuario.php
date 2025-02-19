<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Procesar el formulario para crear un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña

    $sql = "INSERT INTO usuarios (email, password, tipo_usuario) VALUES (?, ?, 'admin')";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $email, $hashed_password);
        if ($stmt->execute()) {
            $success_message = "Usuario administrador creado con éxito.";
        } else {
            $error_message = "Error al crear el usuario.";
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
    <title>Crear Usuario Administrador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Crear Usuario Administrador</h1>

    <!-- Mensaje de éxito o error -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="crear_usuario.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
    
<br><br>
<a href="admin.php" class="btn btn-secondary">Volver a Admin</a>
</div>

</body>
</html>
