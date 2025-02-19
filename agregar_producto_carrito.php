<?php
session_start();

// Verifica si se envió el formulario
if (isset($_POST['agregar'])) {
    // Crea un producto como un array
    $producto = [
        'id' => $_POST['id'], // ID del producto
        'nombre' => $_POST['nombre'], // Nombre del producto
        'precio' => floatval($_POST['precio']), // Precio del producto
        'cantidad' => intval($_POST['cantidad']) // Cantidad del producto
    ];

    // Inicializa el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Verifica si el producto ya está en el carrito
    $encontrado = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] === $producto['id']) {
            $item['cantidad'] += $producto['cantidad']; // Aumenta la cantidad
            $encontrado = true;
            break;
        }
    }

    // Si el producto no está en el carrito, se añade
    if (!$encontrado) {
        $_SESSION['carrito'][] = $producto;
    }

    // Redirige a la página de finalizar compra
    header("Location: carrito.php");
    exit();
}
?>
