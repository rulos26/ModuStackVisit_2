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

require_once __DIR__ . '/InformacionParejaController.php';
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
                } elseif (strpos($error, 'dirección') !== false) {
                    $errores_campos['direccion_empresa'] = $error;
                } elseif (strpos($error, 'La empresa es obligatoria') !== false || strpos($error, 'La empresa no puede exceder') !== false) {
                    $errores_campos['empresa'] = $error;
                } elseif (strpos($error, 'antigüedad') !== false) {
                    $errores_campos['antiguedad'] = $error;
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
    
    // Debug: Mostrar datos existentes
    if ($datos_existentes !== false) {
        error_log('DEBUG tiene_pareja.php: Datos existentes encontrados: ' . print_r($datos_existentes, true));
    } else {
        error_log('DEBUG tiene_pareja.php: No se encontraron datos existentes para cédula: ' . $id_cedula);
    }
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && $datos_existentes !== false) {
        $datos_formulario = $datos_existentes;
        error_log('DEBUG tiene_pareja.php: Cargando datos existentes en formulario: ' . print_r($datos_formulario, true));
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
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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

.campos-pareja { display: none; }
.campos-pareja.show { display: block; }

/* Estilos para errores en campos */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid,
.form-select.is-valid {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #198754;
}

/* Estilo para mensajes de error en form-text */
.form-text.error-message {
    color: #dc3545;
    font-weight: 500;
}

.form-text.success-message {
    color: #198754;
    font-weight: 500;
}
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-heart me-2"></i>
                VISITA DOMICILIARÍA - INFORMACIÓN DE LA PAREJA (CÓNYUGE, COMPAÑERA SENTIMENTAL)
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-heart"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Información Pareja</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-home"></i></div>
                    <div class="step-title">Paso 7</div>
                    <div class="step-description">Tipo de Vivienda</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../composición_familiar/composición_familiar.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formTienePareja').submit();">
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
                    Ya existe información de pareja registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formTienePareja" novalidate autocomplete="off">
                <!-- Campo obligatorio -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tiene_pareja" class="form-label">
                            <i class="bi bi-heart me-1"></i>¿Está usted en relación sentimental actual? <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?php echo !empty($errores_campos['tiene_pareja']) ? 'is-invalid' : (!empty($datos_formulario['tiene_pareja']) ? 'is-valid' : ''); ?>" 
                                id="tiene_pareja" name="tiene_pareja" required onchange="toggleCamposPareja()">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_formulario['tiene_pareja']) && $datos_formulario['tiene_pareja'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?php echo !empty($errores_campos['tiene_pareja']) ? htmlspecialchars($errores_campos['tiene_pareja']) : 'Por favor seleccione si está en relación sentimental actual.'; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Campos de información de pareja (se muestran/ocultan dinámicamente) -->
                <div id="camposPareja" class="campos-pareja">
                    <hr class="my-4">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-person-heart me-2"></i>Información de la Pareja
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cedula" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Cédula:
                            </label>
                            <input type="number" class="form-control <?php echo !empty($errores_campos['cedula']) ? 'is-invalid' : (!empty($datos_formulario['cedula']) ? 'is-valid' : ''); ?>" 
                                   id="cedula" name="cedula" 
                                   value="<?php echo !empty($datos_formulario['cedula']) ? htmlspecialchars($datos_formulario['cedula']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['cedula']) ? htmlspecialchars($errores_campos['cedula']) : 'La cédula debe ser numérica.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="id_tipo_documentos" class="form-label">
                                <i class="bi bi-file-text me-1"></i>Tipo de Documento:
                            </label>
                            <select class="form-select <?php echo !empty($errores_campos['id_tipo_documentos']) ? 'is-invalid' : (!empty($datos_formulario['id_tipo_documentos']) ? 'is-valid' : ''); ?>" 
                                    id="id_tipo_documentos" name="id_tipo_documentos">
                                <option value="">Seleccione</option>
                                <?php foreach ($tipo_documentos as $tipo): ?>
                                    <option value="<?php echo $tipo['id']; ?>" 
                                        <?php echo (!empty($datos_formulario['id_tipo_documentos']) && $datos_formulario['id_tipo_documentos'] == $tipo['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['id_tipo_documentos']) ? htmlspecialchars($errores_campos['id_tipo_documentos']) : 'Debe seleccionar el tipo de documento.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cedula_expedida" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Cédula expedida:
                            </label>
                            <select class="form-select <?php echo !empty($errores_campos['cedula_expedida']) ? 'is-invalid' : (!empty($datos_formulario['cedula_expedida']) ? 'is-valid' : ''); ?>" 
                                    id="cedula_expedida" name="cedula_expedida">
                                <option value="">Seleccione</option>
                                <?php foreach ($municipios as $municipio): ?>
                                    <option value="<?php echo $municipio['id_municipio']; ?>" 
                                        <?php echo (!empty($datos_formulario['cedula_expedida']) && $datos_formulario['cedula_expedida'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($municipio['municipio']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['cedula_expedida']) ? htmlspecialchars($errores_campos['cedula_expedida']) : 'Debe seleccionar dónde fue expedida la cédula.'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombres" class="form-label">
                                <i class="bi bi-person me-1"></i>Nombres Completos:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['nombres']) ? 'is-invalid' : (!empty($datos_formulario['nombres']) ? 'is-valid' : ''); ?>" 
                                   id="nombres" name="nombres" 
                                   value="<?php echo !empty($datos_formulario['nombres']) ? htmlspecialchars($datos_formulario['nombres']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['nombres']) ? htmlspecialchars($errores_campos['nombres']) : 'Los nombres completos son obligatorios.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edad" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Edad:
                            </label>
                            <input type="number" class="form-control <?php echo !empty($errores_campos['edad']) ? 'is-invalid' : (!empty($datos_formulario['edad']) ? 'is-valid' : ''); ?>" 
                                   id="edad" name="edad" 
                                   value="<?php echo !empty($datos_formulario['edad']) ? htmlspecialchars($datos_formulario['edad']) : ''; ?>" 
                                   min="18" max="120">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['edad']) ? htmlspecialchars($errores_campos['edad']) : 'La edad debe estar entre 18 y 120 años.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="id_genero" class="form-label">
                                <i class="bi bi-gender-ambiguous me-1"></i>Género:
                            </label>
                            <select class="form-select <?php echo !empty($errores_campos['id_genero']) ? 'is-invalid' : (!empty($datos_formulario['id_genero']) ? 'is-valid' : ''); ?>" 
                                    id="id_genero" name="id_genero">
                                <option value="">Seleccione</option>
                                <?php foreach ($generos as $genero): ?>
                                    <option value="<?php echo $genero['id']; ?>" 
                                        <?php echo (!empty($datos_formulario['id_genero']) && $datos_formulario['id_genero'] == $genero['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genero['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['id_genero']) ? htmlspecialchars($errores_campos['id_genero']) : 'Debe seleccionar el género.'; ?>
                            </div>
                        </div>
                    </div>
                    
                        <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="id_nivel_academico" class="form-label">
                                <i class="bi bi-mortarboard me-1"></i>Nivel Académico:
                            </label>
                            <select class="form-select <?php echo !empty($errores_campos['id_nivel_academico']) ? 'is-invalid' : (!empty($datos_formulario['id_nivel_academico']) ? 'is-valid' : ''); ?>" 
                                    id="id_nivel_academico" name="id_nivel_academico">
                                <option value="">Seleccione</option>
                                <?php foreach ($niveles_academicos as $nivel): ?>
                                    <option value="<?php echo $nivel['id']; ?>" 
                                        <?php echo (!empty($datos_formulario['id_nivel_academico']) && $datos_formulario['id_nivel_academico'] == $nivel['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($nivel['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['id_nivel_academico']) ? htmlspecialchars($errores_campos['id_nivel_academico']) : 'Debe seleccionar el nivel académico.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="actividad" class="form-label">
                                <i class="bi bi-briefcase me-1"></i>Actividad:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['actividad']) ? 'is-invalid' : (!empty($datos_formulario['actividad']) ? 'is-valid' : ''); ?>" 
                                   id="actividad" name="actividad" 
                                   value="<?php echo !empty($datos_formulario['actividad']) ? htmlspecialchars($datos_formulario['actividad']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['actividad']) ? htmlspecialchars($errores_campos['actividad']) : 'La actividad es obligatoria.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="empresa" class="form-label">
                                <i class="bi bi-building me-1"></i>Empresa:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['empresa']) ? 'is-invalid' : (!empty($datos_formulario['empresa']) ? 'is-valid' : ''); ?>" 
                                   id="empresa" name="empresa" 
                                   value="<?php echo !empty($datos_formulario['empresa']) ? htmlspecialchars($datos_formulario['empresa']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['empresa']) ? htmlspecialchars($errores_campos['empresa']) : 'La empresa es obligatoria.'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="antiguedad" class="form-label">
                                <i class="bi bi-clock-history me-1"></i>Antigüedad:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['antiguedad']) ? 'is-invalid' : (!empty($datos_formulario['antiguedad']) ? 'is-valid' : ''); ?>" 
                                   id="antiguedad" name="antiguedad" 
                                   value="<?php echo !empty($datos_formulario['antiguedad']) ? htmlspecialchars($datos_formulario['antiguedad']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['antiguedad']) ? htmlspecialchars($errores_campos['antiguedad']) : 'La antigüedad es obligatoria.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="direccion_empresa" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Dirección Empresa:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['direccion_empresa']) ? 'is-invalid' : (!empty($datos_formulario['direccion_empresa']) ? 'is-valid' : ''); ?>" 
                                   id="direccion_empresa" name="direccion_empresa" 
                                   value="<?php echo !empty($datos_formulario['direccion_empresa']) ? htmlspecialchars($datos_formulario['direccion_empresa']) : ''; ?>">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['direccion_empresa']) ? htmlspecialchars($errores_campos['direccion_empresa']) : 'La dirección de la empresa es obligatoria.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="telefono_1" class="form-label">
                                <i class="bi bi-telephone me-1"></i>Teléfono 1:
                            </label>
                            <input type="tel" class="form-control <?php echo !empty($errores_campos['telefono_1']) ? 'is-invalid' : (!empty($datos_formulario['telefono_1']) ? 'is-valid' : ''); ?>" 
                                   id="telefono_1" name="telefono_1" 
                                   value="<?php echo !empty($datos_formulario['telefono_1']) ? htmlspecialchars($datos_formulario['telefono_1']) : ''; ?>" 
                                   pattern="[0-9]{7,10}">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['telefono_1']) ? htmlspecialchars($errores_campos['telefono_1']) : 'El teléfono 1 es obligatorio (7-10 dígitos).'; ?>
                            </div>
                        </div>
                            </div>
                            
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="telefono_2" class="form-label">
                                <i class="bi bi-telephone-plus me-1"></i>Teléfono 2:
                            </label>
                            <input type="tel" class="form-control <?php echo !empty($errores_campos['telefono_2']) ? 'is-invalid' : (!empty($datos_formulario['telefono_2']) ? 'is-valid' : ''); ?>" 
                                   id="telefono_2" name="telefono_2" 
                                   value="<?php echo !empty($datos_formulario['telefono_2']) ? htmlspecialchars($datos_formulario['telefono_2']) : ''; ?>" 
                                   pattern="[0-9]{7,10}">
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['telefono_2']) ? htmlspecialchars($errores_campos['telefono_2']) : 'El teléfono 2 debe tener entre 7 y 10 dígitos.'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="vive_candidato" class="form-label">
                                <i class="bi bi-house-heart me-1"></i>Vive con el candidato:
                            </label>
                            <select class="form-select <?php echo !empty($errores_campos['vive_candidato']) ? 'is-invalid' : (!empty($datos_formulario['vive_candidato']) ? 'is-valid' : ''); ?>" 
                                    id="vive_candidato" name="vive_candidato">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones_parametro as $opcion): ?>
                                    <option value="<?php echo $opcion['id']; ?>" 
                                        <?php echo (!empty($datos_formulario['vive_candidato']) && $datos_formulario['vive_candidato'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo !empty($errores_campos['vive_candidato']) ? htmlspecialchars($errores_campos['vive_candidato']) : 'Debe seleccionar si vive con el candidato.'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="observacion" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Observación:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" 
                                      rows="4" maxlength="1000"><?php echo !empty($datos_formulario['observacion']) ? htmlspecialchars($datos_formulario['observacion']) : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_formulario['tiene_pareja']) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../composición_familiar/composición_familiar.php" class="btn btn-secondary btn-lg">
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

    <script>
function toggleCamposPareja() {
    const tieneParejaSelect = document.getElementById('tiene_pareja');
    const camposParejaDiv = document.getElementById('camposPareja');
    const campos = camposParejaDiv.querySelectorAll('input, select, textarea');

    if (tieneParejaSelect.value === '2') { // '2' corresponde a "Sí"
        camposParejaDiv.classList.add('show');
    } else {
        camposParejaDiv.classList.remove('show');
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
    toggleCamposPareja();
});

// Validación del formulario (mejorada)
document.getElementById('formTienePareja').addEventListener('submit', function(event) {
    const tieneParejaSelect = document.getElementById('tiene_pareja');
    
    // Validar que se haya seleccionado una opción principal
    if (!tieneParejaSelect.value || tieneParejaSelect.value === '') {
        event.preventDefault();
        alert('Por favor, seleccione si está en una relación sentimental actual.');
        tieneParejaSelect.focus();
        return;
    }
    
    // Validar campos de la pareja solo si se seleccionó "Sí"
    if (tieneParejaSelect.value === '2') {
        const camposObligatorios = [
            'cedula', 'id_tipo_documentos', 'cedula_expedida', 'nombres', 'edad', 
            'id_genero', 'id_nivel_academico', 'actividad', 'empresa', 
            'antiguedad', 'direccion_empresa', 'telefono_1', 'vive_candidato'
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
$theme = 'evaluador'; // Set theme for evaluator

// Intentar múltiples rutas posibles para el dashboard
$dashboard_paths = [
    __DIR__ . '/../../../../../../layout/dashboard.php',
    dirname(__DIR__, 6) . '/layout/dashboard.php',
    dirname(__DIR__, 5) . '/layout/dashboard.php',
    dirname(__DIR__, 4) . '/layout/dashboard.php'
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
    echo '<strong>Error:</strong> No se pudo cargar el layout del dashboard. Rutas probadas:<br>';
    foreach ($dashboard_paths as $path) {
        echo '- ' . htmlspecialchars($path) . '<br>';
    }
    echo '</div>';
}
?> 