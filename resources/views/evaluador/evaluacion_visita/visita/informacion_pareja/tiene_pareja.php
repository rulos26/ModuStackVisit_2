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
    
    // Determinar si tiene pareja basado en los datos existentes
    $tiene_pareja_valor = '';
    if (!empty($datos_formulario)) {
        // Si hay datos de pareja (nombres, cédula, etc.), asumir que tiene pareja
        if (!empty($datos_formulario['nombres']) && !empty($datos_formulario['cedula']) && $datos_formulario['cedula'] != '00') {
            $tiene_pareja_valor = '2'; // Sí tiene pareja
        } elseif (isset($datos_formulario['observacion']) && strpos($datos_formulario['observacion'], 'no tener pareja') !== false) {
            $tiene_pareja_valor = '1'; // No tiene pareja
        }
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
<!-- Puedes usar este código como base para tu formulario y menú responsive -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Responsive y Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Menú horizontal en desktop */
        @media (min-width: 992px) {
            .navbar-desktop {
                display: flex !important;
            }
            .navbar-mobile {
                display: none !important;
            }
        }
        /* Menú hamburguesa en móvil/tablet */
        @media (max-width: 991.98px) {
            .navbar-desktop {
                display: none !important;
            }
            .navbar-mobile {
                display: block !important;
            }
        }
        /* Ajuste para observaciones */
        .obs-row {
            flex-wrap: wrap;
        }
        .obs-col {
            flex: 1 0 100%;
            max-width: 100%;
        }
        /* Forzar 4 columnas desde 1440px (ajustado para pantallas grandes) */
        @media (min-width: 1440px) {
            .form-responsive-row > [class*="col-"] {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
        /* Bootstrap row display flex fix para forzar columnas */
        .form-responsive-row {
            display: flex;
            flex-wrap: wrap;
        }
        /* Mejorar visual de la card */
        .card {
            box-shadow: 0 2px 16px 0 rgba(0,0,0,0.07);
        }
        /* Pasos */
        .steps-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            width: 100%;
            gap: 0.5rem;
        }
        .step-horizontal {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-horizontal:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateX(50%);
        }
        .step-horizontal .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-horizontal.active .step-icon {
            background: #4361ee;
            border-color: #4361ee;
            color: #fff;
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        .step-horizontal.complete .step-icon {
            background: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }
        .step-horizontal .step-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .step-horizontal .step-description {
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
        .step-horizontal.active .step-title,
        .step-horizontal.active .step-description {
            color: #4361ee;
        }
        .step-horizontal.complete .step-title,
        .step-horizontal.complete .step-description {
            color: #2ecc71;
        }
        .campos-pareja { 
            display: none; 
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .campos-pareja.show { 
            display: block; 
            opacity: 1;
            max-height: 2000px;
        }
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
        .form-text.error-message {
            color: #dc3545;
            font-weight: 500;
        }
        .form-text.success-message {
            color: #198754;
            font-weight: 500;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
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
                        <div class="step-icon"><i class="fas fa-id-card"></i></div>
                        <div class="step-title">Paso 1</div>
                        <div class="step-description">Información Personal</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-building"></i></div>
                        <div class="step-title">Paso 2</div>
                        <div class="step-description">Cámara de Comercio</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                        <div class="step-title">Paso 3</div>
                        <div class="step-description">Salud</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-users"></i></div>
                        <div class="step-title">Paso 4</div>
                        <div class="step-description">Composición Familiar</div>
                    </div>
                    <div class="step-horizontal active">
                        <div class="step-icon"><i class="fas fa-heart"></i></div>
                        <div class="step-title">Paso 5</div>
                        <div class="step-description">Información Pareja</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-camera"></i></div>
                        <div class="step-title">Paso 6</div>
                        <div class="step-description">Registro Fotográfico</div>
                    </div>
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
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Datos cargados:</strong> Se encontró información de pareja registrada para esta cédula. Los datos se han cargado automáticamente.
                    <br><small>Valor tiene_pareja: <?php echo $tiene_pareja_valor; ?> | Cédula: <?php echo !empty($datos_formulario['cedula']) ? $datos_formulario['cedula'] : 'vacía'; ?> | Nombres: <?php echo !empty($datos_formulario['nombres']) ? $datos_formulario['nombres'] : 'vacíos'; ?></small>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Sin datos:</strong> No se encontró información de pareja registrada para esta cédula. Complete el formulario para registrar la información.
                </div>
            <?php endif; ?>
            
                <div class="row mb-4">
                    <div class="col-12 text-end">
                        <div class="text-muted">
                            <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                            <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Nota informativa sobre campos obligatorios -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Información importante:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios y deben ser completados antes de continuar.
                </div>
            
            <form action="" method="POST" id="formTienePareja" novalidate autocomplete="off">
                <!-- Campo obligatorio -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tiene_pareja" class="form-label required-field">
                            <i class="bi bi-heart me-1"></i>¿Está usted en relación sentimental actual?
                        </label>
                        <select class="form-select <?php echo !empty($errores_campos['tiene_pareja']) ? 'is-invalid' : (!empty($tiene_pareja_valor) ? 'is-valid' : ''); ?>" 
                                id="tiene_pareja" name="tiene_pareja" required onchange="toggleCamposPareja()">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones_parametro as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo ($tiene_pareja_valor == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errores_campos['tiene_pareja'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errores_campos['tiene_pareja']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Campos de información de pareja (se muestran/ocultan dinámicamente) -->
                <div id="camposPareja" class="campos-pareja <?php echo ($tiene_pareja_valor == '2') ? 'show' : ''; ?>">
                    <hr class="my-4">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-person-heart me-2"></i>Información de la Pareja
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cedula" class="form-label required-field">
                                <i class="bi bi-card-text me-1"></i>Cédula:
                            </label>
                            <input type="number" class="form-control <?php echo !empty($errores_campos['cedula']) ? 'is-invalid' : (!empty($datos_formulario['cedula']) ? 'is-valid' : ''); ?>" 
                                   id="cedula" name="cedula" 
                                   value="<?php echo !empty($datos_formulario['cedula']) ? htmlspecialchars($datos_formulario['cedula']) : ''; ?>">
                            <?php if (!empty($errores_campos['cedula'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errores_campos['cedula']); ?>
                                </div>
                            <?php endif; ?>
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
                            <label for="nombres" class="form-label required-field">
                                <i class="bi bi-person me-1"></i>Nombres Completos:
                            </label>
                            <input type="text" class="form-control <?php echo !empty($errores_campos['nombres']) ? 'is-invalid' : (!empty($datos_formulario['nombres']) ? 'is-valid' : ''); ?>" 
                                   id="nombres" name="nombres" 
                                   value="<?php echo !empty($datos_formulario['nombres']) ? htmlspecialchars($datos_formulario['nombres']) : ''; ?>">
                            <?php if (!empty($errores_campos['nombres'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errores_campos['nombres']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edad" class="form-label required-field">
                                <i class="bi bi-calendar me-1"></i>Edad:
                            </label>
                            <input type="number" class="form-control <?php echo !empty($errores_campos['edad']) ? 'is-invalid' : (!empty($datos_formulario['edad']) ? 'is-valid' : ''); ?>" 
                                   id="edad" name="edad" 
                                   value="<?php echo !empty($datos_formulario['edad']) ? htmlspecialchars($datos_formulario['edad']) : ''; ?>" 
                                   min="18" max="120">
                            <?php if (!empty($errores_campos['edad'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errores_campos['edad']); ?>
                                </div>
                            <?php endif; ?>
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
                            <label for="telefono_1" class="form-label required-field">
                                <i class="bi bi-telephone me-1"></i>Teléfono 1:
                            </label>
                            <input type="tel" class="form-control <?php echo !empty($errores_campos['telefono_1']) ? 'is-invalid' : (!empty($datos_formulario['telefono_1']) ? 'is-valid' : ''); ?>" 
                                   id="telefono_1" name="telefono_1" 
                                   value="<?php echo !empty($datos_formulario['telefono_1']) ? htmlspecialchars($datos_formulario['telefono_1']) : ''; ?>" 
                                   pattern="[0-9]{7,10}">
                            <?php if (!empty($errores_campos['telefono_1'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errores_campos['telefono_1']); ?>
                                </div>
                            <?php endif; ?>
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
                            <label for="vive_candidato" class="form-label required-field">
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
                            <?php if (!empty($errores_campos['vive_candidato'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errores_campos['vive_candidato']); ?>
                                </div>
                            <?php endif; ?>
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
    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
    // Verificar si hay datos cargados y mostrar campos si es necesario
    const tieneParejaSelect = document.getElementById('tiene_pareja');
    if (tieneParejaSelect && tieneParejaSelect.value === '2') {
        // Si ya está seleccionado "Sí", mostrar los campos
        const camposParejaDiv = document.getElementById('camposPareja');
        if (camposParejaDiv) {
            camposParejaDiv.classList.add('show');
        }
    }
    
    // Ejecutar la función normal
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

// Verificar si la sesión ya está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../../../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../../../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Pareja - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .steps-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            width: 100%;
            gap: 0.5rem;
        }
        .step-horizontal {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-horizontal:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateX(50%);
        }
        .step-horizontal .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-horizontal.active .step-icon {
            background: #4361ee;
            border-color: #4361ee;
            color: #fff;
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        .step-horizontal.complete .step-icon {
            background: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }
        .step-horizontal .step-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .step-horizontal .step-description {
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
        .step-horizontal.active .step-title,
        .step-horizontal.active .step-description {
            color: #4361ee;
        }
        .step-horizontal.complete .step-title,
        .step-horizontal.complete .step-description {
            color: #2ecc71;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .invalid-feedback {
            font-size: 0.875rem;
        }
        .valid-feedback {
            font-size: 0.875rem;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        .campos-pareja { 
            display: none; 
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .campos-pareja.show { 
            display: block; 
            opacity: 1;
            max-height: 2000px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Información de Pareja</h1>
                            <p class="text-muted mb-0">Formulario de información de pareja o cónyuge</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 