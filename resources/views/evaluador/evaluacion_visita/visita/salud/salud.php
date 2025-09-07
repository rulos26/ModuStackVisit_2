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

require_once __DIR__ . '/SaludController.php';
use App\Controllers\SaludController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = SaludController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../composición_familiar/composición_familiar.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en salud.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = SaludController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $estados_salud = $controller->obtenerOpciones('estados');
    $opciones_parametro = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en salud.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="fas fa-heartbeat me-2"></i>SALUD</h1>
            <p class="subtitle">Estado de salud y condiciones médicas del evaluado</p>
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
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="wizard-step-title">Paso 4</div>
                    <div class="wizard-step-description">Salud</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 5</div>
                    <div class="wizard-step-description">Composición Familiar</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="wizard-step-title">Paso 6</div>
                    <div class="wizard-step-description">Finalización</div>
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
                            Ya existe información de salud registrada para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario -->
                <form action="" method="POST" id="formSalud" class="wizard-form" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="id_estado_salud" class="form-label">
                                <i class="fas fa-heart"></i>
                                Estado de Salud:
                            </label>
                            <select class="form-select" id="id_estado_salud" name="id_estado_salud" required>
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($estados_salud as $estado): ?>
                                    <option value="<?php echo $estado['id']; ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_estado_salud'] == $estado['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($estado['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado de salud.</div>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label for="tipo_enfermedad" class="form-label">
                                <i class="fas fa-exclamation-triangle"></i>
                                ¿Padece algún tipo de enfermedad?:
                            </label>
                            <select class="form-select" id="tipo_enfermedad" name="tipo_enfermedad" required>
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($opciones_parametro as $opcion): ?>
                                    <option value="<?php echo $opcion['id']; ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['tipo_enfermedad'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione si padece algún tipo de enfermedad.</div>
                        </div>
                        
                        <div class="col-md-4 form-group wizard-conditional-fields" id="tipo_enfermedad_cual" style="display: none;">
                            <label for="tipo_enfermedad_cual" class="form-label">
                                <i class="fas fa-comment"></i>
                                ¿Cuál(es)?
                            </label>
                            <input type="text" class="form-control" id="tipo_enfermedad_cual" name="tipo_enfermedad_cual" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['tipo_enfermedad_cual']) : ''; ?>" 
                                   maxlength="200" data-depends-on="tipo_enfermedad" data-depends-value="2" data-required-when-visible="true">
                            <div class="invalid-feedback">Por favor especifique qué tipo de enfermedad padece.</div>
                        </div>
                    </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="limitacion_fisica" class="form-label">
                            <i class="bi bi-person-x me-1"></i>¿Tiene alguna limitación física?
                        </label>
                        <select class="form-select" id="limitacion_fisica" name="limitacion_fisica" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['limitacion_fisica'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si tiene alguna limitación física.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3 campos-adicionales" id="limitacion_fisica_cual" style="display: none;">
                        <label for="limitacion_fisica_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="limitacion_fisica_cual" name="limitacion_fisica_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['limitacion_fisica_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué limitación física tiene.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="tipo_medicamento" class="form-label">
                            <i class="bi bi-capsule me-1"></i>Tipo de Medicamento:
                        </label>
                        <select class="form-select" id="tipo_medicamento" name="tipo_medicamento" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['tipo_medicamento'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el tipo de medicamento.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3 campos-adicionales" id="tipo_medicamento_cual" style="display: none;">
                        <label for="tipo_medicamento_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="tipo_medicamento_cual" name="tipo_medicamento_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['tipo_medicamento_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué medicamentos toma.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="ingiere_alcohol" class="form-label">
                            <i class="bi bi-cup-straw me-1"></i>Ingiere Alcohol:
                        </label>
                        <select class="form-select" id="ingiere_alcohol" name="ingiere_alcohol" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['ingiere_alcohol'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si ingiere alcohol.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3 campos-adicionales" id="ingiere_alcohol_cual" style="display: none;">
                        <label for="ingiere_alcohol_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="ingiere_alcohol_cual" name="ingiere_alcohol_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['ingiere_alcohol_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué tipo de alcohol ingiere.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fuma" class="form-label">
                            <i class="bi bi-smoking me-1"></i>Fuma:
                        </label>
                        <select class="form-select" id="fuma" name="fuma" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['fuma'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si fuma.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="observacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Observación:
                        </label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="4" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                        <div class="form-text">Máximo 1000 caracteres</div>
                    </div>
                </div>
                
                </form>
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <a href="../camara_comercio/camara_comercio.php" class="wizard-btn wizard-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Anterior
            </a>
            <div class="text-center">
                <small class="text-muted">Paso 4 de 22</small>
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
    const form = document.getElementById('formSalud');
    const inputs = form.querySelectorAll('input, select, textarea');
    const nextBtn = document.getElementById('nextBtn');
    
    // Función de validación de campo
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        
        // Remover clases de validación anteriores
        field.classList.remove('is-valid', 'is-invalid');
        
        // Validación básica
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Si pasa todas las validaciones
        if (value || !isRequired) {
            field.classList.add('is-valid');
            return true;
        }
        
        return false;
    }
    
    // Función para verificar validez del formulario
    function checkFormValidity() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Event listeners para validación en tiempo real
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
            checkFormValidity();
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
                checkFormValidity();
            }
        });
    });
    
    // Manejo de campos condicionales
    const tipoEnfermedad = document.getElementById('tipo_enfermedad');
    const limitacionFisica = document.getElementById('limitacion_fisica');
    const tipoMedicamento = document.getElementById('tipo_medicamento');
    const ingiereAlcohol = document.getElementById('ingiere_alcohol');
    
    const tipoEnfermedadCual = document.getElementById('tipo_enfermedad_cual');
    const limitacionFisicaCual = document.getElementById('limitacion_fisica_cual');
    const tipoMedicamentoCual = document.getElementById('tipo_medicamento_cual');
    const ingiereAlcoholCual = document.getElementById('ingiere_alcohol_cual');
    
    function toggleCampos() {
        // Mostrar u ocultar campos según el valor seleccionado
        if (tipoEnfermedadCual) {
            tipoEnfermedadCual.style.display = tipoEnfermedad.value === '2' ? 'block' : 'none';
            if (tipoEnfermedad.value !== '2') tipoEnfermedadCual.value = '';
        }
        
        if (limitacionFisicaCual) {
            limitacionFisicaCual.style.display = limitacionFisica.value === '2' ? 'block' : 'none';
            if (limitacionFisica.value !== '2') limitacionFisicaCual.value = '';
        }
        
        if (tipoMedicamentoCual) {
            tipoMedicamentoCual.style.display = tipoMedicamento.value === '2' ? 'block' : 'none';
            if (tipoMedicamento.value !== '2') tipoMedicamentoCual.value = '';
        }
        
        if (ingiereAlcoholCual) {
            ingiereAlcoholCual.style.display = ingiereAlcohol.value === '2' ? 'block' : 'none';
            if (ingiereAlcohol.value !== '2') ingiereAlcoholCual.value = '';
        }
        
        checkFormValidity();
    }
    
    // Ejecutar al cargar la página
    toggleCampos();
    
    // Escuchar el evento de cambio en los campos
    if (tipoEnfermedad) tipoEnfermedad.addEventListener('change', toggleCampos);
    if (limitacionFisica) limitacionFisica.addEventListener('change', toggleCampos);
    if (tipoMedicamento) tipoMedicamento.addEventListener('change', toggleCampos);
    if (ingiereAlcohol) ingiereAlcohol.addEventListener('change', toggleCampos);
    
    // Navegación con el botón siguiente
    nextBtn.addEventListener('click', function() {
        if (checkFormValidity()) {
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
    
    // Verificar validez inicial
    checkFormValidity();
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