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

require_once __DIR__ . '/ComposicionFamiliarController.php';
use App\Controllers\ComposicionFamiliarController;

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ComposicionFamiliarController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        // Guardar los datos del formulario para mantenerlos en caso de error
        $datos_formulario = $datos;
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../informacion_pareja/tiene_pareja.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Procesar errores para mostrarlos en campos específicos
            foreach ($errores as $error) {
                if (preg_match('/miembro (\d+)/', $error, $matches)) {
                    $num_miembro = $matches[1] - 1; // Convertir a índice base 0
                    
                    if (strpos($error, 'nombre') !== false) {
                        $errores_campos['nombre'][$num_miembro] = $error;
                    } elseif (strpos($error, 'parentesco') !== false) {
                        $errores_campos['id_parentesco'][$num_miembro] = $error;
                    } elseif (strpos($error, 'edad') !== false) {
                        $errores_campos['edad'][$num_miembro] = $error;
                    } elseif (strpos($error, 'ocupación') !== false) {
                        $errores_campos['id_ocupacion'][$num_miembro] = $error;
                    } elseif (strpos($error, 'teléfono') !== false) {
                        $errores_campos['telefono'][$num_miembro] = $error;
                    } elseif (strpos($error, 'convive') !== false) {
                        $errores_campos['id_conviven'][$num_miembro] = $error;
                    }
                } else {
                    $_SESSION['error'] = $error;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en composición_familiar.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = ComposicionFamiliarController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && !empty($datos_existentes)) {
        // Convertir datos existentes al formato del formulario
        $datos_formulario = [
            'nombre' => array_column($datos_existentes, 'nombre'),
            'id_parentesco' => array_column($datos_existentes, 'id_parentesco'),
            'edad' => array_column($datos_existentes, 'edad'),
            'id_ocupacion' => array_column($datos_existentes, 'id_ocupacion'),
            'telefono' => array_column($datos_existentes, 'telefono'),
            'id_conviven' => array_column($datos_existentes, 'id_conviven'),
            'observacion' => array_column($datos_existentes, 'observacion')
        ];
    }
    
    // Obtener opciones para los select
    $parentescos = $controller->obtenerOpciones('parentesco');
    $ocupaciones = $controller->obtenerOpciones('ocupacion');
    $opciones_parametro = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en composición_familiar.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Configurar variables del wizard
$wizard_step = 5;
$wizard_title = 'COMPOSICIÓN FAMILIAR';
$wizard_subtitle = 'Información de los miembros de la familia del evaluado';
$wizard_icon = 'fas fa-users';
$wizard_form_id = 'formComposicionFamiliar';
$wizard_form_action = '';
$wizard_previous_url = '../salud/salud.php';
$wizard_next_url = '../informacion_pareja/tiene_pareja.php';
?>

<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Estilos específicos para composición familiar */
.miembro-familiar { 
    border: 2px solid var(--border-color); 
    border-radius: var(--border-radius); 
    padding: 25px; 
    margin-bottom: 25px; 
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: var(--transition);
    position: relative;
}

.miembro-familiar:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
}

.miembro-familiar .miembro-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 20px; 
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
}

.miembro-familiar .miembro-title { 
    font-weight: 600; 
    color: var(--primary-color); 
    margin: 0; 
    font-size: 1.1rem;
}

.btn-eliminar-miembro { 
    background: var(--danger-color); 
    border: none; 
    color: white; 
    padding: 8px 15px; 
    border-radius: 6px; 
    transition: var(--transition);
    font-size: 0.9rem;
}

