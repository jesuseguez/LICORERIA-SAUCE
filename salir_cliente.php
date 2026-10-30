<?php
session_start();

// Limpiar variables
$_SESSION = array();

// Borrar cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir sesión
session_destroy();

// Redirigir a inicio
header('Location: home.html');
exit;
?>