<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

require_once __DIR__ . '/../../../app/Controllers/SuperAdminController.php';
use App\Controllers\SuperAdminController;

$superAdmin = new SuperAdminController();
$estadisticas = $superAdmin->getEstadisticasGenerales();
$usuario = $_SESSION['username'] ?? 'Superadministrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadministrador - Sistema de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../public/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Menú lateral -->
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; min-height: 100vh;">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span class="fs-4 fw-bold">Superadmin</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link active text-white">
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
                    <a href="test_menu.php" class="nav-link text-white">
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
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        Panel de Superadministrador
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

            <!-- Contenido del dashboard -->
            <div class="container-fluid py-4">
                <!-- Tarjetas de estadísticas -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2 stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Usuarios
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $total_usuarios = 0;
                                            if ($estadisticas && isset($estadisticas['usuarios_por_rol'])) {
                                                foreach ($estadisticas['usuarios_por_rol'] as $rol) {
                                                    $total_usuarios += $rol['total'];
                                                }
                                            }
                                            echo $total_usuarios;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2 stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Evaluaciones
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $estadisticas ? $estadisticas['total_evaluaciones'] : 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clipboard-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2 stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Cartas de Autorización
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $estadisticas ? $estadisticas['total_cartas'] : 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2 stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Evaluadores Activos
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $evaluadores = 0;
                                            if ($estadisticas && isset($estadisticas['usuarios_por_rol'])) {
                                                foreach ($estadisticas['usuarios_por_rol'] as $rol) {
                                                    if ($rol['rol'] == 2) {
                                                        $evaluadores = $rol['total'];
                                                        break;
                                                    }
                                                }
                                            }
                                            echo $evaluadores;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-badge fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="row">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Evaluaciones por Mes</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="evaluacionesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Distribución de Usuarios</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="usuariosChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="gestion_usuarios.php" class="btn btn-primary w-100">
                                            <i class="bi bi-person-plus me-2"></i>
                                            Crear Usuario
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="respaldo.php" class="btn btn-success w-100">
                                            <i class="bi bi-download me-2"></i>
                                            Crear Respaldo
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="logs_sistema.php" class="btn btn-info w-100">
                                            <i class="bi bi-journal-text me-2"></i>
                                            Ver Logs
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="configuracion_sistema.php" class="btn btn-warning w-100">
                                            <i class="bi bi-gear me-2"></i>
                                            Configuración
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="gestion_tablas_principales.php" class="btn btn-danger w-100">
                                            <i class="bi bi-database me-2"></i>
                                            Tablas Principales
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfico de evaluaciones por mes
        const evaluacionesCtx = document.getElementById('evaluacionesChart').getContext('2d');
        const evaluacionesChart = new Chart(evaluacionesCtx, {
            type: 'line',
            data: {
                labels: <?php 
                    if ($estadisticas && isset($estadisticas['evaluaciones_por_mes'])) {
                        echo json_encode(array_column($estadisticas['evaluaciones_por_mes'], 'mes'));
                    } else {
                        echo '[]';
                    }
                ?>,
                datasets: [{
                    label: 'Evaluaciones',
                    data: <?php 
                        if ($estadisticas && isset($estadisticas['evaluaciones_por_mes'])) {
                            echo json_encode(array_column($estadisticas['evaluaciones_por_mes'], 'total'));
                        } else {
                            echo '[]';
                        }
                    ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de distribución de usuarios
        const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
        const usuariosChart = new Chart(usuariosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Administradores', 'Evaluadores', 'Superadministradores'],
                datasets: [{
                    data: <?php 
                        $datos_usuarios = [0, 0, 0];
                        if ($estadisticas && isset($estadisticas['usuarios_por_rol'])) {
                            foreach ($estadisticas['usuarios_por_rol'] as $rol) {
                                $datos_usuarios[$rol['rol'] - 1] = $rol['total'];
                            }
                        }
                        echo json_encode($datos_usuarios);
                    ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
