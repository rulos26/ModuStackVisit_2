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

require_once __DIR__ . '/InformacionJudicialController.php';
use App\Controllers\InformacionJudicialController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InformacionJudicialController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../experiencia_laboral/experiencia_laboral.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en informacion_judicial.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = InformacionJudicialController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    $opciones = $controller->obtenerOpciones();
    $hora_actual = date('Y-m-d H:i:s');
} catch (Exception $e) {
    error_log("Error en informacion_judicial.php: " . $e->getMessage());
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
.desc-field { display: none; }
.desc-field.show { display: block; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-shield-exclamation me-2"></i>
                VISITA DOMICILIARÍA - INFORMACIÓN JUDICIAL
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-shield-exclamation"></i></div>
                    <div class="step-title">Paso 19</div>
                    <div class="step-description">Información Judicial</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../estudios/estudios.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formJudicial').submit();">
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
                    Ya existe información judicial registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formJudicial" novalidate autocomplete="off">
                <!-- Revisión Fiscal -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="revi_fiscal" class="form-label">
                            <i class="bi bi-shield-check me-1"></i>
                            ANTECEDENTES JUDICIALES Y DISCIPLINARIOS POLICÍA Y CONTRALORÍA, PROCURADURÍA, LISTAS CLINTON, INTERPOL ORFAC
                        </label>
                        <textarea name="revi_fiscal" id="revi_fiscal" class="form-control" rows="5" minlength="50" required><?php 
                            if (!empty($datos_existentes)) {
                                echo htmlspecialchars($datos_existentes['revi_fiscal']);
                            } else {
                                echo "El señor Luis carlos matiz Valbuena identificado con cedula de ciudadanía n° 79632323 expedía en Bogotá según certificación No presenta antecedentes judiciales, No tiene asuntos pendientes con las autoridades judiciales, No se encuentra reportado como responsable fiscal, No registra sanciones o inhabilidades vigentes, No registra lista OFAC, CLINTON, INTERPOL. VERIFICACION EN TIEMPO REAL: " . $hora_actual;
                            }
                        ?></textarea>
                        <div class="form-text">Mínimo 50 caracteres</div>
                    </div>
                </div>
                
                <!-- Campos de información judicial -->
                <div class="row">
                    <!-- Denuncias -->
                    <div class="col-md-4 mb-3">
                        <label for="denuncias_opc" class="form-label">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            ¿Ha presentado denuncias o demandas a persona natural o persona jurídica?
                        </label>
                        <select class="form-select" id="denuncias_opc" name="denuncias_opc" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_existentes) && $datos_existentes['denuncias_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3 desc-field" id="denuncias_desc_field">
                        <label for="denuncias_desc" class="form-label">
                            <i class="bi bi-pencil me-1"></i>Descripción de Denuncias:
                        </label>
                        <input type="text" class="form-control" id="denuncias_desc" name="denuncias_desc" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['denuncias_desc']) : ''; ?>"
                               placeholder="Describa las denuncias..." minlength="10">
                        <div class="form-text">Mínimo 10 caracteres</div>
                    </div>
                    
                    <!-- Procesos Judiciales -->
                    <div class="col-md-4 mb-3">
                        <label for="procesos_judiciales_opc" class="form-label">
                            <i class="bi bi-gavel me-1"></i>
                            ¿Presenta procesos judiciales o disciplinarios en contra?
                        </label>
                        <select class="form-select" id="procesos_judiciales_opc" name="procesos_judiciales_opc" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_existentes) && $datos_existentes['procesos_judiciales_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3 desc-field" id="procesos_judiciales_desc_field">
                        <label for="procesos_judiciales_desc" class="form-label">
                            <i class="bi bi-pencil me-1"></i>Descripción de Procesos Judiciales:
                        </label>
                        <input type="text" class="form-control" id="procesos_judiciales_desc" name="procesos_judiciales_desc" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['procesos_judiciales_desc']) : ''; ?>"
                               placeholder="Describa los procesos..." minlength="10">
                        <div class="form-text">Mínimo 10 caracteres</div>
                    </div>
                    
                    <!-- Privación de Libertad -->
                    <div class="col-md-4 mb-3">
                        <label for="preso_opc" class="form-label">
                            <i class="bi bi-lock me-1"></i>
                            ¿Ha sido privado de la libertad? (Policía, Fiscalía)
                        </label>
                        <select class="form-select" id="preso_opc" name="preso_opc" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_existentes) && $datos_existentes['preso_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3 desc-field" id="preso_desc_field">
                        <label for="preso_desc" class="form-label">
                            <i class="bi bi-pencil me-1"></i>Descripción de Privación de Libertad:
                        </label>
                        <input type="text" class="form-control" id="preso_desc" name="preso_desc" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['preso_desc']) : ''; ?>"
                               placeholder="Describa la situación..." minlength="10">
                        <div class="form-text">Mínimo 10 caracteres</div>
                    </div>
                    
                    <!-- Familia Detenida -->
                    <div class="col-md-4 mb-3">
                        <label for="familia_detenido_opc" class="form-label">
                            <i class="bi bi-people me-1"></i>
                            ¿Algún miembro de la familia ha sido detenido por algún delito?
                        </label>
                        <select class="form-select" id="familia_detenido_opc" name="familia_detenido_opc" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_existentes) && $datos_existentes['familia_detenido_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3 desc-field" id="familia_detenido_desc_field">
                        <label for="familia_detenido_desc" class="form-label">
                            <i class="bi bi-pencil me-1"></i>Descripción de Familia Detenida:
                        </label>
                        <input type="text" class="form-control" id="familia_detenido_desc" name="familia_detenido_desc" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['familia_detenido_desc']) : ''; ?>"
                               placeholder="Describa la situación..." minlength="10">
                        <div class="form-text">Mínimo 10 caracteres</div>
                    </div>
                    
                    <!-- Centros Penitenciarios -->
                    <div class="col-md-4 mb-3">
                        <label for="centros_penitenciarios_opc" class="form-label">
                            <i class="bi bi-building me-1"></i>
                            ¿Ha visitado centros penitenciarios?
                        </label>
                        <select class="form-select" id="centros_penitenciarios_opc" name="centros_penitenciarios_opc" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id']; ?>" 
                                    <?php echo (!empty($datos_existentes) && $datos_existentes['centros_penitenciarios_opc'] == $opcion['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($opcion['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3 desc-field" id="centros_penitenciarios_desc_field">
                        <label for="centros_penitenciarios_desc" class="form-label">
                            <i class="bi bi-pencil me-1"></i>Descripción de Centros Penitenciarios:
                        </label>
                        <input type="text" class="form-control" id="centros_penitenciarios_desc" name="centros_penitenciarios_desc" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['centros_penitenciarios_desc']) : ''; ?>"
                               placeholder="Describa las visitas..." minlength="10">
                        <div class="form-text">Mínimo 10 caracteres</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../estudios/estudios.php" class="btn btn-secondary btn-lg">
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
// Función para mostrar/ocultar campos de descripción
function toggleDescFields() {
    const campos = [
        { opcion: 'denuncias_opc', desc: 'denuncias_desc_field' },
        { opcion: 'procesos_judiciales_opc', desc: 'procesos_judiciales_desc_field' },
        { opcion: 'preso_opc', desc: 'preso_desc_field' },
        { opcion: 'familia_detenido_opc', desc: 'familia_detenido_desc_field' },
        { opcion: 'centros_penitenciarios_opc', desc: 'centros_penitenciarios_desc_field' }
    ];
    
    campos.forEach(campo => {
        const opcionSelect = document.getElementById(campo.opcion);
        const descField = document.getElementById(campo.desc);
        
        if (opcionSelect && descField) {
            if (opcionSelect.value === '2') { // Si es "Sí"
                descField.classList.add('show');
                const descInput = descField.querySelector('input');
                if (descInput) descInput.required = true;
            } else {
                descField.classList.remove('show');
                const descInput = descField.querySelector('input');
                if (descInput) descInput.required = false;
            }
        }
    });
}

// Agregar event listeners
document.addEventListener('DOMContentLoaded', function() {
    const selects = ['denuncias_opc', 'procesos_judiciales_opc', 'preso_opc', 'familia_detenido_opc', 'centros_penitenciarios_opc'];
    
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', toggleDescFields);
        }
    });
    
    // Ejecutar al cargar la página
    toggleDescFields();
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