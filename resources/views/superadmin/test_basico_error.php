<?php
// Test básico sin dependencias para diagnosticar error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Básico de Error 500</h2>";

// Test 1: Verificar PHP básico
echo "<h3>1. Test PHP Básico</h3>";
echo "✅ PHP funcionando<br>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "Directorio actual: " . __DIR__ . "<br>";

// Test 2: Verificar sesión
echo "<br><h3>2. Test de Sesión</h3>";
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['rol'] == 3) {
    echo "✅ Sesión válida<br>";
    echo "Usuario ID: " . $_SESSION['user_id'] . "<br>";
    echo "Rol: " . $_SESSION['rol'] . "<br>";
} else {
    echo "❌ Sesión inválida o no es Superadministrador<br>";
    echo "user_id: " . ($_SESSION['user_id'] ?? 'no definido') . "<br>";
    echo "rol: " . ($_SESSION['rol'] ?? 'no definido') . "<br>";
}

// Test 3: Verificar archivos
echo "<br><h3>3. Test de Archivos</h3>";

$archivos = [
    '../../vendor/autoload.php' => 'Autoloader',
    '../../app/Database/Database.php' => 'Database',
    '../../app/Services/LoggerService.php' => 'LoggerService',
    '../../app/Controllers/TablasPrincipalesController.php' => 'TablasPrincipalesController'
];

foreach ($archivos as $archivo => $nombre) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        echo "✅ $nombre existe<br>";
    } else {
        echo "❌ $nombre NO existe en: $ruta<br>";
    }
}

// Test 4: Verificar conexión directa
echo "<br><h3>4. Test de Conexión Directa</h3>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=u130454517_modulo_vista;charset=utf8mb4",
        'u130454517_root',
        '0382646740Ju*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión PDO exitosa<br>";
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluados WHERE id_cedula IS NOT NULL");
    $result = $stmt->fetch();
    echo "Usuarios evaluados: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// Test 5: Verificar permisos de directorio
echo "<br><h3>5. Test de Permisos</h3>";
$directorios = [
    '../../logs' => 'Directorio de logs',
    '../../app' => 'Directorio app',
    '../../vendor' => 'Directorio vendor'
];

foreach ($directorios as $dir => $nombre) {
    $ruta = __DIR__ . '/' . $dir;
    if (is_dir($ruta)) {
        if (is_writable($ruta)) {
            echo "✅ $nombre es escribible<br>";
        } else {
            echo "⚠️ $nombre existe pero no es escribible<br>";
        }
    } else {
        echo "❌ $nombre no existe<br>";
    }
}

// Test 6: Verificar errores de PHP
echo "<br><h3>6. Test de Errores PHP</h3>";
$errorLog = ini_get('error_log');
echo "Log de errores: " . ($errorLog ?: 'No configurado') . "<br>";

$displayErrors = ini_get('display_errors');
echo "Mostrar errores: " . ($displayErrors ? 'Sí' : 'No') . "<br>";

$logErrors = ini_get('log_errors');
echo "Registrar errores: " . ($logErrors ? 'Sí' : 'No') . "<br>";

// Test 7: Probar include simple
echo "<br><h3>7. Test de Include Simple</h3>";
try {
    if (file_exists(__DIR__ . '/../../app/Database/Database.php')) {
        echo "Intentando incluir Database.php...<br>";
        require_once __DIR__ . '/../../app/Database/Database.php';
        echo "✅ Database.php incluido correctamente<br>";
    } else {
        echo "❌ Database.php no encontrado<br>";
    }
} catch (Exception $e) {
    echo "❌ Error incluyendo Database.php: " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "❌ Error fatal incluyendo Database.php: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
