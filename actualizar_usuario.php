<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];

    $sql = "UPDATE usuarios SET nombre = ?, email = ?, tipo_usuario = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $email, $tipo_usuario, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ver_usuarios.php"); // Redirigir a la lista de usuarios
    exit;
}
?>
