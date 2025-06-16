<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Iniciar sesión si es necesario
session_start();

// Cargar configuración
$config = require __DIR__ . '/../app/Config/config.php';

// Aquí iría el enrutamiento básico
$request = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Eliminar el path base de la URL
$request = str_replace($basePath, '', $request);

// Enrutamiento básico
switch ($request) {
    case '/':
        require __DIR__ . '/../app/Controllers/HomeController.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/../app/Views/404.php';
        break;
} 