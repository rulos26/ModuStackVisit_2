<?php
// Archivo de entrada para el explorador de imágenes
require_once 'vendor/autoload.php';

use App\Controllers\ExploradorImagenesController;

$controller = new ExploradorImagenesController();
$controller->index();
?>
