<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Final</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            border: 2px solid rgb(175, 0, 0);
            padding: 12px;
            margin-bottom: 20px;
        }
        .logo-container {
            border: 1px solid rgb(175,0,0);
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 100%;
            height: auto;
            max-height: 103px;
        }
        .content {
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .cedula-info {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .debug-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <?php if (!empty($logo_b64)): ?>
                <img src="<?= $logo_b64 ?>" alt="Logo" class="logo">
            <?php else: ?>
                <div class="debug-info">
                    <strong>Logo no disponible</strong><br>
                    Ruta del logo: <?= __DIR__ . '/../../public/images/header.jpg' ?><br>
                    ¿Existe el archivo? <?= file_exists(__DIR__ . '/../../public/images/header.jpg') ? 'Sí' : 'No' ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="content">
            <h2>Informe Final de Visita Domiciliaria</h2>
            <div class="cedula-info">
                Cédula del Evaluado: <?= htmlspecialchars($cedula) ?>
            </div>
        </div>
    </div>
</body>
</html>