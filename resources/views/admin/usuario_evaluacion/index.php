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
                                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar evaluado por cédula o nombre...">
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
                                <table class="table table-hover" id="evaluadosTable">
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
                                                            <?php if ($evaluado['estado'] === 'Completada'): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                        onclick="verInforme('<?php echo htmlspecialchars($evaluado['id_cedula']); ?>')" 
                                                                        title="Ver informe">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </button>
                                                            <?php elseif ($evaluado['estado'] === 'En Proceso'): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                        onclick="continuarEvaluacion('<?php echo htmlspecialchars($evaluado['id_cedula']); ?>')" 
                                                                        title="Continuar evaluación">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            <?php elseif ($evaluado['estado'] === 'Pendiente'): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="eliminarEvaluado('<?php echo htmlspecialchars($evaluado['id_cedula']); ?>', '<?php echo htmlspecialchars($evaluado['nombres']); ?>')" 
                                                                        title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
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
    <script>
        // Funcionalidad de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('evaluadosTable');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                rows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    let found = false;

                    // Buscar en las columnas de cédula (índice 0) y nombre completo (índice 1)
                    if (cells.length > 1) {
                        const cedula = cells[0].textContent.toLowerCase();
                        const nombres = cells[1].textContent.toLowerCase();

                        if (cedula.includes(searchTerm) || nombres.includes(searchTerm)) {
                            found = true;
                        }
                    }

                    // Mostrar u ocultar la fila
                    row.style.display = found ? '' : 'none';
                });

                // Mostrar mensaje si no hay resultados
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                let noResultsRow = tbody.querySelector('.no-results');
                
                if (visibleRows.length === 0 && searchTerm !== '') {
                    if (!noResultsRow) {
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-results';
                        newRow.innerHTML = `
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <p>No se encontraron resultados para "${this.value}"</p>
                            </td>
                        `;
                        tbody.appendChild(newRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            });
        });

        // Funciones de acción (placeholders)
        function verInforme(cedula) {
            // Abrir nueva pestaña con la página que guardará la sesión
            const nuevaPestana = window.open(`guardar_cedula_informe.php?cedula=${encodeURIComponent(cedula)}`, '_blank');
            
            // Verificar si se abrió correctamente
            if (nuevaPestana) {
                // Opcional: Mostrar mensaje de confirmación
                console.log('Nueva pestaña abierta para ver informe del evaluado');
            } else {
                alert('No se pudo abrir la nueva pestaña. Verifica que el bloqueador de ventanas emergentes esté desactivado.');
            }
        }

        function continuarEvaluacion(cedula) {
            // Mostrar indicador de carga
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            // Verificar estado de tablas
            fetch('verificar_tablas_evaluacion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cedula=' + encodeURIComponent(cedula)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar modal con información de tablas faltantes
                    mostrarModalTablasFaltantes(data, cedula);
                } else {
                    alert('Error al verificar tablas: ' + data.message);
                }
                // Restaurar botón
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al verificar tablas. Por favor, inténtalo de nuevo.');
                // Restaurar botón
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }

        function eliminarEvaluado(cedula, nombres) {
            if (confirm(`¿Estás seguro de que deseas eliminar al evaluado ${nombres} (Cédula: ${cedula})?\n\nEsta acción eliminará todos los registros relacionados y no se puede deshacer.`)) {
                // Mostrar indicador de carga
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                // Realizar la eliminación
                fetch('eliminar_evaluado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'cedula=' + encodeURIComponent(cedula)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        alert('Evaluado eliminado exitosamente');
                        // Recargar la página para actualizar la tabla
                        location.reload();
                    } else {
                        alert('Error al eliminar evaluado: ' + data.message);
                        // Restaurar botón
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar evaluado. Por favor, inténtalo de nuevo.');
                    // Restaurar botón
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
            }
        }

        // Función para mostrar modal con tablas faltantes
        function mostrarModalTablasFaltantes(data, cedula) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'modalTablasFaltantes';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-tasks me-2"></i>Estado de Completitud - Cédula: ${cedula}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Progreso:</strong> ${data.tablas_completadas} de ${data.total_tablas} módulos completados (${data.porcentaje_completado}%)
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-3">Estado de cada módulo:</h6>
                                    ${data.tablas.map(tabla => `
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 ${tabla.completada ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10'} rounded">
                                            <div>
                                                <i class="fas ${tabla.completada ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning'} me-2"></i>
                                                <strong>${tabla.nombre}</strong>
                                            </div>
                                            <div>
                                                ${tabla.completada ? 
                                                    '<span class="badge bg-success">Completado</span>' : 
                                                    '<span class="badge bg-warning">Pendiente</span>'
                                                }
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            
                            ${data.puede_acceder ? `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="mb-3">Acciones disponibles:</h6>
                                        <div class="d-grid gap-2">
                                            ${data.tablas_faltantes.map(tabla => `
                                                <a href="${tabla.url}" class="btn btn-warning">
                                                    <i class="fas ${tabla.icono} me-2"></i>
                                                    Completar ${tabla.nombre}
                                                </a>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            ` : `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Acceso restringido:</strong> Se requiere completar al menos 15 de 20 módulos para acceder al sistema.
                                        </div>
                                    </div>
                                </div>
                            `}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Limpiar modal cuando se cierre
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }
    </script>
</body>
</html> 