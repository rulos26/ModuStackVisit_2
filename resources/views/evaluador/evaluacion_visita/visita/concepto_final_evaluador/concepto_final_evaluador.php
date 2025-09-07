<?php
// Redirigir al nuevo wizard
header('Location: ../concepto_final_evaluador_wizard.php');
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
</style>

<div class="container mt-4">
    <div class="card mt-5">
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

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../experiencia_laboral/experiencia_laboral.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formConcepto').submit();">
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
                    Ya existe concepto final registrado para esta cédula. Puede actualizar los datos.
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