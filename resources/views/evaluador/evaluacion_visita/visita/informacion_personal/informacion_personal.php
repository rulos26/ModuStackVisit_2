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
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);

        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../camara_comercio/camara_comercio.php');
                exit();
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
    $controller = InformacionPersonalController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body { font-family: 'Montserrat', Arial, sans-serif; background: #f4f6fb; }
.card-wizard {
    border: none;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(67,97,238,0.08);
    padding: 36px 36px 18px 36px;
    background: #fff;
}
.wizard-steps {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 24px;
    gap: 8px;
}
.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
}
.wizard-step .step-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e7f3fe;
    color: #4361ee;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    margin-bottom: 4px;
    border: 2px solid #e7f3fe;
    transition: background 0.2s, border 0.2s;
}
.wizard-step.active .step-icon {
    background: #4361ee;
    color: #fff;
    border: 2px solid #4361ee;
}
.wizard-step.complete .step-icon {
    background: #4bb543;
    color: #fff;
    border: 2px solid #4bb543;
}
.wizard-step .step-title {
    font-size: 0.95em;
    color: #888;
    text-align: center;
    font-weight: 500;
}
.wizard-step.active .step-title {
    color: #4361ee;
    font-weight: 600;
}
@media (max-width: 600px) {
    .card-wizard { padding: 16px 4px 10px 4px; }
    .wizard-step .step-title { font-size: 0.85em; }
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">
            <div class="card-wizard">
                <!-- Wizard Steps Header -->
                <div class="wizard-steps mb-4">
                    <div class="wizard-step active">
                        <span class="step-icon"><i class="bi bi-person"></i></span>
                        <span class="step-title">Información Personal</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-briefcase"></i></span>
                        <span class="step-title">Cámara de Comercio</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-heart-pulse"></i></span>
                        <span class="step-title">Salud</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-people"></i></span>
                        <span class="step-title">Composición Familiar</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-house"></i></span>
                        <span class="step-title">Tipo de Vivienda</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-check2-circle"></i></span>
                        <span class="step-title">Finalizar</span>
                    </div>
                </div>
                <h4 class="mb-3 text-center">Información Personal</h4>
                <!-- Mensajes de alerta -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['nombres'] ?? '') : ''; ?>" 
                                   required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese nombres válidos (solo letras).</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellidos" class="form-label">
                                <i class="bi bi-person me-1"></i>Apellidos:
                            </label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['apellidos'] ?? '') : ''; ?>" 
                                   required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese apellidos válidos (solo letras).</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edad" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Edad:
                            </label>
                            <input type="number" class="form-control" id="edad" name="edad" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['edad'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['fecha_expedicion'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_1'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_2'] ?? '') : ''; ?>" 
                                   pattern="[0-9]{10}" placeholder="3001234567">
                            <div class="invalid-feedback">Ingrese un número de celular válido (10 dígitos).</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone me-1"></i>Teléfono:
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['telefono'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['hacer_cuanto'] ?? '') : ''; ?>" 
                                   min="0" max="50" placeholder="Años">
                            <div class="form-text">Años en el estado civil actual</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_hijos" class="form-label">
                                <i class="bi bi-people me-1"></i>Número de Hijos:
                            </label>
                            <input type="number" class="form-control" id="numero_hijos" name="numero_hijos" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['numero_hijos'] ?? '') : ''; ?>" 
                                   min="0" max="20">
                            <div class="invalid-feedback">El número de hijos debe estar entre 0 y 20.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="direccion" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Dirección:
                            </label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['direccion'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['localidad'] ?? '') : ''; ?>" 
                                   required maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese la localidad.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="barrio" class="form-label">
                                <i class="bi bi-house me-1"></i>Barrio:
                            </label>
                            <input type="text" class="form-control" id="barrio" name="barrio" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['barrio'] ?? '') : ''; ?>" 
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
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['correo'] ?? '') : ''; ?>" 
                                   required maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cargo" class="form-label">
                                <i class="bi bi-briefcase me-1"></i>Cargo:
                            </label>
                            <input type="text" class="form-control" id="cargo" name="cargo" 
                                   value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['cargo'] ?? '') : ''; ?>" 
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
                                      maxlength="1000" placeholder="Ingrese observaciones adicionales..."><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion'] ?? '') : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres</div>
                        </div>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </a>
                        <button type="submit" class="btn btn-primary" id="nextBtn">
                            Guardar y continuar <i class="bi bi-arrow-right-circle ms-2"></i>
                        </button>
                    </div>
                </form>
                <div class="card-footer text-end mt-4" style="color:#b0b0b0;font-size:0.93em;">
                    © 2024 V0.01 - Sistema de Visitas Domiciliarias
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../../../../../public/js/validacionInformacionPersonal.js"></script>
<script>
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
</script>
<?php
$contenido = ob_get_clean();
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