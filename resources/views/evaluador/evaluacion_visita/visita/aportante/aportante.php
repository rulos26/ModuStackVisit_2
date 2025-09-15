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

require_once __DIR__ . '/AportanteController.php';
use App\Controllers\AportanteController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = AportanteController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../data_credito/data_credito.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en aportante.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = AportanteController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en aportante.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.steps-horizontal { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; width: 100%; gap: 0.5rem; }
.step-horizontal { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.step-horizontal:not(:last-child)::after { content: ''; position: absolute; top: 24px; left: 50%; width: 100%; height: 4px; background: #e0e0e0; z-index: 0; transform: translateX(50%); }
.step-horizontal .step-icon { width: 48px; height: 48px; border-radius: 50%; background: #e0e0e0; color: #888; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.5rem; border: 2px solid #e0e0e0; z-index: 1; transition: all 0.3s; }
.step-horizontal.active .step-icon { background: #4361ee; border-color: #4361ee; color: #fff; box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2); }
.step-horizontal.complete .step-icon { background: #2ecc71; border-color: #2ecc71; color: #fff; }
.step-horizontal .step-title { font-weight: bold; font-size: 1rem; margin-bottom: 0.2rem; }
.step-horizontal .step-description { font-size: 0.85rem; color: #888; text-align: center; }
.step-horizontal.active .step-title, .step-horizontal.active .step-description { color: #4361ee; }
.step-horizontal.complete .step-title, .step-horizontal.complete .step-description { color: #2ecc71; }
.aportante-item { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f8f9fa; }
.aportante-item h6 { color: #495057; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px; }
.btn-remove-aportante { position: absolute; top: 10px; right: 10px; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>
                VISITA DOMICILIARÍA - PERSONAS QUE APORTAN ECONÓMICAMENTE AL HOGAR
            </h5>
        </div>
        <div class="card-body">
            <!-- Indicador de pasos -->
            <div class="steps-horizontal mb-4">
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-user"></i></div>
                    <div class="step-title">Paso 1</div>
                    <div class="step-description">Datos Básicos</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                    <div class="step-title">Paso 2</div>
                    <div class="step-description">Información Personal</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-building"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Cámara de Comercio</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Salud</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Composición Familiar</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-heart"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Información Pareja</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-home"></i></div>
                    <div class="step-title">Paso 7</div>
                    <div class="step-description">Tipo de Vivienda</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="step-title">Paso 8</div>
                    <div class="step-description">Estado de Vivienda</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-box-seam"></i></div>
                    <div class="step-title">Paso 9</div>
                    <div class="step-description">Inventario de Enseres</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-lightning-charge"></i></div>
                    <div class="step-title">Paso 10</div>
                    <div class="step-description">Servicios Públicos</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-bank"></i></div>
                    <div class="step-title">Paso 11</div>
                    <div class="step-description">Patrimonio</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                    <div class="step-title">Paso 12</div>
                    <div class="step-description">Cuentas Bancarias</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="step-title">Paso 13</div>
                    <div class="step-description">Pasivos</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 14</div>
                    <div class="step-description">Aportantes</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../pasivos/pasivos.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formAportantes').submit();">
                    Siguiente<i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>

            <!-- Mensajes de sesión -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($datos_existentes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Ya existen <?php echo count($datos_existentes); ?> aportante(s) registrado(s) para esta cédula. Puede actualizar los datos.
                </div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 300px;">
                </div>
                <div class="col-md-6 text-end">
                    <div class="text-muted">
                        <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                        <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                    </div>
                </div>
            </div>
            
            <form action="" method="POST" id="formAportantes" novalidate autocomplete="off">
                <div id="aportantes-container">
                    <!-- Aportante inicial -->
                    <div class="aportante-item" data-aportante="0">
                        <h6><i class="fas fa-user me-2"></i>Aportante #1</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_0" class="form-label">
                                    <i class="bi bi-person me-1"></i>Nombre:
                                </label>
                                <input type="text" class="form-control" id="nombre_0" name="nombre[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['nombre'] ?? '') : ''; ?>"
                                       placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                                <div class="form-text">Mínimo 3 caracteres, máximo 100</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="valor_0" class="form-label">
                                    <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="valor_0" name="valor[]" 
                                           value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['valor'] ?? '') : ''; ?>"
                                           placeholder="0.00" required>
                                </div>
                                <div class="form-text">Ingrese el valor mensual del aporte</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aportantes adicionales si existen datos -->
                    <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
                        <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
                            <div class="aportante-item" data-aportante="<?php echo $i; ?>">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-aportante" onclick="removeAportante(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h6><i class="fas fa-user me-2"></i>Aportante #<?php echo $i + 1; ?></h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-person me-1"></i>Nombre:
                                        </label>
                                        <input type="text" class="form-control" id="nombre_<?php echo $i; ?>" name="nombre[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['nombre']); ?>"
                                               placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="valor_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control" id="valor_<?php echo $i; ?>" name="valor[]" 
                                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['valor']); ?>"
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
                        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarAportante">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Otro Aportante
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../pasivos/pasivos.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            <div class="row">
                <div class="col-md-6">
                    <small>© 2024 V0.01 - Sistema de Visitas Domiciliarias</small>
                </div>
                <div class="col-md-6 text-end">
                    <small>Usuario: <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0/dist/autoNumeric.min.js"></script>
<script>
let aportanteCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;

// Inicializar autoNumeric para campos de dinero existentes
document.querySelectorAll('input[id^="valor_"]').forEach(function(input) {
    new AutoNumeric(input, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
});

document.getElementById('btnAgregarAportante').addEventListener('click', function() {
    const container = document.getElementById('aportantes-container');
    const nuevoAportante = document.createElement('div');
    nuevoAportante.className = 'aportante-item';
    nuevoAportante.setAttribute('data-aportante', aportanteCounter);
    
    nuevoAportante.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm btn-remove-aportante" onclick="removeAportante(this)">
            <i class="fas fa-times"></i>
        </button>
        <h6><i class="fas fa-user me-2"></i>Aportante #${aportanteCounter + 1}</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre_${aportanteCounter}" class="form-label">
                    <i class="bi bi-person me-1"></i>Nombre:
                </label>
                <input type="text" class="form-control" id="nombre_${aportanteCounter}" name="nombre[]" 
                       placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                <div class="form-text">Mínimo 3 caracteres, máximo 100</div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="valor_${aportanteCounter}" class="form-label">
                    <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="valor_${aportanteCounter}" name="valor[]" 
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Ingrese el valor mensual del aporte</div>
            </div>
        </div>
    `;
    
    container.appendChild(nuevoAportante);
    
    // Inicializar autoNumeric para el nuevo campo de dinero
    new AutoNumeric(`#valor_${aportanteCounter}`, {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
    
    aportanteCounter++;
});

function removeAportante(button) {
    const aportanteItem = button.closest('.aportante-item');
    aportanteItem.remove();
    
    // Renumerar los aportantes restantes
    const aportantes = document.querySelectorAll('.aportante-item');
    aportantes.forEach((aportante, index) => {
        const titulo = aportante.querySelector('h6');
        titulo.innerHTML = `<i class="fas fa-user me-2"></i>Aportante #${index + 1}`;
    });
}
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