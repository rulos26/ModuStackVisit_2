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

require_once __DIR__ . '/data_credito/DataCreditoController.php';
use App\Controllers\DataCreditoController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = DataCreditoController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        
        if (isset($datos['reportado_centrales']) && $datos['reportado_centrales'] == '1') {
            // No está reportado en centrales de riesgo
            $resultado = $controller->guardarSinReportes();
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../ingresos_mensuales/ingresos_mensuales.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Está reportado, redirigir al formulario detallado
            header('Location: reportado_wizard.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Error en data_credito.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = DataCreditoController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $parametros = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en data_credito.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Configurar variables del wizard
$wizard_step = 15;
$wizard_title = 'REPORTES EN CENTRALES DE RIESGO';
$wizard_subtitle = 'Información sobre reportes en centrales de riesgo crediticio';
$wizard_icon = 'fas fa-shield-check';
$wizard_form_id = 'formDataCredito';
$wizard_form_action = '';
$wizard_previous_url = '../aportante_wizard.php';
$wizard_next_url = '../reportado_wizard.php';
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
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="wizard-step-title">Paso 11</div>
                    <div class="wizard-step-description">Cuentas Bancarias</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="wizard-step-title">Paso 12</div>
                    <div class="wizard-step-description">Pasivos</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="wizard-step-title">Paso 13</div>
                    <div class="wizard-step-description">Detalles Pasivos</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 14</div>
                    <div class="wizard-step-description">Aportantes</div>
                </div>
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div class="wizard-step-title">Paso 15</div>
                    <div class="wizard-step-description">Data Crédito</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="wizard-step-title">Paso 16</div>
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
                            Ya existe información de data crédito registrada para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">
                                <i class="fas fa-question-circle"></i>
                                ¿Se encuentra reportado en centrales de riesgo? <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="reportado_centrales" name="reportado_centrales" required>
                                <option value="">Seleccione una opción</option>
                                <option value="1" <?php echo (!empty($datos_existentes) && $datos_existentes[0]['entidad'] == 'N/A') ? 'selected' : ''; ?>>No</option>
                                <?php foreach ($parametros as $parametro): ?>
                                    <option value="<?php echo $parametro['id']; ?>" 
                                        <?php echo (!empty($datos_existentes) && $datos_existentes[0]['entidad'] != 'N/A' && $datos_existentes[0]['entidad'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Seleccione "No" si no está reportado, o "Sí" para continuar con el formulario detallado.</div>
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
    const reportadoCentralesSelect = document.getElementById('reportado_centrales');
    
    // Función para validar el formulario
    function validateForm() {
        let isValid = true;
        
        if (!reportadoCentralesSelect.value) {
            reportadoCentralesSelect.classList.add('is-invalid');
            reportadoCentralesSelect.classList.remove('is-valid');
            isValid = false;
        } else {
            reportadoCentralesSelect.classList.remove('is-invalid');
            reportadoCentralesSelect.classList.add('is-valid');
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
                window.wizard.showAlert('Por favor seleccione una opción antes de continuar.', 'warning');
            } else {
                alert('Por favor seleccione una opción antes de continuar.');
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