.btn-eliminar-miembro:hover { 
    background: #c82333; 
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.btn-agregar-miembro {
    background: var(--success-color);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    transition: var(--transition);
    font-weight: 600;
    margin-bottom: 20px;
}

.btn-agregar-miembro:hover {
    background: #27ae60;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
}

/* Animación para agregar/eliminar miembros */
.miembro-familiar.removing {
    animation: slideOut 0.3s ease-out forwards;
}

@keyframes slideOut {
    to {
        opacity: 0;
        transform: translateX(-100%);
        max-height: 0;
        margin-bottom: 0;
        padding: 0;
    }
}

.miembro-familiar.adding {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="<?php echo $wizard_icon; ?> me-2"></i><?php echo $wizard_title; ?></h1>
            <p class="subtitle"><?php echo $wizard_subtitle; ?></p>
        </div>

        <!-- Barra de Progreso -->
        <div class="wizard-progress">
            <div class="wizard-steps">
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="wizard-step-title">Paso 1</div>
                    <div class="wizard-step-description">Datos Básicos</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="wizard-step-title">Paso 2</div>
                    <div class="wizard-step-description">Información Personal</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="wizard-step-title">Paso 3</div>
                    <div class="wizard-step-description">Cámara de Comercio</div>
                </div>
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="wizard-step-title">Paso 4</div>
                    <div class="wizard-step-description">Salud</div>
                </div>
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 5</div>
                    <div class="wizard-step-description">Composición Familiar</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="wizard-step-title">Paso 6</div>
                    <div class="wizard-step-description">Finalización</div>
                </div>
            </div>
        </div>

        <!-- Contenido del Wizard -->
        <div class="wizard-content">
            <div class="wizard-step-content active">
                <!-- Información del Evaluado -->
                <div class="wizard-evaluado-info">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="wizard-logo">
                        </div>
                        <div class="col-md-6 wizard-evaluado-details">
                            <div class="detail-item">
                                <span class="detail-label">Fecha:</span> <?php echo date('d/m/Y'); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Cédula:</span> <?php echo htmlspecialchars($id_cedula); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Usuario:</span> <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de sesión -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo $_SESSION['error']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="wizard-alert wizard-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Éxito:</strong><br>
                            <?php echo $_SESSION['success']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <!-- Botón para agregar miembro -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn-agregar-miembro" id="btnAgregarMiembro">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Miembro Familiar
                        </button>
                    </div>

                    <!-- Contenedor de miembros familiares -->
                    <div id="miembrosContainer">
                        <?php
                        // Si hay datos del formulario, mostrar los miembros
                        if (!empty($datos_formulario['nombre'])) {
                            $num_miembros = count($datos_formulario['nombre']);
                            for ($i = 0; $i < $num_miembros; $i++):
                        ?>
                        <div class="miembro-familiar" data-index="<?php echo $i; ?>">
                            <div class="miembro-header">
                                <h5 class="miembro-title">Miembro Familiar <?php echo $i + 1; ?></h5>
                                <button type="button" class="btn-eliminar-miembro" onclick="eliminarMiembro(<?php echo $i; ?>)">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>
                                        Nombre Completo:
                                    </label>
                                    <input type="text" class="form-control" name="nombre[]" 
                                           value="<?php echo htmlspecialchars($datos_formulario['nombre'][$i] ?? ''); ?>" 
                                           required maxlength="100">
                                    <?php if (isset($errores_campos['nombre'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['nombre'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-users"></i>
                                        Parentesco:
                                    </label>
                                    <select class="form-select" name="id_parentesco[]" required>
                                        <option value="">Seleccione parentesco</option>
                                        <?php foreach ($parentescos as $parentesco): ?>
                                            <option value="<?php echo $parentesco['id']; ?>" 
                                                <?php echo (isset($datos_formulario['id_parentesco'][$i]) && $datos_formulario['id_parentesco'][$i] == $parentesco['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores_campos['id_parentesco'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['id_parentesco'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-calendar"></i>
                                        Edad:
                                    </label>
                                    <input type="number" class="form-control" name="edad[]" 
                                           value="<?php echo htmlspecialchars($datos_formulario['edad'][$i] ?? ''); ?>" 
                                           required min="0" max="120">
                                    <?php if (isset($errores_campos['edad'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['edad'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-briefcase"></i>
                                        Ocupación:
                                    </label>
                                    <select class="form-select" name="id_ocupacion[]">
                                        <option value="">Seleccione ocupación</option>
                                        <?php foreach ($ocupaciones as $ocupacion): ?>
                                            <option value="<?php echo $ocupacion['id']; ?>" 
                                                <?php echo (isset($datos_formulario['id_ocupacion'][$i]) && $datos_formulario['id_ocupacion'][$i] == $ocupacion['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores_campos['id_ocupacion'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['id_ocupacion'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-phone"></i>
                                        Teléfono:
                                    </label>
                                    <input type="tel" class="form-control" name="telefono[]" 
                                           value="<?php echo htmlspecialchars($datos_formulario['telefono'][$i] ?? ''); ?>" 
                                           required pattern="[0-9]{7,10}">
                                    <?php if (isset($errores_campos['telefono'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['telefono'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-home"></i>
                                        ¿Convive con el evaluado?:
                                    </label>
                                    <select class="form-select" name="id_conviven[]" required>
                                        <option value="">Seleccione opción</option>
                                        <?php foreach ($opciones_parametro as $opcion): ?>
                                            <option value="<?php echo $opcion['id']; ?>" 
                                                <?php echo (isset($datos_formulario['id_conviven'][$i]) && $datos_formulario['id_conviven'][$i] == $opcion['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($opcion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores_campos['id_conviven'][$i])): ?>
                                        <div class="invalid-feedback"><?php echo $errores_campos['id_conviven'][$i]; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-comment"></i>
                                        Observaciones:
                                    </label>
                                    <textarea class="form-control" name="observacion[]" rows="2" maxlength="500"><?php echo htmlspecialchars($datos_formulario['observacion'][$i] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endfor;
                        } else {
                            // Mostrar al menos un miembro por defecto
                        ?>
                        <div class="miembro-familiar" data-index="0">
                            <div class="miembro-header">
                                <h5 class="miembro-title">Miembro Familiar 1</h5>
                                <button type="button" class="btn-eliminar-miembro" onclick="eliminarMiembro(0)" style="display: none;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>
                                        Nombre Completo:
                                    </label>
                                    <input type="text" class="form-control" name="nombre[]" required maxlength="100">
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-users"></i>
                                        Parentesco:
                                    </label>
                                    <select class="form-select" name="id_parentesco[]" required>
                                        <option value="">Seleccione parentesco</option>
                                        <?php foreach ($parentescos as $parentesco): ?>
                                            <option value="<?php echo $parentesco['id']; ?>">
                                                <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-calendar"></i>
                                        Edad:
                                    </label>
                                    <input type="number" class="form-control" name="edad[]" required min="0" max="120">
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-briefcase"></i>
                                        Ocupación:
                                    </label>
                                    <select class="form-select" name="id_ocupacion[]">
                                        <option value="">Seleccione ocupación</option>
                                        <?php foreach ($ocupaciones as $ocupacion): ?>
                                            <option value="<?php echo $ocupacion['id']; ?>">
                                                <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-phone"></i>
                                        Teléfono:
                                    </label>
                                    <input type="tel" class="form-control" name="telefono[]" required pattern="[0-9]{7,10}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-home"></i>
                                        ¿Convive con el evaluado?:
                                    </label>
                                    <select class="form-select" name="id_conviven[]" required>
                                        <option value="">Seleccione opción</option>
                                        <?php foreach ($opciones_parametro as $opcion): ?>
                                            <option value="<?php echo $opcion['id']; ?>">
                                                <?php echo htmlspecialchars($opcion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label class="form-label">
                                        <i class="fas fa-comment"></i>
                                        Observaciones:
                                    </label>
                                    <textarea class="form-control" name="observacion[]" rows="2" maxlength="500"></textarea>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <a href="<?php echo $wizard_previous_url; ?>" class="wizard-btn wizard-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Anterior
            </a>
            <div class="text-center">
                <small class="text-muted">Paso <?php echo $wizard_step; ?> de 22</small>
            </div>
            <button type="button" class="wizard-btn wizard-btn-primary wizard-btn-next" id="nextBtn" disabled>
                Siguiente
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<script src="../../../../../public/js/wizard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?php echo $wizard_form_id; ?>');
    const nextBtn = document.getElementById('nextBtn');
    const btnAgregarMiembro = document.getElementById('btnAgregarMiembro');
    const miembrosContainer = document.getElementById('miembrosContainer');
    let miembroIndex = <?php echo !empty($datos_formulario['nombre']) ? count($datos_formulario['nombre']) : 1; ?>;
    
    // Función para validar un miembro
    function validateMiembro(miembroElement) {
        const inputs = miembroElement.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });
        
        return isValid;
    }
    
    // Función para validar todo el formulario
    function validateForm() {
        const miembros = miembrosContainer.querySelectorAll('.miembro-familiar');
        let isValid = true;
        
        if (miembros.length === 0) {
            isValid = false;
        }
        
        miembros.forEach(miembro => {
            if (!validateMiembro(miembro)) {
                isValid = false;
            }
        });
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Función para agregar miembro
    function agregarMiembro() {
        const nuevoMiembro = document.createElement('div');
        nuevoMiembro.className = 'miembro-familiar adding';
        nuevoMiembro.setAttribute('data-index', miembroIndex);
        
        nuevoMiembro.innerHTML = `
            <div class="miembro-header">
                <h5 class="miembro-title">Miembro Familiar ${miembroIndex + 1}</h5>
                <button type="button" class="btn-eliminar-miembro" onclick="eliminarMiembro(${miembroIndex})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Nombre Completo:
                    </label>
                    <input type="text" class="form-control" name="nombre[]" required maxlength="100">
                </div>
                
                <div class="col-md-6 form-group">
                    <label class="form-label">
                        <i class="fas fa-users"></i>
                        Parentesco:
                    </label>
                    <select class="form-select" name="id_parentesco[]" required>
                        <option value="">Seleccione parentesco</option>
                        <?php foreach ($parentescos as $parentesco): ?>
                            <option value="<?php echo $parentesco['id']; ?>">
                                <?php echo htmlspecialchars($parentesco['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar"></i>
                        Edad:
                    </label>
                    <input type="number" class="form-control" name="edad[]" required min="0" max="120">
                </div>
                
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-briefcase"></i>
                        Ocupación:
                    </label>
                    <select class="form-select" name="id_ocupacion[]">
                        <option value="">Seleccione ocupación</option>
                        <?php foreach ($ocupaciones as $ocupacion): ?>
                            <option value="<?php echo $ocupacion['id']; ?>">
                                <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        Teléfono:
                    </label>
                    <input type="tel" class="form-control" name="telefono[]" required pattern="[0-9]{7,10}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">
                        <i class="fas fa-home"></i>
                        ¿Convive con el evaluado?:
                    </label>
                    <select class="form-select" name="id_conviven[]" required>
                        <option value="">Seleccione opción</option>
                        <?php foreach ($opciones_parametro as $opcion): ?>
                            <option value="<?php echo $opcion['id']; ?>">
                                <?php echo htmlspecialchars($opcion['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 form-group">
                    <label class="form-label">
                        <i class="fas fa-comment"></i>
                        Observaciones:
                    </label>
                    <textarea class="form-control" name="observacion[]" rows="2" maxlength="500"></textarea>
                </div>
            </div>
        `;
        
        miembrosContainer.appendChild(nuevoMiembro);
        miembroIndex++;
        
        // Actualizar números de miembros
        actualizarNumerosMiembros();
        validateForm();
    }
    
    // Función para eliminar miembro
    window.eliminarMiembro = function(index) {
        const miembro = document.querySelector(`[data-index="${index}"]`);
        if (miembro) {
            miembro.classList.add('removing');
            setTimeout(() => {
                miembro.remove();
                actualizarNumerosMiembros();
                validateForm();
            }, 300);
        }
    }
    
    // Función para actualizar números de miembros
    function actualizarNumerosMiembros() {
        const miembros = miembrosContainer.querySelectorAll('.miembro-familiar');
        const botonesEliminar = miembrosContainer.querySelectorAll('.btn-eliminar-miembro');
        
        miembros.forEach((miembro, index) => {
            const titulo = miembro.querySelector('.miembro-title');
            titulo.textContent = `Miembro Familiar ${index + 1}`;
            
            // Mostrar/ocultar botón eliminar según cantidad de miembros
            const botonEliminar = miembro.querySelector('.btn-eliminar-miembro');
            if (miembros.length > 1) {
                botonEliminar.style.display = 'block';
            } else {
                botonEliminar.style.display = 'none';
            }
        });
    }
    
    // Event listeners
    btnAgregarMiembro.addEventListener('click', agregarMiembro);
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) {
        if (e.target.matches('input, select, textarea')) {
            const miembro = e.target.closest('.miembro-familiar');
            if (miembro) {
                validateMiembro(miembro);
                validateForm();
            }
        }
    });
    
    // Navegación con el botón siguiente
    nextBtn.addEventListener('click', function() {
        if (validateForm()) {
            // Mostrar animación de carga
            nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            nextBtn.disabled = true;
            
            // Enviar formulario
            setTimeout(() => {
                form.submit();
            }, 500);
        } else {
            // Mostrar alerta
            if (window.wizard) {
                window.wizard.showAlert('Por favor complete todos los campos obligatorios antes de continuar.', 'warning');
            } else {
                alert('Por favor complete todos los campos obligatorios antes de continuar.');
            }
        }
    });
    
    // Validación inicial
    validateForm();
    actualizarNumerosMiembros();
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
