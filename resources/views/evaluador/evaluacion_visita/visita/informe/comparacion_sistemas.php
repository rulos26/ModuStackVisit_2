<?php
/**
 * COMPARACIÓN DE SISTEMAS
 * Muestra las diferencias entre el sistema original y el modularizado
 */

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../../public/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparación de Sistemas - Informes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        
        .system {
            padding: 30px;
            position: relative;
        }
        
        .system.original {
            background: #fffbf0;
            border-right: 2px solid #e9ecef;
        }
        
        .system.modular {
            background: #f8fff9;
        }
        
        .system-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .system-title {
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .system.original .system-title {
            color: #856404;
        }
        
        .system.modular .system-title {
            color: #155724;
        }
        
        .system-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .badge-legacy {
            background: #ffc107;
            color: #333;
        }
        
        .badge-modular {
            background: #28a745;
            color: white;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            padding-left: 30px;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list li:before {
            position: absolute;
            left: 0;
            top: 12px;
            font-size: 1.2em;
        }
        
        .system.original .feature-list li:before {
            content: "⚠️";
        }
        
        .system.modular .feature-list li:before {
            content: "✅";
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .feature-description {
            font-size: 0.9em;
            color: #666;
            line-height: 1.4;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .stats h4 {
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            font-weight: 500;
            color: #555;
        }
        
        .stat-value {
            font-weight: 600;
        }
        
        .system.original .stat-value {
            color: #856404;
        }
        
        .system.modular .stat-value {
            color: #155724;
        }
        
        .action-buttons {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-top: 2px solid #e9ecef;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border-color: #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .comparison {
                grid-template-columns: 1fr;
            }
            
            .system.original {
                border-right: none;
                border-bottom: 2px solid #e9ecef;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Comparación de Sistemas</h1>
            <p>Análisis detallado entre el sistema original y el modularizado</p>
        </div>
        
        <div class="comparison">
            <div class="system original">
                <div class="system-header">
                    <div class="system-badge badge-legacy">SISTEMA ORIGINAL</div>
                    <div class="system-title">Versión 2.0</div>
                    <p>Sistema actual en producción</p>
                </div>
                
                <ul class="feature-list">
                    <li>
                        <div class="feature-title">Estructura de Código</div>
                        <div class="feature-description">Archivo monolítico de 655 líneas con lógica mezclada</div>
                    </li>
                    <li>
                        <div class="feature-title">Manejo de Errores</div>
                        <div class="feature-description">Warnings de TCPDF por datos nulos y arrays vacíos</div>
                    </li>
                    <li>
                        <div class="feature-title">Validación de Datos</div>
                        <div class="feature-description">Validación básica, datos nulos pueden causar errores</div>
                    </li>
                    <li>
                        <div class="feature-title">Mantenibilidad</div>
                        <div class="feature-description">Difícil de mantener y modificar, código duplicado</div>
                    </li>
                    <li>
                        <div class="feature-title">Escalabilidad</div>
                        <div class="feature-description">Limitada, agregar nuevas secciones requiere modificar archivo principal</div>
                    </li>
                    <li>
                        <div class="feature-title">Debugging</div>
                        <div class="feature-description">Complejo, errores difíciles de aislar</div>
                    </li>
                    <li>
                        <div class="feature-title">Reutilización</div>
                        <div class="feature-description">Código no reutilizable, lógica acoplada</div>
                    </li>
                </ul>
                
                <div class="stats">
                    <h4>📈 Estadísticas</h4>
                    <div class="stat-item">
                        <span class="stat-label">Líneas de código:</span>
                        <span class="stat-value">655</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Archivos:</span>
                        <span class="stat-value">1 principal</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Warnings TCPDF:</span>
                        <span class="stat-value">Múltiples</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Mantenibilidad:</span>
                        <span class="stat-value">Baja</span>
                    </div>
                </div>
            </div>
            
            <div class="system modular">
                <div class="system-header">
                    <div class="system-badge badge-modular">SISTEMA MODULAR</div>
                    <div class="system-title">Versión 3.0</div>
                    <p>Nueva versión completamente reescrita</p>
                </div>
                
                <ul class="feature-list">
                    <li>
                        <div class="feature-title">Estructura de Código</div>
                        <div class="feature-description">Módulos independientes con herencia y reutilización</div>
                    </li>
                    <li>
                        <div class="feature-title">Manejo de Errores</div>
                        <div class="feature-description">Cero warnings, validación completa de datos</div>
                    </li>
                    <li>
                        <div class="feature-title">Validación de Datos</div>
                        <div class="feature-description">Validación robusta con valores por defecto</div>
                    </li>
                    <li>
                        <div class="feature-title">Mantenibilidad</div>
                        <div class="feature-description">Alta, cada módulo es independiente y fácil de modificar</div>
                    </li>
                    <li>
                        <div class="feature-title">Escalabilidad</div>
                        <div class="feature-description">Excelente, fácil agregar nuevos módulos</div>
                    </li>
                    <li>
                        <div class="feature-title">Debugging</div>
                        <div class="feature-description">Simple, errores aislados por módulo</div>
                    </li>
                    <li>
                        <div class="feature-title">Reutilización</div>
                        <div class="feature-description">Máxima, módulos reutilizables en otros proyectos</div>
                    </li>
                </ul>
                
                <div class="stats">
                    <h4>📈 Estadísticas</h4>
                    <div class="stat-item">
                        <span class="stat-label">Líneas de código:</span>
                        <span class="stat-value">Distribuidas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Archivos:</span>
                        <span class="stat-value">8 modulares</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Warnings TCPDF:</span>
                        <span class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Mantenibilidad:</span>
                        <span class="stat-value">Alta</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="menu_principal.php" class="btn btn-back">← Volver al Menú</a>
            <a href="InformeModular.php" class="btn btn-primary">🚀 Probar Sistema Modular</a>
            <a href="index.php" class="btn btn-secondary">📄 Ver Sistema Original</a>
        </div>
    </div>
</body>
</html> 