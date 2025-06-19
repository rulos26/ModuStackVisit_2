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

require_once __DIR__ . '/SaludController.php';
use App\Controllers\SaludController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = SaludController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../registro_fotografico/registro_fotografico.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en salud.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = SaludController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $estados_salud = $controller->obtenerOpciones('estados');
    $opciones_parametro = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en salud.php: " . $e->getMessage());
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
                <i class="bi bi-heartbeat me-2"></i>
                VISITA DOMICILIARÍA - SALUD
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Salud</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Finalización</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../camara_comercio/camara_comercio.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formSalud').submit();">
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
                    Ya existe información de salud registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formSalud" novalidate autocomplete="off">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_estado_salud" class="form-label">
                            <i class="bi bi-heart me-1"></i>Estado de Salud:
                        </label>
                        <select class="form-select" id="id_estado_salud" name="id_estado_salud" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($estados_salud as $estado): ?>
                                <option value="<?php echo $estado['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['id_estado_salud'] == $estado['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($estado['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el estado de salud.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="tipo_enfermedad" class="form-label">
                            <i class="bi bi-exclamation-triangle me-1"></i>¿Padece algún tipo de enfermedad?:
                        </label>
                        <select class="form-select" id="tipo_enfermedad" name="tipo_enfermedad" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['tipo_enfermedad'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si padece algún tipo de enfermedad.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3 campos-adicionales" id="tipo_enfermedad_cual" style="display: none;">
                        <label for="tipo_enfermedad_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="tipo_enfermedad_cual" name="tipo_enfermedad_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['tipo_enfermedad_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué tipo de enfermedad padece.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="limitacion_fisica" class="form-label">
                            <i class="bi bi-person-x me-1"></i>¿Tiene alguna limitación física?
                        </label>
                        <select class="form-select" id="limitacion_fisica" name="limitacion_fisica" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['limitacion_fisica'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si tiene alguna limitación física.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3 campos-adicionales" id="limitacion_fisica_cual" style="display: none;">
                        <label for="limitacion_fisica_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="limitacion_fisica_cual" name="limitacion_fisica_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['limitacion_fisica_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué limitación física tiene.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="tipo_medicamento" class="form-label">
                            <i class="bi bi-capsule me-1"></i>Tipo de Medicamento:
                        </label>
                        <select class="form-select" id="tipo_medicamento" name="tipo_medicamento" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['tipo_medicamento'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el tipo de medicamento.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3 campos-adicionales" id="tipo_medicamento_cual" style="display: none;">
                        <label for="tipo_medicamento_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="tipo_medicamento_cual" name="tipo_medicamento_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['tipo_medicamento_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué medicamentos toma.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="ingiere_alcohol" class="form-label">
                            <i class="bi bi-cup-straw me-1"></i>Ingiere Alcohol:
                        </label>
                        <select class="form-select" id="ingiere_alcohol" name="ingiere_alcohol" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['ingiere_alcohol'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si ingiere alcohol.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3 campos-adicionales" id="ingiere_alcohol_cual" style="display: none;">
                        <label for="ingiere_alcohol_cual" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>¿Cuál(es)?
                        </label>
                        <input type="text" class="form-control" id="ingiere_alcohol_cual" name="ingiere_alcohol_cual" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['ingiere_alcohol_cual']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor especifique qué tipo de alcohol ingiere.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fuma" class="form-label">
                            <i class="bi bi-smoking me-1"></i>Fuma:
                        </label>
                        <select class="form-select" id="fuma" name="fuma" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['fuma'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si fuma.</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="observacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Observación:
                        </label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="4" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                        <div class="form-text">Máximo 1000 caracteres</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../camara_comercio/camara_comercio.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tipoEnfermedad = document.getElementById('tipo_enfermedad');
                const limitacionFisica = document.getElementById('limitacion_fisica');
                const tipoMedicamento = document.getElementById('tipo_medicamento');
                const ingiereAlcohol = document.getElementById('ingiere_alcohol');
                const fuma = document.getElementById('fuma');

                const tipoEnfermedadCual = document.getElementById('tipo_enfermedad_cual');
                const limitacionFisicaCual = document.getElementById('limitacion_fisica_cual');
                const tipoMedicamentoCual = document.getElementById('tipo_medicamento_cual');
                const ingiereAlcoholCual = document.getElementById('ingiere_alcohol_cual');
                
                // Verificar si hay datos existentes
                const tieneDatosExistentes = <?php echo ($datos_existentes) ? 'true' : 'false'; ?>;
                
                function toggleCampos() {
                    // Mostrar u ocultar campos según el valor seleccionado
                    tipoEnfermedadCual.style.display = tipoEnfermedad.value === '2' ? 'block' : 'none';
                    limitacionFisicaCual.style.display = limitacionFisica.value === '2' ? 'block' : 'none';
                    tipoMedicamentoCual.style.display = tipoMedicamento.value === '2' ? 'block' : 'none';
                    ingiereAlcoholCual.style.display = ingiereAlcohol.value === '2' ? 'block' : 'none';
                    
                    // Limpiar campos cuando se ocultan
                    if (tipoEnfermedad.value !== '2') tipoEnfermedadCual.value = '';
                    if (limitacionFisica.value !== '2') limitacionFisicaCual.value = '';
                    if (tipoMedicamento.value !== '2') tipoMedicamentoCual.value = '';
                    if (ingiereAlcohol.value !== '2') ingiereAlcoholCual.value = '';
                }

                // Ejecutar al cargar la página
                toggleCampos();
                
                // Si hay datos existentes, mostrar campos correspondientes
                if (tieneDatosExistentes) {
                    setTimeout(toggleCampos, 100);
                }

                // Escuchar el evento de cambio en los campos
                tipoEnfermedad.addEventListener('change', toggleCampos);
                limitacionFisica.addEventListener('change', toggleCampos);
                tipoMedicamento.addEventListener('change', toggleCampos);
                ingiereAlcohol.addEventListener('change', toggleCampos);
                fuma.addEventListener('change', toggleCampos);
            });
            </script>
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