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

require_once __DIR__ . '/cuentas_bancarias/CuentasBancariasController.php';
use App\Controllers\CuentasBancariasController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = CuentasBancariasController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../pasivos/tiene_pasivo.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en cuentas_bancarias.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = CuentasBancariasController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $municipios = $controller->obtenerMunicipios();
} catch (Exception $e) {
    error_log("Error en cuentas_bancarias.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Configurar variables del wizard
$wizard_step = 11;
$wizard_title = 'CUENTAS BANCARIAS';
$wizard_subtitle = 'Registro de cuentas bancarias y entidades financieras';
$wizard_icon = 'fas fa-university';
$wizard_form_id = 'formCuentasBancarias';
$wizard_form_action = '';
$wizard_previous_url = '../servicios_publicos_wizard.php';
$wizard_next_url = '../pasivos/tiene_pasivo.php';
?>

<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Estilos específicos para cuentas bancarias */
.cuenta-item { 
    border: 2px solid var(--border-color); 
    border-radius: var(--border-radius); 
    padding: 25px; 
    margin-bottom: 25px; 
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: var(--transition);
    position: relative;
}

.cuenta-item:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
}

.cuenta-item h6 { 
    color: var(--primary-color); 
    margin-bottom: 20px; 
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
    font-weight: 600;
}

.btn-remove-cuenta { 
    position: absolute; 
    top: 15px; 
    right: 15px; 
    background: var(--danger-color); 
    border: none; 
    color: white; 
    padding: 8px 12px; 
    border-radius: 6px; 
    transition: var(--transition);
    font-size: 0.9rem;
}

.btn-remove-cuenta:hover { 
    background: #c82333; 
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.btn-agregar-cuenta {
    background: var(--success-color);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    transition: var(--transition);
    font-weight: 600;
    margin-bottom: 20px;
}

.btn-agregar-cuenta:hover {
    background: #27ae60;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
}

/* Animación para agregar/eliminar cuentas */
.cuenta-item.removing {
    animation: slideOut 0.3s ease-out forwards;
}

@keyframes slideOut {
    to {
        opacity: 0;
        transform: translateX(-100%);
        max-height: 0;
        margin-bottom: 0;
        padding: 0;
    }
}

.cuenta-item.adding {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

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
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="wizard-step-title">Paso 7</div>
                    <div class="wizard-step-description">Tipo de Vivienda</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="wizard-step-title">Paso 8</div>
                    <div class="wizard-step-description">Estado de Vivienda</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-couch"></i>
                    </div>
                    <div class="wizard-step-title">Paso 9</div>
                    <div class="wizard-step-description">Inventario Enseres</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="wizard-step-title">Paso 10</div>
                    <div class="wizard-step-description">Servicios Públicos</div>
                </div>
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="wizard-step-title">Paso 11</div>
                    <div class="wizard-step-description">Cuentas Bancarias</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="wizard-step-title">Paso 12</div>
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

                <?php if (!empty($datos_existentes)): ?>
                    <div class="wizard-alert wizard-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Información:</strong><br>
                            Ya existen <?php echo count($datos_existentes); ?> cuenta(s) bancaria(s) registrada(s) para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <!-- Botón para agregar cuenta -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn-agregar-cuenta" id="btnAgregarCuenta">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Cuenta Bancaria
                        </button>
                    </div>

                    <!-- Contenedor de cuentas bancarias -->
                    <div id="cuentas-container">
                        <!-- Cuenta inicial -->
                        <div class="cuenta-item" data-cuenta="0">
                            <h6><i class="fas fa-credit-card me-2"></i>Cuenta Bancaria #1</h6>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-university"></i>
                                        Entidad:
                                    </label>
                                    <input type="text" class="form-control" id="id_entidad_0" name="id_entidad[]" 
                                           value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_entidad'] ?? '') : ''; ?>"
                                           placeholder="Ej: Banco de Bogotá" minlength="3" required>
                                    <div class="form-text">Mínimo 3 caracteres</div>
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-credit-card"></i>
                                        Tipo de Cuenta:
                                    </label>
                                    <input type="text" class="form-control" id="id_tipo_cuenta_0" name="id_tipo_cuenta[]" 
                                           value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_tipo_cuenta'] ?? '') : ''; ?>"
                                           placeholder="Ej: Ahorros, Corriente" minlength="3" required>
                                    <div class="form-text">Mínimo 3 caracteres</div>
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Ciudad:
                                    </label>
                                    <select class="form-select" id="id_ciudad_0" name="id_ciudad[]" required>
                                        <option value="">Seleccione una ciudad</option>
                                        <?php foreach ($municipios as $municipio): ?>
                                            <option value="<?php echo $municipio['id_municipio']; ?>" 
                                                <?php echo (!empty($datos_existentes) && $datos_existentes[0]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($municipio['municipio']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-comment"></i>
                                        Observaciones:
                                    </label>
                                    <textarea class="form-control" id="observaciones_0" name="observaciones[]" 
                                              rows="3" maxlength="500"><?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['observaciones'] ?? '') : ''; ?></textarea>
                                    <div class="form-text">Opcional. Máximo 500 caracteres. Mínimo 10 caracteres si se llena.</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cuentas adicionales si existen datos -->
                        <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
                            <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
                                <div class="cuenta-item" data-cuenta="<?php echo $i; ?>">
                                    <button type="button" class="btn-remove-cuenta" onclick="removeCuenta(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <h6><i class="fas fa-credit-card me-2"></i>Cuenta Bancaria #<?php echo $i + 1; ?></h6>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label class="form-label">
                                                <i class="fas fa-university"></i>
                                                Entidad:
                                            </label>
                                            <input type="text" class="form-control" id="id_entidad_<?php echo $i; ?>" name="id_entidad[]" 
                                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['id_entidad']); ?>"
                                                   placeholder="Ej: Banco de Bogotá" minlength="3" required>
                                        </div>
                                        
                                        <div class="col-md-4 form-group">
                                            <label class="form-label">
                                                <i class="fas fa-credit-card"></i>
                                                Tipo de Cuenta:
                                            </label>
                                            <input type="text" class="form-control" id="id_tipo_cuenta_<?php echo $i; ?>" name="id_tipo_cuenta[]" 
                                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['id_tipo_cuenta']); ?>"
                                                   placeholder="Ej: Ahorros, Corriente" minlength="3" required>
                                        </div>
                                        
                                        <div class="col-md-4 form-group">
                                            <label class="form-label">
                                                <i class="fas fa-map-marker-alt"></i>
                                                Ciudad:
                                            </label>
                                            <select class="form-select" id="id_ciudad_<?php echo $i; ?>" name="id_ciudad[]" required>
                                                <option value="">Seleccione una ciudad</option>
                                                <?php foreach ($municipios as $municipio): ?>
                                                    <option value="<?php echo $municipio['id_municipio']; ?>" 
                                                        <?php echo ($datos_existentes[$i]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($municipio['municipio']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 form-group">
                                            <label class="form-label">
                                                <i class="fas fa-comment"></i>
                                                Observaciones:
                                            </label>
                                            <textarea class="form-control" id="observaciones_<?php echo $i; ?>" name="observaciones[]" 
                                                      rows="3" maxlength="500"><?php echo htmlspecialchars($datos_existentes[$i]['observaciones']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        <?php endif; ?>
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
    const btnAgregarCuenta = document.getElementById('btnAgregarCuenta');
    const cuentasContainer = document.getElementById('cuentas-container');
    let cuentaCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;
    
    // Función para validar una cuenta
    function validateCuenta(cuentaElement) {
        const inputs = cuentaElement.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });
        
        // Validar observaciones si se llenan
        const observaciones = cuentaElement.querySelector('textarea[name="observaciones[]"]');
        if (observaciones.value.trim() !== '' && observaciones.value.trim().length < 10) {
            observaciones.classList.add('is-invalid');
            observaciones.classList.remove('is-valid');
            isValid = false;
        } else if (observaciones.value.trim() !== '') {
            observaciones.classList.remove('is-invalid');
            observaciones.classList.add('is-valid');
        }
        
        return isValid;
    }
    
    // Función para validar todo el formulario
    function validateForm() {
        const cuentas = cuentasContainer.querySelectorAll('.cuenta-item');
        let isValid = true;
        
        if (cuentas.length === 0) {
            isValid = false;
        }
        
        cuentas.forEach(cuenta => {
            if (!validateCuenta(cuenta)) {
                isValid = false;
            }
        });
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Función para agregar cuenta
    function agregarCuenta() {
        const nuevaCuenta = document.createElement('div');
        nuevaCuenta.className = 'cuenta-item adding';
        nuevaCuenta.setAttribute('data-cuenta', cuentaCounter);
        
        nuevaCuenta.innerHTML = `
            <button type="button" class="btn-remove-cuenta" onclick="removeCuenta(this)">
                <i class="fas fa-times"></i>
            </button>
            <h6><i class="fas fa-credit-card me-2"></i>Cuenta Bancaria #${cuentaCounter + 1}</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-university"></i>
                        Entidad:
                    </label>
                    <input type="text" class="form-control" id="id_entidad_${cuentaCounter}" name="id_entidad[]" 
                           placeholder="Ej: Banco de Bogotá" minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
                
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-credit-card"></i>
                        Tipo de Cuenta:
                    </label>
                    <input type="text" class="form-control" id="id_tipo_cuenta_${cuentaCounter}" name="id_tipo_cuenta[]" 
                           placeholder="Ej: Ahorros, Corriente" minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
                
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Ciudad:
                    </label>
                    <select class="form-select" id="id_ciudad_${cuentaCounter}" name="id_ciudad[]" required>
                        <option value="">Seleccione una ciudad</option>
                        <?php foreach ($municipios as $municipio): ?>
                            <option value="<?php echo $municipio['id_municipio']; ?>">
                                <?php echo htmlspecialchars($municipio['municipio']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-12 form-group">
                    <label class="form-label">
                        <i class="fas fa-comment"></i>
                        Observaciones:
                    </label>
                    <textarea class="form-control" id="observaciones_${cuentaCounter}" name="observaciones[]" 
                              rows="3" maxlength="500"></textarea>
                    <div class="form-text">Opcional. Máximo 500 caracteres. Mínimo 10 caracteres si se llena.</div>
                </div>
            </div>
        `;
        
        cuentasContainer.appendChild(nuevaCuenta);
        cuentaCounter++;
        
        // Actualizar números de cuentas
        actualizarNumerosCuentas();
        validateForm();
    }
    
    // Función para eliminar cuenta
    window.removeCuenta = function(button) {
        const cuenta = button.closest('.cuenta-item');
        const cuentas = cuentasContainer.querySelectorAll('.cuenta-item');
        
        // No permitir eliminar si solo hay una cuenta
        if (cuentas.length <= 1) {
            if (window.wizard) {
                window.wizard.showAlert('Debe mantener al menos una cuenta bancaria.', 'warning');
            } else {
                alert('Debe mantener al menos una cuenta bancaria.');
            }
            return;
        }
        
        cuenta.classList.add('removing');
        setTimeout(() => {
            cuenta.remove();
            actualizarNumerosCuentas();
            validateForm();
        }, 300);
    }
    
    // Función para actualizar números de cuentas
    function actualizarNumerosCuentas() {
        const cuentas = cuentasContainer.querySelectorAll('.cuenta-item');
        const botonesEliminar = cuentasContainer.querySelectorAll('.btn-remove-cuenta');
        
        cuentas.forEach((cuenta, index) => {
            const titulo = cuenta.querySelector('h6');
            titulo.innerHTML = `<i class="fas fa-credit-card me-2"></i>Cuenta Bancaria #${index + 1}`;
            
            // Mostrar/ocultar botón eliminar según cantidad de cuentas
            const botonEliminar = cuenta.querySelector('.btn-remove-cuenta');
            if (cuentas.length > 1) {
                botonEliminar.style.display = 'block';
            } else {
                botonEliminar.style.display = 'none';
            }
        });
    }
    
    // Event listeners
    btnAgregarCuenta.addEventListener('click', agregarCuenta);
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) {
        if (e.target.matches('input, select, textarea')) {
            const cuenta = e.target.closest('.cuenta-item');
            if (cuenta) {
                validateCuenta(cuenta);
                validateForm();
            }
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
    actualizarNumerosCuentas();
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
