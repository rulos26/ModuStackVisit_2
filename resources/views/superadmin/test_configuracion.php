<?php
// Test de configuración paso a paso
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Configuración Paso a Paso</h2>";

// Test 1: Verificar archivo de configuración
echo "<h3>1. Test de Archivo de Configuración</h3>";
$configFile = __DIR__ . '/../../app/Config/config.php';
if (file_exists($configFile)) {
    echo "✅ Archivo config.php existe<br>";
    try {
        $config = require $configFile;
        echo "✅ Configuración cargada correctamente<br>";
        echo "Host: " . $config['database']['host'] . "<br>";
        echo "Base de datos: " . $config['database']['dbname'] . "<br>";
        echo "Usuario: " . $config['database']['username'] . "<br>";
    } catch (Exception $e) {
        echo "❌ Error cargando configuración: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Archivo config.php no existe<br>";
}

// Test 2: Verificar clase Database paso a paso
echo "<br><h3>2. Test de Clase Database</h3>";
try {
    echo "Incluyendo Database.php...<br>";
    require_once __DIR__ . '/../../app/Database/Database.php';
    echo "✅ Database.php incluido<br>";
    
    echo "Verificando namespace...<br>";
    if (class_exists('App\\Database\\Database')) {
        echo "✅ Clase Database existe<br>";
    } else {
        echo "❌ Clase Database no existe<br>";
    }
    
    echo "Creando instancia...<br>";
    $db = App\Database\Database::getInstance();
    echo "✅ Instancia creada<br>";
    
    echo "Obteniendo conexión...<br>";
    $connection = $db->getConnection();
    echo "✅ Conexión obtenida<br>";
    
} catch (Exception $e) {
    echo "❌ Error en Database: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ Error fatal en Database: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}

// Test 3: Verificar LoggerService
echo "<br><h3>3. Test de LoggerService</h3>";
try {
    echo "Incluyendo LoggerService.php...<br>";
    require_once __DIR__ . '/../../app/Services/LoggerService.php';
    echo "✅ LoggerService.php incluido<br>";
    
    echo "Verificando namespace...<br>";
    if (class_exists('App\\Services\\LoggerService')) {
        echo "✅ Clase LoggerService existe<br>";
    } else {
        echo "❌ Clase LoggerService no existe<br>";
    }
    
    echo "Creando instancia...<br>";
    $logger = new App\Services\LoggerService();
    echo "✅ Instancia creada<br>";
    
} catch (Exception $e) {
    echo "❌ Error en LoggerService: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ Error fatal en LoggerService: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}

// Test 4: Verificar TablasPrincipalesController
echo "<br><h3>4. Test de TablasPrincipalesController</h3>";
try {
    echo "Incluyendo TablasPrincipalesController.php...<br>";
    require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
    echo "✅ TablasPrincipalesController.php incluido<br>";
    
    echo "Verificando namespace...<br>";
    if (class_exists('App\\Controllers\\TablasPrincipalesController')) {
        echo "✅ Clase TablasPrincipalesController existe<br>";
    } else {
        echo "❌ Clase TablasPrincipalesController no existe<br>";
    }
    
    echo "Creando instancia...<br>";
    $controller = new App\Controllers\TablasPrincipalesController();
    echo "✅ Instancia creada<br>";
    
} catch (Exception $e) {
    echo "❌ Error en TablasPrincipalesController: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ Error fatal en TablasPrincipalesController: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}

// Test 5: Verificar autoloader
echo "<br><h3>5. Test de Autoloader</h3>";
$autoloaderFile = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloaderFile)) {
    echo "✅ Autoloader existe<br>";
    try {
        require_once $autoloaderFile;
        echo "✅ Autoloader cargado<br>";
    } catch (Exception $e) {
        echo "❌ Error cargando autoloader: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Autoloader no existe<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
