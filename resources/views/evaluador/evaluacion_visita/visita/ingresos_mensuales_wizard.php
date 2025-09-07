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

require_once __DIR__ . '/ingresos_mensuales/IngresosMensualesController.php';
use App\Controllers\IngresosMensualesController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = IngresosMensualesController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: gasto_wizard.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en ingresos_mensuales_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = IngresosMensualesController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en ingresos_mensuales_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 17;
$wizard_title = 'Ingresos Mensuales del Núcleo Familiar';
$wizard_subtitle = 'Ingrese los ingresos mensuales de todos los miembros del núcleo familiar';
$wizard_icon = 'fas fa-money-bill-wave';
$wizard_form_id = 'formIngresos';
$wizard_form_action = '';
$wizard_previous_url = 'reportado_wizard.php';
$wizard_next_url = 'gasto_wizard.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div class="row">
    <!-- Campo Salario -->
    <div class="col-md-4 mb-3">
        <label for="salario_val" class="form-label">
            <i class="bi bi-briefcase me-1"></i>Salario:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="salario_val" name="salario_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['salario_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Ingrese el salario mensual</div>
    </div>

    <!-- Campo Pensión -->
    <div class="col-md-4 mb-3">
        <label for="pension_val" class="form-label">
            <i class="bi bi-person-check me-1"></i>Pensión:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="pension_val" name="pension_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['pension_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Ingrese el valor de la pensión</div>
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
        <div class="form-text">Ingrese el valor del arriendo</div>
    </div>

    <!-- Campo Trabajo Independiente -->
    <div class="col-md-4 mb-3">
        <label for="trabajo_independiente_val" class="form-label">
            <i class="bi bi-person-workspace me-1"></i>Trabajo Independiente:
        </label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="trabajo_independiente_val" name="trabajo_independiente_val" 
                   value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['trabajo_independiente_val'] ?? '') : ''; ?>"
                   placeholder="0.00" required>
        </div>
        <div class="form-text">Ingrese ingresos por trabajo independiente</div>
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
        <div class="form-text">Ingrese otros ingresos</div>
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
