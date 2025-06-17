<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mensaje de error personalizado si existe
$errorMsg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Ha ocurrido un error inesperado.';
$from = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : null;
$test = isset($_GET['test']) ? htmlspecialchars($_GET['test']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error del sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-danger bg-opacity-75 border-0 shadow-lg">
                    <div class="card-body">
                        <h1 class="display-5 mb-4"><i class="bi bi-exclamation-triangle-fill"></i> Error del sistema</h1>
                        <p class="lead">Se ha producido un error en la aplicación.</p>
                        <hr class="my-4">
                        <div class="alert alert-dark">
                            <strong>Mensaje:</strong> <?php echo $errorMsg; ?>
                        </div>
                        <?php if ($from || $test): ?>
                        <div class="alert alert-info mt-3">
                            <strong>Debug:</strong><br>
                            <?php if ($from): ?>Origen: <b><?php echo $from; ?></b><br><?php endif; ?>
                            <?php if ($test): ?>Test: <b><?php echo $test; ?></b><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_detail'])): ?>
                        <div class="alert alert-warning mt-3">
                            <strong>Detalle:</strong> <br>
                            <pre><?php echo htmlspecialchars($_SESSION['error_detail']); ?></pre>
                        </div>
                        <?php unset($_SESSION['error_detail']); endif; ?>
                        <a href="/ModuStackVisit_2/index.php" class="btn btn-light mt-4">Volver al inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 