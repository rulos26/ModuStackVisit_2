<?php
// Redirigir al nuevo wizard
header('Location: ../composicion_familiar_wizard.php');
exit();

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
            <h1><i class="fas fa-users me-2"></i>COMPOSICIÓN FAMILIAR</h1>
            <p class="subtitle">Información de los miembros de la familia del evaluado</p>
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
                                        <label for="nombre_<?php echo $index; ?>" class="form-label">
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
                                        <label for="id_parentesco_<?php echo $index; ?>" class="form-label">
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
                                        <label for="edad_<?php echo $index; ?>" class="form-label">
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
                                        <label for="telefono_<?php echo $index; ?>" class="form-label">
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
                                        <label for="id_conviven_<?php echo $index; ?>" class="form-label">
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
                                    <label for="nombre_0" class="form-label">
                                        <i class="bi bi-person me-1"></i>Nombre:
                                    </label>
                                    <input type="text" class="form-control" id="nombre_0" name="nombre[]" required>
                                    <div class="invalid-feedback">El nombre es obligatorio.</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="id_parentesco_0" class="form-label">
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
                                    <label for="edad_0" class="form-label">
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
                                    <label for="telefono_0" class="form-label">
                                        <i class="bi bi-telephone me-1"></i>Teléfono:
                                    </label>
                                    <input type="text" class="form-control" id="telefono_0" name="telefono[]" 
                                           pattern="[0-9]{7,10}" required>
                                    <div class="invalid-feedback">El teléfono es obligatorio (7-10 dígitos).</div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="id_conviven_0" class="form-label">
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