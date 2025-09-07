<?php
session_start();

// Verificar que el usuario esté autenticado y sea Administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Imágenes - Administrador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .explorador-container {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .sidebar {
            background-color: #212529;
            min-height: 100vh;
            color: white;
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
        
        .breadcrumb {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        
        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        
        .contenido-carpeta {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }
        
        .item-carpeta {
            text-align: center;
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .item-carpeta:hover {
            background-color: #f8f9fa;
            border-color: #007bff;
            transform: translateY(-2px);
        }
        
        .item-carpeta .icono {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .item-carpeta .nombre {
            font-size: 0.9rem;
            font-weight: 500;
            word-break: break-word;
        }
        
        .carpeta .icono {
            color: #ffc107;
        }
        
        .imagen .icono {
            color: #28a745;
        }
        
        .archivo .icono {
            color: #6c757d;
        }
        
        .thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        
        .acciones-item {
            position: absolute;
            top: 5px;
            right: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .item-carpeta:hover .acciones-item {
            opacity: 1;
        }
        
        .btn-accion {
            width: 30px;
            height: 30px;
            padding: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: block;
        }
        
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .item-container {
            position: relative;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Menú lateral -->
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; min-height: 100vh;">
            <a href="../dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-images me-2"></i>
                <span class="fs-4 fw-bold">Explorador</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="../dashboard.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="explorador_imagenes.php" class="nav-link active text-white">
                        <i class="bi bi-images me-2"></i>
                        Explorador de Imágenes
                    </a>
                </li>
                <li>
                    <a href="../gestion_usuarios.php" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i>
                        Gestión de Usuarios
                    </a>
                </li>
                <li>
                    <a href="../gestion_opciones.php" class="nav-link text-white">
                        <i class="bi bi-gear me-2"></i>
                        Gestión de Opciones
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                    <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrador'); ?></strong>
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
                        <i class="bi bi-images me-2"></i>
                        Explorador de Imágenes
                    </span>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">
                            <i class="bi bi-clock"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </span>
                        <button class="btn btn-outline-light btn-sm me-2" onclick="recargarContenido()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Recargar
                        </button>
                        <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Contenido del explorador -->
            <div class="container-fluid py-4">
                <!-- Breadcrumb -->
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb" id="breadcrumb">
                                <!-- Se llenará dinámicamente -->
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Contenido de la carpeta -->
                <div class="row">
                    <div class="col-12">
                        <div class="contenido-carpeta">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="bi bi-folder me-2"></i>
                                    Contenido de la carpeta
                                </h5>
                                <div class="loading" id="loading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid-container" id="contenido-carpeta">
                                <!-- Se llenará dinámicamente -->
                            </div>
                            
                            <div class="text-center text-muted mt-4" id="mensaje-vacio" style="display: none;">
                                <i class="bi bi-folder-x fs-1"></i>
                                <p class="mt-2">Esta carpeta está vacía</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Seguro que desea eliminar esta imagen?</p>
                    <p class="text-muted small" id="nombre-archivo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminar">
                        <i class="bi bi-trash me-1"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        let rutaActual = '<?php echo htmlspecialchars($rutaActual ?? ''); ?>';
        let archivoAEliminar = null;
        let modalEliminar = null;
        
        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
            cargarContenido();
        });
        
        // Cargar contenido de la carpeta
        function cargarContenido() {
            mostrarLoading(true);
            
            const url = `procesar_explorador.php?accion=obtener_contenido&ruta=${encodeURIComponent(rutaActual)}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarBreadcrumb(data.breadcrumb);
                        mostrarContenido(data.contenido);
                    } else {
                        mostrarError(data.error || 'Error al cargar el contenido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error de conexión');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }
        
        // Mostrar breadcrumb
        function mostrarBreadcrumb(breadcrumb) {
            const breadcrumbElement = document.getElementById('breadcrumb');
            breadcrumbElement.innerHTML = '';
            
            breadcrumb.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'breadcrumb-item';
                
                if (index === breadcrumb.length - 1) {
                    li.classList.add('active');
                    li.textContent = item.nombre;
                } else {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.textContent = item.nombre;
                    a.onclick = (e) => {
                        e.preventDefault();
                        navegarACarpeta(item.ruta);
                    };
                    li.appendChild(a);
                }
                
                breadcrumbElement.appendChild(li);
            });
        }
        
        // Mostrar contenido de la carpeta
        function mostrarContenido(contenido) {
            const contenedor = document.getElementById('contenido-carpeta');
            const mensajeVacio = document.getElementById('mensaje-vacio');
            
            contenedor.innerHTML = '';
            
            const totalItems = contenido.carpetas.length + contenido.archivos.length;
            
            if (totalItems === 0) {
                mensajeVacio.style.display = 'block';
                return;
            }
            
            mensajeVacio.style.display = 'none';
            
            // Mostrar carpetas primero
            contenido.carpetas.forEach(carpeta => {
                const item = crearItemCarpeta(carpeta);
                contenedor.appendChild(item);
            });
            
            // Mostrar archivos después
            contenido.archivos.forEach(archivo => {
                const item = crearItemArchivo(archivo);
                contenedor.appendChild(item);
            });
        }
        
        // Crear elemento de carpeta
        function crearItemCarpeta(carpeta) {
            const div = document.createElement('div');
            div.className = 'item-container';
            
            div.innerHTML = `
                <div class="item-carpeta carpeta" ondblclick="navegarACarpeta('${carpeta.ruta}')">
                    <div class="icono">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                    <div class="nombre">${carpeta.nombre}</div>
                </div>
            `;
            
            return div;
        }
        
        // Crear elemento de archivo
        function crearItemArchivo(archivo) {
            const div = document.createElement('div');
            div.className = 'item-container';
            
            let contenido = '';
            
            if (archivo.tipo === 'imagen') {
                contenido = `
                    <div class="item-carpeta imagen">
                        <div class="icono">
                            <img src="../../public/images/${archivo.ruta}" alt="${archivo.nombre}" class="thumbnail" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="bi bi-image" style="display: none;"></i>
                        </div>
                        <div class="nombre">${archivo.nombre}</div>
                        <div class="acciones-item">
                            <button class="btn btn-danger btn-sm btn-accion" onclick="confirmarEliminar('${archivo.ruta}', '${archivo.nombre}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                contenido = `
                    <div class="item-carpeta archivo">
                        <div class="icono">
                            <i class="bi bi-file-earmark"></i>
                        </div>
                        <div class="nombre">${archivo.nombre}</div>
                    </div>
                `;
            }
            
            div.innerHTML = contenido;
            return div;
        }
        
        // Navegar a una carpeta
        function navegarACarpeta(ruta) {
            rutaActual = ruta;
            cargarContenido();
        }
        
        // Recargar contenido
        function recargarContenido() {
            cargarContenido();
        }
        
        // Confirmar eliminación
        function confirmarEliminar(ruta, nombre) {
            archivoAEliminar = ruta;
            document.getElementById('nombre-archivo').textContent = nombre;
            modalEliminar.show();
        }
        
        // Eliminar archivo
        document.getElementById('confirmar-eliminar').addEventListener('click', function() {
            if (!archivoAEliminar) return;
            
            const formData = new FormData();
            formData.append('accion', 'eliminar_imagen');
            formData.append('ruta', archivoAEliminar);
            
            fetch('procesar_explorador.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalEliminar.hide();
                
                if (data.success) {
                    // Eliminar el elemento del DOM sin recargar
                    const elementos = document.querySelectorAll('.item-carpeta');
                    elementos.forEach(elemento => {
                        const botonEliminar = elemento.querySelector('.btn-accion');
                        if (botonEliminar && botonEliminar.onclick.toString().includes(archivoAEliminar)) {
                            elemento.closest('.item-container').remove();
                        }
                    });
                    
                    // Verificar si la carpeta quedó vacía
                    const contenedor = document.getElementById('contenido-carpeta');
                    if (contenedor.children.length === 0) {
                        document.getElementById('mensaje-vacio').style.display = 'block';
                    }
                    
                    mostrarMensaje('Imagen eliminada correctamente', 'success');
                } else {
                    mostrarError(data.error || 'Error al eliminar la imagen');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalEliminar.hide();
                mostrarError('Error de conexión');
            });
            
            archivoAEliminar = null;
        });
        
        // Mostrar loading
        function mostrarLoading(mostrar) {
            const loading = document.getElementById('loading');
            if (mostrar) {
                loading.classList.add('show');
            } else {
                loading.classList.remove('show');
            }
        }
        
        // Mostrar error
        function mostrarError(mensaje) {
            // Crear toast de error
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-danger border-0';
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remover el toast después de que se oculte
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        
        // Mostrar mensaje de éxito
        function mostrarMensaje(mensaje, tipo = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${tipo} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remover el toast después de que se oculte
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
    </script>
</body>
</html>
