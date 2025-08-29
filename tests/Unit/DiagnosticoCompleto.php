<?php
// Script de diagnóstico completo del sistema
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 DIAGNÓSTICO COMPLETO DEL SISTEMA</h2>";
echo "<hr>";

// 1. Verificar rutas y archivos críticos
echo "<h3>1. Verificación de Archivos Críticos</h3>";
$critical_files = [
    'app/Database/Database.php',
    'app/Config/config.php',
    'app/Controllers/LoginController.php',
    'app/Services/LoggerService.php',
    'vendor/autoload.php'
];

foreach ($critical_files as $file) {
    $fullPath = __DIR__ . '/../../' . $file;
    $exists = file_exists($fullPath) ? "✅" : "❌";
    $readable = is_readable($fullPath) ? "✅" : "❌";
    echo "<p>$exists <strong>$file</strong> - Existe: $exists, Legible: $readable</p>";
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        echo "<p style='margin-left: 20px; color: #666;'>Tamaño: " . number_format($size) . " bytes</p>";
    }
}

// 2. Cargar configuración
echo "<h3>2. Configuración de Base de Datos</h3>";
try {
    $configPath = __DIR__ . '/../../app/Config/config.php';
    if (file_exists($configPath)) {
        $config = require $configPath;
        echo "<p>✅ Archivo de configuración cargado</p>";
        echo "<p><strong>Host:</strong> " . $config['database']['host'] . "</p>";
        echo "<p><strong>Base de datos:</strong> " . $config['database']['dbname'] . "</p>";
        echo "<p><strong>Usuario:</strong> " . $config['database']['username'] . "</p>";
        echo "<p><strong>Contraseña:</strong> " . (strlen($config['database']['password']) > 0 ? 'Configurada' : 'Vacía') . "</p>";
    } else {
        echo "<p>❌ Archivo de configuración no encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar configuración: " . $e->getMessage() . "</p>";
}

// 3. Prueba de conexión con credenciales correctas
echo "<h3>3. Prueba de Conexión a Base de Datos</h3>";
try {
    if (isset($config)) {
        $host = $config['database']['host'];
        $dbname = $config['database']['dbname'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
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
            
            // Contar usuarios
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
            $result = $stmt->fetch();
            echo "<p><strong>Total usuarios:</strong> " . $result['total'] . "</p>";
            
        } else {
            echo "<p>❌ Tabla 'usuarios' no existe</p>";
        }
        
    } else {
        echo "<p>❌ No se pudo cargar la configuración</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// 4. Verificar autoloader de Composer
echo "<h3>4. Verificación de Composer Autoloader</h3>";
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    try {
        require_once $autoloadPath;
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

// 5. Prueba de instanciación de clases
echo "<h3>5. Prueba de Instanciación de Clases</h3>";

// Probar Database
try {
    if (class_exists('App\Database\Database')) {
        $db = App\Database\Database::getInstance();
        echo "<p>✅ Clase Database instanciada correctamente</p>";
        
        $connection = $db->getConnection();
        echo "<p>✅ Conexión PDO obtenida</p>";
    } else {
        echo "<p>❌ Clase Database no existe</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar Database: " . $e->getMessage() . "</p>";
}

// Probar LoggerService
try {
    if (class_exists('App\Services\LoggerService')) {
        $logger = new App\Services\LoggerService();
        echo "<p>✅ Clase LoggerService instanciada correctamente</p>";
        
        // Probar escritura de log
        $logger->info('Prueba de escritura de logs');
        echo "<p>✅ Escritura de logs exitosa</p>";
    } else {
        echo "<p>❌ Clase LoggerService no existe</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoggerService: " . $e->getMessage() . "</p>";
}

// Probar LoginController
try {
    if (class_exists('App\Controllers\LoginController')) {
        $loginController = new App\Controllers\LoginController();
        echo "<p>✅ Clase LoginController instanciada correctamente</p>";
        
        // Probar método authenticate
        $result = $loginController->authenticate('root', 'root');
        if (is_array($result) && isset($result['success'])) {
            echo "<p>✅ Método authenticate funciona correctamente</p>";
            echo "<p><strong>Resultado:</strong> " . ($result['success'] ? 'Éxito' : 'Falló') . "</p>";
            if (!$result['success']) {
                echo "<p><strong>Mensaje:</strong> " . $result['message'] . "</p>";
            }
        } else {
            echo "<p>❌ Método authenticate no retorna formato esperado</p>";
        }
    } else {
        echo "<p>❌ Clase LoginController no existe</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en LoginController: " . $e->getMessage() . "</p>";
}

// 6. Verificar directorio de logs
echo "<h3>6. Verificación de Directorio de Logs</h3>";
$logsDir = __DIR__ . '/../../logs';
if (!is_dir($logsDir)) {
    if (mkdir($logsDir, 0755, true)) {
        echo "<p>✅ Directorio logs creado correctamente</p>";
    } else {
        echo "<p>❌ No se pudo crear el directorio logs</p>";
    }
} else {
    echo "<p>✅ Directorio logs existe</p>";
}

if (is_writable($logsDir)) {
    echo "<p>✅ Directorio logs es escribible</p>";
} else {
    echo "<p>❌ Directorio logs no es escribible</p>";
}

// 7. Verificar archivos de log
echo "<h3>7. Verificación de Archivos de Log</h3>";
$logFiles = [
    'logs/debug.log',
    'logs/app.log'
];

foreach ($logFiles as $logFile) {
    $fullPath = __DIR__ . '/../../' . $logFile;
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        $modified = date('Y-m-d H:i:s', filemtime($fullPath));
        echo "<p>✅ $logFile - Tamaño: " . number_format($size) . " bytes, Última modificación: $modified</p>";
    } else {
        echo "<p>❌ $logFile no existe</p>";
    }
}

echo "<hr>";
echo "<h3>🎯 RESUMEN DEL DIAGNÓSTICO</h3>";
echo "<p>Si todos los puntos muestran ✅, el sistema está funcionando correctamente.</p>";
echo "<p>Si hay ❌, revisa los errores específicos mostrados arriba.</p>";

echo "<h3>🔗 Enlaces Útiles</h3>";
echo "<p>";
echo "<a href='VerLogsDebug.php' style='margin-right: 10px;'>🔍 Ver Logs Debug</a>";
echo "<a href='TestLoginControllerOptimizado.php' style='margin-right: 10px;'>⚡ Test LoginController</a>";
echo "<a href='ActualizarTablaUsuariosV2.php' style='margin-right: 10px;'>🗄️ Actualizar Tabla</a>";
echo "<a href='CorregirHashUsuario.php'>🔧 Corregir Hash</a>";
echo "</p>";
?>
