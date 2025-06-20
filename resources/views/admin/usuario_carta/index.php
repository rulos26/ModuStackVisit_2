<?php
session_start();

// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_carta');
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Administrador';

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

// Consulta para obtener todos los registros de autorizaciones con verificación de completitud
$sql = "
    SELECT 
        a.id,
        a.cedula,
        a.nombres,
        a.correo,
        CASE 
            WHEN ua.id IS NOT NULL AND f.id IS NOT NULL AND fp.id IS NOT NULL 
            THEN 'Completada'
            WHEN ua.id IS NOT NULL OR f.id IS NOT NULL OR fp.id IS NOT NULL 
            THEN 'En Proceso'
            ELSE 'Pendiente'
        END as estado,
        CASE 
            WHEN ua.id IS NOT NULL AND f.id IS NOT NULL AND fp.id IS NOT NULL 
            THEN 1
            ELSE 0
        END as completada,
        CASE 
            WHEN (ua.id IS NOT NULL OR f.id IS NOT NULL OR fp.id IS NOT NULL) 
            AND NOT (ua.id IS NOT NULL AND f.id IS NOT NULL AND fp.id IS NOT NULL)
            THEN 1
            ELSE 0
        END as en_proceso,
        CASE 
            WHEN ua.id IS NULL AND f.id IS NULL AND fp.id IS NULL 
            THEN 1
            ELSE 0
        END as pendiente
    FROM autorizaciones a
    LEFT JOIN ubicacion_autorizacion ua ON a.cedula = ua.cedula
    LEFT JOIN firmas f ON a.cedula = f.cedula
    LEFT JOIN foto_perfil_autorizacion fp ON a.cedula = fp.cedula
    ORDER BY a.id DESC
";

$result = $mysqli->query($sql);

// Contadores para las estadísticas
$total_usuarios = 0;
$cartas_completadas = 0;
$en_proceso = 0;
$pendientes = 0;

if ($result) {
    $total_usuarios = $result->num_rows;
    
    // Reiniciar el puntero del resultado para contar las estadísticas
    $result->data_seek(0);
    
    while ($row = $result->fetch_assoc()) {
        $cartas_completadas += $row['completada'];
        $en_proceso += $row['en_proceso'];
        $pendientes += $row['pendiente'];
    }
    
    // Reiniciar el puntero para mostrar los datos en la tabla
    $result->data_seek(0);
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
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                                    <td>
                                                        <?php 
                                                        $estado = $row['estado'];
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
                                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
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
    </script>
</body>
</html> 