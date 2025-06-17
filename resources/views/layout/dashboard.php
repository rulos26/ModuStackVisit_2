<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = $_SESSION['username'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/menu.php'; ?>
        <div class="flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Mi Dashboard</a>
                    <div class="d-flex align-items-center ms-auto">
                        <span class="text-white me-3"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($usuario); ?></span>
                        <a href="/app/Controllers/CerrarSesionController.php" class="btn btn-outline-light">Cerrar sesión</a>
                    </div>
                </div>
            </nav>
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card shadow">
                            <div class="card-body">
                                <h3 class="card-title mb-4">Bienvenido, <?php echo htmlspecialchars($usuario); ?>!</h3>
                                <!-- Aquí puedes incluir el contenido específico del dashboard -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 