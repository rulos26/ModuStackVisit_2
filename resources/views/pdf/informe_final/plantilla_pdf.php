<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Simple</title>
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
        .cedula-info {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
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
                <span style="color: #888;">Logo no disponible</span>
            <?php endif; ?>
        </div>
        
        <div class="cedula-info">
            Cédula: <?= htmlspecialchars($cedula) ?>
        </div>
    </div>
</body>
</html>