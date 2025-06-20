<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_carta');
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Administrador';

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

// 1. Obtener todas las cédulas de autorizaciones
$sql_autorizaciones = "SELECT id, cedula, nombres, correo FROM autorizaciones ORDER BY id DESC";
$result_autorizaciones = $mysqli->query($sql_autorizaciones);

$usuarios = [];
$total_usuarios = 0;
$cartas_completadas = 0;
$en_proceso = 0;
$pendientes = 0;

if ($result_autorizaciones) {
    $total_usuarios = $result_autorizaciones->num_rows;
    
    while ($row = $result_autorizaciones->fetch_assoc()) {
        $cedula = $row['cedula'];
        
        // 2. Verificar si existe en ubicacion_autorizacion
        $sql_ubicacion = "SELECT id FROM ubicacion_autorizacion WHERE id_cedula = '$cedula'";
        $result_ubicacion = $mysqli->query($sql_ubicacion);
        $tiene_ubicacion = $result_ubicacion && $result_ubicacion->num_rows > 0;
        
        // 3. Verificar si existe en firmas
        $sql_firmas = "SELECT id FROM firmas WHERE id_cedula = '$cedula'";
        $result_firmas = $mysqli->query($sql_firmas);
        $tiene_firmas = $result_firmas && $result_firmas->num_rows > 0;
        
        // 4. Verificar si existe en foto_perfil_autorizacion
        $sql_foto = "SELECT id FROM foto_perfil_autorizacion WHERE id_cedula = '$cedula'";
        $result_foto = $mysqli->query($sql_foto);
        $tiene_foto = $result_foto && $result_foto->num_rows > 0;
        
        // 5. Determinar estado
        if ($tiene_ubicacion && $tiene_firmas && $tiene_foto) {
            $estado = 'Completada';
            $cartas_completadas++;
        } elseif ($tiene_ubicacion || $tiene_firmas || $tiene_foto) {
            $estado = 'En Proceso';
            $en_proceso++;
        } else {
            $estado = 'Pendiente';
            $pendientes++;
        }
        
        // 6. Agregar a array de usuarios
        $usuarios[] = [
            'id' => $row['id'],
            'cedula' => $row['cedula'],
            'nombres' => $row['nombres'],
            'correo' => $row['correo'],
            'estado' => $estado,
            'tiene_ubicacion' => $tiene_ubicacion,
            'tiene_firmas' => $tiene_firmas,
            'tiene_foto' => $tiene_foto
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Usuarios Carta</title>
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
                        <a class="nav-link text-white active" href="/ModuStackVisit_2/resources/views/admin/usuario_carta/index.php">
                            <i class="fas fa-envelope me-2"></i>Usuarios Carta
                        </a>
                        <a class="nav-link text-white" href="/ModuStackVisit_2/resources/views/admin/usuario_evaluacion/index.php">
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
                                <i class="fas fa-envelope me-2"></i>Gestión de Usuarios Carta
                            </h2>
                            <p class="text-muted">Administra los usuarios del módulo de carta de autorización</p>
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
                                    <h4 class="card-title">Total Usuarios</h4>
                                    <h2 class="text-primary"><?php echo $total_usuarios; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h4 class="card-title">Cartas Completadas</h4>
                                    <h2 class="text-success"><?php echo $cartas_completadas; ?></h2>
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
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <h4 class="card-title">Pendientes</h4>
                                    <h2 class="text-danger"><?php echo $pendientes; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Lista de Usuarios Carta
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar usuario por cédula o nombre...">
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                                    </button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="usersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cédula</th>
                                            <th>Nombres</th>
                                            <th>Correo</th>
                                            <th>Estado</th>
                                            <th>Fecha Creación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($usuarios)): ?>
                                            <?php foreach ($usuarios as $usuario): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($usuario['cedula']); ?></td>
                                                    <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                                                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                                    <td>
                                                        <?php 
                                                        $estado = $usuario['estado'];
                                                        $badgeClass = '';
                                                        switch($estado) {
                                                            case 'Completada':
                                                                $badgeClass = 'bg-success';
                                                                break;
                                                            case 'En Proceso':
                                                                $badgeClass = 'bg-warning';
                                                                break;
                                                            case 'Pendiente':
                                                                $badgeClass = 'bg-danger';
                                                                break;
                                                            default:
                                                                $badgeClass = 'bg-secondary';
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y'); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <?php if ($usuario['estado'] !== 'Pendiente'): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="eliminarUsuario('<?php echo htmlspecialchars($usuario['cedula']); ?>', '<?php echo htmlspecialchars($usuario['nombres']); ?>')" 
                                                                        title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="editarUsuario('<?php echo htmlspecialchars($usuario['cedula']); ?>', '<?php echo htmlspecialchars($usuario['nombres']); ?>')" 
                                                                    title="Editar/Completar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p>No hay usuarios registrados</p>
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
            const table = document.getElementById('usersTable');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                rows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    let found = false;

                    // Buscar en las columnas de cédula (índice 1) y nombres (índice 2)
                    if (cells.length > 2) {
                        const cedula = cells[1].textContent.toLowerCase();
                        const nombres = cells[2].textContent.toLowerCase();

                        if (cedula.includes(searchTerm) || nombres.includes(searchTerm)) {
                            found = true;
                        }
                    }

                    // Mostrar u ocultar la fila según el resultado de la búsqueda
                    if (found) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Mostrar mensaje si no hay resultados
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                const noResultsRow = tbody.querySelector('.no-results');
                
                if (visibleRows.length === 0 && searchTerm !== '') {
                    if (!noResultsRow) {
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-results';
                        newRow.innerHTML = `
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <p>No se encontraron resultados para "${searchTerm}"</p>
                            </td>
                        `;
                        tbody.appendChild(newRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            });

            // Limpiar búsqueda al hacer clic en el ícono de búsqueda
            document.querySelector('.input-group-text').addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            });
        });

        // Función para eliminar usuario
        function eliminarUsuario(cedula, nombres) {
            if (confirm(`¿Estás seguro de que deseas eliminar al usuario ${nombres} (Cédula: ${cedula})?\n\nEsta acción eliminará todos los registros relacionados y no se puede deshacer.`)) {
                // Mostrar indicador de carga
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                // Realizar la eliminación
                fetch('eliminar_usuario.php', {
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
                        alert('Usuario eliminado exitosamente');
                        // Recargar la página para actualizar la tabla
                        location.reload();
                    } else {
                        alert('Error al eliminar usuario: ' + data.message);
                        // Restaurar botón
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar usuario. Por favor, inténtalo de nuevo.');
                    // Restaurar botón
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
            }
        }

        // Función para editar/completar usuario
        function editarUsuario(cedula, nombres) {
            // Mostrar indicador de carga
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            // Verificar estado de tablas
            fetch('verificar_tablas_usuario.php', {
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
                    mostrarModalTablasFaltantes(data, cedula, nombres);
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

        // Función para mostrar modal con tablas faltantes
        function mostrarModalTablasFaltantes(data, cedula, nombres) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'modalTablasFaltantes';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-tasks me-2"></i>Estado de Completitud - ${nombres}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Progreso:</strong> ${data.tablas_completadas} de ${data.total_tablas} tablas completadas (${data.porcentaje_completado}%)
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
                                                    '<span class="badge bg-success">Completada</span>' : 
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
                                            <strong>Acceso restringido:</strong> Se requiere completar al menos 3 de 4 módulos para acceder al sistema.
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