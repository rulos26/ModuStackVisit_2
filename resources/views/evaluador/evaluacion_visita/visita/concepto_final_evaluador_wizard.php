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

require_once __DIR__ . '/concepto_final_evaluador/ConceptoFinalEvaluadorController.php';
use App\Controllers\ConceptoFinalEvaluadorController;

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ConceptoFinalEvaluadorController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        // Guardar los datos del formulario para mantenerlos en caso de error
        $datos_formulario = $datos;
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../registro_fotos/registro_fotos.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Procesar errores para mostrarlos en campos específicos
            foreach ($errores as $error) {
                if (strpos($error, 'Actitud del evaluado') !== false) {
                    $errores_campos['actitud'] = $error;
                } elseif (strpos($error, 'Condiciones de Vivienda') !== false) {
                    $errores_campos['condiciones_vivienda'] = $error;
                } elseif (strpos($error, 'Dinámica Familiar') !== false) {
                    $errores_campos['dinamica_familiar'] = $error;
                } elseif (strpos($error, 'Condiciones Socio Económicas') !== false) {
                    $errores_campos['condiciones_economicas'] = $error;
                } elseif (strpos($error, 'Condiciones Académicas') !== false) {
                    $errores_campos['condiciones_academicas'] = $error;
                } elseif (strpos($error, 'Evaluación Experiencia Laboral') !== false) {
                    $errores_campos['evaluacion_experiencia_laboral'] = $error;
                } elseif (strpos($error, 'Observaciones') !== false) {
                    $errores_campos['observaciones'] = $error;
                } elseif (strpos($error, 'Concepto Final de la Visita') !== false) {
                    $errores_campos['id_concepto_final'] = $error;
                } elseif (strpos($error, 'Nombre del Evaluador') !== false) {
                    $errores_campos['nombre_evaluador'] = $error;
                } elseif (strpos($error, 'concepto de seguridad') !== false) {
                    $errores_campos['id_concepto_seguridad'] = $error;
                } else {
                    $_SESSION['error'] = $error;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en concepto_final_evaluador_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = ConceptoFinalEvaluadorController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $conceptos_finales = $controller->obtenerConceptosFinales();
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && !empty($datos_existentes)) {
        $datos_formulario = $datos_existentes;
    }
} catch (Exception $e) {
    error_log("Error en concepto_final_evaluador_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 22;
$wizard_title = 'Concepto Final del Evaluador';
$wizard_subtitle = 'Complete la evaluación final y emita el concepto de la visita domiciliaria';
$wizard_icon = 'fas fa-clipboard-check';
$wizard_form_id = 'formConcepto';
$wizard_form_action = '';
$wizard_previous_url = 'experiencia_laboral_wizard.php';
$wizard_next_url = '../registro_fotos/registro_fotos.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="actitud" class="form-label">
            <i class="bi bi-people me-1"></i>Actitud del evaluado y su grupo familiar:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['actitud']) ? 'is-invalid' : (!empty($datos_formulario['actitud']) ? 'is-valid' : ''); ?>" 
                  id="actitud" name="actitud" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Colaborativa, receptiva"><?php echo !empty($datos_formulario['actitud']) ? htmlspecialchars($datos_formulario['actitud']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['actitud']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['actitud']) ? htmlspecialchars($errores_campos['actitud']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="condiciones_vivienda" class="form-label">
            <i class="bi bi-house me-1"></i>Condiciones de Vivienda:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_vivienda']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_vivienda']) ? 'is-valid' : ''); ?>" 
                  id="condiciones_vivienda" name="condiciones_vivienda" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Adecuadas, buenas condiciones"><?php echo !empty($datos_formulario['condiciones_vivienda']) ? htmlspecialchars($datos_formulario['condiciones_vivienda']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['condiciones_vivienda']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['condiciones_vivienda']) ? htmlspecialchars($errores_campos['condiciones_vivienda']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="dinamica_familiar" class="form-label">
            <i class="bi bi-heart me-1"></i>Dinámica Familiar:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['dinamica_familiar']) ? 'is-invalid' : (!empty($datos_formulario['dinamica_familiar']) ? 'is-valid' : ''); ?>" 
                  id="dinamica_familiar" name="dinamica_familiar" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Armónica, unida"><?php echo !empty($datos_formulario['dinamica_familiar']) ? htmlspecialchars($datos_formulario['dinamica_familiar']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['dinamica_familiar']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['dinamica_familiar']) ? htmlspecialchars($errores_campos['dinamica_familiar']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="condiciones_economicas" class="form-label">
            <i class="bi bi-cash-stack me-1"></i>Condiciones Socio Económicas:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_economicas']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_economicas']) ? 'is-valid' : ''); ?>" 
                  id="condiciones_economicas" name="condiciones_economicas" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Estables, suficientes"><?php echo !empty($datos_formulario['condiciones_economicas']) ? htmlspecialchars($datos_formulario['condiciones_economicas']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['condiciones_economicas']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['condiciones_economicas']) ? htmlspecialchars($errores_campos['condiciones_economicas']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="condiciones_academicas" class="form-label">
            <i class="bi bi-mortarboard me-1"></i>Condiciones Académicas:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_academicas']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_academicas']) ? 'is-valid' : ''); ?>" 
                  id="condiciones_academicas" name="condiciones_academicas" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Buenas, adecuadas"><?php echo !empty($datos_formulario['condiciones_academicas']) ? htmlspecialchars($datos_formulario['condiciones_academicas']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['condiciones_academicas']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['condiciones_academicas']) ? htmlspecialchars($errores_campos['condiciones_academicas']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="evaluacion_experiencia_laboral" class="form-label">
            <i class="bi bi-briefcase me-1"></i>Evaluación Experiencia Laboral:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? 'is-invalid' : (!empty($datos_formulario['evaluacion_experiencia_laboral']) ? 'is-valid' : ''); ?>" 
                  id="evaluacion_experiencia_laboral" name="evaluacion_experiencia_laboral" 
                  rows="3" maxlength="500" minlength="10" required
                  placeholder="Ej: Positiva, estable"><?php echo !empty($datos_formulario['evaluacion_experiencia_laboral']) ? htmlspecialchars($datos_formulario['evaluacion_experiencia_laboral']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? htmlspecialchars($errores_campos['evaluacion_experiencia_laboral']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="observaciones" class="form-label">
            <i class="bi bi-chat-quote me-1"></i>Observaciones:
        </label>
        <textarea class="form-control <?php echo !empty($errores_campos['observaciones']) ? 'is-invalid' : (!empty($datos_formulario['observaciones']) ? 'is-valid' : ''); ?>" 
                  id="observaciones" name="observaciones" 
                  rows="4" maxlength="1000" minlength="15" required
                  placeholder="Ej: Observaciones generales de la visita"><?php echo !empty($datos_formulario['observaciones']) ? htmlspecialchars($datos_formulario['observaciones']) : ''; ?></textarea>
        <div class="form-text <?php echo !empty($errores_campos['observaciones']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['observaciones']) ? htmlspecialchars($errores_campos['observaciones']) : 'Mínimo 15 caracteres, máximo 1000 caracteres'; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="id_concepto_final" class="form-label">
            <i class="bi bi-check-circle me-1"></i>CONCEPTO FINAL DE LA VISITA:
        </label>
        <select class="form-select <?php echo !empty($errores_campos['id_concepto_final']) ? 'is-invalid' : (!empty($datos_formulario['id_concepto_final']) ? 'is-valid' : ''); ?>" 
                id="id_concepto_final" name="id_concepto_final" required>
            <option value="">Seleccione un concepto</option>
            <?php foreach ($conceptos_finales as $concepto): ?>
                <option value="<?php echo $concepto['id']; ?>" 
                    <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_final'] == $concepto['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($concepto['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text <?php echo !empty($errores_campos['id_concepto_final']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['id_concepto_final']) ? htmlspecialchars($errores_campos['id_concepto_final']) : ''; ?>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="nombre_evaluador" class="form-label">
            <i class="bi bi-person-badge me-1"></i>Nombre del Evaluador:
        </label>
        <input type="text" class="form-control <?php echo !empty($errores_campos['nombre_evaluador']) ? 'is-invalid' : (!empty($datos_formulario['nombre_evaluador']) ? 'is-valid' : ''); ?>" 
               id="nombre_evaluador" name="nombre_evaluador" 
               value="<?php echo !empty($datos_formulario['nombre_evaluador']) ? htmlspecialchars($datos_formulario['nombre_evaluador']) : ''; ?>"
               placeholder="Ej: Juan Pérez" minlength="5" required>
        <div class="form-text <?php echo !empty($errores_campos['nombre_evaluador']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['nombre_evaluador']) ? htmlspecialchars($errores_campos['nombre_evaluador']) : 'Mínimo 5 caracteres'; ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label for="id_concepto_seguridad" class="form-label">
            <i class="bi bi-shield-check me-1"></i>CONCEPTO DE SEGURIDAD:
        </label>
        <select class="form-select <?php echo !empty($errores_campos['id_concepto_seguridad']) ? 'is-invalid' : (!empty($datos_formulario['id_concepto_seguridad']) ? 'is-valid' : ''); ?>" 
                id="id_concepto_seguridad" name="id_concepto_seguridad" required>
            <option value="">Seleccione un concepto</option>
            <option value="1" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '1') ? 'selected' : ''; ?>>Aptos</option>
            <option value="2" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '2') ? 'selected' : ''; ?>>No Apto</option>
            <option value="3" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '3') ? 'selected' : ''; ?>>Apto con reserva</option>
        </select>
        <div class="form-text <?php echo !empty($errores_campos['id_concepto_seguridad']) ? 'error-message' : ''; ?>">
            <?php echo !empty($errores_campos['id_concepto_seguridad']) ? htmlspecialchars($errores_campos['id_concepto_seguridad']) : ''; ?>
        </div>
    </div>
</div>
