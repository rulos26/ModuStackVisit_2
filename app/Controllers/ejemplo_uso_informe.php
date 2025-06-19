<?php
/**
 * EJEMPLO DE USO DEL CONTROLADOR InformeFinalPdfController
 * Muestra cómo usar el controlador para generar informes PDF
 * 
 * @author Sistema de Informes
 * @version 3.0
 * @date 2024
 */

// Incluir el controlador
require_once 'InformeFinalPdfController.php';

use App\Controllers\InformeFinalPdfController;

// Ejemplo de uso básico
try {
    // Crear instancia del controlador
    $controlador = new InformeFinalPdfController();
    
    // Generar el informe PDF
    $controlador->generarInforme();
    
    echo "Informe generado exitosamente!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo de Uso - Controlador Informe PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .method {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 15px 0;
        }
        .method h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1976d2;
        }
        .btn-success {
            background: #4caf50;
        }
        .btn-success:hover {
            background: #388e3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Controlador InformeFinalPdfController</h1>
            <p>Ejemplo de uso y documentación del controlador</p>
        </div>
        
        <h2>📋 Descripción</h2>
        <p>El <strong>InformeFinalPdfController</strong> es un controlador que maneja toda la lógica de negocio para la generación de informes PDF de visita domiciliaria.</p>
        
        <h2>🔧 Características Principales</h2>
        <ul>
            <li><strong>Validación de Sesión:</strong> Verifica automáticamente la autenticación del usuario</li>
            <li><strong>Manejo de Datos:</strong> Obtiene datos de todos los módulos de la base de datos</li>
            <li><strong>Generación de PDF:</strong> Integra con el sistema modularizado para generar PDFs</li>
            <li><strong>Logging:</strong> Registra todas las operaciones y errores</li>
            <li><strong>Estadísticas:</strong> Proporciona métricas sobre los datos obtenidos</li>
        </ul>
        
        <h2>💻 Uso Básico</h2>
        <div class="code-block">
&lt;?php
// Incluir el controlador
require_once 'app/Controllers/InformeFinalPdfController.php';

use App\Controllers\InformeFinalPdfController;

try {
    // Crear instancia del controlador
    $controlador = new InformeFinalPdfController();
    
    // Generar el informe PDF
    $controlador->generarInforme();
    
    echo "Informe generado exitosamente!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?&gt;
        </div>
        
        <h2>📊 Métodos Disponibles</h2>
        
        <div class="method">
            <h4>🔹 generarInforme()</h4>
            <p><strong>Descripción:</strong> Método principal que genera el informe PDF completo</p>
            <p><strong>Uso:</strong> $controlador->generarInforme();</p>
            <p><strong>Retorna:</strong> Genera y muestra el PDF en el navegador</p>
        </div>
        
        <div class="method">
            <h4>🔹 obtenerEstadisticas()</h4>
            <p><strong>Descripción:</strong> Obtiene estadísticas sobre los datos del informe</p>
            <p><strong>Uso:</strong> $estadisticas = $controlador->obtenerEstadisticas();</p>
            <p><strong>Retorna:</strong> Array con métricas de los módulos</p>
        </div>
        
        <div class="method">
            <h4>🔹 validarPermisos()</h4>
            <p><strong>Descripción:</strong> Valida si el usuario tiene permisos para generar informes</p>
            <p><strong>Uso:</strong> $tienePermisos = $controlador->validarPermisos();</p>
            <p><strong>Retorna:</strong> Boolean (true/false)</p>
        </div>
        
        <h2>📈 Ejemplo con Estadísticas</h2>
        <div class="code-block">
&lt;?php
$controlador = new InformeFinalPdfController();

// Obtener estadísticas antes de generar el informe
$estadisticas = $controlador->obtenerEstadisticas();

echo "Total de módulos: " . $estadisticas['total_modulos'] . "\n";
echo "Módulos con datos: " . $estadisticas['modulos_con_datos'] . "\n";
echo "Módulos vacíos: " . $estadisticas['modulos_vacios'] . "\n";
echo "Fecha: " . $estadisticas['fecha_generacion'] . "\n";

// Generar el informe
$controlador->generarInforme();
?&gt;
        </div>
        
        <h2>🔗 Integración con el Menú</h2>
        <p>Para integrar el controlador con el menú principal, puedes crear un enlace que llame al controlador:</p>
        
        <div class="code-block">
&lt;a href="app/Controllers/ejemplo_uso_informe.php" class="btn btn-success"&gt;
    📄 Generar Informe con Controlador
&lt;/a&gt;
        </div>
        
        <h2>⚠️ Consideraciones Importantes</h2>
        <ul>
            <li><strong>Sesión Activa:</strong> El usuario debe estar autenticado</li>
            <li><strong>Datos Requeridos:</strong> Debe existir información del evaluado en la base de datos</li>
            <li><strong>Permisos:</strong> El usuario debe tener permisos para generar informes</li>
            <li><strong>Memoria:</strong> Para informes grandes, considerar límites de memoria PHP</li>
        </ul>
        
        <h2>🚀 Próximos Pasos</h2>
        <p>Para usar el controlador en producción:</p>
        <ol>
            <li>Integrar con el sistema de rutas</li>
            <li>Agregar validación de permisos específicos</li>
            <li>Implementar caché para mejorar rendimiento</li>
            <li>Agregar notificaciones de éxito/error</li>
        </ol>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="menu_principal.php" class="btn">🏠 Volver al Menú Principal</a>
            <a href="InformeModular.php" class="btn btn-success">🚀 Probar Sistema Modular</a>
        </div>
    </div>
</body>
</html> 