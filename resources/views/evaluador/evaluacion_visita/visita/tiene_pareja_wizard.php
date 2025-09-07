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

require_once __DIR__ . '/informacion_pareja/InformacionParejaController.php';
use App\Controllers\InformacionParejaController;

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InformacionParejaController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        // Guardar los datos del formulario para mantenerlos en caso de error
        $datos_formulario = $datos;
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../tipo_vivienda/tipo_vivienda.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Procesar errores para mostrarlos en campos específicos
            foreach ($errores as $error) {
                if (strpos($error, 'relación sentimental') !== false) {
                    $errores_campos['tiene_pareja'] = $error;
                } elseif (strpos($error, 'cédula') !== false) {
                    $errores_campos['cedula'] = $error;
                } elseif (strpos($error, 'tipo de documento') !== false) {
                    $errores_campos['id_tipo_documentos'] = $error;
                } elseif (strpos($error, 'expedida') !== false) {
                    $errores_campos['cedula_expedida'] = $error;
                } elseif (strpos($error, 'nombres') !== false) {
                    $errores_campos['nombres'] = $error;
                } elseif (strpos($error, 'edad') !== false) {
                    $errores_campos['edad'] = $error;
                } elseif (strpos($error, 'género') !== false) {
                    $errores_campos['id_genero'] = $error;
                } elseif (strpos($error, 'nivel académico') !== false) {
                    $errores_campos['id_nivel_academico'] = $error;
                } elseif (strpos($error, 'actividad') !== false) {
                    $errores_campos['actividad'] = $error;
                } elseif (strpos($error, 'empresa') !== false) {
                    $errores_campos['empresa'] = $error;
                } elseif (strpos($error, 'antigüedad') !== false) {
                    $errores_campos['antiguedad'] = $error;
                } elseif (strpos($error, 'dirección') !== false) {
                    $errores_campos['direccion_empresa'] = $error;
                } elseif (strpos($error, 'teléfono 1') !== false) {
                    $errores_campos['telefono_1'] = $error;
                } elseif (strpos($error, 'teléfono 2') !== false) {
                    $errores_campos['telefono_2'] = $error;
                } elseif (strpos($error, 'vive con el candidato') !== false) {
                    $errores_campos['vive_candidato'] = $error;
                } else {
                    $_SESSION['error'] = $error;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en tiene_pareja.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = InformacionParejaController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && !empty($datos_existentes)) {
        $datos_formulario = $datos_existentes;
    }
    
    // Obtener opciones para los select
    $opciones_parametro = $controller->obtenerOpciones('parametro');
    $tipo_documentos = $controller->obtenerOpciones('tipo_documentos');
    $municipios = $controller->obtenerOpciones('municipios');
    $generos = $controller->obtenerOpciones('genero');
    $niveles_academicos = $controller->obtenerOpciones('nivel_academico');
} catch (Exception $e) {
    error_log("Error en tiene_pareja.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}

// Configurar variables del wizard
$wizard_step = 6;
$wizard_title = 'INFORMACIÓN DE LA PAREJA';
$wizard_subtitle = 'Datos del cónyuge o compañero sentimental del evaluado';
$wizard_icon = 'fas fa-heart';
$wizard_form_id = 'formTienePareja';
$wizard_form_action = '';
$wizard_previous_url = '../composicion_familiar_wizard.php';
$wizard_next_url = '../tipo_vivienda/tipo_vivienda.php';
?>

<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Estilos específicos para información de pareja */
.campos-pareja { 
    display: none; 
    margin-top: 30px;
    padding: 25px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius);
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.campos-pareja.show { 
    display: block; 
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

.campos-pareja h6 {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border-color);
}

/* Estilos para campos condicionales */
.wizard-conditional-fields {
    transition: var(--transition);
}

.wizard-conditional-fields.hidden {
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

.wizard-conditional-fields.visible {
    opacity: 1;
    max-height: none;
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
                <div class="wizard-step completed">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 5</div>
                    <div class="wizard-step-description">Composición Familiar</div>
                </div>
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="wizard-step-title">Paso 6</div>
                    <div class="wizard-step-description">Información Pareja</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="wizard-step-title">Paso 7</div>
                    <div class="wizard-step-description">Tipo de Vivienda</div>
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
                
                <?php if ($datos_existentes): ?>
                    <div class="wizard-alert wizard-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Información:</strong><br>
                            Ya existe información de pareja registrada para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <!-- Campo principal -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-heart"></i>
                            ¿Está usted en relación sentimental actual? <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="tiene_pareja" name="tiene_pareja" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_formulario['tiene_pareja']) && $datos_formulario['tiene_pareja'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errores_campos['tiene_pareja'])): ?>
                            <div class="invalid-feedback"><?php echo $errores_campos['tiene_pareja']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Campos de información de pareja (condicionales) -->
                    <div id="camposPareja" class="wizard-conditional-fields" data-depends-on="tiene_pareja" data-depends-value="2">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user-heart me-2"></i>Información de la Pareja
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-id-card"></i>
                                    Cédula:
                                </label>
                                <input type="number" class="form-control" id="cedula" name="cedula" 
                                       value="<?php echo !empty($datos_formulario['cedula']) ? htmlspecialchars($datos_formulario['cedula']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['cedula'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['cedula']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-text"></i>
                                    Tipo de Documento:
                                </label>
                                <select class="form-select" id="id_tipo_documentos" name="id_tipo_documentos" data-required-when-visible="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($tipo_documentos as $tipo): ?>
                                        <option value="<?php echo $tipo['id']; ?>" 
                                            <?php echo (!empty($datos_formulario['id_tipo_documentos']) && $datos_formulario['id_tipo_documentos'] == $tipo['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errores_campos['id_tipo_documentos'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['id_tipo_documentos']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Cédula expedida:
                                </label>
                                <select class="form-select" id="cedula_expedida" name="cedula_expedida" data-required-when-visible="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($municipios as $municipio): ?>
                                        <option value="<?php echo $municipio['id_municipio']; ?>" 
                                            <?php echo (!empty($datos_formulario['cedula_expedida']) && $datos_formulario['cedula_expedida'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($municipio['municipio']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errores_campos['cedula_expedida'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['cedula_expedida']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nombres Completos:
                                </label>
                                <input type="text" class="form-control" id="nombres" name="nombres" 
                                       value="<?php echo !empty($datos_formulario['nombres']) ? htmlspecialchars($datos_formulario['nombres']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['nombres'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['nombres']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i>
                                    Edad:
                                </label>
                                <input type="number" class="form-control" id="edad" name="edad" 
                                       value="<?php echo !empty($datos_formulario['edad']) ? htmlspecialchars($datos_formulario['edad']) : ''; ?>" 
                                       min="18" max="120" data-required-when-visible="true">
                                <?php if (!empty($errores_campos['edad'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['edad']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars"></i>
                                    Género:
                                </label>
                                <select class="form-select" id="id_genero" name="id_genero" data-required-when-visible="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($generos as $genero): ?>
                                        <option value="<?php echo $genero['id']; ?>" 
                                            <?php echo (!empty($datos_formulario['id_genero']) && $datos_formulario['id_genero'] == $genero['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($genero['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errores_campos['id_genero'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['id_genero']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-graduation-cap"></i>
                                    Nivel Académico:
                                </label>
                                <select class="form-select" id="id_nivel_academico" name="id_nivel_academico" data-required-when-visible="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($niveles_academicos as $nivel): ?>
                                        <option value="<?php echo $nivel['id']; ?>" 
                                            <?php echo (!empty($datos_formulario['id_nivel_academico']) && $datos_formulario['id_nivel_academico'] == $nivel['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($nivel['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errores_campos['id_nivel_academico'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['id_nivel_academico']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-briefcase"></i>
                                    Actividad:
                                </label>
                                <input type="text" class="form-control" id="actividad" name="actividad" 
                                       value="<?php echo !empty($datos_formulario['actividad']) ? htmlspecialchars($datos_formulario['actividad']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['actividad'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['actividad']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-building"></i>
                                    Empresa:
                                </label>
                                <input type="text" class="form-control" id="empresa" name="empresa" 
                                       value="<?php echo !empty($datos_formulario['empresa']) ? htmlspecialchars($datos_formulario['empresa']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['empresa'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['empresa']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i>
                                    Antigüedad:
                                </label>
                                <input type="text" class="form-control" id="antiguedad" name="antiguedad" 
                                       value="<?php echo !empty($datos_formulario['antiguedad']) ? htmlspecialchars($datos_formulario['antiguedad']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['antiguedad'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['antiguedad']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Dirección Empresa:
                                </label>
                                <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa" 
                                       value="<?php echo !empty($datos_formulario['direccion_empresa']) ? htmlspecialchars($datos_formulario['direccion_empresa']) : ''; ?>"
                                       data-required-when-visible="true">
                                <?php if (!empty($errores_campos['direccion_empresa'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['direccion_empresa']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Teléfono 1:
                                </label>
                                <input type="tel" class="form-control" id="telefono_1" name="telefono_1" 
                                       value="<?php echo !empty($datos_formulario['telefono_1']) ? htmlspecialchars($datos_formulario['telefono_1']) : ''; ?>" 
                                       pattern="[0-9]{7,10}" data-required-when-visible="true">
                                <?php if (!empty($errores_campos['telefono_1'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['telefono_1']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone-alt"></i>
                                    Teléfono 2:
                                </label>
                                <input type="tel" class="form-control" id="telefono_2" name="telefono_2" 
                                       value="<?php echo !empty($datos_formulario['telefono_2']) ? htmlspecialchars($datos_formulario['telefono_2']) : ''; ?>" 
                                       pattern="[0-9]{7,10}">
                                <?php if (!empty($errores_campos['telefono_2'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['telefono_2']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label class="form-label">
                                    <i class="fas fa-home"></i>
                                    Vive con el candidato:
                                </label>
                                <select class="form-select" id="vive_candidato" name="vive_candidato" data-required-when-visible="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($opciones_parametro as $opcion): ?>
                                        <option value="<?php echo $opcion['id']; ?>" 
                                            <?php echo (!empty($datos_formulario['vive_candidato']) && $datos_formulario['vive_candidato'] == $opcion['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($opcion['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errores_campos['vive_candidato'])): ?>
                                    <div class="invalid-feedback"><?php echo $errores_campos['vive_candidato']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label class="form-label">
                                    <i class="fas fa-comment"></i>
                                    Observación:
                                </label>
                                <textarea class="form-control" id="observacion" name="observacion" 
                                          rows="4" maxlength="1000"><?php echo !empty($datos_formulario['observacion']) ? htmlspecialchars($datos_formulario['observacion']) : ''; ?></textarea>
                                <div class="form-text">Máximo 1000 caracteres</div>
                            </div>
                        </div>
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
    const tieneParejaSelect = document.getElementById('tiene_pareja');
    const camposPareja = document.getElementById('camposPareja');
    
    // Función para mostrar/ocultar campos de pareja
    function toggleCamposPareja() {
        if (tieneParejaSelect.value === '2') { // '2' corresponde a "Sí"
            camposPareja.classList.add('visible');
            camposPareja.classList.remove('hidden');
        } else {
            camposPareja.classList.add('hidden');
            camposPareja.classList.remove('visible');
            
            // Limpiar campos cuando se ocultan
            const campos = camposPareja.querySelectorAll('input, select, textarea');
            campos.forEach(campo => {
                if (campo.type === 'select-one') {
                    campo.selectedIndex = 0;
                } else {
                    campo.value = '';
                }
                campo.classList.remove('is-valid', 'is-invalid');
            });
        }
        validateForm();
    }
    
    // Función para validar el formulario
    function validateForm() {
        let isValid = true;
        
        // Validar campo principal
        if (!tieneParejaSelect.value) {
            tieneParejaSelect.classList.add('is-invalid');
            tieneParejaSelect.classList.remove('is-valid');
            isValid = false;
        } else {
            tieneParejaSelect.classList.remove('is-invalid');
            tieneParejaSelect.classList.add('is-valid');
        }
        
        // Validar campos de pareja solo si se seleccionó "Sí"
        if (tieneParejaSelect.value === '2') {
            const camposObligatorios = [
                'cedula', 'id_tipo_documentos', 'cedula_expedida', 'nombres', 'edad', 
                'id_genero', 'id_nivel_academico', 'actividad', 'empresa', 
                'antiguedad', 'direccion_empresa', 'telefono_1', 'vive_candidato'
            ];
            
            camposObligatorios.forEach(idCampo => {
                const elemento = document.getElementById(idCampo);
                if (!elemento.value || elemento.value.trim() === '') {
                    elemento.classList.add('is-invalid');
                    elemento.classList.remove('is-valid');
                    isValid = false;
                } else {
                    elemento.classList.remove('is-invalid');
                    elemento.classList.add('is-valid');
                }
            });
        }
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Event listeners
    tieneParejaSelect.addEventListener('change', toggleCamposPareja);
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) {
        if (e.target.matches('input, select, textarea')) {
            validateForm();
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
    toggleCamposPareja();
    validateForm();
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
