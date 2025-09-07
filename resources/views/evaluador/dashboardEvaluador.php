<?php
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Evaluador - Sistema de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .task-card {
            border-left: 4px solid #28a745;
        }
        .task-card.urgent {
            border-left-color: #dc3545;
        }
        .task-card.pending {
            border-left-color: #ffc107;
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
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
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
                                <i class="bi bi-list-task me-2"></i>
                                Mis Evaluaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-week me-2"></i>
                                Agenda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="evaluacion_visita/index_evaluacion.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear me-2"></i>
                                Configuración
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../layout/dashboard.php">
                                <i class="bi bi-layout-text-sidebar me-2"></i>
                                Layout Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Demo Dashboard raíz
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../logout.php">
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
                            <h1 class="h3 mb-0">Dashboard Evaluador</h1>
                            <p class="text-muted mb-0">Panel de control para evaluadores del sistema</p>
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
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    ¡Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>!
                                </h2>
                                <p class="mb-0">Gestiona tus evaluaciones y mantén al día con las visitas programadas.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="bi bi-clipboard-data" style="font-size: 4rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-list-task mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">15</div>
                                    <div>Evaluaciones Pendientes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">23</div>
                                    <div>Evaluaciones Completadas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">8</div>
                                    <div>Visitas Hoy</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-star mb-3" style="font-size: 2rem;"></i>
                                    <div class="stats-number">4.9</div>
                                    <div>Calificación Promedio</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-lightning me-2"></i>
                                        Acciones Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-success btn-lg p-4 w-100">
                                                <i class="bi bi-plus-circle mb-2" style="font-size: 2rem; display: block;"></i>
                                                Nueva Evaluación
                                            </a>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-primary btn-lg p-4 w-100">
                                                <i class="bi bi-calendar-check mb-2" style="font-size: 2rem; display: block;"></i>
                                                Ver Agenda
                                            </a>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <a href="#" class="btn btn-outline-info btn-lg p-4 w-100">
                                                <i class="bi bi-file-earmark-text mb-2" style="font-size: 2rem; display: block;"></i>
                                                Generar Reporte
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

                    <!-- Tasks and Notifications -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-list-check me-2"></i>
                                        Tareas Pendientes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item task-card urgent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Evaluación de vivienda - Calle 123 #45-67</h6>
                                                    <small class="text-muted">Cliente: Juan Pérez - Cédula: 12345678</small>
                                                    <br>
                                                    <span class="badge bg-danger">Urgente</span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">Hoy, 14:00</small><br>
                                                    <button class="btn btn-sm btn-success mt-1">Iniciar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item task-card">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Evaluación de vivienda - Carrera 78 #90-12</h6>
                                                    <small class="text-muted">Cliente: María García - Cédula: 87654321</small>
                                                    <br>
                                                    <span class="badge bg-primary">Programada</span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">Mañana, 10:00</small><br>
                                                    <button class="btn btn-sm btn-outline-primary mt-1">Ver Detalles</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item task-card pending">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Evaluación de vivienda - Avenida 5 #23-45</h6>
                                                    <small class="text-muted">Cliente: Carlos López - Cédula: 11223344</small>
                                                    <br>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">15/01/2025, 16:00</small><br>
                                                    <button class="btn btn-sm btn-outline-warning mt-1">Reagendar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
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
                                                Nueva evaluación asignada
                                            </div>
                                            <small class="text-muted">Hace 30 min</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                Evaluación completada
                                            </div>
                                            <small class="text-muted">Hace 2 horas</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-info-circle text-info me-2"></i>
                                                Cliente canceló cita
                                            </div>
                                            <small class="text-muted">Hace 1 día</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-star text-warning me-2"></i>
                                                Nueva calificación recibida
                                            </div>
                                            <small class="text-muted">Hace 2 días</small>
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
