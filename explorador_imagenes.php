<?php
/**
 * EXPLORADOR DE IMÁGENES
 * Módulo para explorar y gestionar imágenes en el servidor
 * Solo accesible para superadministradores
 * 
 * @author Sistema de Visitas
 * @version 1.0
 * @date 2024
 */

// Iniciar sesión
session_start();

// Verificar autenticación y rol de superadministrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: index.php');
    exit();
}

require_once 'app/Controllers/ExploradorImagenesController.php';

$explorador = new ExploradorImagenesController();
$currentPath = $_GET['path'] ?? '';
$content = $explorador->getFolderContent($currentPath);

// Obtener información del usuario
$usuario = $_SESSION['username'] ?? 'Superadministrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Imágenes - Sistema de Visitas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .file-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .file-item:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.15);
            transform: translateY(-2px);
        }
        
        .file-item.directory {
            border-color: #28a745;
        }
        
        .file-item.directory:hover {
            border-color: #1e7e34;
            box-shadow: 0 4px 8px rgba(40,167,69,0.15);
        }
        
        .file-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            color: #6c757d;
        }
        
        .file-item.directory .file-icon {
            color: #28a745;
        }
        
        .file-item.image .file-icon {
            color: #007bff;
        }
        
        .file-thumbnail {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        
        .file-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: #495057;
            word-break: break-word;
            line-height: 1.2;
        }
        
        .file-info {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .file-actions {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .file-item:hover .file-actions {
            opacity: 1;
        }
        
        .btn-delete {
            background: #dc3545;
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .toolbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .path-display {
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
            font-family: monospace;
            font-size: 0.875rem;
            flex: 1;
        }
        
        .stats {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .breadcrumb-container {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Menú lateral -->
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; min-height: 100vh;">
            <a href="resources/views/superadmin/dashboardSuperAdmin.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span class="fs-4 fw-bold">Superadmin</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="resources/views/superadmin/dashboardSuperAdmin.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="resources/views/superadmin/gestion_usuarios.php" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i>
                        Gestión de Usuarios
                    </a>
                </li>
                <li>
                    <a href="resources/views/superadmin/gestion_opciones.php" class="nav-link text-white">
                        <i class="bi bi-gear me-2"></i>
                        Gestión de Opciones
                    </a>
                </li>
                <li>
                    <a href="resources/views/superadmin/gestion_tablas_principales.php" class="nav-link text-white">
                        <i class="bi bi-database me-2"></i>
                        Tablas Principales
                    </a>
                </li>
                <li>
                    <a href="explorador_imagenes.php" class="nav-link active text-white">
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
                    <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="flex-grow-1">
            <!-- Header -->
            <div class="bg-white border-bottom p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h4 mb-0">
                            <i class="bi bi-images me-2"></i>
                            Explorador de Imágenes
                        </h1>
                        <p class="text-muted mb-0">Gestiona las imágenes del servidor</p>
                    </div>
                </div>
            </div>
            
            <!-- Toolbar -->
            <div class="toolbar">
                <button class="btn btn-outline-primary btn-sm" onclick="goBack()" id="btnBack" disabled>
                    <i class="bi bi-arrow-left me-1"></i>
                    Atrás
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="reloadContent()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Recargar
                </button>
                <div class="path-display" id="currentPathDisplay">
                    public/images<?php echo $currentPath ? '/' . $currentPath : ''; ?>
                </div>
            </div>
            
            <!-- Breadcrumb -->
            <div class="breadcrumb-container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" id="breadcrumb">
                        <?php if ($content['success']): ?>
                            <?php foreach ($content['breadcrumb'] as $index => $crumb): ?>
                                <li class="breadcrumb-item <?php echo $index === count($content['breadcrumb']) - 1 ? 'active' : ''; ?>">
                                    <?php if ($index === count($content['breadcrumb']) - 1): ?>
                                        <?php echo htmlspecialchars($crumb['name']); ?>
                                    <?php else: ?>
                                        <a href="?path=<?php echo urlencode($crumb['path']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($crumb['name']); ?>
                                        </a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="p-4">
                <?php if (!$content['success']): ?>
                    <div class="error-message">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($content['error']); ?>
                    </div>
                <?php else: ?>
                    <!-- Stats -->
                    <div class="stats">
                        <i class="bi bi-info-circle me-1"></i>
                        <?php echo $content['totalItems']; ?> elementos encontrados
                    </div>
                    
                    <!-- File Grid -->
                    <div class="file-grid" id="fileGrid">
                        <?php if (empty($content['items'])): ?>
                            <div class="col-12 text-center text-muted py-5">
                                <i class="bi bi-folder-x fs-1 mb-3"></i>
                                <p>Esta carpeta está vacía</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($content['items'] as $item): ?>
                                <div class="file-item <?php echo $item['type']; ?>" 
                                     data-path="<?php echo htmlspecialchars($item['path']); ?>"
                                     data-type="<?php echo $item['type']; ?>"
                                     data-name="<?php echo htmlspecialchars($item['name']); ?>">
                                    
                                    <?php if ($item['type'] === 'directory'): ?>
                                        <div class="file-icon">
                                            <i class="bi bi-folder-fill"></i>
                                        </div>
                                        <div class="file-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="file-info">
                                            <?php echo $explorador->formatFileSize($item['size']); ?>
                                        </div>
                                    <?php elseif ($item['type'] === 'image'): ?>
                                        <img src="public/images/<?php echo htmlspecialchars($item['path']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="file-thumbnail"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="file-icon" style="display: none;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="file-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="file-info">
                                            <?php echo $explorador->formatFileSize($item['size']); ?>
                                        </div>
                                        <div class="file-actions">
                                            <button class="btn btn-delete" onclick="deleteFile('<?php echo htmlspecialchars($item['path']); ?>', '<?php echo htmlspecialchars($item['name']); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="file-icon">
                                            <i class="bi bi-file-earmark"></i>
                                        </div>
                                        <div class="file-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="file-info">
                                            <?php echo $explorador->formatFileSize($item['size']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentPath = '<?php echo $currentPath; ?>';
        let pathHistory = [];
        
        // Navegación por doble clic
        document.addEventListener('DOMContentLoaded', function() {
            const fileItems = document.querySelectorAll('.file-item');
            
            fileItems.forEach(item => {
                item.addEventListener('dblclick', function() {
                    const path = this.dataset.path;
                    const type = this.dataset.type;
                    
                    if (type === 'directory') {
                        navigateToFolder(path);
                    }
                });
            });
            
            // Actualizar botón atrás
            updateBackButton();
        });
        
        function navigateToFolder(path) {
            pathHistory.push(currentPath);
            window.location.href = '?path=' + encodeURIComponent(path);
        }
        
        function goBack() {
            if (pathHistory.length > 0) {
                const previousPath = pathHistory.pop();
                window.location.href = '?path=' + encodeURIComponent(previousPath);
            } else {
                // Ir a la carpeta padre
                const pathParts = currentPath.split('/');
                pathParts.pop();
                const parentPath = pathParts.join('/');
                window.location.href = '?path=' + encodeURIComponent(parentPath);
            }
        }
        
        function reloadContent() {
            window.location.reload();
        }
        
        function updateBackButton() {
            const btnBack = document.getElementById('btnBack');
            const pathParts = currentPath.split('/').filter(part => part !== '');
            btnBack.disabled = pathParts.length === 0;
        }
        
        function deleteFile(path, name) {
            if (confirm('¿Seguro que desea eliminar la imagen "' + name + '"?\n\nEsta acción no se puede deshacer.')) {
                // Mostrar loading
                const fileItem = document.querySelector(`[data-path="${path}"]`);
                if (fileItem) {
                    fileItem.style.opacity = '0.5';
                    fileItem.style.pointerEvents = 'none';
                }
                
                // Enviar petición AJAX
                fetch('procesar_explorador_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&path=' + encodeURIComponent(path)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Eliminar el elemento del DOM
                        if (fileItem) {
                            fileItem.remove();
                        }
                        
                        // Mostrar mensaje de éxito
                        showMessage('success', 'Imagen eliminada correctamente');
                        
                        // Actualizar contador
                        updateStats();
                    } else {
                        showMessage('error', 'Error al eliminar la imagen: ' + data.error);
                        
                        // Restaurar el elemento
                        if (fileItem) {
                            fileItem.style.opacity = '1';
                            fileItem.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', 'Error de conexión');
                    
                    // Restaurar el elemento
                    if (fileItem) {
                        fileItem.style.opacity = '1';
                        fileItem.style.pointerEvents = 'auto';
                    }
                });
            }
        }
        
        function showMessage(type, message) {
            const alertClass = type === 'success' ? 'success-message' : 'error-message';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = alertClass;
            alertDiv.innerHTML = `<i class="${icon} me-2"></i>${message}`;
            
            const content = document.querySelector('.p-4');
            content.insertBefore(alertDiv, content.firstChild);
            
            // Remover el mensaje después de 5 segundos
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        function updateStats() {
            const fileItems = document.querySelectorAll('.file-item');
            const stats = document.querySelector('.stats');
            if (stats) {
                stats.innerHTML = `<i class="bi bi-info-circle me-1"></i>${fileItems.length} elementos encontrados`;
            }
        }
    </script>
</body>
</html>