<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../app/Controllers/CerrarSesionController.php';
$controlador = new CerrarSesionController();
$controlador->cerrar(); // método que destruye la sesión y redirige
?>
