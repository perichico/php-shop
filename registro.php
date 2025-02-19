<?php
session_start();
require 'config.php';

// Verificar si se envió el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el correo ya está registrado
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<p>El correo electrónico ya está registrado. Intenta con otro.</p>";
        } else {
            // Registrar el nuevo usuario (asegúrate de hashear la contraseña)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hasheando la contraseña
            $sql_insert = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";

            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("sss", $nombre, $email, $hashed_password);
                if ($stmt_insert->execute()) {
                    header('Location: login.php');
                } else {
                    echo "<p>Error al registrar: " . $conn->error . "</p>";
                }
                $stmt_insert->close();
            }
        }
        $stmt->close();
    } else {
        echo "<p>Error al preparar la consulta: " . $conn->error . "</p>";
    }
    
    // Cerrar conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="my-4">Registro</h1>
    <form action="registro.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
    <p class="mt-2">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</div>
</body>
</html>
