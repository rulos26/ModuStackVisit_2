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

require_once __DIR__ . '/tipo_vivienda/TipoViviendaController.php';
use App\Controllers\TipoViviendaController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = TipoViviendaController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../estado_vivienda/estado_vivienda.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en tipo_vivienda.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = TipoViviendaController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $tipos_vivienda = $controller->obtenerOpciones('tipo_vivienda');
    $sectores = $controller->obtenerOpciones('sector');
    $propiedades = $controller->obtenerOpciones('propiedad');
} catch (Exception $e) {
    error_log("Error en tipo_vivienda.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Configurar variables del wizard
$wizard_step = 7;
$wizard_title = 'TIPO DE VIVIENDA';
$wizard_subtitle = 'Información sobre las características de la vivienda del evaluado';
$wizard_icon = 'fas fa-home';
$wizard_form_id = 'formTipoVivienda';
$wizard_form_action = '';
$wizard_previous_url = '../tiene_pareja_wizard.php';
$wizard_next_url = '../estado_vivienda/estado_vivienda.php';
?>

<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="<?php echo $wizard_icon; ?> me-2"></i><?php echo $wizard_title; ?></h1>
            <p class="subtitle"><?php echo $wizard_subtitle; ?></p>
        </div>

        <!-- Barra de Progreso -->
        <div class="wizard-progress">
            <div class="wizard-steps">
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="wizard-step-title">Paso 1</div>
                    <div class="wizard-step-description">Datos Básicos</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="wizard-step-title">Paso 2</div>
                    <div class="wizard-step-description">Información Personal</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="wizard-step-title">Paso 3</div>
                    <div class="wizard-step-description">Cámara de Comercio</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="wizard-step-title">Paso 4</div>
                    <div class="wizard-step-description">Salud</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 5</div>
                    <div class="wizard-step-description">Composición Familiar</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="wizard-step-title">Paso 6</div>
                    <div class="wizard-step-description">Información Pareja</div>
                </div>
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="wizard-step-title">Paso 7</div>
                    <div class="wizard-step-description">Tipo de Vivienda</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div class="wizard-step-title">Paso 8</div>
                    <div class="wizard-step-description">Estado de Vivienda</div>
                </div>
            </div>
        </div>

        <!-- Contenido del Wizard -->
        <div class="wizard-content">
            <div class="wizard-step-content active">
                <!-- Información del Evaluado -->
                <div class="wizard-evaluado-info">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="wizard-logo">
                        </div>
                        <div class="col-md-6 wizard-evaluado-details">
                            <div class="detail-item">
                                <span class="detail-label">Fecha:</span> <?php echo date('d/m/Y'); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Cédula:</span> <?php echo htmlspecialchars($id_cedula); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Usuario:</span> <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de sesión -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo $_SESSION['error']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="wizard-alert wizard-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Éxito:</strong><br>
                            <?php echo $_SESSION['success']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($datos_existentes): ?>
                    <div class="wizard-alert wizard-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Información:</strong><br>
                            Ya existe información de tipo de vivienda registrada para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-home"></i>
                                Tipo de Vivienda: <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="id_tipo_vivienda" name="id_tipo_vivienda" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($tipos_vivienda as $tipo): ?>
                                    <option value="<?php echo $tipo['id']; ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_tipo_vivienda'] == $tipo['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Sector: <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="id_sector" name="id_sector" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($sectores as $sector): ?>
                                    <option value="<?php echo $sector['id']; ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_sector'] == $sector['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sector['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-user-tie"></i>
                                Propietario: <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="id_propietario" name="id_propietario" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($propiedades as $propiedad): ?>
                                    <option value="<?php echo $propiedad['id']; ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_propietario'] == $propiedad['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($propiedad['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-users"></i>
                                Número de hogares que habitan en la vivienda: <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="numero_de_familia" name="numero_de_familia" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['numero_de_familia']) : ''; ?>" 
                                   min="1" max="50" required>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-user-friends"></i>
                                Personas que conforman núcleo familiar: <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="personas_nucleo_familiar" name="personas_nucleo_familiar" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['personas_nucleo_familiar']) : ''; ?>" 
                                   min="1" max="100" required>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Tiempo en Residencia en el Sector: <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tiempo_sector" name="tiempo_sector" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['tiempo_sector']) : ''; ?>" 
                                   max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">
                                <i class="fas fa-building"></i>
                                Número de Pisos de la Vivienda: <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="numero_de_pisos" name="numero_de_pisos" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['numero_de_pisos']) : ''; ?>" 
                                   min="1" max="50" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="form-label">
                                <i class="fas fa-comment"></i>
                                Observación:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" 
                                      rows="4" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres. Mínimo 10 caracteres si se llena.</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <a href="<?php echo $wizard_previous_url; ?>" class="wizard-btn wizard-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Anterior
            </a>
            <div class="text-center">
                <small class="text-muted">Paso <?php echo $wizard_step; ?> de 22</small>
            </div>
            <button type="button" class="wizard-btn wizard-btn-primary wizard-btn-next" id="nextBtn" disabled>
                Siguiente
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<script src="../../../../../public/js/wizard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?php echo $wizard_form_id; ?>');
    const nextBtn = document.getElementById('nextBtn');
    
    // Función para validar el formulario
    function validateForm() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value || field.value.trim() === '') {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });
        
        // Validación específica para observación si se llena
        const observacion = document.getElementById('observacion');
        if (observacion.value.trim() !== '' && observacion.value.trim().length < 10) {
            observacion.classList.add('is-invalid');
            observacion.classList.remove('is-valid');
            isValid = false;
        } else if (observacion.value.trim() !== '') {
            observacion.classList.remove('is-invalid');
            observacion.classList.add('is-valid');
        }
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) {
        if (e.target.matches('input, select, textarea')) {
            validateForm();
        }
    });
    
    // Navegación con el botón siguiente
    nextBtn.addEventListener('click', function() {
        if (validateForm()) {
            // Mostrar animación de carga
            nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            nextBtn.disabled = true;
            
            // Enviar formulario
            setTimeout(() => {
                form.submit();
            }, 500);
        } else {
            // Mostrar alerta
            if (window.wizard) {
                window.wizard.showAlert('Por favor complete todos los campos obligatorios antes de continuar.', 'warning');
            } else {
                alert('Por favor complete todos los campos obligatorios antes de continuar.');
            }
        }
    });
    
    // Validación inicial
    validateForm();
});
</script>

<?php
$contenido = ob_get_clean();

// Intentar múltiples rutas posibles para el dashboard
$dashboard_paths = [
    dirname(__DIR__, 4) . '/layout/dashboard.php',
    dirname(__DIR__, 5) . '/layout/dashboard.php',
    dirname(__DIR__, 6) . '/layout/dashboard.php',
    __DIR__ . '/../../../../../layout/dashboard.php',
    __DIR__ . '/../../../../../../layout/dashboard.php'
];

$dashboard_incluido = false;
foreach ($dashboard_paths as $path) {
    if (file_exists($path)) {
        include $path;
        $dashboard_incluido = true;
        break;
    }
}

if (!$dashboard_incluido) {
    echo $contenido;
    echo '<div style="background: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem; border: 1px solid #f5c6cb; border-radius: 0.25rem;">';
    echo '<strong>Advertencia:</strong> No se pudo cargar el layout del dashboard. Rutas probadas:<br>';
    foreach ($dashboard_paths as $path) {
        echo '- ' . htmlspecialchars($path) . '<br>';
    }
    echo '</div>';
}
?>
