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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ComposicionFamiliarController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
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
            $_SESSION['error'] = implode('<br>', $errores);
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
    
    // Obtener opciones para los select
    $parentescos = $controller->obtenerOpciones('parentesco');
    $ocupaciones = $controller->obtenerOpciones('ocupacion');
    $opciones_parametro = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en composición_familiar.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
.miembro-familiar { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f8f9fa; }
.miembro-familiar .miembro-header { display: flex; justify-content: between; align-items: center; margin-bottom: 15px; }
.miembro-familiar .miembro-title { font-weight: bold; color: #495057; margin: 0; }
.btn-eliminar-miembro { background: #dc3545; border: none; color: white; padding: 5px 10px; border-radius: 4px; }
.btn-eliminar-miembro:hover { background: #c82333; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Composición Familiar</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../salud/salud.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formComposicionFamiliar').submit();">
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
                    <?php if (!empty($datos_existentes)): ?>
                        <?php foreach ($datos_existentes as $index => $miembro): ?>
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
                                        <input type="text" class="form-control" id="nombre_<?php echo $index; ?>" name="nombre[]" 
                                               value="<?php echo htmlspecialchars($miembro['nombre']); ?>" required>
                                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_parentesco_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-diagram-3 me-1"></i>Parentesco:
                                        </label>
                                        <select class="form-select" id="id_parentesco_<?php echo $index; ?>" name="id_parentesco[]" required>
                                            <option value="">Seleccione</option>
                                            <?php foreach ($parentescos as $parentesco): ?>
                                                <option value="<?php echo $parentesco['id']; ?>" 
                                                    <?php echo ($miembro['id_parentesco'] == $parentesco['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($parentesco['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Debe seleccionar el parentesco.</div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="edad_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-calendar me-1"></i>Edad:
                                        </label>
                                        <input type="number" class="form-control" id="edad_<?php echo $index; ?>" name="edad[]" 
                                               value="<?php echo htmlspecialchars($miembro['edad']); ?>" min="0" max="120" required>
                                        <div class="invalid-feedback">La edad es obligatoria (0-120).</div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_ocupacion_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-briefcase me-1"></i>Ocupación:
                                        </label>
                                        <select class="form-select" id="id_ocupacion_<?php echo $index; ?>" name="id_ocupacion[]">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($ocupaciones as $ocupacion): ?>
                                                <option value="<?php echo $ocupacion['id']; ?>" 
                                                    <?php echo ($miembro['id_ocupacion'] == $ocupacion['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ocupacion['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="telefono_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-telephone me-1"></i>Teléfono:
                                        </label>
                                        <input type="text" class="form-control" id="telefono_<?php echo $index; ?>" name="telefono[]" 
                                               value="<?php echo htmlspecialchars($miembro['telefono']); ?>" 
                                               pattern="[0-9]{7,10}" required>
                                        <div class="invalid-feedback">El teléfono es obligatorio (7-10 dígitos).</div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="id_conviven_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-house me-1"></i>Conviven:
                                        </label>
                                        <select class="form-select" id="id_conviven_<?php echo $index; ?>" name="id_conviven[]" required>
                                            <option value="">Seleccione</option>
                                            <?php foreach ($opciones_parametro as $opcion): ?>
                                                <option value="<?php echo $opcion['id']; ?>" 
                                                    <?php echo ($miembro['id_conviven'] == $opcion['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Debe seleccionar si convive.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="observacion_<?php echo $index; ?>" class="form-label">
                                            <i class="bi bi-chat-text me-1"></i>Observación:
                                        </label>
                                        <textarea class="form-control" id="observacion_<?php echo $index; ?>" name="observacion[]" 
                                                  rows="3" maxlength="500"><?php echo htmlspecialchars($miembro['observacion'] ?? ''); ?></textarea>
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
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../salud/salud.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                let miembroIndex = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;
                
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