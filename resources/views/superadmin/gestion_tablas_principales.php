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
                <!-- Botones de Acción -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="gestion_tablas_simple.php" class="btn btn-success me-2">
                                    <i class="bi bi-lightning"></i> Versión Simple
                                </a>
                                <a href="test_funcionalidad.php" class="btn btn-primary me-2">
                                    <i class="bi bi-play-circle"></i> Test Funcionalidad
                                </a>
                                <a href="test_basico.php" class="btn btn-success me-2">
                                    <i class="bi bi-check-circle"></i> Test Básico
                                </a>
                                <a href="diagnostico_tablas.php" class="btn btn-info me-2">
                                    <i class="bi bi-search"></i> Diagnóstico de Base de Datos
                                </a>
                                <a href="test_conexion.php" class="btn btn-warning me-2">
                                    <i class="bi bi-tools"></i> Test de Conexión
                                </a>
                                <a href="test_simple.php" class="btn btn-secondary">
                                    <i class="bi bi-gear"></i> Test Simple
                                </a>
                            </div>
                            <button class="btn btn-danger btn-lg" onclick="confirmarVaciarTablas()">
                                <i class="bi bi-trash3"></i> Vaciar Tablas
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de Usuarios Evaluados -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-people"></i> Usuarios Evaluados
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tablaUsuarios">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID Cédula</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyUsuarios">
                                            <!-- Los usuarios se cargarán aquí -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resultados -->
                <div class="row mt-4" id="resultados" style="display: none;">
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
    
    <!-- Modal de Confirmación para Eliminar Usuario -->
    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación de Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>🚨 ADVERTENCIA CRÍTICA:</strong>
                        <p class="mb-0">Estás a punto de eliminar completamente al usuario <strong id="nombreUsuarioEliminar"></strong> (Cédula: <strong id="cedulaUsuarioEliminar"></strong>).</p>
                        <p class="mb-0">Esta acción eliminará TODOS los registros y archivos asociados. Es <strong>IRREVERSIBLE</strong>.</p>
                    </div>
                    
                    <div id="tablasConDatos">
                        <!-- Se mostrarán las tablas que contienen datos -->
                    </div>
                    
                    <p class="mt-3">Para confirmar, escribe exactamente: <code>ELIMINAR_USUARIO_COMPLETO</code></p>
                    
                    <input type="text" class="form-control" id="confirmacionEliminarUsuario" placeholder="Escribe la confirmación">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarEliminacionUsuario()">
                        <i class="bi bi-trash"></i> Eliminar Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Vaciar Tablas -->
    <div class="modal fade" id="modalVaciarTablas" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Vaciar Todas las Tablas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>🚨 ADVERTENCIA CRÍTICA MÁXIMA:</strong>
                        <p class="mb-0">Estás a punto de <strong>VACIAR COMPLETAMENTE</strong> todas las tablas del sistema.</p>
                        <p class="mb-0">Esta acción eliminará:</p>
                        <ul class="mb-0">
                            <li>Todos los registros de la tabla <strong>evaluados</strong></li>
                            <li>Todos los registros de <strong>26 tablas relacionadas</strong></li>
                            <li>Todos los <strong>archivos físicos</strong> asociados</li>
                        </ul>
                        <p class="mb-0 mt-2"><strong>ESTA ACCIÓN ES COMPLETAMENTE IRREVERSIBLE</strong></p>
                    </div>
                    
                    <p>Para confirmar, escribe exactamente: <code>VACIAR_TODAS_LAS_TABLAS</code></p>
                    
                    <input type="text" class="form-control" id="confirmacionVaciarTablas" placeholder="Escribe la confirmación">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarVaciarTablas()">
                        <i class="bi bi-trash3"></i> Vaciar Todas las Tablas
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
        let modalEliminarUsuario = null;
        let modalVaciarTablas = null;
        let usuarioSeleccionado = null;
        
        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarUsuariosEvaluados();
            modalEliminarUsuario = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
            modalVaciarTablas = new bootstrap.Modal(document.getElementById('modalVaciarTablas'));
        });
        
        // Cargar usuarios evaluados
        function cargarUsuariosEvaluados() {
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'accion=obtener_usuarios_evaluados'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    mostrarError(`Error: ${data.error}<br><br>Haz clic en "Diagnóstico de Base de Datos" para más información.`);
                    return;
                }
                
                const tbody = document.getElementById('tbodyUsuarios');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay usuarios evaluados</td></tr>';
                    return;
                }
                
                data.forEach(usuario => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${usuario.id_cedula}</td>
                        <td>${usuario.nombres}</td>
                        <td>${usuario.apellidos}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminarUsuario(${usuario.id_cedula}, '${usuario.nombres} ${usuario.apellidos}')">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError(`Error de conexión: ${error.message}<br><br>Verifica la conexión a la base de datos.`);
            });
        }
        
        // Confirmar eliminación de usuario
        function confirmarEliminarUsuario(idCedula, nombreCompleto) {
            usuarioSeleccionado = idCedula;
            
            // Mostrar información del usuario
            document.getElementById('cedulaUsuarioEliminar').textContent = idCedula;
            document.getElementById('nombreUsuarioEliminar').textContent = nombreCompleto;
            document.getElementById('confirmacionEliminarUsuario').value = '';
            
            // Verificar tablas con datos
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=verificar_tablas_con_datos&id_cedula=${idCedula}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    mostrarError(data.error);
                    return;
                }
                
                const tablasConDatos = document.getElementById('tablasConDatos');
                if (Object.keys(data).length === 0) {
                    tablasConDatos.innerHTML = '<p class="text-muted">No se encontraron datos asociados a este usuario.</p>';
                } else {
                    let html = '<h6>Tablas que contienen datos:</h6><ul class="list-group">';
                    Object.entries(data).forEach(([tabla, cantidad]) => {
                        html += `<li class="list-group-item d-flex justify-content-between"><span>${tabla}</span><span class="badge bg-primary">${cantidad} registros</span></li>`;
                    });
                    html += '</ul>';
                    tablasConDatos.innerHTML = html;
                }
                
                modalEliminarUsuario.show();
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al verificar las tablas');
            });
        }
        
        // Ejecutar eliminación de usuario
        function ejecutarEliminacionUsuario() {
            const confirmacion = document.getElementById('confirmacionEliminarUsuario').value.trim();
            
            if (confirmacion !== 'ELIMINAR_USUARIO_COMPLETO') {
                mostrarError('Confirmación incorrecta. Por favor escribe exactamente: ELIMINAR_USUARIO_COMPLETO');
                return;
            }
            
            const formData = new FormData();
            formData.append('accion', 'eliminar_usuario_completo');
            formData.append('id_cedula', usuarioSeleccionado);
            formData.append('confirmacion', confirmacion);
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalEliminarUsuario.hide();
                mostrarResultado(data);
                if (data.success) {
                    cargarUsuariosEvaluados(); // Recargar tabla
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al eliminar el usuario');
            });
        }
        
        // Confirmar vaciar tablas
        function confirmarVaciarTablas() {
            document.getElementById('confirmacionVaciarTablas').value = '';
            modalVaciarTablas.show();
        }
        
        // Ejecutar vaciar tablas
        function ejecutarVaciarTablas() {
            const confirmacion = document.getElementById('confirmacionVaciarTablas').value.trim();
            
            if (confirmacion !== 'VACIAR_TODAS_LAS_TABLAS') {
                mostrarError('Confirmación incorrecta. Por favor escribe exactamente: VACIAR_TODAS_LAS_TABLAS');
                return;
            }
            
            const formData = new FormData();
            formData.append('accion', 'vaciar_todas_las_tablas');
            formData.append('confirmacion', confirmacion);
            
            fetch('procesar_tablas_principales.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalVaciarTablas.hide();
                mostrarResultado(data);
                if (data.success) {
                    cargarUsuariosEvaluados(); // Recargar tabla
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al vaciar las tablas');
            });
        }
        
        // Mostrar resultado
        function mostrarResultado(data) {
            const resultados = document.getElementById('resultados');
            const contenido = document.getElementById('contenidoResultados');
            
            if (data.success) {
                let html = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> ${data.mensaje}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            ${data.id_cedula ? `<p><strong>Usuario:</strong> ${data.id_cedula}</p>` : ''}
                            ${data.registros_eliminados ? `<p><strong>Registros Eliminados:</strong> ${data.registros_eliminados}</p>` : ''}
                            ${data.tablas_procesadas ? `<p><strong>Tablas Procesadas:</strong> ${Object.keys(data.tablas_procesadas).length}</p>` : ''}
                            ${data.tablas_truncadas ? `<p><strong>Tablas Truncadas:</strong> ${data.tablas_truncadas.length}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            ${data.archivos_eliminados ? `<p><strong>Archivos Eliminados:</strong> ${data.archivos_eliminados.length}</p>` : ''}
                            ${data.errores_archivos && data.errores_archivos.length > 0 ? `<p><strong>Errores:</strong> ${data.errores_archivos.length}</p>` : ''}
                        </div>
                    </div>
                `;
                
                if (data.archivos_eliminados && data.archivos_eliminados.length > 0) {
                    html += '<h6>Archivos eliminados:</h6><ul class="list-group">';
                    data.archivos_eliminados.forEach(archivo => {
                        html += `<li class="list-group-item">${archivo}</li>`;
                    });
                    html += '</ul>';
                }
                
                if (data.errores_archivos && data.errores_archivos.length > 0) {
                    html += '<h6>Errores al eliminar archivos:</h6><ul class="list-group">';
                    data.errores_archivos.forEach(error => {
                        html += `<li class="list-group-item text-danger">${error}</li>`;
                    });
                    html += '</ul>';
                }
                
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> ${data.error}
                    </div>
                `;
            }
            
            resultados.style.display = 'block';
            
            // Ocultar después de 15 segundos
            setTimeout(() => {
                resultados.style.display = 'none';
            }, 15000);
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
