<?php
// Test final para verificar que todo funciona
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Final - Verificación Completa</h2>";

// Test 1: Verificar que el procesador simple funciona
echo "<h3>1. Test del Procesador Simple</h3>";
echo "<button onclick='probarProcesadorSimple()' class='btn btn-primary'>Probar Procesador Simple</button>";
echo "<div id='resultadoProcesador' class='mt-3'></div>";

// Test 2: Verificar que la versión original funciona
echo "<br><h3>2. Test de la Versión Original</h3>";
echo "<button onclick='probarVersionOriginal()' class='btn btn-success'>Probar Versión Original</button>";
echo "<div id='resultadoOriginal' class='mt-3'></div>";

// Test 3: Verificar conexión directa
echo "<br><h3>3. Test de Conexión Directa</h3>";
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
    
    // Probar consulta de usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluados WHERE id_cedula IS NOT NULL");
    $result = $stmt->fetch();
    echo "Usuarios evaluados: " . $result['total'] . "<br>";
    
    // Probar verificación de tablas
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM autorizaciones WHERE cedula = ?");
    $stmt->execute([1014199434]);
    $result = $stmt->fetch();
    echo "Registros en autorizaciones para usuario 1014199434: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
echo "<br><a href='gestion_tablas_simple.php'>Ir a Versión Simple</a>";
?>

<script>
function probarProcesadorSimple() {
    const resultadoDiv = document.getElementById('resultadoProcesador');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando procesador simple...</div>';
    
    const formData = new FormData();
    formData.append('accion', 'obtener_usuarios_evaluados');
    
    fetch('procesar_simple.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-success">✅ Procesador Simple funciona. Usuarios: ${data.length}</div>`;
            }
        } catch (parseError) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error parseando JSON: ${parseError.message}</div>`;
        }
    })
    .catch(error => {
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

function probarVersionOriginal() {
    const resultadoDiv = document.getElementById('resultadoOriginal');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando versión original...</div>';
    
    // Simular lo que hace la versión original
    const formData = new FormData();
    formData.append('accion', 'obtener_usuarios_evaluados');
    
    fetch('procesar_simple.php', { // Ahora usa el procesador simple directamente
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-success">✅ Versión Original funciona. Usuarios: ${data.length}</div>`;
            }
        } catch (parseError) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error parseando JSON: ${parseError.message}</div>`;
        }
    })
    .catch(error => {
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}
</script>
