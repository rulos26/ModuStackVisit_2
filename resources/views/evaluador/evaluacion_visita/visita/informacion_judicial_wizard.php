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

require_once __DIR__ . '/informacion_judicial/InformacionJudicialController.php';
use App\Controllers\InformacionJudicialController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InformacionJudicialController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: experiencia_laboral_wizard.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en informacion_judicial_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = InformacionJudicialController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $opciones = $controller->obtenerOpciones();
    $hora_actual = date('Y-m-d H:i:s');
} catch (Exception $e) {
    error_log("Error en informacion_judicial_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 20;
$wizard_title = 'Información Judicial';
$wizard_subtitle = 'Verificación de antecedentes judiciales y disciplinarios';
$wizard_icon = 'fas fa-gavel';
$wizard_form_id = 'formJudicial';
$wizard_form_action = '';
$wizard_previous_url = 'estudios_wizard.php';
$wizard_next_url = 'experiencia_laboral_wizard.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<!-- Revisión Fiscal -->
<div class="row mb-4">
    <div class="col-12">
        <label for="revi_fiscal" class="form-label">
            <i class="bi bi-shield-check me-1"></i>
            ANTECEDENTES JUDICIALES Y DISCIPLINARIOS POLICÍA Y CONTRALORÍA, PROCURADURÍA, LISTAS CLINTON, INTERPOL ORFAC
        </label>
        <textarea name="revi_fiscal" id="revi_fiscal" class="form-control" rows="5" minlength="50" required><?php 
            if (!empty($datos_existentes)) {
                echo htmlspecialchars($datos_existentes['revi_fiscal']);
            } else {
                echo "El señor Luis carlos matiz Valbuena identificado con cedula de ciudadanía n° 79632323 expedía en Bogotá según certificación No presenta antecedentes judiciales, No tiene asuntos pendientes con las autoridades judiciales, No se encuentra reportado como responsable fiscal, No registra sanciones o inhabilidades vigentes, No registra lista OFAC, CLINTON, INTERPOL. VERIFICACION EN TIEMPO REAL: " . $hora_actual;
            }
        ?></textarea>
        <div class="form-text">Mínimo 50 caracteres</div>
    </div>
</div>

<!-- Campos de información judicial -->
<div class="row">
    <!-- Denuncias -->
    <div class="col-md-4 mb-3">
        <label for="denuncias_opc" class="form-label">
            <i class="bi bi-exclamation-triangle me-1"></i>
            ¿Ha presentado denuncias o demandas a persona natural o persona jurídica?
        </label>
        <select class="form-select" id="denuncias_opc" name="denuncias_opc" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($opciones as $opcion): ?>
                <option value="<?php echo $opcion['id']; ?>" 
                    <?php echo (!empty($datos_existentes) && $datos_existentes['denuncias_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="col-md-4 mb-3 wizard-conditional-fields" data-depends-on="denuncias_opc" data-depends-value="2">
        <label for="denuncias_desc" class="form-label">
            <i class="bi bi-pencil me-1"></i>Descripción de Denuncias:
        </label>
        <input type="text" class="form-control" id="denuncias_desc" name="denuncias_desc" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['denuncias_desc']) : ''; ?>"
               placeholder="Describa las denuncias..." minlength="10">
        <div class="form-text">Mínimo 10 caracteres</div>
    </div>
    
    <!-- Procesos Judiciales -->
    <div class="col-md-4 mb-3">
        <label for="procesos_judiciales_opc" class="form-label">
            <i class="bi bi-gavel me-1"></i>
            ¿Presenta procesos judiciales o disciplinarios en contra?
        </label>
        <select class="form-select" id="procesos_judiciales_opc" name="procesos_judiciales_opc" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($opciones as $opcion): ?>
                <option value="<?php echo $opcion['id']; ?>" 
                    <?php echo (!empty($datos_existentes) && $datos_existentes['procesos_judiciales_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3 wizard-conditional-fields" data-depends-on="procesos_judiciales_opc" data-depends-value="2">
        <label for="procesos_judiciales_desc" class="form-label">
            <i class="bi bi-pencil me-1"></i>Descripción de Procesos Judiciales:
        </label>
        <input type="text" class="form-control" id="procesos_judiciales_desc" name="procesos_judiciales_desc" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['procesos_judiciales_desc']) : ''; ?>"
               placeholder="Describa los procesos..." minlength="10">
        <div class="form-text">Mínimo 10 caracteres</div>
    </div>
    
    <!-- Preso -->
    <div class="col-md-4 mb-3">
        <label for="preso_opc" class="form-label">
            <i class="bi bi-person-x me-1"></i>
            ¿Ha estado preso?
        </label>
        <select class="form-select" id="preso_opc" name="preso_opc" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($opciones as $opcion): ?>
                <option value="<?php echo $opcion['id']; ?>" 
                    <?php echo (!empty($datos_existentes) && $datos_existentes['preso_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="col-md-4 mb-3 wizard-conditional-fields" data-depends-on="preso_opc" data-depends-value="2">
        <label for="preso_desc" class="form-label">
            <i class="bi bi-pencil me-1"></i>Descripción de Preso:
        </label>
        <input type="text" class="form-control" id="preso_desc" name="preso_desc" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['preso_desc']) : ''; ?>"
               placeholder="Describa la situación..." minlength="10">
        <div class="form-text">Mínimo 10 caracteres</div>
    </div>
</div>

<div class="row">
    <!-- Familia Detenida -->
    <div class="col-md-4 mb-3">
        <label for="familia_detenido_opc" class="form-label">
            <i class="bi bi-people me-1"></i>
            ¿Tiene familia detenida?
        </label>
        <select class="form-select" id="familia_detenido_opc" name="familia_detenido_opc" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($opciones as $opcion): ?>
                <option value="<?php echo $opcion['id']; ?>" 
                    <?php echo (!empty($datos_existentes) && $datos_existentes['familia_detenido_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="col-md-4 mb-3 wizard-conditional-fields" data-depends-on="familia_detenido_opc" data-depends-value="2">
        <label for="familia_detenido_desc" class="form-label">
            <i class="bi bi-pencil me-1"></i>Descripción de Familia Detenida:
        </label>
        <input type="text" class="form-control" id="familia_detenido_desc" name="familia_detenido_desc" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['familia_detenido_desc']) : ''; ?>"
               placeholder="Describa la situación..." minlength="10">
        <div class="form-text">Mínimo 10 caracteres</div>
    </div>
    
    <!-- Centros Penitenciarios -->
    <div class="col-md-4 mb-3">
        <label for="centros_penitenciarios_opc" class="form-label">
            <i class="bi bi-building me-1"></i>
            ¿Ha visitado centros penitenciarios?
        </label>
        <select class="form-select" id="centros_penitenciarios_opc" name="centros_penitenciarios_opc" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($opciones as $opcion): ?>
                <option value="<?php echo $opcion['id']; ?>" 
                    <?php echo (!empty($datos_existentes) && $datos_existentes['centros_penitenciarios_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3 wizard-conditional-fields" data-depends-on="centros_penitenciarios_opc" data-depends-value="2">
        <label for="centros_penitenciarios_desc" class="form-label">
            <i class="bi bi-pencil me-1"></i>Descripción de Centros Penitenciarios:
        </label>
        <input type="text" class="form-control" id="centros_penitenciarios_desc" name="centros_penitenciarios_desc" 
               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['centros_penitenciarios_desc']) : ''; ?>"
               placeholder="Describa las visitas..." minlength="10">
        <div class="form-text">Mínimo 10 caracteres</div>
    </div>
</div>
