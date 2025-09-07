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
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../ingresos_mensuales/ingresos_mensuales.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en reportado_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = DataCreditoController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en reportado_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 16;
$wizard_title = 'Detalles de Reportes en Centrales de Riesgo';
$wizard_subtitle = 'Ingrese la información detallada de los reportes en centrales de riesgo';
$wizard_icon = 'fas fa-flag';
$wizard_form_id = 'formReportesDetallado';
$wizard_form_action = '';
$wizard_previous_url = 'data_credito_wizard.php';
$wizard_next_url = '../ingresos_mensuales/ingresos_mensuales.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div id="reportes-container">
    <!-- Reporte inicial -->
    <div class="reporte-item" data-reporte="0">
        <h6><i class="fas fa-shield-check me-2"></i>Reporte #1</h6>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="entidad_0" class="form-label">
                    <i class="bi bi-building me-1"></i>Entidad:
                </label>
                <input type="text" class="form-control" id="entidad_0" name="entidad[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['entidad'] ?? '') : ''; ?>"
                       placeholder="Ej: Banco de Bogotá" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="cuotas_0" class="form-label">
                    <i class="bi bi-calendar-check me-1"></i>N° Cuotas:
                </label>
                <input type="text" class="form-control" id="cuotas_0" name="cuotas[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['cuotas'] ?? '') : ''; ?>"
                       placeholder="0" required>
                <div class="form-text">Número de cuotas pendientes</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="pago_mensual_0" class="form-label">
                    <i class="bi bi-cash-stack me-1"></i>Pago Mensual:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="pago_mensual_0" name="pago_mensual[]" 
                           value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['pago_mensual'] ?? '') : ''; ?>"
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Valor del pago mensual</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="deuda_0" class="form-label">
                    <i class="bi bi-exclamation-triangle me-1"></i>Total Deuda:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="deuda_0" name="deuda[]" 
                           value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['deuda'] ?? '') : ''; ?>"
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Valor total de la deuda</div>
            </div>
        </div>
    </div>
    
    <!-- Reportes adicionales si existen datos -->
    <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
        <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
            <div class="reporte-item" data-reporte="<?php echo $i; ?>">
                <button type="button" class="btn btn-danger btn-sm btn-remove-reporte" onclick="removeReporte(this)">
                    <i class="fas fa-times"></i>
                </button>
                <h6><i class="fas fa-shield-check me-2"></i>Reporte #<?php echo $i + 1; ?></h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="entidad_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-building me-1"></i>Entidad:
                        </label>
                        <input type="text" class="form-control" id="entidad_<?php echo $i; ?>" name="entidad[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['entidad']); ?>"
                               placeholder="Ej: Banco de Bogotá" minlength="3" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="cuotas_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-calendar-check me-1"></i>N° Cuotas:
                        </label>
                        <input type="text" class="form-control" id="cuotas_<?php echo $i; ?>" name="cuotas[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['cuotas']); ?>"
                               placeholder="0" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="pago_mensual_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-cash-stack me-1"></i>Pago Mensual:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="pago_mensual_<?php echo $i; ?>" name="pago_mensual[]" 
                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['pago_mensual']); ?>"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="deuda_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-exclamation-triangle me-1"></i>Total Deuda:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="deuda_<?php echo $i; ?>" name="deuda[]" 
                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['deuda']); ?>"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-12 text-center">
        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarReporte">
            <i class="bi bi-plus-circle me-2"></i>Agregar Otro Reporte
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0/dist/autoNumeric.min.js"></script>
<script>
let reporteCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;

// Inicializar autoNumeric para campos de dinero existentes
document.querySelectorAll('input[id^="pago_mensual_"], input[id^="deuda_"]').forEach(function(input) {
    new AutoNumeric(input, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
});

document.getElementById('btnAgregarReporte').addEventListener('click', function() {
    const container = document.getElementById('reportes-container');
    const nuevoReporte = document.createElement('div');
    nuevoReporte.className = 'reporte-item';
    nuevoReporte.setAttribute('data-reporte', reporteCounter);
    
    nuevoReporte.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm btn-remove-reporte" onclick="removeReporte(this)">
            <i class="fas fa-times"></i>
        </button>
        <h6><i class="fas fa-shield-check me-2"></i>Reporte #${reporteCounter + 1}</h6>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="entidad_${reporteCounter}" class="form-label">
                    <i class="bi bi-building me-1"></i>Entidad:
                </label>
                <input type="text" class="form-control" id="entidad_${reporteCounter}" name="entidad[]" 
                       placeholder="Ej: Banco de Bogotá" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="cuotas_${reporteCounter}" class="form-label">
                    <i class="bi bi-calendar-check me-1"></i>N° Cuotas:
                </label>
                <input type="text" class="form-control" id="cuotas_${reporteCounter}" name="cuotas[]" 
                       placeholder="0" required>
                <div class="form-text">Número de cuotas pendientes</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="pago_mensual_${reporteCounter}" class="form-label">
                    <i class="bi bi-cash-stack me-1"></i>Pago Mensual:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="pago_mensual_${reporteCounter}" name="pago_mensual[]" 
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Valor del pago mensual</div>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="deuda_${reporteCounter}" class="form-label">
                    <i class="bi bi-exclamation-triangle me-1"></i>Total Deuda:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="deuda_${reporteCounter}" name="deuda[]" 
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Valor total de la deuda</div>
            </div>
        </div>
    `;
    
    container.appendChild(nuevoReporte);
    
    // Inicializar autoNumeric para los nuevos campos de dinero
    new AutoNumeric(`#pago_mensual_${reporteCounter}`, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
    
    new AutoNumeric(`#deuda_${reporteCounter}`, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
    
    reporteCounter++;
});

function removeReporte(button) {
    const reporteItem = button.closest('.reporte-item');
    reporteItem.remove();
    
    // Renumerar los reportes restantes
    const reportes = document.querySelectorAll('.reporte-item');
    reportes.forEach((reporte, index) => {
        const titulo = reporte.querySelector('h6');
        titulo.innerHTML = `<i class="fas fa-shield-check me-2"></i>Reporte #${index + 1}`;
    });
}
</script>
?>
