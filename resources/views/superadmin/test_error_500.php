<?php
// Test específico para diagnosticar error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test de Error 500 - Diagnóstico</h2>";

// Test 1: Verificar si las clases se cargan
echo "<h3>1. Test de Carga de Clases</h3>";

try {
    echo "Probando autoloader...<br>";
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        echo "✅ Autoloader cargado<br>";
    } else {
        echo "❌ Autoloader no encontrado<br>";
    }
    
    echo "Probando Database...<br>";
    require_once __DIR__ . '/../../app/Database/Database.php';
    use App\Database\Database;
    $db = Database::getInstance();
    echo "✅ Database cargado<br>";
    
    echo "Probando LoggerService...<br>";
    require_once __DIR__ . '/../../app/Services/LoggerService.php';
    use App\Services\LoggerService;
    $logger = new LoggerService();
    echo "✅ LoggerService cargado<br>";
    
    echo "Probando TablasPrincipalesController...<br>";
    require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
    use App\Controllers\TablasPrincipalesController;
    $controller = new TablasPrincipalesController();
    echo "✅ TablasPrincipalesController cargado<br>";
    
} catch (Exception $e) {
    echo "❌ Error cargando clases: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

// Test 2: Probar métodos del controlador
echo "<br><h3>2. Test de Métodos del Controlador</h3>";

if (isset($controller)) {
    try {
        echo "Probando obtenerUsuariosEvaluados()...<br>";
        $resultado = $controller->obtenerUsuariosEvaluados();
        if (isset($resultado['error'])) {
            echo "❌ Error en obtenerUsuariosEvaluados: " . $resultado['error'] . "<br>";
        } else {
            echo "✅ obtenerUsuariosEvaluados funciona. Usuarios: " . count($resultado) . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en obtenerUsuariosEvaluados: " . $e->getMessage() . "<br>";
    }
    
    try {
        echo "Probando verificarTablasConDatos()...<br>";
        $resultado = $controller->verificarTablasConDatos(1014199434);
        if (isset($resultado['error'])) {
            echo "❌ Error en verificarTablasConDatos: " . $resultado['error'] . "<br>";
        } else {
            echo "✅ verificarTablasConDatos funciona. Tablas: " . count($resultado) . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en verificarTablasConDatos: " . $e->getMessage() . "<br>";
    }
}

// Test 3: Simular el procesador principal
echo "<br><h3>3. Test del Procesador Principal</h3>";

echo "<button onclick='probarProcesadorPrincipal()' class='btn btn-primary'>Probar Procesador Principal</button>";
echo "<div id='resultadoProcesador' class='mt-3'></div>";

// Test 4: Verificar logs de errores
echo "<br><h3>4. Verificar Logs de Errores</h3>";

$logFile = __DIR__ . '/../../logs/php_errors.log';
if (file_exists($logFile)) {
    echo "✅ Archivo de log existe<br>";
    $logs = file_get_contents($logFile);
    if (!empty($logs)) {
        echo "Últimas líneas del log:<br>";
        $lines = explode("\n", $logs);
        $lastLines = array_slice($lines, -10);
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
        foreach ($lastLines as $line) {
            if (!empty(trim($line))) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre>";
    } else {
        echo "Log vacío<br>";
    }
} else {
    echo "❌ Archivo de log no existe<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>

<script>
function probarProcesadorPrincipal() {
    const resultadoDiv = document.getElementById('resultadoProcesador');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando procesador principal...</div>';
    
    const formData = new FormData();
    formData.append('accion', 'verificar_tablas_con_datos');
    formData.append('id_cedula', '1014199434');
    
    fetch('procesar_tablas_principales.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-success">✅ Éxito. Tablas: ${data.length || Object.keys(data).length}</div>`;
            }
        } catch (parseError) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error parseando JSON: ${parseError.message}<br>Respuesta cruda: ${text}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}
</script>
