<?php
// Test de la estructura real del servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Estructura Real del Servidor</h2>";

// Test 1: Explorar directorio public
echo "<h3>1. Estructura del Directorio Public</h3>";
$publicDir = __DIR__ . '/../../public/';
echo "Directorio public: " . $publicDir . "<br>";

if (is_dir($publicDir)) {
    echo "✅ Directorio public existe<br>";
    $items = scandir($publicDir);
    echo "Contenido del directorio public:<br>";
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $publicDir . $item;
            if (is_dir($path)) {
                echo "📁 " . $item . "/<br>";
            } else {
                echo "📄 " . $item . "<br>";
            }
        }
    }
} else {
    echo "❌ Directorio public no existe<br>";
}

// Test 2: Explorar directorio views
echo "<br><h3>2. Estructura del Directorio Views</h3>";
$viewsDir = __DIR__ . '/../../views/';
echo "Directorio views: " . $viewsDir . "<br>";

if (is_dir($viewsDir)) {
    echo "✅ Directorio views existe<br>";
    $items = scandir($viewsDir);
    echo "Contenido del directorio views:<br>";
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $viewsDir . $item;
            if (is_dir($path)) {
                echo "📁 " . $item . "/<br>";
            } else {
                echo "📄 " . $item . "<br>";
            }
        }
    }
} else {
    echo "❌ Directorio views no existe<br>";
}

// Test 3: Buscar archivos de conexión en toda la estructura
echo "<br><h3>3. Buscar Archivos de Conexión en Toda la Estructura</h3>";
$directorios = [
    '../../',
    '../../public/',
    '../../views/',
    '../../img/',
    '../',
    './'
];

$archivosBuscados = [
    'conexion.php',
    'database.php',
    'config.php',
    'db.php',
    'connection.php',
    'connect.php'
];

foreach ($directorios as $dir) {
    echo "<strong>Buscando en: " . $dir . "</strong><br>";
    foreach ($archivosBuscados as $archivo) {
        $ruta = __DIR__ . '/' . $dir . $archivo;
        if (file_exists($ruta)) {
            echo "✅ Encontrado: " . $archivo . "<br>";
        }
    }
}

// Test 4: Buscar archivos PHP en subdirectorios
echo "<br><h3>4. Buscar Archivos PHP en Subdirectorios</h3>";
$directorios = [
    '../../public/',
    '../../views/',
    '../'
];

foreach ($directorios as $dir) {
    $dirPath = __DIR__ . '/' . $dir;
    if (is_dir($dirPath)) {
        echo "<strong>Archivos PHP en " . $dir . ":</strong><br>";
        $files = glob($dirPath . '*.php');
        if (count($files) > 0) {
            foreach ($files as $file) {
                echo "📄 " . basename($file) . "<br>";
            }
        } else {
            echo "No se encontraron archivos PHP<br>";
        }
    }
}

// Test 5: Buscar archivos de configuración
echo "<br><h3>5. Buscar Archivos de Configuración</h3>";
$archivosConfig = [
    '../../.env',
    '../../config.ini',
    '../../settings.php',
    '../../config.php',
    '../../database.php',
    '../../conexion.php',
    '../config.php',
    './config.php'
];

foreach ($archivosConfig as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        echo "✅ Encontrado: " . $archivo . "<br>";
        // Leer las primeras líneas para ver si contiene configuración de BD
        $content = file_get_contents($ruta);
        if (strpos($content, 'mysql') !== false || strpos($content, 'database') !== false) {
            echo "  → Parece contener configuración de base de datos<br>";
        }
    }
}

// Test 6: Verificar si hay archivos de conexión en el directorio actual
echo "<br><h3>6. Archivos en el Directorio Actual</h3>";
$currentDir = __DIR__ . '/';
$files = glob($currentDir . '*.php');
if (count($files) > 0) {
    echo "Archivos PHP en el directorio actual:<br>";
    foreach ($files as $file) {
        echo "📄 " . basename($file) . "<br>";
    }
} else {
    echo "No se encontraron archivos PHP en el directorio actual<br>";
}

// Test 7: Buscar en directorios padre
echo "<br><h3>7. Buscar en Directorios Padre</h3>";
$parentDirs = [
    '../../../',
    '../../../../',
    '../../../../../'
];

foreach ($parentDirs as $parentDir) {
    $dirPath = __DIR__ . '/' . $parentDir;
    if (is_dir($dirPath)) {
        echo "<strong>Contenido de " . $parentDir . ":</strong><br>";
        $items = scandir($dirPath);
        $count = 0;
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $count++;
                if ($count <= 10) { // Mostrar solo los primeros 10
                    $path = $dirPath . $item;
                    if (is_dir($path)) {
                        echo "📁 " . $item . "/<br>";
                    } else {
                        echo "📄 " . $item . "<br>";
                    }
                }
            }
        }
        if ($count > 10) {
            echo "... y " . ($count - 10) . " elementos más<br>";
        }
    }
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
