<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

require_once __DIR__ . '/CamaraComercioController.php';
use App\Controllers\CamaraComercioController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = CamaraComercioController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../salud/salud.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en camara_comercio.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = CamaraComercioController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en camara_comercio.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<!-- Puedes usar este código como base para tu formulario y menú responsive -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Responsive y Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Menú horizontal en desktop */
        @media (min-width: 992px) {
            .navbar-desktop {
                display: flex !important;
            }
            .navbar-mobile {
                display: none !important;
            }
        }
        /* Menú hamburguesa en móvil/tablet */
        @media (max-width: 991.98px) {
            .navbar-desktop {
                display: none !important;
            }
            .navbar-mobile {
                display: block !important;
            }
        }
        /* Ajuste para observaciones */
        .obs-row {
            flex-wrap: wrap;
        }
        .obs-col {
            flex: 1 0 100%;
            max-width: 100%;
        }
        /* Forzar 4 columnas desde 1440px (ajustado para pantallas grandes) */
        @media (min-width: 1440px) {
            .form-responsive-row > [class*="col-"] {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
        /* Bootstrap row display flex fix para forzar columnas */
        .form-responsive-row {
            display: flex;
            flex-wrap: wrap;
        }
        /* Mejorar visual de la card */
        .card {
            box-shadow: 0 2px 16px 0 rgba(0,0,0,0.07);
        }
        /* Pasos */
        .steps-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            width: 100%;
            gap: 0.5rem;
        }
        .step-horizontal {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-horizontal:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateX(50%);
        }
        .step-horizontal .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-horizontal.active .step-icon {
            background: #4361ee;
            border-color: #4361ee;
            color: #fff;
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        .step-horizontal.complete .step-icon {
            background: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }
        .step-horizontal .step-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .step-horizontal .step-description {
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
        .step-horizontal.active .step-title,
        .step-horizontal.active .step-description {
            color: #4361ee;
        }
        .step-horizontal.complete .step-title,
        .step-horizontal.complete .step-description {
            color: #2ecc71;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building me-2"></i>
                    VISITA DOMICILIARÍA - CÁMARA DE COMERCIO
                </h5>
            </div>
            <div class="card-body">
                <!-- Indicador de pasos -->
                <div class="steps-horizontal mb-4">
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-id-card"></i></div>
                        <div class="step-title">Paso 1</div>
                        <div class="step-description">Información Personal</div>
                    </div>
                    <div class="step-horizontal active">
                        <div class="step-icon"><i class="fas fa-building"></i></div>
                        <div class="step-title">Paso 2</div>
                        <div class="step-description">Cámara de Comercio</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-camera"></i></div>
                        <div class="step-title">Paso 3</div>
                        <div class="step-description">Registro Fotográfico</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="step-title">Paso 4</div>
                        <div class="step-description">Ubicación</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                        <div class="step-title">Paso 22</div>
                        <div class="step-description">Registro Fotos</div>
                    </div>
                </div>

                <!-- Mensajes de sesión -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-12 text-end">
                        <div class="text-muted">
                            <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                            <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Nota informativa sobre campos obligatorios -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Información importante:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios y deben ser completados antes de continuar.
                </div>
                
                <form action="" method="POST" id="formCamaraComercio" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tiene_camara" class="form-label required-field">
                                <i class="bi bi-building me-1"></i>¿Tiene Cámara de Comercio?
                            </label>
                            <select class="form-select" id="tiene_camara" name="tiene_camara" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si" <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                <option value="No" <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione si tiene cámara de comercio.</div>
                        </div>
                        <div class="col-md-4 mb-3 campos-adicionales" id="campo-nombre" style="display: none;">
                            <label for="nombre" class="form-label required-field">
                                <i class="bi bi-building me-1"></i>Nombre de Empresa:
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['nombre']) : ''; ?>" 
                                   maxlength="200">
                            <div class="invalid-feedback">Por favor ingrese el nombre de la empresa.</div>
                        </div>
                        <div class="col-md-4 mb-3 campos-adicionales" id="campo-razon" style="display: none;">
                            <label for="razon" class="form-label required-field">
                                <i class="bi bi-briefcase me-1"></i>Razón Social:
                            </label>
                            <input type="text" class="form-control" id="razon" name="razon" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['razon']) : ''; ?>" 
                                   maxlength="200">
                            <div class="invalid-feedback">Por favor ingrese la razón social.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 campos-adicionales" id="campo-activdad" style="display: none;">
                            <label for="activdad" class="form-label required-field">
                                <i class="bi bi-gear me-1"></i>Actividad:
                            </label>
                            <input type="text" class="form-control" id="activdad" name="activdad" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['activdad']) : ''; ?>" 
                                   maxlength="200">
                            <div class="invalid-feedback">Por favor ingrese la actividad.</div>
                        </div>
                        <div class="col-md-6 mb-3 campos-adicionales" id="campo-observacion" style="display: none;">
                            <label for="observacion" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Observaciones:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" rows="2" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg me-2">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                            </button>
                            <a href="../informacion_personal/informacion_personal.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer text-body-secondary">
                <div class="row">
                    <div class="col-md-6">
                        <small>© 2024 V0.01 - Sistema de Visitas Domiciliarias</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>Usuario: <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tieneCamaraSelect = document.getElementById('tiene_camara');
        const camposAdicionales = document.querySelectorAll('.campos-adicionales');
        
        // Verificar si hay datos existentes
        const tieneDatosExistentes = <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'Si') ? 'true' : 'false'; ?>;
        
        // Función para mostrar/ocultar campos
        function toggleCampos() {
            const valor = tieneCamaraSelect.value;
            
            if (valor === 'Si') {
                camposAdicionales.forEach(campo => {
                    campo.style.display = 'block';
                });
            } else {
                camposAdicionales.forEach(campo => {
                    campo.style.display = 'none';
                    // Limpiar valores cuando se ocultan
                    const input = campo.querySelector('input, textarea');
                    if (input) {
                        input.value = '';
                    }
                });
            }
        }
        
        // Ejecutar al cargar la página
        toggleCampos();
        
        // Si hay datos existentes y tiene_camara es 'Si', mostrar campos
        if (tieneDatosExistentes) {
            camposAdicionales.forEach(campo => {
                campo.style.display = 'block';
            });
        }
        
        // Ejecutar cuando cambie la selección
        tieneCamaraSelect.addEventListener('change', toggleCampos);
    });
    </script>
</body>
</html>
<?php
$contenido = ob_get_clean();

// Verificar si la sesión ya está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../../../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../../../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cámara de Comercio - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
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
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .steps-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            width: 100%;
            gap: 0.5rem;
        }
        .step-horizontal {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-horizontal:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateX(50%);
        }
        .step-horizontal .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-horizontal.active .step-icon {
            background: #4361ee;
            border-color: #4361ee;
            color: #fff;
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        .step-horizontal.complete .step-icon {
            background: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }
        .step-horizontal .step-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .step-horizontal .step-description {
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
        .step-horizontal.active .step-title,
        .step-horizontal.active .step-description {
            color: #4361ee;
        }
        .step-horizontal.complete .step-title,
        .step-horizontal.complete .step-description {
            color: #2ecc71;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .invalid-feedback {
            font-size: 0.875rem;
        }
        .valid-feedback {
            font-size: 0.875rem;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Cámara de Comercio</h1>
                            <p class="text-muted mb-0">Formulario de información empresarial</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>