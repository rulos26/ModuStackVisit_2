<?php
// Script de diagnóstico para error HTTP 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 DIAGNÓSTICO DE ERROR HTTP 500</h2>";
echo "<hr>";

// 1. Verificar versión de PHP
echo "<h3>1. Información de PHP</h3>";
echo "<p><strong>Versión de PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensiones cargadas:</strong></p>";
echo "<ul>";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext) ? "✅" : "❌";
    echo "<li>$loaded $ext</li>";
}
echo "</ul>";

// 2. Verificar archivos críticos
echo "<h3>2. Verificación de Archivos Críticos</h3>";
$critical_files = [
    'app/Database/Database.php',
    'app/Config/config.php',
    'app/Controllers/LoginController.php',
    'app/Services/LoggerService.php',
    'vendor/autoload.php'
];

foreach ($critical_files as $file) {
    $exists = file_exists($file) ? "✅" : "❌";
    $readable = is_readable($file) ? "✅" : "❌";
    echo "<p>$exists <strong>$file</strong> - Existe: $exists, Legible: $readable</p>";
}

// 3. Verificar configuración de base de datos
echo "<h3>3. Prueba de Conexión a Base de Datos</h3>";
try {
    if (file_exists('app/Config/config.php')) {
        require_once 'app/Config/config.php';
        echo "<p>✅ Archivo de configuración cargado</p>";
        
        // Intentar conexión directa
        $host = 'localhost';
        $dbname = 'modustackvisit';
        $username = 'root';
        $password = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar tabla usuarios
        $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Tabla 'usuarios' existe</p>";
            
            // Verificar estructura
            $stmt = $pdo->query("DESCRIBE usuarios");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Columnas en tabla usuarios:</strong></p>";
            echo "<ul>";
            foreach ($columns as $column) {
                echo "<li>$column</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ Tabla 'usuarios' no existe</p>";
        }
        
    } else {
        echo "<p>❌ Archivo de configuración no encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// 4. Verificar autoloader de Composer
echo "<h3>4. Verificación de Composer Autoloader</h3>";
if (file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        echo "<p>✅ Autoloader de Composer cargado</p>";
        
        // Verificar clases
        $classes = [
            'App\Database\Database',
            'App\Controllers\LoginController',
            'App\Services\LoggerService'
        ];
        
        foreach ($classes as $class) {
            if (class_exists($class)) {
                echo "<p>✅ Clase $class existe</p>";
            } else {
                echo "<p>❌ Clase $class no existe</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error al cargar autoloader: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Autoloader de Composer no encontrado</p>";
}

// 5. Verificar permisos de directorios
echo "<h3>5. Verificación de Permisos</h3>";
$directories = [
    'logs',
    'app',
    'tests'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? "✅" : "❌";
        echo "<p>$writable Directorio $dir - Escritura: $writable</p>";
    } else {
        echo "<p>❌ Directorio $dir no existe</p>";
    }
}

// 6. Verificar archivo .htaccess
echo "<h3>6. Verificación de .htaccess</h3>";
if (file_exists('.htaccess')) {
    echo "<p>✅ Archivo .htaccess existe</p>";
    $htaccess_content = file_get_contents('.htaccess');
    if (strpos($htaccess_content, 'RewriteEngine') !== false) {
        echo "<p>✅ RewriteEngine configurado</p>";
    } else {
        echo "<p>❌ RewriteEngine no configurado</p>";
    }
} else {
    echo "<p>❌ Archivo .htaccess no existe</p>";
}

// 7. Prueba de carga de clases
echo "<h3>7. Prueba de Carga de Clases</h3>";
try {
    if (file_exists('app/Database/Database.php')) {
        require_once 'app/Database/Database.php';
        $db = App\Database\Database::getInstance();
        echo "<p>✅ Clase Database cargada correctamente</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar Database: " . $e->getMessage() . "</p>";
}

try {
    if (file_exists('app/Services/LoggerService.php')) {
        require_once 'app/Services/LoggerService.php';
        $logger = new App\Services\LoggerService();
        echo "<p>✅ Clase LoggerService cargada correctamente</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar LoggerService: " . $e->getMessage() . "</p>";
}

// 8. Verificar logs de error
echo "<h3>8. Verificación de Logs de Error</h3>";
$log_files = [
    'logs/app.log',
    'logs/error.log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        $size = filesize($log_file);
        $modified = date('Y-m-d H:i:s', filemtime($log_file));
        echo "<p>✅ $log_file - Tamaño: $size bytes, Última modificación: $modified</p>";
        
        // Mostrar últimas líneas del log
        $lines = file($log_file);
        $last_lines = array_slice($lines, -5);
        if (!empty($last_lines)) {
            echo "<p><strong>Últimas 5 líneas:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            foreach ($last_lines as $line) {
                echo htmlspecialchars($line);
            }
            echo "</pre>";
        }
    } else {
        echo "<p>❌ $log_file no existe</p>";
    }
}

echo "<hr>";
echo "<h3>🎯 RECOMENDACIONES</h3>";
echo "<ul>";
echo "<li>Si hay errores en los logs, revisa los mensajes específicos</li>";
echo "<li>Verifica que XAMPP esté ejecutándose correctamente</li>";
echo "<li>Asegúrate de que el módulo rewrite esté habilitado en Apache</li>";
echo "<li>Verifica que la base de datos esté activa y accesible</li>";
echo "<li>Comprueba que los permisos de archivos sean correctos</li>";
echo "</ul>";

echo "<p><strong>Para más detalles, revisa los logs de Apache en:</strong></p>";
echo "<ul>";
echo "<li>XAMPP Control Panel → Apache → Logs → error.log</li>";
echo "<li>XAMPP Control Panel → MySQL → Logs → error.log</li>";
echo "</ul>";
?>
