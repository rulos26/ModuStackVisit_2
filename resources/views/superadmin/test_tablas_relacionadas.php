<?php
// Test de tablas relacionadas
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test de Tablas Relacionadas</h2>";

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
    
    echo "✅ Conexión PDO exitosa<br><br>";
    
    // Lista de tablas que deberían existir según el código
    $tablasEsperadas = [
        'autorizaciones', 'camara_comercio', 'composicion_familiar', 'concepto_final_evaluador',
        'cuentas_bancarias', 'data_credito', 'estados_salud', 'estado_vivienda', 'estudios',
        'evidencia_fotografica', 'experiencia_laboral', 'firmas', 'foto_perfil_autorizacion',
        'gasto', 'informacion_judicial', 'informacion_pareja', 'ingresos_mensuales',
        'inventario_enseres', 'pasivos', 'patrimonio', 'servicios_publicos', 'tipo_vivienda',
        'ubicacion', 'ubicacion_autorizacion', 'ubicacion_foto', 'foto_perfil_visita'
    ];
    
    echo "<h3>Verificación de Tablas Esperadas:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Tabla</th><th>Existe</th><th>Registros</th><th>Columna id_cedula</th></tr>";
    
    $tablasExistentes = [];
    $tablasConIdCedula = [];
    
    foreach ($tablasEsperadas as $tabla) {
        echo "<tr>";
        echo "<td>$tabla</td>";
        
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "<td style='color: green;'>✅ Sí</td>";
            $tablasExistentes[] = $tabla;
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$tabla`");
            $result = $stmt->fetch();
            echo "<td>" . $result['count'] . "</td>";
            
            // Verificar si tiene columna id_cedula
            $stmt = $pdo->query("SHOW COLUMNS FROM `$tabla` LIKE 'id_cedula'");
            if ($stmt->rowCount() > 0) {
                echo "<td style='color: green;'>✅ Sí</td>";
                $tablasConIdCedula[] = $tabla;
            } else {
                echo "<td style='color: red;'>❌ No</td>";
            }
        } else {
            echo "<td style='color: red;'>❌ No</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>Resumen:</h3>";
    echo "Tablas esperadas: " . count($tablasEsperadas) . "<br>";
    echo "Tablas existentes: " . count($tablasExistentes) . "<br>";
    echo "Tablas con columna id_cedula: " . count($tablasConIdCedula) . "<br>";
    
    echo "<br><h3>Tablas que SÍ existen y tienen id_cedula:</h3>";
    if (count($tablasConIdCedula) > 0) {
        foreach ($tablasConIdCedula as $tabla) {
            echo "• " . $tabla . "<br>";
        }
    } else {
        echo "Ninguna tabla tiene la columna id_cedula.<br>";
    }
    
    echo "<br><h3>Tablas que NO existen:</h3>";
    $tablasNoExistentes = array_diff($tablasEsperadas, $tablasExistentes);
    if (count($tablasNoExistentes) > 0) {
        foreach ($tablasNoExistentes as $tabla) {
            echo "• " . $tabla . "<br>";
        }
    } else {
        echo "Todas las tablas esperadas existen.<br>";
    }
    
    // Test con un usuario específico
    echo "<br><h3>Test con Usuario Específico:</h3>";
    $stmt = $pdo->query("SELECT id_cedula, nombres, apellidos FROM evaluados WHERE id_cedula IS NOT NULL LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $idCedula = $usuario['id_cedula'];
        echo "Probando con usuario: " . $usuario['nombres'] . " " . $usuario['apellidos'] . " (ID: $idCedula)<br><br>";
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Tabla</th><th>Registros para este usuario</th></tr>";
        
        foreach ($tablasConIdCedula as $tabla) {
            echo "<tr>";
            echo "<td>$tabla</td>";
            
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$tabla` WHERE id_cedula = ?");
                $stmt->execute([$idCedula]);
                $result = $stmt->fetch();
                echo "<td>" . $result['count'] . "</td>";
            } catch (Exception $e) {
                echo "<td style='color: red;'>Error: " . $e->getMessage() . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
echo "<br><a href='gestion_tablas_simple.php'>Ir a Versión Simple</a>";
?>
