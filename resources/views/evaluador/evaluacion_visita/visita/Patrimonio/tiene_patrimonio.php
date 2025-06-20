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

require_once __DIR__ . '/PatrimonioController.php';
use App\Controllers\PatrimonioController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = PatrimonioController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../cuentas_bancarias/cuentas_bancarias.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en tiene_patrimonio.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = PatrimonioController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $parametros = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en tiene_patrimonio.php: " . $e->getMessage());
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
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-bank me-2"></i>
                VISITA DOMICILIARÍA - PATRIMONIO
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-bank"></i></div>
                    <div class="step-title">Paso 11</div>
                    <div class="step-description">Patrimonio</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../servicios_publicos/servicios_publicos.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formPatrimonio').submit();">
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
            
            <?php if ($datos_existentes): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Ya existe información de patrimonio registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formPatrimonio" novalidate autocomplete="off">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tiene_patrimonio" class="form-label">
                            <i class="bi bi-question-circle me-1"></i>¿Posee usted patrimonio? <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="tiene_patrimonio" name="tiene_patrimonio" required onchange="toggleFormularioPatrimonio()">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['tiene_patrimonio'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Seleccione "No" si no posee patrimonio, o "Sí" para continuar con el formulario detallado.</div>
                    </div>
                </div>
                
                <!-- Campos de patrimonio detallado (se muestran/ocultan dinámicamente) -->
                <div id="camposPatrimonio" class="campos-patrimonio" style="display: none;">
                    <hr class="my-4">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-bank me-2"></i>Detalles del Patrimonio
                    </h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="valor_vivienda" class="form-label">
                                <i class="bi bi-house-dollar me-1"></i>Valor de la Vivienda:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="valor_vivienda" name="valor_vivienda" 
                                       value="<?php echo $datos_existentes && $datos_existentes['valor_vivienda'] != 'N/A' ? htmlspecialchars($datos_existentes['valor_vivienda']) : ''; ?>"
                                       placeholder="0.00">
                            </div>
                            <div class="form-text">Ingrese el valor estimado de su vivienda</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="direccion" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Dirección:
                            </label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?php echo $datos_existentes && $datos_existentes['direccion'] != 'N/A' ? htmlspecialchars($datos_existentes['direccion']) : ''; ?>"
                                   placeholder="Dirección de la vivienda" minlength="10">
                            <div class="form-text">Mínimo 10 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_vehiculo" class="form-label">
                                <i class="bi bi-car-front me-1"></i>Vehículo:
                            </label>
                            <input type="text" class="form-control" id="id_vehiculo" name="id_vehiculo" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_vehiculo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_vehiculo']) : ''; ?>"
                                   placeholder="Tipo de vehículo" minlength="3">
                            <div class="form-text">Mínimo 3 caracteres</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="id_marca" class="form-label">
                                <i class="bi bi-tag me-1"></i>Marca:
                            </label>
                            <input type="text" class="form-control" id="id_marca" name="id_marca" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_marca'] != 'N/A' ? htmlspecialchars($datos_existentes['id_marca']) : ''; ?>"
                                   placeholder="Marca del vehículo" minlength="2">
                            <div class="form-text">Mínimo 2 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_modelo" class="form-label">
                                <i class="bi bi-gear me-1"></i>Modelo:
                            </label>
                            <input type="text" class="form-control" id="id_modelo" name="id_modelo" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_modelo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_modelo']) : ''; ?>"
                                   placeholder="Modelo del vehículo" minlength="2">
                            <div class="form-text">Mínimo 2 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_ahorro" class="form-label">
                                <i class="bi bi-piggy-bank me-1"></i>Ahorro (CDT, Inversiones):
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="id_ahorro" name="id_ahorro" 
                                       value="<?php echo $datos_existentes && $datos_existentes['id_ahorro'] != 'N/A' ? htmlspecialchars($datos_existentes['id_ahorro']) : ''; ?>"
                                       placeholder="0.00">
                            </div>
                            <div class="form-text">Ingrese el valor total de sus ahorros</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="otros" class="form-label">
                                <i class="bi bi-plus-circle me-1"></i>Otros Bienes:
                            </label>
                            <input type="text" class="form-control" id="otros" name="otros" 
                                   value="<?php echo $datos_existentes && $datos_existentes['otros'] != 'N/A' ? htmlspecialchars($datos_existentes['otros']) : ''; ?>"
                                   placeholder="Otros bienes o activos">
                            <div class="form-text">Opcional - Otros bienes o activos que posea</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label for="observacion" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Observación:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" 
                                      rows="4" maxlength="1000"><?php echo $datos_existentes && $datos_existentes['observacion'] != 'N/A' ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres. Mínimo 10 caracteres si se llena.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../servicios_publicos/servicios_publicos.php" class="btn btn-secondary btn-lg">
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
function toggleFormularioPatrimonio() {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    const camposPatrimonioDiv = document.getElementById('camposPatrimonio');
    const campos = camposPatrimonioDiv.querySelectorAll('input, select, textarea');

    if (tienePatrimonioSelect.value === '2') { // '2' corresponde a "Sí"
        camposPatrimonioDiv.style.display = 'block';
    } else {
        camposPatrimonioDiv.style.display = 'none';
        // Limpiar todos los campos cuando se ocultan para no enviar datos antiguos
        campos.forEach(campo => {
            if (campo.type === 'select-one') {
                campo.selectedIndex = 0; // Resetea el select
            } else {
                campo.value = ''; // Limpia inputs y textareas
            }
        });
    }
}

// Ejecutar al cargar la página para establecer el estado inicial correcto
document.addEventListener('DOMContentLoaded', function() {
    toggleFormularioPatrimonio();
    
    // Inicializar autoNumeric para campos de dinero
    new AutoNumeric('#valor_vivienda', {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });

    new AutoNumeric('#id_ahorro', {
        currencySymbol: '$',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        minimumValue: '0'
    });
});

// Validación del formulario
document.getElementById('formPatrimonio').addEventListener('submit', function(event) {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    
    // Validar que se haya seleccionado una opción principal
    if (!tienePatrimonioSelect.value || tienePatrimonioSelect.value === '') {
        event.preventDefault();
        alert('Por favor, seleccione si posee patrimonio.');
        tienePatrimonioSelect.focus();
        return;
    }
    
    // Validar campos de patrimonio solo si se seleccionó "Sí"
    if (tienePatrimonioSelect.value === '2') {
        const camposObligatorios = [
            'valor_vivienda', 'direccion', 'id_vehiculo', 'id_marca', 'id_modelo', 'id_ahorro'
        ];
        
        for (const idCampo of camposObligatorios) {
            const elemento = document.getElementById(idCampo);
            if (!elemento.value || elemento.value.trim() === '') {
                event.preventDefault();
                // Obtener el texto de la etiqueta para un mensaje más claro
                const label = elemento.closest('.mb-3').querySelector('label');
                const labelText = label ? label.innerText.replace('*', '').trim() : idCampo;
                alert(`El campo "${labelText}" es obligatorio.`);
                elemento.focus();
                return;
            }
        }
    }
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