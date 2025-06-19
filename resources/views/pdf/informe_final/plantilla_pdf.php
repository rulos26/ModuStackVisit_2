<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Simplesss</title>
</head>
<body>
    <h1>Test de Variables</h1>
    
    <h2>Variable Cédula:</h2>
    <p>Valor: <?php echo isset($cedula) ? $cedula : 'NO DEFINIDA'; ?></p>
    
    <h2>Variable Logo:</h2>
    <p>Valor: <?php echo isset($logo_b64) ? (empty($logo_b64) ? 'VACÍA' : 'CON DATOS') : 'NO DEFINIDA'; ?></p>
    
    <h2>Debug Info:</h2>
    <p>Variables definidas: <?php echo implode(', ', array_keys(get_defined_vars())); ?></p>
    
    <h2>Contenido de Variables:</h2>
    <pre><?php print_r(get_defined_vars()); ?></pre>
</body>
</html> 