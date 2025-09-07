<?php
session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tablas Principales - Superadministrador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
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
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stats-card .card-body {
            padding: 1.5rem;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border: none;
            border-radius: 8px;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
            border: none;
            border-radius: 8px;
            color: #2c3e50;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #48dbfb 0%, #0abde3 100%);
            border: none;
            border-radius: 8px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
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
                    <a href="gestion_tablas_principales.php" class="nav-link active text-white">
                        <i class="bi bi-database me-2"></i>
                        Tablas Principales
                    </a>
                </li>
                <li>
                    <a href="configuracion_sistema.php" class="nav-link text-white">
                        <i class="bi bi-gear me-2"></i>
                        Configuración
                    </a>
                </li>
                <li>
                    <a href="logs_sistema.php" class="nav-link text-white">
                        <i class="bi bi-journal-text me-2"></i>
                        Logs del Sistema
                    </a>
                </li>
                <li>
                    <a href="auditoria.php" class="nav-link text-white">
                        <i class="bi bi-shield-check me-2"></i>
                        Auditoría
                    </a>
                </li>
                <li>
                    <a href="respaldo.php" class="nav-link text-white">
                        <i class="bi bi-download me-2"></i>
                        Respaldos
                    </a>
                </li>
                <li>
                    <a href="reportes.php" class="nav-link text-white">
                        <i class="bi bi-graph-up me-2"></i>
                        Reportes
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                    <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Superadministrador'); ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="../../logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-grow-1">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="bi bi-database me-2"></i>
                        Gestión de Tablas Principales
                    </span>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">
                            <i class="bi bi-clock"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </span>
                        <button class="btn btn-outline-light btn-sm me-2" onclick="cargarEstadisticasGenerales()">
                            <i class="bi bi-graph-up me-1"></i>
                            Estadísticas Generales
                        </button>
                        <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Contenido del dashboard -->
            <div class="container-fluid py-4">
                    
                    <!-- Estadísticas Generales -->
                    <div class="row mb-4" id="estadisticasGenerales" style="display: none;">
                        <div class="col-12">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="stats-number" id="totalTablas">0</div>
                                            <small>Total de Tablas</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stats-number" id="totalRegistros">0</div>
                                            <small>Total de Registros</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stats-number" id="tablasConCedula">0</div>
                                            <small>Tablas con Cédula</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selector de Tabla -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-table"></i> Seleccionar Tabla
                                    </h6>
                                    <select class="form-select" id="selectorTabla" onchange="cargarEstadisticasTabla()">
                                        <option value="">Selecciona una tabla...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-info-circle"></i> Información de la Tabla
                                    </h6>
                                    <div id="infoTabla">
                                        <p class="text-muted mb-0">Selecciona una tabla para ver su información</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estadísticas de Tabla Seleccionada -->
                    <div class="row mb-4" id="estadisticasTabla" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-bar-chart"></i> Estadísticas de la Tabla
                                    </h6>
                                    <div class="row" id="statsTabla">
                                        <!-- Las estadísticas se cargarán aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="row mb-4" id="accionesTabla" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-tools"></i> Acciones Disponibles
                                    </h6>
                                    
                                    <!-- Eliminar por Cédula -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">
                                                        <i class="bi bi-person-x"></i> Eliminar por Cédula
                                                    </h6>
                                                    <p class="card-text small">Elimina todos los registros asociados a una cédula específica en esta tabla.</p>
                                                    
                                                    <div class="input-group mb-2">
                                                        <input type="number" class="form-control" id="cedulaEliminar" placeholder="Número de cédula">
                                                        <button class="btn btn-warning" onclick="eliminarPorCedula()">
                                                            <i class="bi bi-trash"></i> Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger">
                                                        <i class="bi bi-trash"></i> Truncar Tabla
                                                    </h6>
                                                    <p class="card-text small">Elimina TODOS los registros de esta tabla. Esta acción no se puede deshacer.</p>
                                                    
                                                    <button class="btn btn-danger" onclick="confirmarTruncarTabla()">
                                                        <i class="bi bi-exclamation-triangle"></i> Truncar Tabla
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Eliminación Masiva -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card border-danger">
                                                <div class="card-body">
                                                    <h6 class="card-title text-danger">
                                                        <i class="bi bi-exclamation-triangle"></i> Eliminación Masiva por Cédula
                                                    </h6>
                                                    <p class="card-text small">
                                                        <strong>⚠️ ADVERTENCIA:</strong> Esta acción eliminará TODOS los registros asociados a una cédula 
                                                        en TODAS las tablas del sistema. Esta operación es irreversible.
                                                    </p>
                                                    
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="cedulaEliminacionMasiva" placeholder="Número de cédula">
                                                        <button class="btn btn-danger" onclick="confirmarEliminacionMasiva()">
                                                            <i class="bi bi-exclamation-triangle"></i> Eliminación Masiva
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resultados -->
                    <div class="row" id="resultados" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-clipboard-check"></i> Resultados de la Operación
                                    </h6>
                                    <div id="contenidoResultados">
                                        <!-- Los resultados se mostrarán aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Truncar -->
    <div class="modal fade" id="modalTruncarTabla" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Truncamiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>⚠️ ADVERTENCIA CRÍTICA:</strong>
                        <p class="mb-0">Estás a punto de eliminar TODOS los registros de la tabla <strong id="nombreTablaTruncar"></strong>.</p>
                        <p class="mb-0">Esta acción es <strong>IRREVERSIBLE</strong> y no se puede deshacer.</p>
                    </div>
                    
                    <p>Para confirmar, escribe exactamente: <code>TRUNCAR_TABLA_COMPLETA</code></p>
                    
                    <input type="text" class="form-control" id="confirmacionTruncar" placeholder="Escribe la confirmación">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="truncarTabla()">
                        <i class="bi bi-trash"></i> Truncar Tabla
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Eliminación Masiva -->
    <div class="modal fade" id="modalEliminacionMasiva" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación Masiva
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>🚨 ADVERTENCIA CRÍTICA:</strong>
                        <p class="mb-0">Estás a punto de eliminar TODOS los registros asociados a la cédula <strong id="cedulaEliminacionMasivaModal"></strong> en TODAS las tablas del sistema.</p>
                        <p class="mb-0">Esta acción es <strong>IRREVERSIBLE</strong> y afectará múltiples tablas.</p>
                    </div>
                    
                    <p>Para confirmar, escribe exactamente: <code>ELIMINAR_TODOS_LOS_REGISTROS</code></p>
                    
                    <input type="text" class="form-control" id="confirmacionEliminacionMasiva" placeholder="Escribe la confirmación">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarEliminacionMasiva()">
                        <i class="bi bi-exclamation-triangle"></i> Eliminación Masiva
                    </button>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        let tablaSeleccionada = '';
        let modalTruncar = null;
        let modalEliminacionMasiva = null;
        
        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarTablas();
            modalTruncar = new bootstrap.Modal(document.getElementById('modalTruncarTabla'));
            modalEliminacionMasiva = new bootstrap.Modal(document.getElementById('modalEliminacionMasiva'));
        });
        
        // Cargar lista de tablas
        function cargarTablas() {
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'accion=obtener_tablas'
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    mostrarError(data.error);
                    return;
                }
                
                const selector = document.getElementById('selectorTabla');
                selector.innerHTML = '<option value="">Selecciona una tabla...</option>';
                
                Object.keys(data).forEach(nombreTabla => {
                    const option = document.createElement('option');
                    option.value = nombreTabla;
                    option.textContent = data[nombreTabla].nombre;
                    selector.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar las tablas');
            });
        }
        
        // Cargar estadísticas de una tabla específica
        function cargarEstadisticasTabla() {
            const selector = document.getElementById('selectorTabla');
            tablaSeleccionada = selector.value;
            
            if (!tablaSeleccionada) {
                ocultarSecciones();
                return;
            }
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=obtener_estadisticas&tabla=${tablaSeleccionada}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    mostrarError(data.error);
                    return;
                }
                
                mostrarInformacionTabla(data);
                mostrarEstadisticasTabla(data);
                mostrarAccionesTabla();
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar las estadísticas de la tabla');
            });
        }
        
        // Mostrar información de la tabla
        function mostrarInformacionTabla(data) {
            const infoTabla = document.getElementById('infoTabla');
            infoTabla.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> ${data.nombre}</p>
                        <p><strong>Descripción:</strong> ${data.descripcion}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Columna Cédula:</strong> ${data.columna_cedula || 'No aplica'}</p>
                        <p><strong>Total Registros:</strong> ${data.total_registros}</p>
                    </div>
                </div>
            `;
        }
        
        // Mostrar estadísticas de la tabla
        function mostrarEstadisticasTabla(data) {
            const statsTabla = document.getElementById('statsTabla');
            let html = '';
            
            if (data.tiene_cedula) {
                html = `
                    <div class="col-md-3 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">${data.total_registros}</div>
                                <small>Total Registros</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">${data.cedulas_unicas || 0}</div>
                                <small>Cédulas Únicas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">${data.registros_sin_cedula || 0}</div>
                                <small>Sin Cédula</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">${data.columna_cedula}</div>
                                <small>Columna Cédula</small>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="col-md-6 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">${data.total_registros}</div>
                                <small>Total Registros</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="stats-card">
                            <div class="card-body">
                                <div class="stats-number">-</div>
                                <small>Sin Columna Cédula</small>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            statsTabla.innerHTML = html;
            document.getElementById('estadisticasTabla').style.display = 'block';
        }
        
        // Mostrar acciones disponibles
        function mostrarAccionesTabla() {
            document.getElementById('accionesTabla').style.display = 'block';
        }
        
        // Ocultar secciones
        function ocultarSecciones() {
            document.getElementById('estadisticasTabla').style.display = 'none';
            document.getElementById('accionesTabla').style.display = 'none';
            document.getElementById('resultados').style.display = 'none';
        }
        
        // Eliminar registros por cédula
        function eliminarPorCedula() {
            const cedula = document.getElementById('cedulaEliminar').value.trim();
            
            if (!cedula) {
                mostrarError('Por favor ingresa un número de cédula');
                return;
            }
            
            if (!tablaSeleccionada) {
                mostrarError('Por favor selecciona una tabla');
                return;
            }
            
            if (!confirm(`¿Estás seguro de que quieres eliminar todos los registros de la cédula ${cedula} en la tabla ${tablaSeleccionada}?`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('accion', 'eliminar_por_cedula');
            formData.append('tabla', tablaSeleccionada);
            formData.append('cedula', cedula);
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarResultado(data);
                if (data.success) {
                    document.getElementById('cedulaEliminar').value = '';
                    cargarEstadisticasTabla(); // Recargar estadísticas
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al eliminar los registros');
            });
        }
        
        // Confirmar truncamiento de tabla
        function confirmarTruncarTabla() {
            if (!tablaSeleccionada) {
                mostrarError('Por favor selecciona una tabla');
                return;
            }
            
            document.getElementById('nombreTablaTruncar').textContent = tablaSeleccionada;
            document.getElementById('confirmacionTruncar').value = '';
            modalTruncar.show();
        }
        
        // Truncar tabla
        function truncarTabla() {
            const confirmacion = document.getElementById('confirmacionTruncar').value.trim();
            
            if (confirmacion !== 'TRUNCAR_TABLA_COMPLETA') {
                mostrarError('Confirmación incorrecta. Por favor escribe exactamente: TRUNCAR_TABLA_COMPLETA');
                return;
            }
            
            const formData = new FormData();
            formData.append('accion', 'truncar_tabla');
            formData.append('tabla', tablaSeleccionada);
            formData.append('confirmacion', confirmacion);
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalTruncar.hide();
                mostrarResultado(data);
                if (data.success) {
                    cargarEstadisticasTabla(); // Recargar estadísticas
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al truncar la tabla');
            });
        }
        
        // Confirmar eliminación masiva
        function confirmarEliminacionMasiva() {
            const cedula = document.getElementById('cedulaEliminacionMasiva').value.trim();
            
            if (!cedula) {
                mostrarError('Por favor ingresa un número de cédula');
                return;
            }
            
            document.getElementById('cedulaEliminacionMasivaModal').textContent = cedula;
            document.getElementById('confirmacionEliminacionMasiva').value = '';
            modalEliminacionMasiva.show();
        }
        
        // Ejecutar eliminación masiva
        function ejecutarEliminacionMasiva() {
            const confirmacion = document.getElementById('confirmacionEliminacionMasiva').value.trim();
            const cedula = document.getElementById('cedulaEliminacionMasiva').value.trim();
            
            if (confirmacion !== 'ELIMINAR_TODOS_LOS_REGISTROS') {
                mostrarError('Confirmación incorrecta. Por favor escribe exactamente: ELIMINAR_TODOS_LOS_REGISTROS');
                return;
            }
            
            const formData = new FormData();
            formData.append('accion', 'eliminar_por_cedula_todas_tablas');
            formData.append('cedula', cedula);
            formData.append('confirmacion', confirmacion);
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalEliminacionMasiva.hide();
                mostrarResultado(data);
                if (data.success) {
                    document.getElementById('cedulaEliminacionMasiva').value = '';
                    cargarEstadisticasTabla(); // Recargar estadísticas
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al ejecutar la eliminación masiva');
            });
        }
        
        // Cargar estadísticas generales
        function cargarEstadisticasGenerales() {
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'accion=obtener_estadisticas_generales'
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    mostrarError(data.error);
                    return;
                }
                
                document.getElementById('totalTablas').textContent = data.total_tablas;
                document.getElementById('totalRegistros').textContent = data.total_registros_sistema;
                
                // Contar tablas con cédula
                let tablasConCedula = 0;
                Object.values(data.estadisticas_por_tabla).forEach(tabla => {
                    if (tabla.tiene_cedula) {
                        tablasConCedula++;
                    }
                });
                document.getElementById('tablasConCedula').textContent = tablasConCedula;
                
                document.getElementById('estadisticasGenerales').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar las estadísticas generales');
            });
        }
        
        // Mostrar resultado
        function mostrarResultado(data) {
            const resultados = document.getElementById('resultados');
            const contenido = document.getElementById('contenidoResultados');
            
            if (data.success) {
                contenido.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> ${data.mensaje}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Operación:</strong> ${data.accion || 'Completada'}</p>
                            ${data.tabla ? `<p><strong>Tabla:</strong> ${data.tabla}</p>` : ''}
                            ${data.cedula ? `<p><strong>Cédula:</strong> ${data.cedula}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            ${data.registros_eliminados ? `<p><strong>Registros Eliminados:</strong> ${data.registros_eliminados}</p>` : ''}
                            ${data.tablas_procesadas ? `<p><strong>Tablas Procesadas:</strong> ${data.tablas_procesadas}</p>` : ''}
                            ${data.total_registros_eliminados ? `<p><strong>Total Eliminados:</strong> ${data.total_registros_eliminados}</p>` : ''}
                        </div>
                    </div>
                `;
            } else {
                contenido.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> ${data.error}
                    </div>
                `;
            }
            
            resultados.style.display = 'block';
            
            // Ocultar después de 10 segundos
            setTimeout(() => {
                resultados.style.display = 'none';
            }, 10000);
        }
        
        // Mostrar error
        function mostrarError(mensaje) {
            const resultados = document.getElementById('resultados');
            const contenido = document.getElementById('contenidoResultados');
            
            contenido.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> ${mensaje}
                </div>
            `;
            
            resultados.style.display = 'block';
            
            // Ocultar después de 5 segundos
            setTimeout(() => {
                resultados.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
