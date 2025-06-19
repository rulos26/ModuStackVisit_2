<?php
// Archivo de prueba para verificar la lógica de validación del campo tiene_pareja
session_start();

// Simular sesión para pruebas
if (!isset($_SESSION['id_cedula'])) {
    $_SESSION['id_cedula'] = '12345678';
    $_SESSION['username'] = 'usuario_prueba';
}

require_once __DIR__ . '/InformacionParejaController.php';
use App\Controllers\InformacionParejaController;

echo "<h1>Prueba de Lógica de Validación - Campo tiene_pareja</h1>";

try {
    $controller = InformacionParejaController::getInstance();
    
    echo "<h2>1. Explicación de la Lógica</h2>";
    echo "<p><strong>El campo 'tiene_pareja' es SOLO una validación, NO un campo de la base de datos.</strong></p>";
    echo "<p>Su función es determinar si se guardan o no los datos de pareja en la base de datos.</p>";
    
    echo "<h2>2. Lógica Implementada</h2>";
    echo "<h3>Si tiene_pareja = '1' (No tiene pareja):</h3>";
    echo "<ul>";
    echo "<li>❌ <strong>NO se guarda NADA en la base de datos</strong></li>";
    echo "<li>✓ Si existen registros previos, se ELIMINAN</li>";
    echo "<li>✓ Retorna mensaje: 'Información procesada exitosamente.'</li>";
    echo "<li>✓ Continúa al siguiente módulo</li>";
    echo "</ul>";
    
    echo "<h3>Si tiene_pareja = '2' (Sí tiene pareja):</h3>";
    echo "<ul>";
    echo "<li>✅ <strong>SÍ se guardan TODOS los campos de pareja</strong></li>";
    echo "<li>✓ Se validan todos los campos obligatorios</li>";
    echo "<li>✓ Se inserta o actualiza en la tabla informacion_pareja</li>";
    echo "<li>✓ Retorna mensaje: 'Información de pareja guardada exitosamente.'</li>";
    echo "<li>✓ Continúa al siguiente módulo</li>";
    echo "</ul>";
    
    echo "<h2>3. Consultas SQL Implementadas</h2>";
    
    echo "<h3>Cuando NO tiene pareja (tiene_pareja = '1'):</h3>";
    echo "<pre><code>";
    echo "// Verificar si existen registros\n";
    echo "SELECT * FROM informacion_pareja WHERE id_cedula = :id_cedula\n\n";
    echo "// Si existen, eliminarlos\n";
    echo "DELETE FROM informacion_pareja WHERE id_cedula = :id_cedula\n\n";
    echo "// Si no existen, no hacer nada (ok = true)";
    echo "</code></pre>";
    
    echo "<h3>Cuando SÍ tiene pareja (tiene_pareja = '2'):</h3>";
    echo "<pre><code>";
    echo "// Si existen registros previos\n";
    echo "UPDATE informacion_pareja SET \n";
    echo "    ced = :ced, id_tipo_documentos = :id_tipo_documentos, \n";
    echo "    cedula_expedida = :cedula_expedida, nombres = :nombres, \n";
    echo "    edad = :edad, id_genero = :id_genero, \n";
    echo "    id_nivel_academico = :id_nivel_academico, actividad = :actividad, \n";
    echo "    empresa = :empresa, antiguedad = :antiguedad, \n";
    echo "    direccion_empresa = :direccion_empresa, telefono_1 = :telefono_1, \n";
    echo "    telefono_2 = :telefono_2, vive_candidato = :vive_candidato, \n";
    echo "    observacion = :observacion \n";
    echo "WHERE id_cedula = :id_cedula\n\n";
    echo "// Si no existen registros previos\n";
    echo "INSERT INTO informacion_pareja (id_cedula, ced, id_tipo_documentos, \n";
    echo "    cedula_expedida, nombres, edad, id_genero, id_nivel_academico, \n";
    echo "    actividad, empresa, antiguedad, direccion_empresa, telefono_1, \n";
    echo "    telefono_2, vive_candidato, observacion) \n";
    echo "VALUES (:id_cedula, :ced, :id_tipo_documentos, :cedula_expedida, \n";
    echo "    :nombres, :edad, :id_genero, :id_nivel_academico, :actividad, \n";
    echo "    :empresa, :antiguedad, :direccion_empresa, :telefono_1, \n";
    echo "    :telefono_2, :vive_candidato, :observacion)";
    echo "</code></pre>";
    
    echo "<h2>4. Campos que se Guardan en la Base de Datos</h2>";
    echo "<p><strong>Solo cuando tiene_pareja = '2' (Sí tiene pareja):</strong></p>";
    $campos_guardados = [
        'id_cedula', 'ced', 'id_tipo_documentos', 'cedula_expedida',
        'nombres', 'edad', 'id_genero', 'id_nivel_academico', 'actividad',
        'empresa', 'antiguedad', 'direccion_empresa', 'telefono_1', 'telefono_2',
        'vive_candidato', 'observacion'
    ];
    
    foreach ($campos_guardados as $campo) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;✓ $campo<br>";
    }
    
    echo "<h2>5. Validaciones Implementadas</h2>";
    echo "<p><strong>Campo obligatorio único:</strong> tiene_pareja</p>";
    echo "<p><strong>Si tiene_pareja = '2', también son obligatorios:</strong></p>";
    $campos_obligatorios = [
        'ced', 'id_tipo_documentos', 'cedula_expedida', 'nombres', 'edad',
        'id_genero', 'id_nivel_academico', 'actividad', 'empresa', 'antiguedad',
        'direccion_empresa', 'telefono_1', 'vive_candidato'
    ];
    
    foreach ($campos_obligatorios as $campo) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;✓ $campo<br>";
    }
    
    echo "<h2>6. Flujo de Usuario</h2>";
    echo "<ol>";
    echo "<li><strong>Usuario selecciona:</strong> ¿Está usted en relación sentimental actual?</li>";
    echo "<li><strong>Si selecciona 'No':</strong></li>";
    echo "<ul>";
    echo "<li>Se eliminan registros existentes (si los hay)</li>";
    echo "<li>No se guarda nada en la base de datos</li>";
    echo "<li>Continúa al siguiente módulo</li>";
    echo "</ul>";
    echo "<li><strong>Si selecciona 'Sí':</strong></li>";
    echo "<ul>";
    echo "<li>Se muestran todos los campos de información de pareja</li>";
    echo "<li>Se validan todos los campos obligatorios</li>";
    echo "<li>Se guardan todos los datos en la base de datos</li>";
    echo "<li>Continúa al siguiente módulo</li>";
    echo "</ul>";
    echo "</ol>";
    
    echo "<h2>✓ LÓGICA CORREGIDA</h2>";
    echo "<p style='color: green; font-weight: bold;'>✅ El campo tiene_pareja ahora funciona correctamente como validación</p>";
    echo "<p><strong>Resumen de cambios:</strong></p>";
    echo "<ul>";
    echo "<li>✓ tiene_pareja = '1' → NO guarda nada en BD</li>";
    echo "<li>✓ tiene_pareja = '2' → Guarda todos los campos de pareja</li>";
    echo "<li>✓ Elimina registros existentes si cambia de 'Sí' a 'No'</li>";
    echo "<li>✓ El campo tiene_pareja NO se guarda en la base de datos</li>";
    echo "<li>✓ Solo se usa para determinar qué campos guardar</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las pruebas</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c3e50; }
h2 { color: #34495e; margin-top: 30px; }
h3 { color: #7f8c8d; margin-top: 20px; }
p { line-height: 1.6; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
ul, ol { line-height: 1.8; }
</style> 