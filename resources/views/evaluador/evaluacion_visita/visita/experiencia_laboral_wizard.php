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

require_once __DIR__ . '/experiencia_laboral/ExperienciaLaboralController.php';
use App\Controllers\ExperienciaLaboralController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ExperienciaLaboralController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: concepto_final_evaluador_wizard.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en experiencia_laboral_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = ExperienciaLaboralController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en experiencia_laboral_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 21;
$wizard_title = 'Experiencia Laboral';
$wizard_subtitle = 'Ingrese la información de experiencia laboral del evaluado';
$wizard_icon = 'fas fa-briefcase';
$wizard_form_id = 'formExperiencia';
$wizard_form_action = '';
$wizard_previous_url = 'informacion_judicial_wizard.php';
$wizard_next_url = 'concepto_final_evaluador_wizard.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="empresa" class="form-label">
            <i class="bi bi-building me-1"></i>Empresa:
        </label>
        <input type="text" class="form-control" id="empresa" name="empresa" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['empresa']) : ''; ?>"
               placeholder="Ej: Empresa ABC S.A." minlength="3" required>
        <div class="form-text">Mínimo 3 caracteres</div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="tiempo" class="form-label">
            <i class="bi bi-clock me-1"></i>Tiempo Laborado:
        </label>
        <input type="text" class="form-control" id="tiempo" name="tiempo" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['tiempo']) : ''; ?>"
               placeholder="Ej: 2 años, 6 meses" minlength="3" required>
        <div class="form-text">Mínimo 3 caracteres</div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="cargo" class="form-label">
            <i class="bi bi-person-badge me-1"></i>Cargo Desempeñado:
        </label>
        <input type="text" class="form-control" id="cargo" name="cargo" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['cargo']) : ''; ?>"
               placeholder="Ej: Gerente de Ventas" minlength="3" required>
        <div class="form-text">Mínimo 3 caracteres</div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="salario" class="form-label">
            <i class="bi bi-cash me-1"></i>Salario:
        </label>
        <input type="number" class="form-control" id="salario" name="salario" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['salario']) : ''; ?>"
               placeholder="Ej: 2500000" min="0" step="1000" required>
        <div class="form-text">Salario mensual en pesos</div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="retiro" class="form-label">
            <i class="bi bi-box-arrow-right me-1"></i>Motivo de Retiro:
        </label>
        <input type="text" class="form-control" id="retiro" name="retiro" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['retiro']) : ''; ?>"
               placeholder="Ej: Renuncia voluntaria" minlength="5" required>
        <div class="form-text">Mínimo 5 caracteres</div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="concepto" class="form-label">
            <i class="bi bi-chat-quote me-1"></i>Concepto Emitido:
        </label>
        <input type="text" class="form-control" id="concepto" name="concepto" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['concepto']) : ''; ?>"
               placeholder="Ej: Excelente trabajador" minlength="5" required>
        <div class="form-text">Mínimo 5 caracteres</div>
    </div>
</div>
