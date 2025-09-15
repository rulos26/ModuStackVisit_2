<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

// Incluir el controlador desde la misma carpeta
require_once __DIR__ . '/ConceptoFinalEvaluadorController.php';

use App\Controllers\ConceptoFinalEvaluadorController;

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ConceptoFinalEvaluadorController::getInstance();

        // Sanitizar y validar datos de entrada
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        // Guardar los datos del formulario para mantenerlos en caso de error
        $datos_formulario = $datos;

        if (empty($errores)) {
            // Intentar guardar los datos
            $resultado = $controller->guardar($datos);

            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];

                // Siempre redirigir a la siguiente pantalla después de guardar/actualizar exitosamente
                header('Location: ../registro_fotos/registro_fotos.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Procesar errores para mostrarlos en campos específicos
            foreach ($errores as $error) {
                if (strpos($error, 'Actitud del evaluado') !== false) {
                    $errores_campos['actitud'] = $error;
                } elseif (strpos($error, 'Condiciones de Vivienda') !== false) {
                    $errores_campos['condiciones_vivienda'] = $error;
                } elseif (strpos($error, 'Dinámica Familiar') !== false) {
                    $errores_campos['dinamica_familiar'] = $error;
                } elseif (strpos($error, 'Condiciones Socio Económicas') !== false) {
                    $errores_campos['condiciones_economicas'] = $error;
                } elseif (strpos($error, 'Condiciones Académicas') !== false) {
                    $errores_campos['condiciones_academicas'] = $error;
                } elseif (strpos($error, 'Evaluación Experiencia Laboral') !== false) {
                    $errores_campos['evaluacion_experiencia_laboral'] = $error;
                } elseif (strpos($error, 'Observaciones') !== false) {
                    $errores_campos['observaciones'] = $error;
                } elseif (strpos($error, 'Concepto Final de la Visita') !== false) {
                    $errores_campos['id_concepto_final'] = $error;
                } elseif (strpos($error, 'Nombre del Evaluador') !== false) {
                    $errores_campos['nombre_evaluador'] = $error;
                } elseif (strpos($error, 'concepto de seguridad') !== false) {
                    $errores_campos['id_concepto_seguridad'] = $error;
                } else {
                    $_SESSION['error'] = $error;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en concepto_final_evaluador.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = ConceptoFinalEvaluadorController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $conceptos_finales = $controller->obtenerConceptosFinales();
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && !empty($datos_existentes)) {
        $datos_formulario = $datos_existentes;
    }
} catch (Exception $e) {
    error_log("Error en concepto_final_evaluador.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<!-- Puedes usar este código como base para tu formulario y menú responsive -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Concepto Final Evaluador - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        /* Ajuste para imagen de logo que no carga */
        .logo-empresa {
            max-width: 300px;
            min-width: 120px;
            height: auto;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
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
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>
                    VISITA DOMICILIARÍA - CONCEPTO FINAL DEL PROFESIONAL O EVALUADOR
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
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 14</div>
                    <div class="step-description">Aportantes</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-shield-check"></i></div>
                    <div class="step-title">Paso 15</div>
                    <div class="step-description">Data Crédito</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-cash-stack"></i></div>
                    <div class="step-title">Paso 16</div>
                    <div class="step-description">Ingresos Mensuales</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-cash-coin"></i></div>
                    <div class="step-title">Paso 17</div>
                    <div class="step-description">Gastos Mensuales</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-mortarboard"></i></div>
                    <div class="step-title">Paso 18</div>
                    <div class="step-description">Estudios</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-shield-exclamation"></i></div>
                    <div class="step-title">Paso 19</div>
                    <div class="step-description">Información Judicial</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="step-title">Paso 20</div>
                    <div class="step-description">Experiencia Laboral</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="step-title">Paso 21</div>
                    <div class="step-description">Concepto Final</div>
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
            
            <?php if (!empty($datos_existentes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Ya existe concepto final registrado para esta cédula. Puede actualizar los datos.
                </div>
            <?php endif; ?>
            
            
            <form action="" method="POST" id="formConcepto" novalidate autocomplete="off">
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="actitud" class="form-label">
                            <i class="bi bi-people me-1"></i>Actitud del evaluado y su grupo familiar:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['actitud']) ? 'is-invalid' : (!empty($datos_formulario['actitud']) ? 'is-valid' : ''); ?>" 
                                  id="actitud" name="actitud" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Colaborativa, receptiva"><?php echo !empty($datos_formulario['actitud']) ? htmlspecialchars($datos_formulario['actitud']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['actitud']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['actitud']) ? htmlspecialchars($errores_campos['actitud']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="condiciones_vivienda" class="form-label">
                            <i class="bi bi-house me-1"></i>Condiciones de Vivienda:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_vivienda']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_vivienda']) ? 'is-valid' : ''); ?>" 
                                  id="condiciones_vivienda" name="condiciones_vivienda" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Adecuadas, buenas condiciones"><?php echo !empty($datos_formulario['condiciones_vivienda']) ? htmlspecialchars($datos_formulario['condiciones_vivienda']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['condiciones_vivienda']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['condiciones_vivienda']) ? htmlspecialchars($errores_campos['condiciones_vivienda']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="dinamica_familiar" class="form-label">
                            <i class="bi bi-heart me-1"></i>Dinámica Familiar:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['dinamica_familiar']) ? 'is-invalid' : (!empty($datos_formulario['dinamica_familiar']) ? 'is-valid' : ''); ?>" 
                                  id="dinamica_familiar" name="dinamica_familiar" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Armónica, unida"><?php echo !empty($datos_formulario['dinamica_familiar']) ? htmlspecialchars($datos_formulario['dinamica_familiar']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['dinamica_familiar']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['dinamica_familiar']) ? htmlspecialchars($errores_campos['dinamica_familiar']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="condiciones_economicas" class="form-label">
                            <i class="bi bi-cash-stack me-1"></i>Condiciones Socio Económicas:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_economicas']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_economicas']) ? 'is-valid' : ''); ?>" 
                                  id="condiciones_economicas" name="condiciones_economicas" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Estables, suficientes"><?php echo !empty($datos_formulario['condiciones_economicas']) ? htmlspecialchars($datos_formulario['condiciones_economicas']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['condiciones_economicas']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['condiciones_economicas']) ? htmlspecialchars($errores_campos['condiciones_economicas']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="condiciones_academicas" class="form-label">
                            <i class="bi bi-mortarboard me-1"></i>Condiciones Académicas:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['condiciones_academicas']) ? 'is-invalid' : (!empty($datos_formulario['condiciones_academicas']) ? 'is-valid' : ''); ?>" 
                                  id="condiciones_academicas" name="condiciones_academicas" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Buenas, adecuadas"><?php echo !empty($datos_formulario['condiciones_academicas']) ? htmlspecialchars($datos_formulario['condiciones_academicas']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['condiciones_academicas']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['condiciones_academicas']) ? htmlspecialchars($errores_campos['condiciones_academicas']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="evaluacion_experiencia_laboral" class="form-label">
                            <i class="bi bi-briefcase me-1"></i>Evaluación Experiencia Laboral:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? 'is-invalid' : (!empty($datos_formulario['evaluacion_experiencia_laboral']) ? 'is-valid' : ''); ?>" 
                                  id="evaluacion_experiencia_laboral" name="evaluacion_experiencia_laboral" 
                                  rows="3" maxlength="500" minlength="10" required
                                  placeholder="Ej: Positiva, estable"><?php echo !empty($datos_formulario['evaluacion_experiencia_laboral']) ? htmlspecialchars($datos_formulario['evaluacion_experiencia_laboral']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['evaluacion_experiencia_laboral']) ? htmlspecialchars($errores_campos['evaluacion_experiencia_laboral']) : 'Mínimo 10 caracteres, máximo 500 caracteres'; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="observaciones" class="form-label">
                            <i class="bi bi-chat-quote me-1"></i>Observaciones:
                        </label>
                        <textarea class="form-control <?php echo !empty($errores_campos['observaciones']) ? 'is-invalid' : (!empty($datos_formulario['observaciones']) ? 'is-valid' : ''); ?>" 
                                  id="observaciones" name="observaciones" 
                                  rows="4" maxlength="1000" minlength="15" required
                                  placeholder="Ej: Observaciones generales de la visita"><?php echo !empty($datos_formulario['observaciones']) ? htmlspecialchars($datos_formulario['observaciones']) : ''; ?></textarea>
                        <div class="form-text <?php echo !empty($errores_campos['observaciones']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['observaciones']) ? htmlspecialchars($errores_campos['observaciones']) : 'Mínimo 15 caracteres, máximo 1000 caracteres'; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="id_concepto_final" class="form-label">
                            <i class="bi bi-check-circle me-1"></i>CONCEPTO FINAL DE LA VISITA:
                        </label>
                        <select class="form-select <?php echo !empty($errores_campos['id_concepto_final']) ? 'is-invalid' : (!empty($datos_formulario['id_concepto_final']) ? 'is-valid' : ''); ?>" 
                                id="id_concepto_final" name="id_concepto_final" required>
                            <option value="">Seleccione un concepto</option>
                            <?php foreach ($conceptos_finales as $concepto): ?>
                                <option value="<?php echo $concepto['id']; ?>" 
                                    <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_final'] == $concepto['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($concepto['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text <?php echo !empty($errores_campos['id_concepto_final']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['id_concepto_final']) ? htmlspecialchars($errores_campos['id_concepto_final']) : ''; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="nombre_evaluador" class="form-label">
                            <i class="bi bi-person-badge me-1"></i>Nombre del Evaluador:
                        </label>
                        <input type="text" class="form-control <?php echo !empty($errores_campos['nombre_evaluador']) ? 'is-invalid' : (!empty($datos_formulario['nombre_evaluador']) ? 'is-valid' : ''); ?>" 
                               id="nombre_evaluador" name="nombre_evaluador" 
                               value="<?php echo !empty($datos_formulario['nombre_evaluador']) ? htmlspecialchars($datos_formulario['nombre_evaluador']) : ''; ?>"
                               placeholder="Ej: Juan Pérez" minlength="5" required>
                        <div class="form-text <?php echo !empty($errores_campos['nombre_evaluador']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['nombre_evaluador']) ? htmlspecialchars($errores_campos['nombre_evaluador']) : 'Mínimo 5 caracteres'; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="id_concepto_seguridad" class="form-label">
                            <i class="bi bi-shield-check me-1"></i>CONCEPTO DE SEGURIDAD:
                        </label>
                        <select class="form-select <?php echo !empty($errores_campos['id_concepto_seguridad']) ? 'is-invalid' : (!empty($datos_formulario['id_concepto_seguridad']) ? 'is-valid' : ''); ?>" 
                                id="id_concepto_seguridad" name="id_concepto_seguridad" required>
                            <option value="">Seleccione un concepto</option>
                            <option value="1" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '1') ? 'selected' : ''; ?>>Aptos</option>
                            <option value="2" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '2') ? 'selected' : ''; ?>>No Apto</option>
                            <option value="3" <?php echo (!empty($datos_formulario) && $datos_formulario['id_concepto_seguridad'] == '3') ? 'selected' : ''; ?>>Apto con reserva</option>
                        </select>
                        <div class="form-text <?php echo !empty($errores_campos['id_concepto_seguridad']) ? 'error-message' : ''; ?>">
                            <?php echo !empty($errores_campos['id_concepto_seguridad']) ? htmlspecialchars($errores_campos['id_concepto_seguridad']) : ''; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_formulario) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../experiencia_laboral/experiencia_laboral.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
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
    <title>Concepto Final Evaluador - Dashboard</title>
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
        .logo-empresa {
            max-width: 200px;
            height: auto;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
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
                            <h1 class="h3 mb-0">Concepto Final Evaluador</h1>
                            <p class="text-muted mb-0">Formulario de concepto final para evaluación</p>
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