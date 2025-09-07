<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

require_once __DIR__ . '/../../../app/Controllers/OpcionesController.php';
use App\Controllers\OpcionesController;

$opcionesController = new OpcionesController();
$usuario = $_SESSION['username'] ?? 'Superadministrador';

// Obtener la tabla seleccionada
$tablaSeleccionada = $_GET['tabla'] ?? 'opc_concepto_final';

// Procesar mensajes de respuesta
$mensaje = '';
$tipoMensaje = '';
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $tipoMensaje = $_GET['tipo'] ?? 'info';
}

// Obtener opciones de la tabla seleccionada
$opciones = $opcionesController->obtenerOpciones($tablaSeleccionada);
$estadisticas = $opcionesController->obtenerEstadisticas($tablaSeleccionada);

// Mapeo de nombres amigables para las tablas
$nombresTablas = [
    'opc_concepto_final' => 'Conceptos Finales',
    'opc_concepto_seguridad' => 'Conceptos de Seguridad',
    'opc_conviven' => 'Convivencia',
    'opc_cuenta' => 'Tipos de Cuenta',
    'opc_entidad' => 'Entidades',
    'opc_estados' => 'Estados',
    'opc_estado_civiles' => 'Estados Civiles',
    'opc_estado_vivienda' => 'Estados de Vivienda',
    'opc_estaturas' => 'Estaturas',
    'opc_estratos' => 'Estratos',
    'opc_genero' => 'Géneros',
    'opc_informacion_judicial' => 'Información Judicial',
    'opc_inventario_enseres' => 'Inventario de Enseres',
    'opc_jornada' => 'Jornadas Laborales',
    'opc_marca' => 'Marcas',
    'opc_modelo' => 'Modelos',
    'opc_nivel_academico' => 'Niveles Académicos',
    'opc_num_hijos' => 'Número de Hijos',
    'opc_ocupacion' => 'Ocupaciones',
    'opc_parametro' => 'Parámetros del Sistema',
    'opc_parentesco' => 'Parentescos',
    'opc_peso' => 'Pesos',
    'opc_propiedad' => 'Tipos de Propiedad',
    'opc_resultado' => 'Resultados',
    'opc_rh' => 'Tipos de RH',
    'opc_sector' => 'Sectores',
    'opc_servicios_publicos' => 'Servicios Públicos',
    'opc_tipo_cuenta' => 'Tipos de Cuenta',
    'opc_tipo_documentos' => 'Tipos de Documentos',
    'opc_tipo_inversion' => 'Tipos de Inversión',
    'opc_tipo_vivienda' => 'Tipos de Vivienda',
    'opc_vehiculo' => 'Tipos de Vehículo',
    'opc_viven' => 'Condiciones de Vida'
];

