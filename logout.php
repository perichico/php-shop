<?php
session_start(); // Inicia la sesión

// Eliminar todas las variables de sesión
$_SESSION = [];

// Si se desea, destruir la sesión también
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio o de login
header('Location: index.php'); // Cambia esto a 'index.php' si prefieres redirigir al catálogo
exit;
?>
