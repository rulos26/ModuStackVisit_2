<?php
// Test detallado para diagnosticar el error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Detallado de Error 500</h2>";

// Test 1: Verificar que el archivo procesar_simple.php existe
echo "<h3>1. Verificar Archivo Procesador Simple</h3>";
$archivoProcesador = __DIR__ . '/procesar_simple.php';
if (file_exists($archivoProcesador)) {
    echo "✅ Archivo procesar_simple.php existe<br>";
    echo "Tamaño: " . filesize($archivoProcesador) . " bytes<br>";
    echo "Última modificación: " . date('Y-m-d H:i:s', filemtime($archivoProcesador)) . "<br>";
} else {
    echo "❌ Archivo procesar_simple.php NO existe<br>";
}

// Test 2: Verificar permisos del archivo
echo "<br><h3>2. Verificar Permisos del Archivo</h3>";
if (file_exists($archivoProcesador)) {
    echo "Permisos: " . substr(sprintf('%o', fileperms($archivoProcesador)), -4) . "<br>";
    echo "Legible: " . (is_readable($archivoProcesador) ? "✅ Sí" : "❌ No") . "<br>";
    echo "Ejecutable: " . (is_executable($archivoProcesador) ? "✅ Sí" : "❌ No") . "<br>";
}

// Test 3: Verificar sintaxis PHP
echo "<br><h3>3. Verificar Sintaxis PHP</h3>";
if (file_exists($archivoProcesador)) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($archivoProcesador) . " 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "✅ Sintaxis PHP correcta<br>";
    } else {
        echo "❌ Error de sintaxis PHP:<br>";
        foreach ($output as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
}

// Test 4: Probar conexión directa
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

// Test 5: Simular la llamada al procesador
echo "<br><h3>5. Simular Llamada al Procesador</h3>";
echo "<button onclick='simularLlamada()' class='btn btn-primary'>Simular Llamada</button>";
echo "<div id='resultadoSimulacion' class='mt-3'></div>";

// Test 6: Verificar directorio de logs
echo "<br><h3>6. Verificar Directorio de Logs</h3>";
$directorioLogs = __DIR__ . '/../../logs/';
if (is_dir($directorioLogs)) {
    echo "✅ Directorio logs existe<br>";
    echo "Permisos: " . substr(sprintf('%o', fileperms($directorioLogs)), -4) . "<br>";
    echo "Escribible: " . (is_writable($directorioLogs) ? "✅ Sí" : "❌ No") . "<br>";
} else {
    echo "❌ Directorio logs NO existe<br>";
    echo "Intentando crear directorio...<br>";
    if (mkdir($directorioLogs, 0755, true)) {
        echo "✅ Directorio logs creado exitosamente<br>";
    } else {
        echo "❌ No se pudo crear el directorio logs<br>";
    }
}

// Test 7: Verificar configuración PHP
echo "<br><h3>7. Verificar Configuración PHP</h3>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "Memory limit: " . ini_get('memory_limit') . "<br>";
echo "Max execution time: " . ini_get('max_execution_time') . "<br>";
echo "Error reporting: " . ini_get('error_reporting') . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";
echo "Log errors: " . ini_get('log_errors') . "<br>";
echo "Error log: " . ini_get('error_log') . "<br>";

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>

<script>
function simularLlamada() {
    const resultadoDiv = document.getElementById('resultadoSimulacion');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Simulando llamada al procesador...</div>';
    
    const formData = new FormData();
    formData.append('accion', 'obtener_usuarios_evaluados');
    
    fetch('procesar_simple.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        resultadoDiv.innerHTML += `<div class="alert alert-info">Status: ${response.status} ${response.statusText}</div>`;
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        resultadoDiv.innerHTML += `<div class="alert alert-info">Respuesta recibida (${text.length} caracteres)</div>`;
        
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML += `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML += `<div class="alert alert-success">✅ Éxito. Usuarios: ${data.length}</div>`;
            }
        } catch (parseError) {
            resultadoDiv.innerHTML += `<div class="alert alert-warning">Error parseando JSON: ${parseError.message}</div>`;
            resultadoDiv.innerHTML += `<div class="alert alert-info">Respuesta cruda: <pre>${text.substring(0, 500)}</pre></div>`;
        }
    })
    .catch(error => {
        resultadoDiv.innerHTML += `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}
</script>
