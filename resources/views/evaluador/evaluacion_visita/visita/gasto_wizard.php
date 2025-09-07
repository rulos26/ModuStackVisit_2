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

require_once __DIR__ . '/gasto/GastoController.php';
use App\Controllers\GastoController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = GastoController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: estudios_wizard.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en gasto_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = GastoController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en gasto_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 18;
$wizard_title = 'Gastos o Deudas Mensuales del Núcleo Familiar';
$wizard_subtitle = 'Ingrese los gastos mensuales de todos los miembros del núcleo familiar';
$wizard_icon = 'fas fa-receipt';
$wizard_form_id = 'formGastos';
$wizard_form_action = '';
$wizard_previous_url = 'ingresos_mensuales_wizard.php';
$wizard_next_url = 'estudios_wizard.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div class="row">
    <!-- Campo Alimentación -->
    <div class="col-md-4 mb-3">
        <label for="alimentacion_val" class="form-label">
            <i class="bi bi-egg-fried me-1"></i>Alimentación:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="alimentacion_val" name="alimentacion_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['alimentacion_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en alimentación mensual</div>
    </div>
    
    <!-- Campo Educación -->
    <div class="col-md-4 mb-3">
        <label for="educacion_val" class="form-label">
            <i class="bi bi-book me-1"></i>Educación:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="educacion_val" name="educacion_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['educacion_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en educación mensual</div>
    </div>
    
    <!-- Campo Salud -->
    <div class="col-md-4 mb-3">
        <label for="salud_val" class="form-label">
            <i class="bi bi-heart-pulse me-1"></i>Salud:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="salud_val" name="salud_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['salud_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en salud mensual</div>
    </div>
</div>

<div class="row">
    <!-- Campo Recreación -->
    <div class="col-md-4 mb-3">
        <label for="recreacion_val" class="form-label">
            <i class="bi bi-emoji-smile me-1"></i>Recreación:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="recreacion_val" name="recreacion_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['recreacion_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en recreación mensual</div>
    </div>
    
    <!-- Campo Cuota de Créditos -->
    <div class="col-md-4 mb-3">
        <label for="cuota_creditos_val" class="form-label">
            <i class="bi bi-credit-card me-1"></i>Cuota de Créditos:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="cuota_creditos_val" name="cuota_creditos_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['cuota_creditos_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Cuotas de créditos mensuales</div>
    </div>
    
    <!-- Campo Arriendo -->
    <div class="col-md-4 mb-3">
        <label for="arriendo_val" class="form-label">
            <i class="bi bi-house me-1"></i>Arriendo:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="arriendo_val" name="arriendo_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['arriendo_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en arriendo mensual</div>
    </div>
</div>

<div class="row">
    <!-- Campo Servicios Públicos -->
    <div class="col-md-4 mb-3">
        <label for="servicios_publicos_val" class="form-label">
            <i class="bi bi-lightning-charge me-1"></i>Servicios Públicos:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="servicios_publicos_val" name="servicios_publicos_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['servicios_publicos_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en servicios públicos mensual</div>
    </div>
    
    <!-- Campo Transporte -->
    <div class="col-md-4 mb-3">
        <label for="transporte_val" class="form-label">
            <i class="bi bi-bus-front me-1"></i>Transporte:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="transporte_val" name="transporte_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['transporte_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Gastos en transporte mensual</div>
    </div>
    
    <!-- Campo Otros -->
    <div class="col-md-4 mb-3">
        <label for="otros_val" class="form-label">
            <i class="bi bi-plus-circle me-1"></i>Otros:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="otros_val" name="otros_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['otros_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Otros gastos mensuales</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0/dist/autoNumeric.min.js"></script>
<script>
// Inicializar autoNumeric para campos de dinero
document.querySelectorAll('input[id$="_val"]').forEach(function(input) {
    new AutoNumeric(input, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
});
</script>
