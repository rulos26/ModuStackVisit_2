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
require_once __DIR__ . '/InformacionPersonalController.php';
use App\Controllers\InformacionPersonalController;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InformacionPersonalController::getInstance();
        
        // Sanitizar y validar datos de entrada
        $datos = $controller->sanitizarDatos($_POST);
        
        // Validar datos
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            // Intentar guardar los datos
            $resultado = $controller->guardar($datos);
            
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                
                // Redirigir según la acción realizada
                if ($resultado['action'] === 'created') {
                    // Si es un nuevo registro, continuar al siguiente paso
                    header('Location: ../camara_comercio/camara_comercio.php');
                    exit();
                } else {
                    // Si es una actualización, mostrar mensaje de éxito
                    $_SESSION['success'] = $resultado['message'];
                }
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en informacion_personal.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = InformacionPersonalController::getInstance();
    
    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select boxes
    $opciones = [
        'tipo_documentos' => $controller->obtenerOpciones('tipo_documentos'),
        'municipios' => $controller->obtenerOpciones('municipios'),
        'rh' => $controller->obtenerOpciones('rh'),
        'estaturas' => $controller->obtenerOpciones('estaturas'),
        'pesos' => $controller->obtenerOpciones('pesos'),
        'estado_civil' => $controller->obtenerOpciones('estado_civil'),
        'estratos' => $controller->obtenerOpciones('estratos')
    ];
    
} catch (Exception $e) {
    error_log("Error en informacion_personal.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
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

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-person-fill me-2"></i>
                VISITA DOMICILIARÍA - INFORMACIÓN PERSONAL
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                    <div class="step-title">Paso 2</div>
                    <div class="step-description">Información Personal</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-building"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Cámara de Comercio</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Ubicación</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Finalización</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" disabled>
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
            
            <form action="" method="POST" id="formInformacionPersonal" novalidate autocomplete="off">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_cedula" class="form-label">
                            <i class="bi bi-card-text me-1"></i>Número de Documento:
                        </label>
                        <input type="number" class="form-control" id="id_cedula" name="id_cedula" 
                               value="<?php echo htmlspecialchars($id_cedula); ?>" readonly>
                        <div class="form-text">Documento de identidad</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="id_tipo_documentos" class="form-label">
                            <i class="bi bi-card-list me-1"></i>Tipo de Documento:
                        </label>
                        <select class="form-select" id="id_tipo_documentos" name="id_tipo_documentos" required>
                            <option value="">Seleccione tipo de documento</option>
                            <?php foreach ($opciones['tipo_documentos'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_tipo_documentos'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el tipo de documento.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="cedula_expedida" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Cédula expedida en:
                        </label>
                        <select class="form-select" id="cedula_expedida" name="cedula_expedida" required>
                            <option value="">Seleccione municipio</option>
                            <?php foreach ($opciones['municipios'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['cedula_expedida'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['municipio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el municipio de expedición.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nombres" class="form-label">
                            <i class="bi bi-person me-1"></i>Nombres:
                        </label>
                        <input type="text" class="form-control" id="nombres" name="nombres" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['nombres']) : ''; ?>" 
                               required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                        <div class="invalid-feedback">Por favor ingrese nombres válidos (solo letras).</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="apellidos" class="form-label">
                            <i class="bi bi-person me-1"></i>Apellidos:
                        </label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['apellidos']) : ''; ?>" 
                               required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                        <div class="invalid-feedback">Por favor ingrese apellidos válidos (solo letras).</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="edad" class="form-label">
                            <i class="bi bi-calendar me-1"></i>Edad:
                        </label>
                        <input type="number" class="form-control" id="edad" name="edad" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['edad']) : ''; ?>" 
                               required min="18" max="120">
                        <div class="invalid-feedback">La edad debe estar entre 18 y 120 años.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_expedicion" class="form-label">
                            <i class="bi bi-calendar-date me-1"></i>Fecha de Expedición:
                        </label>
                        <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['fecha_expedicion']) : ''; ?>" 
                               required max="<?php echo date('Y-m-d'); ?>">
                        <div class="invalid-feedback">Por favor ingrese una fecha válida.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="lugar_nacimiento" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Lugar de Nacimiento:
                        </label>
                        <select class="form-select" id="lugar_nacimiento" name="lugar_nacimiento" required>
                            <option value="">Seleccione municipio</option>
                            <?php foreach ($opciones['municipios'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['lugar_nacimiento'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['municipio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el lugar de nacimiento.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="celular_1" class="form-label">
                            <i class="bi bi-phone me-1"></i>Celular 1:
                        </label>
                        <input type="tel" class="form-control" id="celular_1" name="celular_1" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_1']) : ''; ?>" 
                               required pattern="[0-9]{10}" placeholder="3001234567">
                        <div class="invalid-feedback">Ingrese un número de celular válido (10 dígitos).</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="celular_2" class="form-label">
                            <i class="bi bi-phone me-1"></i>Celular 2:
                        </label>
                        <input type="tel" class="form-control" id="celular_2" name="celular_2" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_2']) : ''; ?>" 
                               pattern="[0-9]{10}" placeholder="3001234567">
                        <div class="invalid-feedback">Ingrese un número de celular válido (10 dígitos).</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="telefono" class="form-label">
                            <i class="bi bi-telephone me-1"></i>Teléfono:
                        </label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['telefono']) : ''; ?>" 
                               pattern="[0-9]{7}" placeholder="1234567">
                        <div class="invalid-feedback">Ingrese un número de teléfono válido (7 dígitos).</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="id_rh" class="form-label">
                            <i class="bi bi-droplet me-1"></i>Tipo de RH:
                        </label>
                        <select class="form-select" id="id_rh" name="id_rh" required>
                            <option value="">Seleccione tipo de sangre</option>
                            <?php foreach ($opciones['rh'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_rh'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el tipo de sangre.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_estatura" class="form-label">
                            <i class="bi bi-arrows-vertical me-1"></i>Estatura:
                        </label>
                        <select class="form-select" id="id_estatura" name="id_estatura" required>
                            <option value="">Seleccione estatura</option>
                            <?php foreach ($opciones['estaturas'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_estatura'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione la estatura.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="peso_kg" class="form-label">
                            <i class="bi bi-weight me-1"></i>Peso (kg):
                        </label>
                        <select class="form-select" id="peso_kg" name="peso_kg" required>
                            <option value="">Seleccione peso</option>
                            <?php foreach ($opciones['pesos'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['peso_kg'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el peso.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="id_estado_civil" class="form-label">
                            <i class="bi bi-heart me-1"></i>Estado Civil:
                        </label>
                        <select class="form-select" id="id_estado_civil" name="id_estado_civil" required>
                            <option value="">Seleccione estado civil</option>
                            <?php foreach ($opciones['estado_civil'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_estado_civil'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el estado civil.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="hacer_cuanto" class="form-label">
                            <i class="bi bi-clock me-1"></i>Hace cuánto tiempo:
                        </label>
                        <input type="number" class="form-control" id="hacer_cuanto" name="hacer_cuanto" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['hacer_cuanto']) : ''; ?>" 
                               min="0" max="50" placeholder="Años">
                        <div class="form-text">Años en el estado civil actual</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="numero_hijos" class="form-label">
                            <i class="bi bi-people me-1"></i>Número de Hijos:
                        </label>
                        <input type="number" class="form-control" id="numero_hijos" name="numero_hijos" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['numero_hijos']) : ''; ?>" 
                               min="0" max="20">
                        <div class="invalid-feedback">El número de hijos debe estar entre 0 y 20.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="direccion" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Dirección:
                        </label>
                        <input type="text" class="form-control" id="direccion" name="direccion" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['direccion']) : ''; ?>" 
                               required maxlength="200">
                        <div class="invalid-feedback">Por favor ingrese la dirección.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_ciudad" class="form-label">
                            <i class="bi bi-building me-1"></i>Ciudad:
                        </label>
                        <select class="form-select" id="id_ciudad" name="id_ciudad" required>
                            <option value="">Seleccione ciudad</option>
                            <?php foreach ($opciones['municipios'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_ciudad'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['municipio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione la ciudad.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="localidad" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Localidad:
                        </label>
                        <input type="text" class="form-control" id="localidad" name="localidad" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['localidad']) : ''; ?>" 
                               required maxlength="100">
                        <div class="invalid-feedback">Por favor ingrese la localidad.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="barrio" class="form-label">
                            <i class="bi bi-house me-1"></i>Barrio:
                        </label>
                        <input type="text" class="form-control" id="barrio" name="barrio" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['barrio']) : ''; ?>" 
                               required maxlength="100">
                        <div class="invalid-feedback">Por favor ingrese el barrio.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_estrato" class="form-label">
                            <i class="bi bi-layers me-1"></i>Estrato:
                        </label>
                        <select class="form-select" id="id_estrato" name="id_estrato" required>
                            <option value="">Seleccione estrato</option>
                            <?php foreach ($opciones['estratos'] as $opcion): ?>
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_estrato'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el estrato.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="correo" class="form-label">
                            <i class="bi bi-envelope me-1"></i>Correo Electrónico:
                        </label>
                        <input type="email" class="form-control" id="correo" name="correo" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['correo']) : ''; ?>" 
                               required maxlength="100">
                        <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="cargo" class="form-label">
                            <i class="bi bi-briefcase me-1"></i>Cargo:
                        </label>
                        <input type="text" class="form-control" id="cargo" name="cargo" 
                               value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['cargo']) : ''; ?>" 
                               maxlength="100">
                        <div class="form-text">Cargo o profesión actual</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="observacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Observaciones:
                        </label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="4" 
                                  maxlength="1000" placeholder="Ingrese observaciones adicionales..."><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                        <div class="form-text">Máximo 1000 caracteres</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../index.php" class="btn btn-secondary btn-lg">
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

<script src="../../../../../public/js/validacionInformacionPersonal.js"></script>
<script>
// Validación del formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formInformacionPersonal');
    const inputs = form.querySelectorAll('input, select, textarea');
    const nextBtn = document.getElementById('nextBtn');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    function validateField(field) {
        if (field.checkValidity()) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
        
        // Habilitar/deshabilitar botón siguiente
        checkFormValidity();
    }
    
    function checkFormValidity() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
            }
        });
        
        nextBtn.disabled = !isValid;
    }
    
    // Auto-completar fecha de expedición si está vacía
    const fechaExpedicion = document.getElementById('fecha_expedicion');
    if (!fechaExpedicion.value) {
        fechaExpedicion.value = new Date().toISOString().split('T')[0];
    }
    
    // Auto-calcular edad si se proporciona fecha de nacimiento
    const edadInput = document.getElementById('edad');
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    if (fechaNacimiento) {
        fechaNacimiento.addEventListener('change', function() {
            if (this.value) {
                const fechaNac = new Date(this.value);
                const hoy = new Date();
                const edad = hoy.getFullYear() - fechaNac.getFullYear();
                const mes = hoy.getMonth() - fechaNac.getMonth();
                if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
                    edad--;
                }
                edadInput.value = edad;
            }
        });
    }
    
    // Verificar validez inicial
    checkFormValidity();
});

// Confirmación antes de enviar
document.getElementById('formInformacionPersonal').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Por favor complete todos los campos obligatorios.');
        return false;
    }
    
    return confirm('¿Está seguro de que desea guardar la información?');
});

// Navegación con el botón siguiente
document.getElementById('nextBtn').addEventListener('click', function() {
    const form = document.getElementById('formInformacionPersonal');
    if (form.checkValidity()) {
        form.submit();
    } else {
        form.classList.add('was-validated');
        alert('Por favor complete todos los campos obligatorios antes de continuar.');
    }
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
    // Si no se encuentra el dashboard, mostrar el contenido directamente
    echo $contenido;
    echo '<div style="background: #f8d7da; color: #721c24; padding: 1rem; margin: 1rem; border: 1px solid #f5c6cb; border-radius: 0.25rem;">';
    echo '<strong>Advertencia:</strong> No se pudo cargar el layout del dashboard. Rutas probadas:<br>';
    foreach ($dashboard_paths as $path) {
        echo '- ' . htmlspecialchars($path) . '<br>';
    }
    echo '</div>';
}
?>