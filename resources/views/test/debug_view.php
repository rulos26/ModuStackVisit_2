<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test de Plantilla Simple - Debug</title>
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
            border-bottom: 2px solid #007bff;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Test de Plantilla Simple - Debug</h1>
        
        <div class="section">
            <h2>Variables Definidas</h2>
            <div class="variable">
                <strong>Cédula:</strong> <?= htmlspecialchars($variables['cedula']) ?>
            </div>
            <div class="variable">
                <strong>Logo:</strong> <?= htmlspecialchars($variables['logo_b64']) ?>
            </div>
        </div>
        
        <div class="section">
            <h2>Array de Datos</h2>
            <div class="array-data">
                <pre><?= htmlspecialchars(print_r($data, true)) ?></pre>
            </div>
        </div>
        
        <div class="section">
            <h2>Variables Después del Extract</h2>
            <div class="variable">
                <strong>Cédula después extract:</strong> <?= htmlspecialchars($extractedVars['cedula']) ?>
            </div>
            <div class="variable">
                <strong>Logo_b64 después extract:</strong> <?= htmlspecialchars($extractedVars['logo_b64']) ?>
            </div>
        </div>
        
        <div class="section">
            <h2>HTML Generado</h2>
            <div class="html-preview">
                <textarea readonly><?= htmlspecialchars($html) ?></textarea>
            </div>
        </div>
        
        <div class="section">
            <h2>Acciones</h2>
            <a href="test_pdf_final.php" class="btn btn-success">Generar PDF Final</a>
            <a href="test_informe_pdf.php" class="btn">Volver al Menú Principal</a>
        </div>
        
        <div class="section">
            <h2>Información del Sistema</h2>
            <div class="variable">
                <strong>PHP Version:</strong> <?= phpversion() ?>
            </div>
            <div class="variable">
                <strong>Dompdf Version:</strong> <?= class_exists('Dompdf\Dompdf') ? 'Disponible' : 'No disponible' ?>
            </div>
            <div class="variable">
                <strong>Ruta actual:</strong> <?= __DIR__ ?>
            </div>
        </div>
    </div>
</body>
</html> 