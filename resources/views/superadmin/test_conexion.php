<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test de Conexión y Clases</h2>";

// Test 1: Verificar autoloader
echo "<h3>1. Verificando Autoloader</h3>";
try {
    require_once __DIR__ . '/../../vendor/autoload.php';
    echo "✅ Autoloader cargado correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error cargando autoloader: " . $e->getMessage() . "<br>";
}

// Test 2: Verificar Database
echo "<h3>2. Verificando Clase Database</h3>";
try {
    require_once __DIR__ . '/../../app/Database/Database.php';
    use App\Database\Database;
    echo "✅ Clase Database cargada correctamente<br>";
    
    $db = Database::getInstance();
    echo "✅ Instancia de Database creada<br>";
    
    $connection = $db->getConnection();
    echo "✅ Conexión obtenida<br>";
    
} catch (Exception $e) {
    echo "❌ Error con Database: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

// Test 3: Verificar LoggerService
echo "<h3>3. Verificando LoggerService</h3>";
try {
    require_once __DIR__ . '/../../app/Services/LoggerService.php';
    use App\Services\LoggerService;
    echo "✅ Clase LoggerService cargada correctamente<br>";
    
    $logger = new LoggerService();
    echo "✅ Instancia de LoggerService creada<br>";
    
} catch (Exception $e) {
    echo "❌ Error con LoggerService: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

// Test 4: Verificar TablasPrincipalesController
echo "<h3>4. Verificando TablasPrincipalesController</h3>";
try {
    require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
    use App\Controllers\TablasPrincipalesController;
    echo "✅ Clase TablasPrincipalesController cargada correctamente<br>";
    
    $controller = new TablasPrincipalesController();
    echo "✅ Instancia de TablasPrincipalesController creada<br>";
    
} catch (Exception $e) {
    echo "❌ Error con TablasPrincipalesController: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

// Test 5: Verificar conexión a base de datos
echo "<h3>5. Verificando Conexión a Base de Datos</h3>";
try {
    $stmt = $connection->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Conexión a base de datos exitosa<br>";
    echo "Base de datos: " . $info['db_name'] . "<br>";
    echo "Usuario: " . $info['user_name'] . "<br>";
    echo "Versión MySQL: " . $info['version'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión a base de datos: " . $e->getMessage() . "<br>";
}

// Test 6: Verificar tablas
echo "<h3>6. Verificando Tablas</h3>";
try {
    $stmt = $connection->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tablas obtenidas: " . count($tablas) . "<br>";
    
    foreach ($tablas as $tabla) {
        echo "• " . $tabla . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error obteniendo tablas: " . $e->getMessage() . "<br>";
}

// Test 7: Probar método del controlador
echo "<h3>7. Probando Método del Controlador</h3>";
try {
    if (isset($controller)) {
        $resultado = $controller->obtenerUsuariosEvaluados();
        echo "✅ Método ejecutado correctamente<br>";
        
        if (isset($resultado['error'])) {
            echo "⚠️ Error en el método: " . $resultado['error'] . "<br>";
        } else {
            echo "✅ Resultado: " . count($resultado) . " usuarios encontrados<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error ejecutando método: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
