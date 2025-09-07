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

require_once __DIR__ . '/estudios/EstudiosController.php';
use App\Controllers\EstudiosController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = EstudiosController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: informacion_judicial_wizard.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en estudios_wizard.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = EstudiosController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $municipios = $controller->obtenerMunicipios();
} catch (Exception $e) {
    error_log("Error en estudios_wizard.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Definir variables específicas del paso
$wizard_step = 19;
$wizard_title = 'Verificación Académica';
$wizard_subtitle = 'Ingrese la información académica y de estudios realizados';
$wizard_icon = 'fas fa-graduation-cap';
$wizard_form_id = 'formEstudios';
$wizard_form_action = '';
$wizard_previous_url = 'gasto_wizard.php';
$wizard_next_url = 'informacion_judicial_wizard.php';

// Incluir el template del wizard
include 'wizard-template.php';
?>

<!-- Contenido específico del formulario -->
<div id="estudios-container">
    <!-- Estudio inicial -->
    <div class="estudio-item" data-estudio="0">
        <h6><i class="fas fa-mortarboard me-2"></i>Estudio #1</h6>
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="centro_estudios_0" class="form-label">
                    <i class="bi bi-building me-1"></i>Centro de Estudios:
                </label>
                <input type="text" class="form-control" id="centro_estudios_0" name="centro_estudios[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['centro_estudios'] ?? '') : ''; ?>"
                       placeholder="Ej: Universidad Nacional" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_jornada_0" class="form-label">
                    <i class="bi bi-clock me-1"></i>Jornada:
                </label>
                <input type="text" class="form-control" id="id_jornada_0" name="id_jornada[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_jornada'] ?? '') : ''; ?>"
                       placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_ciudad_0" class="form-label">
                    <i class="bi bi-geo-alt me-1"></i>Ciudad:
                </label>
                <select class="form-select" id="id_ciudad_0" name="id_ciudad[]" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?php echo $municipio['id_municipio']; ?>" 
                            <?php echo (!empty($datos_existentes) && $datos_existentes[0]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($municipio['municipio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="anno_0" class="form-label">
                    <i class="bi bi-calendar me-1"></i>Año:
                </label>
                <input type="number" class="form-control" id="anno_0" name="anno[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['anno'] ?? '') : ''; ?>"
                       placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                <div class="form-text">Año de estudio</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="titulos_0" class="form-label">
                    <i class="bi bi-award me-1"></i>Títulos:
                </label>
                <input type="text" class="form-control" id="titulos_0" name="titulos[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['titulos'] ?? '') : ''; ?>"
                       placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_resultado_0" class="form-label">
                    <i class="bi bi-check-circle me-1"></i>Resultado:
                </label>
                <input type="text" class="form-control" id="id_resultado_0" name="id_resultado[]" 
                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_resultado'] ?? '') : ''; ?>"
                       placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
        </div>
    </div>
    
    <!-- Estudios adicionales si existen datos -->
    <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
        <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
            <div class="estudio-item" data-estudio="<?php echo $i; ?>">
                <button type="button" class="btn btn-danger btn-sm btn-remove-estudio" onclick="removeEstudio(this)">
                    <i class="fas fa-times"></i>
                </button>
                <h6><i class="fas fa-mortarboard me-2"></i>Estudio #<?php echo $i + 1; ?></h6>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="centro_estudios_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-building me-1"></i>Centro de Estudios:
                        </label>
                        <input type="text" class="form-control" id="centro_estudios_<?php echo $i; ?>" name="centro_estudios[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['centro_estudios']); ?>"
                               placeholder="Ej: Universidad Nacional" minlength="3" required>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="id_jornada_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-clock me-1"></i>Jornada:
                        </label>
                        <input type="text" class="form-control" id="id_jornada_<?php echo $i; ?>" name="id_jornada[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['id_jornada']); ?>"
                               placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="id_ciudad_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Ciudad:
                        </label>
                        <select class="form-select" id="id_ciudad_<?php echo $i; ?>" name="id_ciudad[]" required>
                            <option value="">Seleccione una ciudad</option>
                            <?php foreach ($municipios as $municipio): ?>
                                <option value="<?php echo $municipio['id_municipio']; ?>" 
                                    <?php echo ($datos_existentes[$i]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($municipio['municipio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="anno_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-calendar me-1"></i>Año:
                        </label>
                        <input type="number" class="form-control" id="anno_<?php echo $i; ?>" name="anno[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['anno']); ?>"
                               placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="titulos_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-award me-1"></i>Títulos:
                        </label>
                        <input type="text" class="form-control" id="titulos_<?php echo $i; ?>" name="titulos[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['titulos']); ?>"
                               placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="id_resultado_<?php echo $i; ?>" class="form-label">
                            <i class="bi bi-check-circle me-1"></i>Resultado:
                        </label>
                        <input type="text" class="form-control" id="id_resultado_<?php echo $i; ?>" name="id_resultado[]" 
                               value="<?php echo htmlspecialchars($datos_existentes[$i]['id_resultado']); ?>"
                               placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-12 text-center">
        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarEstudio">
            <i class="bi bi-plus-circle me-2"></i>Agregar Otro Estudio
        </button>
    </div>
</div>

<script>
let estudioCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;

document.getElementById('btnAgregarEstudio').addEventListener('click', function() {
    const container = document.getElementById('estudios-container');
    const nuevoEstudio = document.createElement('div');
    nuevoEstudio.className = 'estudio-item';
    nuevoEstudio.setAttribute('data-estudio', estudioCounter);
    
    nuevoEstudio.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm btn-remove-estudio" onclick="removeEstudio(this)">
            <i class="fas fa-times"></i>
        </button>
        <h6><i class="fas fa-mortarboard me-2"></i>Estudio #${estudioCounter + 1}</h6>
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="centro_estudios_${estudioCounter}" class="form-label">
                    <i class="bi bi-building me-1"></i>Centro de Estudios:
                </label>
                <input type="text" class="form-control" id="centro_estudios_${estudioCounter}" name="centro_estudios[]" 
                       placeholder="Ej: Universidad Nacional" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_jornada_${estudioCounter}" class="form-label">
                    <i class="bi bi-clock me-1"></i>Jornada:
                </label>
                <input type="text" class="form-control" id="id_jornada_${estudioCounter}" name="id_jornada[]" 
                       placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_ciudad_${estudioCounter}" class="form-label">
                    <i class="bi bi-geo-alt me-1"></i>Ciudad:
                </label>
                <select class="form-select" id="id_ciudad_${estudioCounter}" name="id_ciudad[]" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?php echo $municipio['id_municipio']; ?>">
                            <?php echo htmlspecialchars($municipio['municipio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="anno_${estudioCounter}" class="form-label">
                    <i class="bi bi-calendar me-1"></i>Año:
                </label>
                <input type="number" class="form-control" id="anno_${estudioCounter}" name="anno[]" 
                       placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                <div class="form-text">Año de estudio</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="titulos_${estudioCounter}" class="form-label">
                    <i class="bi bi-award me-1"></i>Títulos:
                </label>
                <input type="text" class="form-control" id="titulos_${estudioCounter}" name="titulos[]" 
                       placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_resultado_${estudioCounter}" class="form-label">
                    <i class="bi bi-check-circle me-1"></i>Resultado:
                </label>
                <input type="text" class="form-control" id="id_resultado_${estudioCounter}" name="id_resultado[]" 
                       placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
        </div>
    `;
    
    container.appendChild(nuevoEstudio);
    estudioCounter++;
});

function removeEstudio(button) {
    const estudioItem = button.closest('.estudio-item');
    estudioItem.remove();
    
    // Renumerar los estudios restantes
    const estudios = document.querySelectorAll('.estudio-item');
    estudios.forEach((estudio, index) => {
        const titulo = estudio.querySelector('h6');
        titulo.innerHTML = `<i class="fas fa-mortarboard me-2"></i>Estudio #${index + 1}`;
    });
}
</script>
