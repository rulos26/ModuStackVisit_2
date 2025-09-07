<?php
// Test de funcionalidad específica
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test de Funcionalidad</h2>";

// Test 1: Probar el procesador simple
echo "<h3>1. Test del Procesador Simple</h3>";
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
    
    // Probar consulta de usuarios evaluados
    $stmt = $pdo->query("SELECT id_cedula, nombres, apellidos FROM evaluados WHERE id_cedula IS NOT NULL ORDER BY nombres, apellidos");
    $usuarios = $stmt->fetchAll();
    
    echo "✅ Consulta de usuarios exitosa<br>";
    echo "Total de usuarios encontrados: " . count($usuarios) . "<br><br>";
    
    if (count($usuarios) > 0) {
        echo "<h4>Usuarios encontrados:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID Cédula</th><th>Nombres</th><th>Apellidos</th></tr>";
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id_cedula'] . "</td>";
            echo "<td>" . $usuario['nombres'] . "</td>";
            echo "<td>" . $usuario['apellidos'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Probar verificación de tablas con datos para el primer usuario
        if (count($usuarios) > 0) {
            $primerUsuario = $usuarios[0];
            $idCedula = $primerUsuario['id_cedula'];
            
            echo "<br><h4>Test de verificación de tablas para usuario: " . $primerUsuario['nombres'] . " " . $primerUsuario['apellidos'] . " (ID: $idCedula)</h4>";
            
            $tablasRelacionadas = [
                'autorizaciones', 'camara_comercio', 'composicion_familiar', 'concepto_final_evaluador',
                'cuentas_bancarias', 'data_credito', 'estados_salud', 'estado_vivienda', 'estudios',
                'evidencia_fotografica', 'experiencia_laboral', 'firmas', 'foto_perfil_autorizacion',
                'gasto', 'informacion_judicial', 'informacion_pareja', 'ingresos_mensuales',
                'inventario_enseres', 'pasivos', 'patrimonio', 'servicios_publicos', 'tipo_vivienda',
                'ubicacion', 'ubicacion_autorizacion', 'ubicacion_foto', 'foto_perfil_visita'
            ];
            
            $tablasConDatos = [];
            
            // Verificar en evaluados
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM evaluados WHERE id_cedula = ?");
            $stmt->execute([$idCedula]);
            $result = $stmt->fetch();
            if ($result['count'] > 0) {
                $tablasConDatos[] = 'evaluados';
            }
            
            // Verificar en tablas relacionadas
            foreach ($tablasRelacionadas as $tabla) {
                // Verificar si la tabla existe
                $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                if ($stmt->rowCount() > 0) {
                    // Verificar si tiene datos para este id_cedula
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $tabla WHERE id_cedula = ?");
                    $stmt->execute([$idCedula]);
                    $result = $stmt->fetch();
                    if ($result['count'] > 0) {
                        $tablasConDatos[] = $tabla;
                    }
                }
            }
            
            echo "Tablas con datos para este usuario:<br>";
            if (count($tablasConDatos) > 0) {
                foreach ($tablasConDatos as $tabla) {
                    echo "• " . $tabla . "<br>";
                }
            } else {
                echo "No se encontraron datos en tablas relacionadas.<br>";
            }
        }
    } else {
        echo "No hay usuarios evaluados en la base de datos.<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Probar AJAX del procesador simple
echo "<br><h3>2. Test de AJAX</h3>";
echo "<button onclick='probarAjax()' class='btn btn-primary'>Probar AJAX</button>";
echo "<div id='resultadoAjax' class='mt-3'></div>";

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
echo "<br><a href='gestion_tablas_simple.php'>Ir a Versión Simple</a>";
?>

<script>
function probarAjax() {
    const resultadoDiv = document.getElementById('resultadoAjax');
    resultadoDiv.innerHTML = '<div class="alert alert-info">Probando AJAX...</div>';
    
    fetch('procesar_simple.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'accion=obtener_usuarios_evaluados'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
        } else {
            resultadoDiv.innerHTML = `<div class="alert alert-success">✅ AJAX funcionando correctamente. Usuarios encontrados: ${data.length}</div>`;
            
            if (data.length > 0) {
                let tabla = '<table class="table table-sm table-bordered mt-3"><thead><tr><th>ID Cédula</th><th>Nombres</th><th>Apellidos</th></tr></thead><tbody>';
                data.forEach(usuario => {
                    tabla += `<tr><td>${usuario.id_cedula}</td><td>${usuario.nombres}</td><td>${usuario.apellidos}</td></tr>`;
                });
                tabla += '</tbody></table>';
                resultadoDiv.innerHTML += tabla;
            }
        }
    })
    .catch(error => {
        resultadoDiv.innerHTML = `<div class="alert alert-danger">Error de AJAX: ${error.message}</div>`;
    });
}
</script>
