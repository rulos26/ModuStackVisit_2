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
        .miembro-familiar { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            background: #f8f9fa; 
        }
        .miembro-familiar .miembro-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 15px; 
        }
        .miembro-familiar .miembro-title { 
            font-weight: bold; 
            color: #495057; 
            margin: 0; 
        }
        .btn-eliminar-miembro { 
            background: #dc3545; 
            border: none; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px; 
        }
        .btn-eliminar-miembro:hover { 
            background: #c82333; 
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
                    <i class="bi bi-people me-2"></i>
                    VISITA DOMICILIARÍA - COMPOSICIÓN FAMILIAR
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
                    <div class="step-horizontal active">
                        <div class="step-icon"><i class="fas fa-people"></i></div>
                        <div class="step-title">Paso 4</div>
                        <div class="step-description">Composición Familiar</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-camera"></i></div>
                        <div class="step-title">Paso 5</div>
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
            
            <?php if (!empty($datos_existentes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Ya existe información de composición familiar registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formComposicionFamiliar" novalidate autocomplete="off">
                <div id="miembros-container">
                    <?php if (!empty($datos_formulario['nombre'])): ?>
                        <?php foreach ($datos_formulario['nombre'] as $index => $nombre): ?>
                            <div class="miembro-familiar" data-index="<?php echo $index; ?>">
                                <div class="miembro-header">
                                    <h6 class="miembro-title">Miembro Familiar <?php echo $index + 1; ?></h6>
                                    <?php if ($index > 0): ?>
                                        <button type="button" class="btn-eliminar-miembro" onclick="eliminarMiembro(this)">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="nombre_<?php echo $index; ?>" class="form-label required-field">
                                            <i class="bi bi-person me-1"></i>Nombre:
                                        </label>
                                        <input type="text" class="form-control <?php echo !empty($errores_campos['nombre'][$index]) ? 'is-invalid' : (!empty($nombre) ? 'is-valid' : ''); ?>" 
                                               id="nombre_<?php echo $index; ?>" name="nombre[]" 
                                               value="<?php echo htmlspecialchars($nombre); ?>" required>
                                        <div class="invalid-feedback">
                                            <?php echo !empty($errores_campos['nombre'][$index]) ? htmlspecialchars($errores_campos['nombre'][$index]) : 'El nombre es obligatorio.'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_parentesco_<?php echo $index; ?>" class="form-label required-field">
                                            <i class="bi bi-diagram-3 me-1"></i>Parentesco:
                                        </label>
                                        <select class="form-select <?php echo !empty($errores_campos['id_parentesco'][$index]) ? 'is-invalid' : (!empty($datos_formulario['id_parentesco'][$index]) ? 'is-valid' : ''); ?>" 
                                                id="id_parentesco_<?php echo $index; ?>" name="id_parentesco[]" required>
                                            <option value="">Seleccione</option>
                                            <?php foreach ($parentescos as $parentesco): ?>
                                                <option value="<?php echo $parentesco['id']; ?>" 
                                                    <?php echo ($datos_formulario['id_parentesco'][$index] == $parentesco['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?php echo !empty($errores_campos['id_parentesco'][$index]) ? htmlspecialchars($errores_campos['id_parentesco'][$index]) : 'Debe seleccionar el parentesco.'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="edad_<?php echo $index; ?>" class="form-label required-field">
                                            <i class="bi bi-calendar me-1"></i>Edad:
                                        </label>
                                        <input type="number" class="form-control <?php echo !empty($errores_campos['edad'][$index]) ? 'is-invalid' : (!empty($datos_formulario['edad'][$index]) ? 'is-valid' : ''); ?>" 
                                               id="edad_<?php echo $index; ?>" name="edad[]" 
                                               value="<?php echo htmlspecialchars($datos_formulario['edad'][$index]); ?>" min="0" max="120" required>
                                        <div class="invalid-feedback">
                                            <?php echo !empty($errores_campos['edad'][$index]) ? htmlspecialchars($errores_campos['edad'][$index]) : 'La edad es obligatoria (0-120).'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_ocupacion_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-briefcase me-1"></i>Ocupación:
                                        </label>
                                        <select class="form-select <?php echo !empty($errores_campos['id_ocupacion'][$index]) ? 'is-invalid' : (!empty($datos_formulario['id_ocupacion'][$index]) ? 'is-valid' : ''); ?>" 
                                                id="id_ocupacion_<?php echo $index; ?>" name="id_ocupacion[]">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($ocupaciones as $ocupacion): ?>
                                                <option value="<?php echo $ocupacion['id']; ?>" 
                                                    <?php echo ($datos_formulario['id_ocupacion'][$index] == $ocupacion['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (!empty($errores_campos['id_ocupacion'][$index])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errores_campos['id_ocupacion'][$index]); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="telefono_<?php echo $index; ?>" class="form-label required-field">
                                            <i class="bi bi-telephone me-1"></i>Teléfono:
                                        </label>
                                        <input type="text" class="form-control <?php echo !empty($errores_campos['telefono'][$index]) ? 'is-invalid' : (!empty($datos_formulario['telefono'][$index]) ? 'is-valid' : ''); ?>" 
                                               id="telefono_<?php echo $index; ?>" name="telefono[]" 
                                               value="<?php echo htmlspecialchars($datos_formulario['telefono'][$index]); ?>" 
                                               pattern="[0-9]{7,10}" required>
                                        <div class="invalid-feedback">
                                            <?php echo !empty($errores_campos['telefono'][$index]) ? htmlspecialchars($errores_campos['telefono'][$index]) : 'El teléfono es obligatorio (7-10 dígitos).'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_conviven_<?php echo $index; ?>" class="form-label required-field">
                                            <i class="bi bi-house me-1"></i>Conviven:
                                        </label>
                                        <select class="form-select <?php echo !empty($errores_campos['id_conviven'][$index]) ? 'is-invalid' : (!empty($datos_formulario['id_conviven'][$index]) ? 'is-valid' : ''); ?>" 
                                                id="id_conviven_<?php echo $index; ?>" name="id_conviven[]" required>
                                            <option value="">Seleccione</option>
                                            <?php foreach ($opciones_parametro as $opcion): ?>
                                                <option value="<?php echo $opcion['id']; ?>" 
                                                    <?php echo ($datos_formulario['id_conviven'][$index] == $opcion['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?php echo !empty($errores_campos['id_conviven'][$index]) ? htmlspecialchars($errores_campos['id_conviven'][$index]) : 'Debe seleccionar si convive.'; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="observacion_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-chat-text me-1"></i>Observación:
                                        </label>
                                        <textarea class="form-control" id="observacion_<?php echo $index; ?>" name="observacion[]" 
                                                  rows="3" maxlength="500"><?php echo htmlspecialchars($datos_formulario['observacion'][$index] ?? ''); ?></textarea>
                                        <div class="form-text">Máximo 500 caracteres</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Miembro inicial -->
                        <div class="miembro-familiar" data-index="0">
                            <div class="miembro-header">
                                <h6 class="miembro-title">Miembro Familiar 1</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="nombre_0" class="form-label required-field">
                                        <i class="bi bi-person me-1"></i>Nombre:
                                    </label>
                                    <input type="text" class="form-control" id="nombre_0" name="nombre[]" required>
                                    <div class="invalid-feedback">El nombre es obligatorio.</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="id_parentesco_0" class="form-label required-field">
                                        <i class="bi bi-diagram-3 me-1"></i>Parentesco:
                                    </label>
                                    <select class="form-select" id="id_parentesco_0" name="id_parentesco[]" required>
                                        <option value="">Seleccione</option>
                                        <?php foreach ($parentescos as $parentesco): ?>
                                            <option value="<?php echo $parentesco['id']; ?>">
                                                <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Debe seleccionar el parentesco.</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="edad_0" class="form-label required-field">
                                        <i class="bi bi-calendar me-1"></i>Edad:
                                    </label>
                                    <input type="number" class="form-control" id="edad_0" name="edad[]" min="0" max="120" required>
                                    <div class="invalid-feedback">La edad es obligatoria (0-120).</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="id_ocupacion_0" class="form-label">
                                        <i class="bi bi-briefcase me-1"></i>Ocupación:
                                    </label>
                                    <select class="form-select" id="id_ocupacion_0" name="id_ocupacion[]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($ocupaciones as $ocupacion): ?>
                                            <option value="<?php echo $ocupacion['id']; ?>">
                                                <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="telefono_0" class="form-label required-field">
                                        <i class="bi bi-telephone me-1"></i>Teléfono:
                                    </label>
                                    <input type="text" class="form-control" id="telefono_0" name="telefono[]" 
                                           pattern="[0-9]{7,10}" required>
                                    <div class="invalid-feedback">El teléfono es obligatorio (7-10 dígitos).</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="id_conviven_0" class="form-label required-field">
                                        <i class="bi bi-house me-1"></i>Conviven:
                                    </label>
                                    <select class="form-select" id="id_conviven_0" name="id_conviven[]" required>
                                        <option value="">Seleccione</option>
                                        <?php foreach ($opciones_parametro as $opcion): ?>
                                            <option value="<?php echo $opcion['id']; ?>">
                                                <?php echo htmlspecialchars($opcion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Debe seleccionar si convive.</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="observacion_0" class="form-label">
                                        <i class="bi bi-chat-text me-1"></i>Observación:
                                    </label>
                                    <textarea class="form-control" id="observacion_0" name="observacion[]" 
                                              rows="3" maxlength="500"></textarea>
                                    <div class="form-text">Máximo 500 caracteres</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarMiembro">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Miembro
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_formulario['nombre']) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../salud/salud.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                let miembroIndex = <?php echo !empty($datos_formulario['nombre']) ? count($datos_formulario['nombre']) : 1; ?>;
                
                document.getElementById('btnAgregarMiembro').addEventListener('click', function() {
                    agregarMiembro();
                });
                
                function agregarMiembro() {
                    const container = document.getElementById('miembros-container');
                    const nuevoMiembro = document.createElement('div');
                    nuevoMiembro.className = 'miembro-familiar';
                    nuevoMiembro.setAttribute('data-index', miembroIndex);
                    
                    nuevoMiembro.innerHTML = `
                        <div class="miembro-header">
                            <h6 class="miembro-title">Miembro Familiar ${miembroIndex + 1}</h6>
                            <button type="button" class="btn-eliminar-miembro" onclick="eliminarMiembro(this)">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="nombre_${miembroIndex}" class="form-label">
                                    <i class="bi bi-person me-1"></i>Nombre:
                                </label>
                                <input type="text" class="form-control" id="nombre_${miembroIndex}" name="nombre[]" required>
                                <div class="invalid-feedback">El nombre es obligatorio.</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_parentesco_${miembroIndex}" class="form-label">
                                    <i class="bi bi-diagram-3 me-1"></i>Parentesco:
                                </label>
                                <select class="form-select" id="id_parentesco_${miembroIndex}" name="id_parentesco[]" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($parentescos as $parentesco): ?>
                                        <option value="<?php echo $parentesco['id']; ?>">
                                            <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Debe seleccionar el parentesco.</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="edad_${miembroIndex}" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Edad:
                                </label>
                                <input type="number" class="form-control" id="edad_${miembroIndex}" name="edad[]" min="0" max="120" required>
                                <div class="invalid-feedback">La edad es obligatoria (0-120).</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_ocupacion_${miembroIndex}" class="form-label">
                                    <i class="bi bi-briefcase me-1"></i>Ocupación:
                                </label>
                                <select class="form-select" id="id_ocupacion_${miembroIndex}" name="id_ocupacion[]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($ocupaciones as $ocupacion): ?>
                                        <option value="<?php echo $ocupacion['id']; ?>">
                                            <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="telefono_${miembroIndex}" class="form-label">
                                    <i class="bi bi-telephone me-1"></i>Teléfono:
                                </label>
                                <input type="text" class="form-control" id="telefono_${miembroIndex}" name="telefono[]" 
                                       pattern="[0-9]{7,10}" required>
                                <div class="invalid-feedback">El teléfono es obligatorio (7-10 dígitos).</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_conviven_${miembroIndex}" class="form-label">
                                    <i class="bi bi-house me-1"></i>Conviven:
                                </label>
                                <select class="form-select" id="id_conviven_${miembroIndex}" name="id_conviven[]" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($opciones_parametro as $opcion): ?>
                                        <option value="<?php echo $opcion['id']; ?>">
                                            <?php echo htmlspecialchars($opcion['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Debe seleccionar si convive.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="observacion_${miembroIndex}" class="form-label">
                                    <i class="bi bi-chat-text me-1"></i>Observación:
                                </label>
                                <textarea class="form-control" id="observacion_${miembroIndex}" name="observacion[]" 
                                          rows="3" maxlength="500"></textarea>
                                <div class="form-text">Máximo 500 caracteres</div>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(nuevoMiembro);
                    miembroIndex++;
                    actualizarNumeracion();
                }
                
                function eliminarMiembro(button) {
                    const miembro = button.closest('.miembro-familiar');
                    miembro.remove();
                    actualizarNumeracion();
                }
                
                function actualizarNumeracion() {
                    const miembros = document.querySelectorAll('.miembro-familiar');
                    miembros.forEach((miembro, index) => {
                        const title = miembro.querySelector('.miembro-title');
                        title.textContent = `Miembro Familiar ${index + 1}`;
                        miembro.setAttribute('data-index', index);
                    });
                }
                
                // Función global para eliminar miembros
                window.eliminarMiembro = eliminarMiembro;
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
    <title>Composición Familiar - Dashboard Evaluador</title>
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
        .miembro-familiar { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            background: #f8f9fa; 
        }
        .miembro-familiar .miembro-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 15px; 
        }
        .miembro-familiar .miembro-title { 
            font-weight: bold; 
            color: #495057; 
            margin: 0; 
        }
        .btn-eliminar-miembro { 
            background: #dc3545; 
            border: none; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px; 
        }
        .btn-eliminar-miembro:hover { 
            background: #c82333; 
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
                            <h1 class="h3 mb-0">Composición Familiar</h1>
                            <p class="text-muted mb-0">Formulario de información familiar</p>
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