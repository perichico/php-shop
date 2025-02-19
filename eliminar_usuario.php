<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Primero, eliminar los pedidos asociados al usuario
    $sql_delete_pedidos = "DELETE FROM pedidos WHERE usuario_id = ?";
    $stmt_delete_pedidos = $conn->prepare($sql_delete_pedidos);
    $stmt_delete_pedidos->bind_param("i", $id);
    $stmt_delete_pedidos->execute();
    $stmt_delete_pedidos->close();

    // Luego, eliminar el usuario
    $sql_delete_usuario = "DELETE FROM usuarios WHERE id = ?";
    $stmt_delete_usuario = $conn->prepare($sql_delete_usuario);
    $stmt_delete_usuario->bind_param("i", $id);
    $stmt_delete_usuario->execute();
    $stmt_delete_usuario->close();

    header("Location: ver_usuarios.php"); // Redirigir a la lista de usuarios
    exit;
}
?>
