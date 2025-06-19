<?php

require_once __DIR__ . '/InformeFinalPdfController.php';

use App\Controllers\InformeFinalPdfController;

// Crear instancia del controlador
$controller = new InformeFinalPdfController();

// Obtener datos y HTML para debug
$data = $controller->getData();
$html = $controller->getHtml();

// Incluir la vista de debug mejorada
include __DIR__ . '/../../resources/views/test/debug_controller.php';

?> 