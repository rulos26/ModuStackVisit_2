<?php
declare(strict_types=1);

// Cargar autoloader
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('❌ ERROR: No se encontró el autoloader de Composer. Ejecuta: composer install');
}
require_once $autoloadPath;

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar sesión si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
$configPath = __DIR__ . '/../app/Config/config.php';
if (!file_exists($configPath)) {
    die('❌ ERROR: Archivo de configuración no encontrado');
}
$config = require $configPath;

// Obtener la URL de la petición
$request = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Eliminar el path base de la URL
$request = str_replace($basePath, '', $request);

// Limpiar la URL
$request = trim($request, '/');

// Enrutamiento básico
switch ($request) {
    case '':
    case 'index.php':
        // Redirigir al login principal
        header('Location: /index.php');
        exit();
        break;
        
    case 'login':
        // Redirigir al login
        header('Location: /index.php');
        exit();
        break;
        
    case 'admin':
        // Redirigir al dashboard de admin
        header('Location: /resources/views/admin/dashboardAdmin.php');
        exit();
        break;
        
    case 'evaluador':
        // Redirigir al dashboard de evaluador
        header('Location: /resources/views/evaluador/dashboardEavaluador.php');
        exit();
        break;
        
    case 'superadmin':
        // Redirigir al dashboard de superadmin
        header('Location: /resources/views/superadmin/dashboardSuperAdmin.php');
        exit();
        break;
        
    default:
        // Página no encontrada
        http_response_code(404);
        $viewPath = __DIR__ . '/../app/Views/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            // Fallback si no existe la vista 404
            echo '<!DOCTYPE html>';
            echo '<html lang="es">';
            echo '<head><meta charset="UTF-8"><title>404 - Página No Encontrada</title></head>';
            echo '<body style="font-family: Arial, sans-serif; text-align: center; padding: 50px;">';
            echo '<h1 style="color: #dc3545;">404</h1>';
            echo '<h2>Página No Encontrada</h2>';
            echo '<p>La página <strong>' . htmlspecialchars($request) . '</strong> no existe.</p>';
            echo '<p><a href="/index.php" style="color: #007bff;">🏠 Ir al Inicio</a></p>';
            echo '</body></html>';
        }
        break;
} 