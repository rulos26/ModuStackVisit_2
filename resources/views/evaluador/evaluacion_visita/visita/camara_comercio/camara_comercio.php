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

require_once __DIR__ . '/CamaraComercioController.php';
use App\Controllers\CamaraComercioController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = CamaraComercioController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../salud/salud.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en camara_comercio.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = CamaraComercioController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en camara_comercio.php: " . $e->getMessage());
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
                <i class="bi bi-building me-2"></i>
                VISITA DOMICILIARÍA - CÁMARA DE COMERCIO
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-building"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Cámara de Comercio</div>
                </div>
                <div class="step-horizontal">
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
                <a href="../informacion_personal/informacion_personal.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formCamaraComercio').submit();">
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
                    Ya existe información registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formCamaraComercio" novalidate autocomplete="off">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tiene_camara" class="form-label">
                            <i class="bi bi-building me-1"></i>¿Tiene Cámara de Comercio?
                        </label>
                        <select class="form-select" id="tiene_camara" name="tiene_camara" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Si" <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                            <option value="No" <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione si tiene cámara de comercio.</div>
                    </div>
                    <div class="col-md-4 mb-3 campos-adicionales" id="campo-nombre" style="display: none;">
                        <label for="nombre" class="form-label">
                            <i class="bi bi-building me-1"></i>Nombre de Empresa:
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['nombre']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor ingrese el nombre de la empresa.</div>
                    </div>
                    <div class="col-md-4 mb-3 campos-adicionales" id="campo-razon" style="display: none;">
                        <label for="razon" class="form-label">
                            <i class="bi bi-briefcase me-1"></i>Razón Social:
                        </label>
                        <input type="text" class="form-control" id="razon" name="razon" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['razon']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor ingrese la razón social.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 campos-adicionales" id="campo-actividad" style="display: none;">
                        <label for="actividad" class="form-label">
                            <i class="bi bi-gear me-1"></i>Actividad:
                        </label>
                        <input type="text" class="form-control" id="actividad" name="actividad" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['actividad']) : ''; ?>" 
                               maxlength="200">
                        <div class="invalid-feedback">Por favor ingrese la actividad.</div>
                    </div>
                    <div class="col-md-6 mb-3 campos-adicionales" id="campo-observacion" style="display: none;">
                        <label for="observacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Observaciones:
                        </label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="2" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                        <div class="form-text">Máximo 1000 caracteres</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../informacion_personal/informacion_personal.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tieneCamaraSelect = document.getElementById('tiene_camara');
                const camposAdicionales = document.querySelectorAll('.campos-adicionales');
                
                // Verificar si hay datos existentes
                const tieneDatosExistentes = <?php echo ($datos_existentes && $datos_existentes['tiene_camara'] == 'Si') ? 'true' : 'false'; ?>;
                
                // Función para mostrar/ocultar campos
                function toggleCampos() {
                    const valor = tieneCamaraSelect.value;
                    
                    if (valor === 'Si') {
                        camposAdicionales.forEach(campo => {
                            campo.style.display = 'block';
                        });
                    } else {
                        camposAdicionales.forEach(campo => {
                            campo.style.display = 'none';
                            // Limpiar valores cuando se ocultan
                            const input = campo.querySelector('input, textarea');
                            if (input) {
                                input.value = '';
                            }
                        });
                    }
                }
                
                // Ejecutar al cargar la página
                toggleCampos();
                
                // Si hay datos existentes y tiene_camara es 'Si', mostrar campos
                if (tieneDatosExistentes) {
                    camposAdicionales.forEach(campo => {
                        campo.style.display = 'block';
                    });
                }
                
                // Ejecutar cuando cambie la selección
                tieneCamaraSelect.addEventListener('change', toggleCampos);
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
<script src="../../../../../public/js/validacionCamaraComercio.js"></script>
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