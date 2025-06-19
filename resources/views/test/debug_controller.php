<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Controlador Refactorizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .section h2 {
            color: #333;
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }
        .variable {
            background-color: #e9ecef;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
            font-family: monospace;
        }
        .array-data {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .html-preview {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
        }
        textarea {
            width: 100%;
            height: 300px;
            font-family: monospace;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            resize: vertical;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .architecture {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
        }
        .architecture h3 {
            color: #0056b3;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Controlador Refactorizado - MVC</h1>
        
        <div class="section architecture">
            <h3>🏗️ Arquitectura Refactorizada</h3>
            <p><strong>Separación de Responsabilidades:</strong></p>
            <ul>
                <li><strong>Modelo:</strong> Lógica de datos y negocio en la clase</li>
                <li><strong>Vista:</strong> Plantillas HTML separadas</li>
                <li><strong>Controlador:</strong> Orquesta la generación del PDF</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>📊 Datos del Controlador</h2>
            <div class="array-data">
                <pre><?= htmlspecialchars(print_r($data, true)) ?></pre>
            </div>
        </div>
        
        <div class="section">
            <h2>🔍 Variables Individuales</h2>
            <div class="variable">
                <strong>Cédula:</strong> <?= htmlspecialchars($data['cedula']) ?>
            </div>
            <div class="variable">
                <strong>Logo Base64:</strong> <?= empty($data['logo_b64']) ? 'VACÍO' : 'CON DATOS (' . strlen($data['logo_b64']) . ' caracteres)' ?>
            </div>
        </div>
        
        <div class="section">
            <h2>📄 HTML Generado</h2>
            <div class="html-preview">
                <textarea readonly><?= htmlspecialchars($html) ?></textarea>
            </div>
        </div>
        
        <div class="section">
            <h2>🎯 Acciones Disponibles</h2>
            <a href="InformeFinalPdfController.php?action=Informefinalpdf" class="btn btn-success">
                🚀 Generar PDF Final
            </a>
            <a href="test_pdf_final.php" class="btn btn-warning">
                📋 Test PDF Simple
            </a>
            <a href="test_informe_pdf.php" class="btn">
                🏠 Volver al Menú
            </a>
        </div>
        
        <div class="section">
            <h2>📁 Estructura de Archivos</h2>
            <div class="variable">
                <strong>Controlador:</strong> app/Controllers/InformeFinalPdfController.php
            </div>
            <div class="variable">
                <strong>Plantilla PDF:</strong> resources/views/pdf/informe_final/plantilla_pdf.php
            </div>
            <div class="variable">
                <strong>Vista Debug:</strong> resources/views/test/debug_controller.php
            </div>
            <div class="variable">
                <strong>Test Simple:</strong> app/Controllers/test_plantilla_simple.php
            </div>
        </div>
        
        <div class="section">
            <h2>✅ Ventajas de la Refactorización</h2>
            <ul>
                <li><strong>Separación de Lógica:</strong> PHP y HTML están separados</li>
                <li><strong>Reutilización:</strong> Métodos independientes para cada función</li>
                <li><strong>Mantenibilidad:</strong> Código más organizado y fácil de mantener</li>
                <li><strong>Debugging:</strong> Fácil acceso a datos y HTML para testing</li>
                <li><strong>Escalabilidad:</strong> Fácil agregar nuevas funcionalidades</li>
            </ul>
        </div>
    </div>
</body>
</html> 