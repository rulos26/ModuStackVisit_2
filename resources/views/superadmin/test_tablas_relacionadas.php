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
    
    // Lista de tablas con sus campos de identificación
    $tablasEsperadas = [
        'autorizaciones' => 'cedula',
        'camara_comercio' => 'id_cedula',
        'composicion_familiar' => 'id_cedula',
        'concepto_final_evaluador' => 'id_cedula',
        'cuentas_bancarias' => 'id_cedula',
        'data_credito' => 'id_cedula',
        'estados_salud' => 'id_cedula',
        'estado_vivienda' => 'id_cedula',
        'estudios' => 'id_cedula',
        'evidencia_fotografica' => 'id_cedula',
        'experiencia_laboral' => 'id_cedula',
        'firmas' => 'id_cedula',
        'foto_perfil_autorizacion' => 'id_cedula',
        'gasto' => 'id_cedula',
        'informacion_judicial' => 'id_cedula',
        'informacion_pareja' => 'id_cedula',
        'ingresos_mensuales' => 'id_cedula',
        'inventario_enseres' => 'id_cedula',
        'pasivos' => 'id_cedula',
        'patrimonio' => 'id_cedula',
        'servicios_publicos' => 'id_cedula',
        'tipo_vivienda' => 'id_cedula',
        'ubicacion' => 'id_cedula',
        'ubicacion_autorizacion' => 'id_cedula',
        'ubicacion_foto' => 'id_cedula',
        'foto_perfil_visita' => 'id_cedula'
    ];
    
    echo "<h3>Verificación de Tablas Esperadas:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Tabla</th><th>Existe</th><th>Registros</th><th>Campo Esperado</th><th>Campo Existe</th></tr>";
    
    $tablasExistentes = [];
    $tablasConCampoCorrecto = [];
    
    foreach ($tablasEsperadas as $tabla => $campoEsperado) {
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
            
            echo "<td>$campoEsperado</td>";
            
            // Verificar si tiene el campo esperado
            $stmt = $pdo->query("SHOW COLUMNS FROM `$tabla` LIKE '$campoEsperado'");
            if ($stmt->rowCount() > 0) {
                echo "<td style='color: green;'>✅ Sí</td>";
                $tablasConCampoCorrecto[] = $tabla;
            } else {
                echo "<td style='color: red;'>❌ No</td>";
            }
        } else {
            echo "<td style='color: red;'>❌ No</td>";
            echo "<td>-</td>";
            echo "<td>$campoEsperado</td>";
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>Resumen:</h3>";
    echo "Tablas esperadas: " . count($tablasEsperadas) . "<br>";
    echo "Tablas existentes: " . count($tablasExistentes) . "<br>";
    echo "Tablas con campo correcto: " . count($tablasConCampoCorrecto) . "<br>";
    
    echo "<br><h3>Tablas que SÍ existen y tienen el campo correcto:</h3>";
    if (count($tablasConCampoCorrecto) > 0) {
        foreach ($tablasConCampoCorrecto as $tabla) {
            echo "• " . $tabla . " (campo: " . $tablasEsperadas[$tabla] . ")<br>";
        }
    } else {
        echo "Ninguna tabla tiene el campo correcto.<br>";
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
        echo "<tr><th>Tabla</th><th>Campo</th><th>Registros para este usuario</th></tr>";
        
        foreach ($tablasConCampoCorrecto as $tabla) {
            echo "<tr>";
            echo "<td>$tabla</td>";
            echo "<td>" . $tablasEsperadas[$tabla] . "</td>";
            
            try {
                $campo = $tablasEsperadas[$tabla];
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$tabla` WHERE `$campo` = ?");
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
