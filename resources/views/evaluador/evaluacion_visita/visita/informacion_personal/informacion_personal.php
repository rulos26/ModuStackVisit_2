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

// Incluir el controlador
require_once __DIR__ . '/../../../../../app/Controllers/InformacionPersonalController.php';
use App\Controllers\InformacionPersonalController;

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
<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-person-fill me-2"></i>
                VISITA DOMICILIARÍA - INFORMACIÓN PERSONAL
            </h5>
        </div>
        <div class="card-body">
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
            
            <form action="guardar.php" method="POST" id="formInformacionPersonal" novalidate>
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
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['cedula_expedida'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
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
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['lugar_nacimiento'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
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
                                <option value="<?php echo htmlspecialchars($opcion['id']); ?>" 
                                        <?php echo ($datos_existentes && $datos_existentes['id_ciudad'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
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
</script>

<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>