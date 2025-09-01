<?php
// Logout - Cerrar sesión de forma segura
session_start();

// Log de logout si hay información de usuario
if (isset($_SESSION['username'])) {
    // Registrar el logout (opcional)
    error_log('Usuario ' . $_SESSION['username'] . ' cerró sesión desde ' . ($_SERVER['REMOTE_ADDR'] ?? 'IP desconocida'));
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se usan cookies de sesión, destruirlas
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: index.php');
exit();
?>
