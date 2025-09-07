<?php
// Redirigir al nuevo wizard
header('Location: ../experiencia_laboral_wizard.php');
exit();

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];
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
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-briefcase me-2"></i>
                VISITA DOMICILIARÍA - EXPERIENCIA LABORAL
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="step-title">Paso 20</div>
                    <div class="step-description">Experiencia Laboral</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../informacion_judicial/informacion_judicial.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formExperiencia').submit();">
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
                    Ya existe experiencia laboral registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formExperiencia" novalidate autocomplete="off">
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="empresa" class="form-label">
                            <i class="bi bi-building me-1"></i>Empresa:
                        </label>
                        <input type="text" class="form-control" id="empresa" name="empresa" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['empresa']) : ''; ?>"
                               placeholder="Ej: Empresa ABC S.A." minlength="3" required>
                        <div class="form-text">Mínimo 3 caracteres</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="tiempo" class="form-label">
                            <i class="bi bi-clock me-1"></i>Tiempo Laborado:
                        </label>
                        <input type="text" class="form-control" id="tiempo" name="tiempo" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['tiempo']) : ''; ?>"
                               placeholder="Ej: 2 años, 6 meses" minlength="3" required>
                        <div class="form-text">Mínimo 3 caracteres</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="cargo" class="form-label">
                            <i class="bi bi-person-badge me-1"></i>Cargo Desempeñado:
                        </label>
                        <input type="text" class="form-control" id="cargo" name="cargo" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['cargo']) : ''; ?>"
                               placeholder="Ej: Gerente de Ventas" minlength="3" required>
                        <div class="form-text">Mínimo 3 caracteres</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="salario" class="form-label">
                            <i class="bi bi-cash me-1"></i>Salario:
                        </label>
                        <input type="number" class="form-control" id="salario" name="salario" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['salario']) : ''; ?>"
                               placeholder="Ej: 2500000" min="0" step="1000" required>
                        <div class="form-text">Salario mensual en pesos</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="retiro" class="form-label">
                            <i class="bi bi-box-arrow-right me-1"></i>Motivo de Retiro:
                        </label>
                        <input type="text" class="form-control" id="retiro" name="retiro" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['retiro']) : ''; ?>"
                               placeholder="Ej: Renuncia voluntaria" minlength="5" required>
                        <div class="form-text">Mínimo 5 caracteres</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="concepto" class="form-label">
                            <i class="bi bi-chat-quote me-1"></i>Concepto Emitido:
                        </label>
                        <input type="text" class="form-control" id="concepto" name="concepto" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['concepto']) : ''; ?>"
                               placeholder="Ej: Excelente trabajador" minlength="5" required>
                        <div class="form-text">Mínimo 5 caracteres</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">
                            <i class="bi bi-person me-1"></i>Nombre del Contacto:
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['nombre']) : ''; ?>"
                               placeholder="Ej: Juan Pérez" minlength="3" required>
                        <div class="form-text">Mínimo 3 caracteres</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="numero" class="form-label">
                            <i class="bi bi-telephone me-1"></i>Número de Contacto:
                        </label>
                        <input type="number" class="form-control" id="numero" name="numero" 
                               value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes['numero']) : ''; ?>"
                               placeholder="Ej: 3001234567" min="1000000" required>
                        <div class="form-text">Mínimo 7 dígitos</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../informacion_judicial/informacion_judicial.php" class="btn btn-secondary btn-lg">
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