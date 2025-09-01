<?php
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Cliente (2)
if ($_SESSION['rol'] != 2) {
    header('Location: ../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Cliente';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - Sistema de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .welcome-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-person-circle"></i>
                        Cliente
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-text me-2"></i>
                                Mis Visitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-check me-2"></i>
                                Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear me-2"></i>
                                Configuración
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Dashboard Cliente</h1>
                            <p class="text-muted mb-0">Bienvenido al sistema de gestión de visitas</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-3">
                                    <i class="bi bi-emoji-smile me-2"></i>
                                    ¡Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>!
                                </h2>
                                <p class="mb-0">Accede a todas las funcionalidades disponibles para clientes del sistema de visitas.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="bi bi-person-badge" style="font-size: 4rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-event mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">12</div>
                                    <div>Visitas Programadas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">8</div>
                                    <div>Visitas Completadas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">4</div>
                                    <div>Visitas Pendientes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-star mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">4.8</div>
                                    <div>Calificación Promedio</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-lightning me-2"></i>
                                        Acciones Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-primary btn-lg p-4 w-100">
                                                <i class="bi bi-plus-circle mb-2" style="font-size: 2rem; display: block;"></i>
                                                Nueva Visita
                                            </a>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-success btn-lg p-4 w-100">
                                                <i class="bi bi-calendar-plus mb-2" style="font-size: 2rem; display: block;"></i>
                                                Programar Cita
                                            </a>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-info btn-lg p-4 w-100">
                                                <i class="bi bi-file-earmark-text mb-2" style="font-size: 2rem; display: block;"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-warning btn-lg p-4 w-100">
                                                <i class="bi bi-gear mb-2" style="font-size: 2rem; display: block;"></i>
                                                Configuración
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock-history me-2"></i>
                                        Actividad Reciente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                Visita completada - Evaluación de vivienda
                                            </div>
                                            <small class="text-muted">Hace 2 horas</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-calendar-plus text-primary me-2"></i>
                                                Nueva visita programada
                                            </div>
                                            <small class="text-muted">Hace 1 día</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-text text-info me-2"></i>
                                                Reporte generado
                                            </div>
                                            <small class="text-muted">Hace 3 días</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bi bi-bell me-2"></i>
                                        Notificaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                                Visita pendiente para mañana
                                            </div>
                                            <small class="text-muted">Hace 1 hora</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-info-circle text-info me-2"></i>
                                                Nuevo evaluador asignado
                                            </div>
                                            <small class="text-muted">Hace 2 horas</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                Documentos aprobados
                                            </div>
                                            <small class="text-muted">Hace 1 día</small>
                                        </div>
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
</body>
</html>
