<?php
// Inicia la sesión
session_start();

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Redirecciona al usuario a la página de inicio de sesión u otra página deseada
header("Location: ../../index.php");
exit(); // Asegura que el script termine después de redireccionar
?>
