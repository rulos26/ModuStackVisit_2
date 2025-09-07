<?php
// Test de estructura del servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Estructura del Servidor</h2>";

// Test 1: Verificar directorio raíz
echo "<h3>1. Estructura del Directorio Raíz</h3>";
$rootDir = __DIR__ . '/../../';
echo "Directorio raíz: " . $rootDir . "<br>";

if (is_dir($rootDir)) {
    echo "✅ Directorio raíz existe<br>";
    $items = scandir($rootDir);
    echo "Contenido del directorio raíz:<br>";
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $rootDir . $item;
            if (is_dir($path)) {
                echo "📁 " . $item . "/<br>";
            } else {
                echo "📄 " . $item . "<br>";
            }
        }
    }
} else {
    echo "❌ Directorio raíz no existe<br>";
}

// Test 2: Verificar directorio app
echo "<br><h3>2. Estructura del Directorio App</h3>";
$appDir = __DIR__ . '/../../app/';
echo "Directorio app: " . $appDir . "<br>";

if (is_dir($appDir)) {
    echo "✅ Directorio app existe<br>";
    $items = scandir($appDir);
    echo "Contenido del directorio app:<br>";
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $appDir . $item;
            if (is_dir($path)) {
                echo "📁 " . $item . "/<br>";
            } else {
                echo "📄 " . $item . "<br>";
            }
        }
    }
} else {
    echo "❌ Directorio app no existe<br>";
}

// Test 3: Verificar directorio vendor
echo "<br><h3>3. Estructura del Directorio Vendor</h3>";
$vendorDir = __DIR__ . '/../../vendor/';
echo "Directorio vendor: " . $vendorDir . "<br>";

if (is_dir($vendorDir)) {
    echo "✅ Directorio vendor existe<br>";
    $items = scandir($vendorDir);
    echo "Contenido del directorio vendor:<br>";
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $vendorDir . $item;
            if (is_dir($path)) {
                echo "📁 " . $item . "/<br>";
            } else {
                echo "📄 " . $item . "<br>";
            }
        }
    }
} else {
    echo "❌ Directorio vendor no existe<br>";
}

// Test 4: Buscar archivos de conexión
echo "<br><h3>4. Buscar Archivos de Conexión</h3>";
$archivosConexion = [
    '../../conn/conexion.php',
    '../../app/Database/Database.php',
    '../../app/Config/config.php',
    '../../database.php',
    '../../config.php',
    '../../conexion.php'
];

foreach ($archivosConexion as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        echo "✅ Encontrado: " . $archivo . "<br>";
    } else {
        echo "❌ No encontrado: " . $archivo . "<br>";
    }
}

// Test 5: Buscar archivos PHP en el directorio raíz
echo "<br><h3>5. Archivos PHP en el Directorio Raíz</h3>";
$rootDir = __DIR__ . '/../../';
if (is_dir($rootDir)) {
    $files = glob($rootDir . '*.php');
    if (count($files) > 0) {
        echo "Archivos PHP encontrados:<br>";
        foreach ($files as $file) {
            echo "📄 " . basename($file) . "<br>";
        }
    } else {
        echo "No se encontraron archivos PHP en el directorio raíz<br>";
    }
}

// Test 6: Verificar si existe composer.json
echo "<br><h3>6. Verificar Composer</h3>";
$composerFile = __DIR__ . '/../../composer.json';
if (file_exists($composerFile)) {
    echo "✅ composer.json existe<br>";
    try {
        $composer = json_decode(file_get_contents($composerFile), true);
        if ($composer) {
            echo "✅ composer.json válido<br>";
            if (isset($composer['autoload'])) {
                echo "✅ Autoload configurado<br>";
            } else {
                echo "❌ Autoload no configurado<br>";
            }
        } else {
            echo "❌ composer.json inválido<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error leyendo composer.json: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ composer.json no existe<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
