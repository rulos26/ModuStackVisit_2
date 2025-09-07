<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

$usuario = $_SESSION['username'] ?? 'Superadministrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Test - Sistema de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .test-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .test-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Menú lateral -->
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; min-height: 100vh;">
            <a href="dashboardSuperAdmin.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span class="fs-4 fw-bold">Superadmin</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="dashboardSuperAdmin.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="gestion_usuarios.php" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i>
                        Gestión de Usuarios
                    </a>
                </li>
                <li>
                    <a href="gestion_opciones.php" class="nav-link text-white">
                        <i class="bi bi-gear me-2"></i>
                        Gestión de Opciones
                    </a>
                </li>
                <li>
                    <a href="gestion_tablas_principales.php" class="nav-link text-white">
                        <i class="bi bi-database me-2"></i>
                        Tablas Principales
                    </a>
                </li>
                <li>
                    <a href="test_menu.php" class="nav-link active text-white">
                        <i class="bi bi-tools me-2"></i>
                        Test
                    </a>
                </li>
                <li>
                    <a href="../../../explorador_imagenes.php" class="nav-link text-white">
                        <i class="bi bi-images me-2"></i>
                        Explorador de Imágenes
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="../../../logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-grow-1">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="bi bi-tools me-2"></i>
                        Menú de Test
                    </span>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">
                            <i class="bi bi-clock"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </span>
                        <a href="../../../logout.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Contenido principal -->
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <h2 class="mb-4">
                            <i class="bi bi-tools me-2"></i>
                            Herramientas de Diagnóstico y Test
                        </h2>
                        <p class="text-muted mb-4">Selecciona el tipo de test que deseas ejecutar:</p>
                    </div>
                </div>

                <!-- Tests Básicos -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-primary mb-3">
                            <i class="bi bi-check-circle me-2"></i>
                            Tests Básicos
                        </h4>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_basico.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <h5 class="card-title">Test Básico</h5>
                                <p class="card-text">Verificación básica del sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_simple.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-info">
                                    <i class="bi bi-gear"></i>
                                </div>
                                <h5 class="card-title">Test Simple</h5>
                                <p class="card-text">Test de configuración simple</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_conexion.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-warning">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <h5 class="card-title">Test de Conexión</h5>
                                <p class="card-text">Verificar conexión a base de datos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_configuracion.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-primary">
                                    <i class="bi bi-gear-wide-connected"></i>
                                </div>
                                <h5 class="card-title">Test Configuración</h5>
                                <p class="card-text">Verificar configuración del sistema</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests de Estructura -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-primary mb-3">
                            <i class="bi bi-folder me-2"></i>
                            Tests de Estructura
                        </h4>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_estructura_servidor.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-info">
                                    <i class="bi bi-server"></i>
                                </div>
                                <h5 class="card-title">Test Estructura Servidor</h5>
                                <p class="card-text">Verificar estructura del servidor</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_estructura_real.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-success">
                                    <i class="bi bi-folder-check"></i>
                                </div>
                                <h5 class="card-title">Test Estructura Real</h5>
                                <p class="card-text">Verificar estructura real del proyecto</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_tablas_relacionadas.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-warning">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <h5 class="card-title">Test Tablas Relacionadas</h5>
                                <p class="card-text">Verificar relaciones entre tablas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('diagnostico_tablas.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-primary">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h5 class="card-title">Diagnóstico de Tablas</h5>
                                <p class="card-text">Diagnóstico completo de base de datos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests de Funcionalidad -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-primary mb-3">
                            <i class="bi bi-play-circle me-2"></i>
                            Tests de Funcionalidad
                        </h4>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_funcionalidad.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-success">
                                    <i class="bi bi-play-circle"></i>
                                </div>
                                <h5 class="card-title">Test Funcionalidad</h5>
                                <p class="card-text">Verificar funcionalidades del sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_funcionamiento.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-info">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <h5 class="card-title">Test Funcionamiento</h5>
                                <p class="card-text">Verificar funcionamiento general</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_final.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-warning">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <h5 class="card-title">Test Final</h5>
                                <p class="card-text">Test final del sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('procesar_simple.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-primary">
                                    <i class="bi bi-lightning"></i>
                                </div>
                                <h5 class="card-title">Procesador Simple</h5>
                                <p class="card-text">Procesador de datos simple</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests de Error -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-primary mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tests de Error
                        </h4>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_basico_error.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-danger">
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                                <h5 class="card-title">Test Básico Error</h5>
                                <p class="card-text">Test de manejo de errores básicos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_error_500.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <h5 class="card-title">Test Error 500</h5>
                                <p class="card-text">Test de errores del servidor</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_error_500_detallado.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-danger">
                                    <i class="bi bi-exclamation-octagon"></i>
                                </div>
                                <h5 class="card-title">Test Error 500 Detallado</h5>
                                <p class="card-text">Test detallado de errores del servidor</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card test-card h-100" onclick="ejecutarTest('test_procesador_directo.php')">
                            <div class="card-body text-center">
                                <div class="test-icon text-warning">
                                    <i class="bi bi-play-btn"></i>
                                </div>
                                <h5 class="card-title">Test Procesador Directo</h5>
                                <p class="card-text">Test de procesador directo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function ejecutarTest(testFile) {
            // Abrir el test en una nueva ventana
            window.open(testFile, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        }
    </script>
</body>
</html>
