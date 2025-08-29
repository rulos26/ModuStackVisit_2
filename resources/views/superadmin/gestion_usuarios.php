<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

require_once __DIR__ . '/../../app/Controllers/SuperAdminController.php';
use App\Controllers\SuperAdminController;

$superAdmin = new SuperAdminController();
$mensaje = '';
$usuarios = [];

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'crear':
            $resultado = $superAdmin->gestionarUsuarios('crear', [
                'nombre' => $_POST['nombre'],
                'cedula' => $_POST['cedula'],
                'rol' => $_POST['rol'],
                'correo' => $_POST['correo'],
                'usuario' => $_POST['usuario'],
                'password' => $_POST['password']
            ]);
            $mensaje = $resultado['success'] ?? $resultado['error'];
            break;
            
        case 'actualizar':
            $resultado = $superAdmin->gestionarUsuarios('actualizar', [
                'id' => $_POST['id'],
                'nombre' => $_POST['nombre'],
                'cedula' => $_POST['cedula'],
                'rol' => $_POST['rol'],
                'correo' => $_POST['correo'],
                'usuario' => $_POST['usuario'],
                'password' => $_POST['password'] ?? ''
            ]);
            $mensaje = $resultado['success'] ?? $resultado['error'];
            break;
            
        case 'eliminar':
            $resultado = $superAdmin->gestionarUsuarios('eliminar', ['id' => $_POST['id']]);
            $mensaje = $resultado['success'] ?? $resultado['error'];
            break;
    }
}

// Obtener lista de usuarios
$usuarios = $superAdmin->gestionarUsuarios('listar');
$usuario = $_SESSION['username'] ?? 'Superadministrador';
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
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
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
                    <strong><?php echo htmlspecialchars($usuario); ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="../../../public/cerrar_sesion.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-grow-1">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="bi bi-people me-2"></i>
                        Gestión de Usuarios
                    </span>
                    <div class="d-flex align-items-center">
                        <a href="../../../public/cerrar_sesion.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Contenido -->
            <div class="container-fluid py-4">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo strpos($mensaje, 'exitosamente') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($mensaje); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Botón para crear nuevo usuario -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>
                            Lista de Usuarios del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Cédula</th>
                                        <th>Usuario</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (is_array($usuarios)): ?>
                                        <?php foreach ($usuarios as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($user['cedula']); ?></td>
                                                <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                                                <td><?php echo htmlspecialchars($user['correo']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $user['rol'] == 1 ? 'primary' : 
                                                            ($user['rol'] == 2 ? 'success' : 'warning'); 
                                                    ?>">
                                                        <?php echo htmlspecialchars($user['rol_nombre']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['fecha_creacion']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarUsuario(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nombre']); ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No hay usuarios registrados</td>
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

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="crearUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Evaluador</option>
                                <option value="3">Superadministrador</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="edit_cedula" name="cedula" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="edit_usuario" name="usuario" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="edit_correo" name="correo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_rol" class="form-label">Rol</label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="1">Administrador</option>
                                <option value="2">Evaluador</option>
                                <option value="3">Superadministrador</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Nueva Contraseña (dejar vacío para mantener la actual)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar al usuario <strong id="nombre_usuario_eliminar"></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" id="id_usuario_eliminar">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarUsuario(usuario) {
            document.getElementById('edit_id').value = usuario.id;
            document.getElementById('edit_nombre').value = usuario.nombre;
            document.getElementById('edit_cedula').value = usuario.cedula;
            document.getElementById('edit_usuario').value = usuario.usuario;
            document.getElementById('edit_correo').value = usuario.correo;
            document.getElementById('edit_rol').value = usuario.rol;
            document.getElementById('edit_password').value = '';
            
            new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
        }

        function eliminarUsuario(id, nombre) {
            document.getElementById('id_usuario_eliminar').value = id;
            document.getElementById('nombre_usuario_eliminar').textContent = nombre;
            
            new bootstrap.Modal(document.getElementById('eliminarUsuarioModal')).show();
        }
    </script>
</body>
</html>
