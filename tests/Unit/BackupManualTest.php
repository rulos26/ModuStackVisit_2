<?php
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = date('Ymd_His');
    $backupDir = __DIR__ . '/../../backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    $backupFile = $backupDir . "/backup_$fecha.zip";
    $zip = new ZipArchive();
    if ($zip->open($backupFile, ZipArchive::CREATE) === TRUE) {
        $rootPath = realpath(__DIR__ . '/../../');
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
            if (strpos($relativePath, 'backups') === 0) continue; // No incluir backups
            $zip->addFile($filePath, $relativePath);
        }
        $zip->close();
        $mensaje = '<div class="alert alert-success">Backup creado correctamente: <b>' . htmlspecialchars("backups/backup_$fecha.zip") . '</b></div>';
    } else {
        $mensaje = '<div class="alert alert-danger">No se pudo crear el archivo ZIP.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Backup Manual del Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white text-center">
                        <h4>Backup Manual del Proyecto</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <button type="submit" class="btn btn-info w-100">Generar Backup</button>
                        </form>
                        <hr>
                        <div class="alert alert-secondary small">
                            Este script crea un archivo ZIP con todo el proyecto (excepto la carpeta <b>backups</b>) en la carpeta <b>backups</b>.<br>
                            Ruta para probar: <code>tests/Unit/BackupManualTest.php</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 