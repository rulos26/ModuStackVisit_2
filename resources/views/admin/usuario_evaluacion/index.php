<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_evaluacion');
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Administrador';

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

// 1. Obtener todos los evaluados
$sql_evaluados = "SELECT * FROM evaluados ORDER BY id DESC";
$result_evaluados = $mysqli->query($sql_evaluados);

$evaluados_data = [];
$total_evaluados = 0;
$evaluaciones_completadas = 0;
$en_proceso = 0;
$pendientes = 0;
$informes_generados = 0; // Se mantiene por si se usa en el futuro

if ($result_evaluados) {
    $total_evaluados = $result_evaluados->num_rows;
    
    // Lista de tablas de módulos a verificar
    $tablas_a_verificar = [
        'camara_comercio', 'estados_salud', 'composicion_familiar', 'informacion_pareja', 
        'tipo_vivienda', 'inventario_enseres', 'servicios_publicos', 'patrimonio', 
        'cuentas_bancarias', 'pasivos', 'aportante', 'data_credito', 
        'ingresos_mensuales', 'gasto', 'estudios', 'informacion_judicial', 
        'experiencia_laboral', 'concepto_final_evaluador', 'ubicacion_autorizacion', 
        'evidencia_fotografica'
    ];
    $total_tablas = count($tablas_a_verificar);

    while ($row = $result_evaluados->fetch_assoc()) {
        $cedula = $row['id_cedula'];
        $tablas_completadas = 0;

        // 2. Contar cuántas tablas tienen registro para la cédula
        foreach ($tablas_a_verificar as $tabla) {
            $sql_check = "SELECT id FROM `$tabla` WHERE id_cedula = '$cedula' LIMIT 1";
            $result_check = $mysqli->query($sql_check);
            if ($result_check && $result_check->num_rows > 0) {
                $tablas_completadas++;
            }
        }

        // 3. Calcular progreso y determinar estado
        $progreso = $total_tablas > 0 ? ($tablas_completadas / $total_tablas) * 100 : 0;
        
        $estado = '';
        if ($progreso == 100) {
            $estado = 'Completada';
            $evaluaciones_completadas++;
        } elseif ($progreso >= 75) {
            $estado = 'En Proceso';
            $en_proceso++;
        } else {
            $estado = 'Pendiente';
            $pendientes++;
        }

        // 4. Agregar data al array de evaluados
        $evaluados_data[] = array_merge($row, [
            'progreso' => round($progreso),
            'estado' => $estado,
        ]);

        // Lógica de ejemplo para informes generados (si existe el campo)
        if (isset($row['informe_generado']) && $row['informe_generado']) {
            $informes_generados++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Usuarios Evaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .progress {
            height: 8px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="fas fa-user-shield me-2"></i>Admin Panel
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link text-white" href="/ModuStackVisit_2/resources/views/admin/dashboardAdmin.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link text-white" href="/ModuStackVisit_2/resources/views/admin/usuario_carta/index.php">
                            <i class="fas fa-envelope me-2"></i>Usuarios Carta
                        </a>
                        <a class="nav-link text-white active" href="/ModuStackVisit_2/resources/views/admin/usuario_evaluacion/index.php">
                            <i class="fas fa-clipboard-check me-2"></i>Usuarios Evaluación
                        </a>
                        <hr class="text-white">
                        <a class="nav-link text-white" href="/ModuStackVisit_2/public/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">
                                <i class="fas fa-clipboard-check me-2"></i>Gestión de Usuarios Evaluación
                            </h2>
                            <p class="text-muted">Administra los usuarios del módulo de evaluación de visitas domiciliarias</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Bienvenido, <?php echo htmlspecialchars($admin_username); ?></small>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                    <h4 class="card-title">Total Evaluados</h4>
                                    <h2 class="text-primary"><?php echo $total_evaluados; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h4 class="card-title">Evaluaciones Completadas</h4>
                                    <h2 class="text-success"><?php echo $evaluaciones_completadas; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                    <h4 class="card-title">En Proceso</h4>
                                    <h2 class="text-warning"><?php echo $en_proceso; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                    <h4 class="card-title">Pendientes</h4>
                                    <h2 class="text-danger"><?php echo $pendientes; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Overview -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Progreso de Evaluaciones
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $porc_completadas = $total_evaluados > 0 ? round(($evaluaciones_completadas / $total_evaluados) * 100) : 0;
                                    $porc_en_proceso = $total_evaluados > 0 ? round(($en_proceso / $total_evaluados) * 100) : 0;
                                    $porc_pendientes = max(0, 100 - $porc_completadas - $porc_en_proceso);
                                    ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Completadas</span>
                                            <span><?php echo $porc_completadas; ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: <?php echo $porc_completadas; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>En Proceso</span>
                                            <span><?php echo $porc_en_proceso; ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: <?php echo $porc_en_proceso; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Pendientes</span>
                                            <span><?php echo $porc_pendientes; ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: <?php echo $porc_pendientes; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Actividad Reciente
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No hay actividad reciente</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Lista de Usuarios Evaluación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Buscar evaluado por cédula o nombre...">
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Evaluado
                                    </button>
                                    <button class="btn btn-success ms-2">
                                        <i class="fas fa-download me-2"></i>Exportar
                                    </button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre Completo</th>
                                            <th>Progreso</th>
                                            <th>Estado</th>
                                            <th>Evaluador</th>
                                            <th>Fecha Evaluación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($evaluados_data)): ?>
                                            <?php foreach ($evaluados_data as $evaluado): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($evaluado['id_cedula'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($evaluado['nombres'] ?? 'N/A'); ?> <?php echo htmlspecialchars($evaluado['apellidos'] ?? ''); ?></td>
                                                    <td>
                                                        <?php 
                                                        $progreso = $evaluado['progreso'];
                                                        $progreso_color = 'bg-danger';
                                                        if ($evaluado['estado'] === 'Completada') {
                                                            $progreso_color = 'bg-success';
                                                        } elseif ($evaluado['estado'] === 'En Proceso') {
                                                            $progreso_color = 'bg-warning';
                                                        }
                                                        ?>
                                                        <div class="progress" title="<?php echo $progreso; ?>%">
                                                            <div class="progress-bar <?php echo $progreso_color; ?>" style="width: <?php echo $progreso; ?>%"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $estado = $evaluado['estado'];
                                                        $badge_class = 'bg-secondary';
                                                        if ($estado === 'Completada') $badge_class = 'bg-success';
                                                        if ($estado === 'En Proceso') $badge_class = 'bg-warning';
                                                        if ($estado === 'Pendiente') $badge_class = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($evaluado['nombre_evaluador'] ?? 'No asignado'); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($evaluado['fecha_evaluacion']) ? date('d/m/Y', strtotime($evaluado['fecha_evaluacion'])) : 'N/A'); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Ver informe">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" title="Continuar evaluación">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p>No hay evaluados registrados</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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