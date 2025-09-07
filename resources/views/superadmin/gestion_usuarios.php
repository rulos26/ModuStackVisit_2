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
$usuarios = $superAdmin->gestionarUsuarios('listar');
$usuario = $_SESSION['username'] ?? 'Superadministrador';

// Procesar mensajes de respuesta
$mensaje = '';
$tipoMensaje = '';
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $tipoMensaje = $_GET['tipo'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Superadministrador</title>
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
        .password-toggle {
            position: relative;
        }
        .password-toggle .form-control {
            padding-right: 50px;
        }
        .password-toggle .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border: none;
            background: transparent;
        }
        .btn-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .usuario-protegido {
            background-color: rgba(255, 193, 7, 0.1);
        }
        .badge-protegido {
            font-size: 0.75em;
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
                    <a href="gestion_usuarios.php" class="nav-link active text-white">
                        <i class="bi bi-people me-2"></i>
                        Gestión de Usuarios
                    </a>
                </li>
                <li>
                    <a href="gestion_opciones.php" class="nav-link text-white">
                        <i class="bi bi-gear-wide-connected me-2"></i>
                        Gestión de Opciones
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
                <li>
                    <a href="gestion_tablas_principales.php" class="nav-link text-white">
                        <i class="bi bi-database me-2"></i>
                        Tablas Principales
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
                        <i class="bi bi-people-fill me-2"></i>
                        Gestión de Usuarios
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
                                            <?php echo is_array($usuarios) ? count($usuarios) : 0; ?>
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
                                            Usuarios Activos
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $activos = 0;
                                            if (is_array($usuarios)) {
                                                foreach ($usuarios as $user) {
                                                    if ($user['activo']) $activos++;
                                                }
                                            }
                                            echo $activos;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-check fa-2x text-gray-300"></i>
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
                                            Administradores
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $admins = 0;
                                            if (is_array($usuarios)) {
                                                foreach ($usuarios as $user) {
                                                    if ($user['rol'] == 1) $admins++;
                                                }
                                            }
                                            echo $admins;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-shield-fill-check fa-2x text-gray-300"></i>
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
                                            Evaluadores
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $evaluadores = 0;
                                            if (is_array($usuarios)) {
                                                foreach ($usuarios as $user) {
                                                    if ($user['rol'] == 2) $evaluadores++;
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

                <!-- Botón para crear nuevo usuario -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-table me-2"></i>
                            Lista de Usuarios del Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Información sobre usuarios protegidos -->
                        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Usuarios Protegidos:</strong> Los usuarios marcados con 
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-shield-lock me-1"></i>
                                Protegido
                            </span> 
                            son cuentas maestras del sistema que NO pueden ser modificadas, eliminadas o desactivadas por seguridad.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tablaUsuarios">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Cédula</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Último Acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (is_array($usuarios) && !empty($usuarios)): ?>
                                        <?php foreach ($usuarios as $user): ?>
                                            <?php $esProtegido = $superAdmin->esUsuarioProtegido($user['id']); ?>
                                            <tr class="<?php echo $esProtegido ? 'usuario-protegido' : ''; ?>">
                                                <td><?php echo $user['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($user['usuario']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($user['cedula']); ?></td>
                                                <td><?php echo htmlspecialchars($user['correo']); ?></td>
                                                <td>
                                                    <?php
                                                    $rolText = '';
                                                    $rolClass = '';
                                                    switch ($user['rol']) {
                                                        case 1:
                                                            $rolText = 'Administrador';
                                                            $rolClass = 'badge bg-primary';
                                                            break;
                                                        case 2:
                                                            $rolText = 'Cliente';
                                                            $rolClass = 'badge bg-success';
                                                            break;
                                                        case 4:
                                                            $rolText = 'Evaluador';
                                                            $rolClass = 'badge bg-info';
                                                            break;
                                                        case 3:
                                                            $rolText = 'Superadministrador';
                                                            $rolClass = 'badge bg-danger';
                                                            break;
                                                        default:
                                                            $rolText = 'Desconocido';
                                                            $rolClass = 'badge bg-secondary';
                                                    }
                                                    ?>
                                                    <span class="<?php echo $rolClass; ?>"><?php echo $rolText; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($user['activo']): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($user['ultimo_acceso']) {
                                                        echo date('d/m/Y H:i', strtotime($user['ultimo_acceso']));
                                                    } else {
                                                        echo '<span class="text-muted">Nunca</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Verificar si el usuario está protegido
                                                    $esProtegido = $superAdmin->esUsuarioProtegido($user['id']);
                                                    $infoProteccion = $superAdmin->getInfoProteccionUsuarioPorId($user['id']);
                                                    ?>
                                                    
                                                    <?php if ($esProtegido): ?>
                                                        <!-- Usuario protegido - mostrar solo badge y botones deshabilitados -->
                                                        <div class="text-center">
                                                            <span class="badge bg-warning text-dark mb-2">
                                                                <i class="bi bi-shield-lock me-1"></i>
                                                                Protegido
                                                            </span>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-action" disabled 
                                                                        title="Usuario protegido del sistema">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-action" disabled 
                                                                        title="Usuario protegido del sistema">
                                                                    <i class="bi bi-<?php echo $user['activo'] ? 'pause' : 'play'; ?>"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-action" disabled 
                                                                        title="Usuario protegido del sistema">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <!-- Usuario normal - mostrar botones habilitados -->
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-action" 
                                                                    onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-warning btn-action" 
                                                                    onclick="cambiarEstadoUsuario(<?php echo $user['id']; ?>, '<?php echo $user['activo'] ? 'desactivar' : 'activar'; ?>')">
                                                                <i class="bi bi-<?php echo $user['activo'] ? 'pause' : 'play'; ?>"></i>
                                                            </button>
                                                            <?php if ($user['rol'] != 3): // No permitir eliminar superadministradores ?>
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                                                        onclick="confirmarEliminarUsuario(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['usuario']); ?>')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="bi bi-inbox me-2"></i>
                                                No hay usuarios registrados
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

    <!-- Modal para crear/editar usuario -->
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearUsuarioLabel">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUsuario" action="procesar_usuario.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="usuario_id" name="usuario_id" value="">
                        <input type="hidden" id="accion" name="accion" value="crear">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           placeholder="Usuario" required maxlength="50">
                                    <label for="usuario">Usuario *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Nombre completo" required maxlength="100">
                                    <label for="nombre">Nombre completo *</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="cedula" name="cedula" 
                                           placeholder="Cédula" required maxlength="20">
                                    <label for="cedula">Cédula *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           placeholder="Correo electrónico" required maxlength="100">
                                    <label for="correo">Correo electrónico *</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="rol" name="rol" required>
                                        <option value="">Seleccionar rol</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Evaluador</option>
                                        <option value="3">Superadministrador</option>
                                    </select>
                                    <label for="rol">Rol *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating password-toggle">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Contraseña" required maxlength="255">
                                    <label for="password">Contraseña *</label>
                                    <button type="button" class="btn" onclick="togglePassword('password')">
                                        <i class="bi bi-eye-slash" id="password-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="activo" name="activo" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <label for="activo">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4 pt-3">
                                    <input class="form-check-input" type="checkbox" id="enviar_credenciales" name="enviar_credenciales" value="1">
                                    <label class="form-check-label" for="enviar_credenciales">
                                        Enviar credenciales por correo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Guardar Usuario
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
                    <p>¿Estás seguro de que deseas eliminar al usuario <strong id="usuarioEliminar"></strong>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="eliminarUsuario()">
                        <i class="bi bi-trash me-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let usuarioAEliminar = null;

        // Función para editar usuario
        function editarUsuario(usuario) {
            document.getElementById('usuario_id').value = usuario.id;
            document.getElementById('accion').value = 'actualizar';
            document.getElementById('usuario').value = usuario.usuario;
            document.getElementById('nombre').value = usuario.nombre;
            document.getElementById('cedula').value = usuario.cedula;
            document.getElementById('correo').value = usuario.correo;
            document.getElementById('rol').value = usuario.rol;
            document.getElementById('activo').value = usuario.activo ? '1' : '0';
            document.getElementById('password').required = false;
            document.getElementById('password').placeholder = 'Dejar en blanco para mantener la actual';
            
            document.getElementById('modalCrearUsuarioLabel').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
            document.querySelector('#modalCrearUsuario .btn-primary').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Usuario';
            
            const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
            modal.show();
        }

        // Función para cambiar estado del usuario
        function cambiarEstadoUsuario(id, accion) {
            if (confirm(`¿Estás seguro de que deseas ${accion} este usuario?`)) {
                window.location.href = `procesar_usuario.php?accion=${accion}&usuario_id=${id}`;
            }
        }

        // Función para confirmar eliminación
        function confirmarEliminarUsuario(id, nombre) {
            usuarioAEliminar = id;
            document.getElementById('usuarioEliminar').textContent = nombre;
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
            modal.show();
        }

        // Función para eliminar usuario
        function eliminarUsuario() {
            if (usuarioAEliminar) {
                window.location.href = `procesar_usuario.php?accion=eliminar&usuario_id=${usuarioAEliminar}`;
            }
        }

        // Función para mostrar/ocultar contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }

        // Resetear modal al cerrar
        document.getElementById('modalCrearUsuario').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formUsuario').reset();
            document.getElementById('usuario_id').value = '';
            document.getElementById('accion').value = 'crear';
            document.getElementById('password').required = true;
            document.getElementById('password').placeholder = 'Contraseña';
            document.getElementById('modalCrearUsuarioLabel').innerHTML = '<i class="bi bi-person-plus me-2"></i>Crear Nuevo Usuario';
            document.querySelector('#modalCrearUsuario .btn-primary').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Usuario';
        });

        // Validación del formulario
        document.getElementById('formUsuario').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const accion = document.getElementById('accion').value;
            
            if (accion === 'crear' && password.value.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                password.focus();
                return false;
            }
        });
    </script>
</body>
</html>
