<?php
require_once '../../app/Controllers/CerrarSesionController.php';
$controlador = new CerrarSesionController();
$controlador->cerrar(); // método que destruye la sesión y redirige
?>
