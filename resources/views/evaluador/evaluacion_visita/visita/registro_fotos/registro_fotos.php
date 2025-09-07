<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay una sesión activa
$id_cedula = $_SESSION['id_cedula'] ?? $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;

if (!$id_cedula) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=registro_fotos&test=123');
    exit();
}

require_once __DIR__ . '/RegistroFotosController.php';
use App\Controllers\RegistroFotosController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = RegistroFotosController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en registro_fotos.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = RegistroFotosController::getInstance();
    $fotos_existentes = $controller->obtenerPorCedula($id_cedula);
    $tipos_fotos = $controller->obtenerTiposFotos();
    $todas_completas = $controller->todasLasFotosCompletas($id_cedula);
} catch (Exception $e) {
    error_log("Error en registro_fotos.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Determinar la URL base del proyecto dinámicamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
// Asumiendo que ModuStackVisit_2 es el directorio raíz del proyecto en el servidor web
$base_path = '/ModuStackVisit_2/'; 
$base_url = $protocol . $host . $base_path;

// Definir variables específicas del paso
$wizard_step = 23;
$wizard_title = 'Evidencia Fotográfica';
$wizard_subtitle = 'Registre las fotografías requeridas para la visita domiciliaria';
$wizard_icon = 'fas fa-camera';
$wizard_form_id = 'formFotos';
$wizard_form_action = '';
$wizard_previous_url = 'concepto_final_evaluador_wizard.php';
$wizard_next_url = '../ubicacion/ubicacion.php';

// Incluir el template del wizard
include '../wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<style>
.photo-card { border: 2px dashed #dee2e6; transition: all 0.3s; }
.photo-card:hover { border-color: #4361ee; }
.photo-card.complete { border-color: #2ecc71; background-color: #f8fff9; }
.photo-preview { max-width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
</style>

<!-- Grid de fotos -->
<div class="row">
    <?php foreach ($tipos_fotos as $tipo => $descripcion): ?>
        <?php 
        $foto_existente = null;
        foreach ($fotos_existentes as $foto) {
            if ($foto['tipo'] == $tipo) {
                $foto_existente = $foto;
                break;
            }
        }
        ?>
        <div class="col-md-4 mb-4">
            <div class="card photo-card <?php echo $foto_existente ? 'complete' : ''; ?>">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-camera me-2"></i><?php echo htmlspecialchars($descripcion); ?>
                    </h6>
                </div>
                <div class="card-body text-center">
                    <?php if ($foto_existente): ?>
                        <div class="mb-3">
                            <img src="<?php echo $base_url . $foto_existente['ruta'] . $foto_existente['nombre']; ?>" 
                                 alt="<?php echo htmlspecialchars($descripcion); ?>" 
                                 class="photo-preview">
                        </div>
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Foto registrada</strong>
                        </div>
                        <!-- Botón para cambiar foto -->
                        <button type="button" class="btn btn-warning btn-sm" 
                                onclick="mostrarFormularioCambio(<?php echo $tipo; ?>, '<?php echo htmlspecialchars($descripcion); ?>')">
                            <i class="fas fa-edit me-2"></i>Cambiar Foto
                        </button>
                        
                        <!-- Formulario oculto para cambiar foto -->
                        <div id="formulario-cambio-<?php echo $tipo; ?>" class="mt-3" style="display: none;">
                            <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                                <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
                                <div class="mb-3">
                                    <div class="upload-area" style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                                        <input type="file" name="foto" accept="image/*" class="form-control" required>
                                        <small class="text-muted">Formatos: JPG, PNG, GIF (Máx: 5MB)</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i>Subir
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" 
                                            onclick="ocultarFormularioCambio(<?php echo $tipo; ?>)">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Formulario para subir nueva foto -->
                        <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                            <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
                            <div class="mb-3">
                                <div class="upload-area" style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                                    <input type="file" name="foto" accept="image/*" class="form-control" required>
                                    <small class="text-muted">Formatos: JPG, PNG, GIF (Máx: 5MB)</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-upload me-1"></i>Subir Foto
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($todas_completas): ?>
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Todas las fotos han sido registradas!</strong> Puede continuar al siguiente paso.
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function mostrarFormularioCambio(tipo, descripcion) {
    const formulario = document.getElementById('formulario-cambio-' + tipo);
    if (formulario) {
        formulario.style.display = 'block';
        // Hacer scroll suave hacia el formulario
        formulario.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function ocultarFormularioCambio(tipo) {
    const formulario = document.getElementById('formulario-cambio-' + tipo);
    if (formulario) {
        formulario.style.display = 'none';
        // Limpiar el input file
        const inputFile = formulario.querySelector('input[type="file"]');
        if (inputFile) {
            inputFile.value = '';
        }
    }
}

// JavaScript para redirección después de 5 segundos cuando se genera el informe
document.addEventListener('DOMContentLoaded', function() {
    const btnGenerarInforme = document.getElementById('btnGenerarInforme');
    if (btnGenerarInforme) {
        btnGenerarInforme.addEventListener('click', function() {
            // Redirigir después de 5 segundos
            setTimeout(function() {
                window.location.href = "/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/index_evaluacion.php";
            }, 5000);
        });
    }
    
    // Validación de archivos antes de subir
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Validar tipo de archivo
                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Por favor, selecciona una imagen válida (JPG, PNG, GIF).');
                    this.value = '';
                    return;
                }
                
                // Validar tamaño (5MB máximo)
                const tamanoMaximo = 5 * 1024 * 1024; // 5MB
                if (file.size > tamanoMaximo) {
                    alert('La imagen no puede superar los 5MB.');
                    this.value = '';
                    return;
                }
            }
        });
    });
});
</script>