$tablasValidas = array_keys($nombresTablas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Opciones del Sistema - Superadministrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../public/css/styles.css">
    <style>
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-action {
            margin: 2px;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .selector-tabla {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .tabla-option {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .tabla-option:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        .tabla-option.active {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .tabla-option h6 {
            margin: 0;
            color: white;
            font-weight: 600;
        }
        .tabla-option p {
            margin: 5px 0 0 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9em;
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
                    <a href="gestion_opciones.php" class="nav-link active text-white">
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
                        <i class="bi bi-gear-wide-connected me-2"></i>
                        Gestión de Opciones del Sistema
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
                <!-- Mensajes de respuesta -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <?php echo htmlspecialchars($mensaje); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Selector de tabla -->
                <div class="selector-tabla">
                    <h4 class="mb-3">
                        <i class="bi bi-table me-2"></i>
                        Seleccionar Tabla de Opciones
                    </h4>
                    <div class="row">
                        <?php foreach ($tablasValidas as $tabla): ?>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="tabla-option <?php echo $tabla === $tablaSeleccionada ? 'active' : ''; ?>" 
                                     onclick="cambiarTabla('<?php echo $tabla; ?>')">
                                    <h6>
                                        <i class="bi bi-table me-2"></i>
                                        <?php echo $nombresTablas[$tabla]; ?>
                                    </h6>
                                    <p><?php echo $tabla; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tarjetas de estadísticas -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2 stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Opciones
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $estadisticas['total']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-list-ul fa-2x text-gray-300"></i>
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
                                            Tabla Actual
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $nombresTablas[$tablaSeleccionada]; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-table fa-2x text-gray-300"></i>
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
                                            Acciones Disponibles
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            Crear, Editar, Eliminar
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-tools fa-2x text-gray-300"></i>
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
                                            Sistema
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            Activo
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para crear nueva opción -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearOpcion">
                            <i class="bi bi-plus-circle me-2"></i>
                            Crear Nueva Opción en <?php echo $nombresTablas[$tablaSeleccionada]; ?>
                        </button>
                    </div>
                </div>

                <!-- Tabla de opciones -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-table me-2"></i>
                            Opciones de <?php echo $nombresTablas[$tablaSeleccionada]; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tablaOpciones">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (is_array($opciones) && !empty($opciones)): ?>
                                        <?php foreach ($opciones as $opcion): ?>
                                            <tr>
                                                <td><?php echo $opcion[$opcionesController->obtenerColumnaId($tablaSeleccionada)]; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($opcion['nombre']); ?></strong>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-action" 
                                                                onclick="editarOpcion(<?php echo htmlspecialchars(json_encode($opcion)); ?>)">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                                                onclick="confirmarEliminarOpcion(<?php echo $opcion[$opcionesController->obtenerColumnaId($tablaSeleccionada)]; ?>, '<?php echo htmlspecialchars($opcion['nombre']); ?>')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                <i class="bi bi-inbox me-2"></i>
                                                No hay opciones registradas en esta tabla
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

    <!-- Modal para crear/editar opción -->
    <div class="modal fade" id="modalCrearOpcion" tabindex="-1" aria-labelledby="modalCrearOpcionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearOpcionLabel">
                        <i class="bi bi-plus-circle me-2"></i>
                        Crear Nueva Opción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formOpcion" action="procesar_opcion.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="opcion_id" name="id" value="">
                        <input type="hidden" id="accion" name="accion" value="crear">
                        <input type="hidden" name="tabla" value="<?php echo htmlspecialchars($tablaSeleccionada); ?>">
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   placeholder="Nombre de la opción" required maxlength="50">
                            <label for="nombre">Nombre de la Opción *</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Guardar Opción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar la opción <strong id="opcionEliminar"></strong>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="eliminarOpcion()">
                        <i class="bi bi-trash me-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let opcionAEliminar = null;

        // Función para cambiar de tabla
        function cambiarTabla(tabla) {
            window.location.href = `gestion_opciones.php?tabla=${encodeURIComponent(tabla)}`;
        }

        // Función para editar opción
        function editarOpcion(opcion) {
            document.getElementById('opcion_id').value = opcion.id || opcion.id_concepto_final || opcion.id_concepto_seguridad || opcion.id_conviven || opcion.id_entidad || opcion.id_estado || opcion.id_jornada || opcion.id_marca || opcion.id_modelo || opcion.id_resultado || opcion.id_tipo_cuenta || opcion.id_tipo_inversion || opcion.id_vehiculo || opcion.id_vive_candidato;
            document.getElementById('accion').value = 'actualizar';
            document.getElementById('nombre').value = opcion.nombre;
            
            document.getElementById('modalCrearOpcionLabel').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Opción';
            document.querySelector('#modalCrearOpcion .btn-primary').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Opción';
            
            const modal = new bootstrap.Modal(document.getElementById('modalCrearOpcion'));
            modal.show();
        }

        // Función para confirmar eliminación
        function confirmarEliminarOpcion(id, nombre) {
            opcionAEliminar = id;
            document.getElementById('opcionEliminar').textContent = nombre;
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
            modal.show();
        }

        // Función para eliminar opción
        function eliminarOpcion() {
            if (opcionAEliminar) {
                const tabla = '<?php echo $tablaSeleccionada; ?>';
                window.location.href = `procesar_opcion.php?accion=eliminar&id=${opcionAEliminar}&tabla=${encodeURIComponent(tabla)}`;
            }
        }

        // Resetear modal al cerrar
        document.getElementById('modalCrearOpcion').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formOpcion').reset();
            document.getElementById('opcion_id').value = '';
            document.getElementById('accion').value = 'crear';
            document.getElementById('modalCrearOpcionLabel').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Crear Nueva Opción';
            document.querySelector('#modalCrearOpcion .btn-primary').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Opción';
        });

        // Validación del formulario
        document.getElementById('formOpcion').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            
            if (nombre.length === 0) {
                e.preventDefault();
                alert('El nombre de la opción es obligatorio.');
                document.getElementById('nombre').focus();
                return false;
            }
            
            if (nombre.length > 50) {
                e.preventDefault();
                alert('El nombre no puede exceder 50 caracteres.');
                document.getElementById('nombre').focus();
                return false;
            }
        });
    </script>
</body>
</html>
