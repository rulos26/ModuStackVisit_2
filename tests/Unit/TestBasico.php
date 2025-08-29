<?php
// Script de prueba básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 PRUEBA BÁSICA DEL SISTEMA</h2>";
echo "<hr>";

// 1. Prueba de conexión básica
echo "<h3>1. Prueba de Conexión Básica</h3>";
try {
    // Cargar configuración
    $configPath = __DIR__ . '/../../app/Config/config.php';
    if (file_exists($configPath)) {
        $config = require $configPath;
        $host = $config['database']['host'];
        $dbname = $config['database']['dbname'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar tabla usuarios
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $result = $stmt->fetch();
        echo "<p>✅ Tabla usuarios accesible - Total usuarios: " . $result['total'] . "</p>";
    } else {
        echo "<p>❌ Archivo de configuración no encontrado</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// 2. Prueba de carga de archivos
echo "<h3>2. Prueba de Carga de Archivos</h3>";
$files_to_test = [
    'app/Database/Database.php',
    'app/Config/config.php',
    'app/Controllers/LoginController.php',
    'app/Services/LoggerService.php'
];

foreach ($files_to_test as $file) {
    $fullPath = __DIR__ . '/../../' . $file;
    if (file_exists($fullPath)) {
        try {
            require_once $fullPath;
            echo "<p>✅ $file cargado correctamente</p>";
        } catch (Exception $e) {
            echo "<p>❌ Error al cargar $file: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ $file no existe</p>";
    }
}

// 3. Prueba de clases
echo "<h3>3. Prueba de Instanciación de Clases</h3>";

// Cargar autoloader primero
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    echo "<p>✅ Autoloader de Composer cargado</p>";
} else {
    echo "<p>❌ Autoloader de Composer no encontrado</p>";
}

try {
    if (class_exists('App\Database\Database')) {
        $db = App\Database\Database::getInstance();
        echo "<p>✅ Clase Database instanciada correctamente</p>";
    } else {
        echo "<p>❌ Clase Database no existe</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar Database: " . $e->getMessage() . "</p>";
}

try {
    if (class_exists('App\Services\LoggerService')) {
        $logger = new App\Services\LoggerService();
        echo "<p>✅ Clase LoggerService instanciada correctamente</p>";
    } else {
        echo "<p>❌ Clase LoggerService no existe</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoggerService: " . $e->getMessage() . "</p>";
}

// 4. Prueba de LoginController
echo "<h3>4. Prueba de LoginController</h3>";
try {
    if (class_exists('App\Controllers\LoginController')) {
        $loginController = new App\Controllers\LoginController();
        echo "<p>✅ LoginController instanciado correctamente</p>";
        
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

// 5. Prueba de directorio logs
echo "<h3>5. Prueba de Directorio de Logs</h3>";
$logs_dir = __DIR__ . '/../../logs';
if (!is_dir($logs_dir)) {
    if (mkdir($logs_dir, 0755, true)) {
        echo "<p>✅ Directorio logs creado correctamente</p>";
    } else {
        echo "<p>❌ No se pudo crear el directorio logs</p>";
    }
} else {
    echo "<p>✅ Directorio logs existe</p>";
}

if (is_writable($logs_dir)) {
    echo "<p>✅ Directorio logs es escribible</p>";
} else {
    echo "<p>❌ Directorio logs no es escribible</p>";
}

// 6. Prueba de escritura de logs
echo "<h3>6. Prueba de Escritura de Logs</h3>";
try {
    if (class_exists('App\Services\LoggerService')) {
        $logger = new App\Services\LoggerService();
        $logger->info('Prueba de escritura de logs');
        echo "<p>✅ Escritura de logs exitosa</p>";
    } else {
        echo "<p>❌ Clase LoggerService no disponible</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al escribir logs: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🎯 RESULTADO DE LA PRUEBA</h3>";
echo "<p>Si todas las pruebas muestran ✅, el sistema está funcionando correctamente.</p>";
echo "<p>Si hay ❌, revisa los errores específicos mostrados arriba.</p>";

echo "<p><strong>Próximos pasos:</strong></p>";
echo "<ul>";
echo "<li><a href='ActualizarTablaUsuariosV2.php'>Actualizar Tabla Usuarios</a></li>";
echo "<li><a href='CorregirHashUsuario.php'>Corregir Hash de Usuario</a></li>";
echo "<li><a href='TestLoginControllerOptimizado.php'>Probar LoginController</a></li>";
echo "</ul>";
?>
