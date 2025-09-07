<?php
// Test directo del procesador simple
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Directo del Procesador Simple</h2>";

// Test 1: Probar obtener usuarios evaluados
echo "<h3>1. Test: Obtener Usuarios Evaluados</h3>";
echo "<button onclick='probarObtenerUsuarios()' class='btn btn-primary'>Probar Obtener Usuarios</button>";
echo "<div id='resultadoUsuarios' class='mt-3'></div>";

// Test 2: Probar verificar tablas con datos
echo "<br><h3>2. Test: Verificar Tablas con Datos</h3>";
echo "<button onclick='probarVerificarTablas()' class='btn btn-primary'>Probar Verificar Tablas</button>";
echo "<div id='resultadoTablas' class='mt-3'></div>";

// Test 3: Test de conexión directa
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
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluados WHERE id_cedula IS NOT NULL");
    $result = $stmt->fetch();
    echo "Usuarios evaluados: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>

<script>
function probarObtenerUsuarios() {
    const resultadoDiv = document.getElementById('resultadoUsuarios');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando obtener usuarios...</div>';
    
    const formData = new FormData();
    formData.append('accion', 'obtener_usuarios_evaluados');
    
    fetch('procesar_simple.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.text(); // Primero obtener como texto
    })
    .then(text => {
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-success">✅ Éxito. Usuarios encontrados: ${data.length}</div>`;
                
                if (data.length > 0) {
                    let tabla = '<table class="table table-sm table-bordered mt-3"><thead><tr><th>ID Cédula</th><th>Nombres</th><th>Apellidos</th></tr></thead><tbody>';
                    data.forEach(usuario => {
                        tabla += `<tr><td>${usuario.id_cedula}</td><td>${usuario.nombres}</td><td>${usuario.apellidos}</td></tr>`;
                    });
                    tabla += '</tbody></table>';
                    resultadoDiv.innerHTML += tabla;
                }
            }
        } catch (parseError) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error parseando JSON: ${parseError.message}<br>Respuesta cruda: ${text}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error de red: ${error.message}</div>`;
    });
}

function probarVerificarTablas() {
    const resultadoDiv = document.getElementById('resultadoTablas');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando verificar tablas...</div>';
    
    const formData = new FormData();
    formData.append('accion', 'verificar_tablas_con_datos');
    formData.append('id_cedula', '1014199434'); // Usar un ID que sabemos que existe
    
    fetch('procesar_simple.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.text(); // Primero obtener como texto
    })
    .then(text => {
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (data.error) {
                resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-success">✅ Éxito. Tablas con datos: ${data.length}</div>`;
                
                if (data.length > 0) {
                    let lista = '<ul class="mt-3">';
                    data.forEach(tabla => {
                        lista += `<li>${tabla}</li>`;
                    });
                    lista += '</ul>';
                    resultadoDiv.innerHTML += lista;
                } else {
                    resultadoDiv.innerHTML += '<p class="mt-3">No se encontraron datos en tablas relacionadas.</p>';
                }
            }
        } catch (parseError) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error parseando JSON: ${parseError.message}<br>Respuesta cruda: ${text}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error de red: ${error.message}</div>`;
    });
}
</script>
