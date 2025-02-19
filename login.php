<?php
session_start();
require 'config.php';

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT id, password, tipo_usuario FROM usuarios WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($usuario_id, $hashed_password, $tipo_usuario);
            $stmt->fetch();

            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                $_SESSION['usuario_id'] = $usuario_id; // Guarda el ID del usuario en la sesión
                $_SESSION['tipo_usuario'] = $tipo_usuario; // Guarda el tipo de usuario en la sesión

                // Redirigir según el tipo de usuario
                if ($tipo_usuario === 'admin') {
                    header("Location: admin.php"); // Redirigir a la página de administración
                } else {
                    header("Location: index.php"); // Redirigir a la página principal
                }
                exit;
            } else {
                $error_message = "Email o contraseña incorrectos.";
            }
        } else {
            $error_message = "Email o contraseña incorrectos.";
        }
    } else {
        $error_message = "Error en la base de datos. Intenta más tarde.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="my-4">Iniciar Sesión</h1>

    <!-- Mensaje de error -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    <p class="mt-2">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
</div>
</body>
</html>
