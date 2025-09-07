<?php
// Redirigir al nuevo wizard
header('Location: ../estudios_wizard.php');
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
.estudio-item { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f8f9fa; }
.estudio-item h6 { color: #495057; margin-bottom: 15px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px; }
.btn-remove-estudio { position: absolute; top: 10px; right: 10px; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-mortarboard me-2"></i>
                VISITA DOMICILIARÍA - VERIFICACIÓN ACADÉMICA
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-mortarboard"></i></div>
                    <div class="step-title">Paso 18</div>
                    <div class="step-description">Estudios</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../gasto/gasto.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formEstudios').submit();">
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
                    Ya existen <?php echo count($datos_existentes); ?> estudio(s) registrado(s) para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formEstudios" novalidate autocomplete="off">
                <div id="estudios-container">
                    <!-- Estudio inicial -->
                    <div class="estudio-item" data-estudio="0">
                        <h6><i class="fas fa-mortarboard me-2"></i>Estudio #1</h6>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="centro_estudios_0" class="form-label">
                                    <i class="bi bi-building me-1"></i>Centro de Estudios:
                                </label>
                                <input type="text" class="form-control" id="centro_estudios_0" name="centro_estudios[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['centro_estudios'] ?? '') : ''; ?>"
                                       placeholder="Ej: Universidad Nacional" minlength="3" required>
                                <div class="form-text">Mínimo 3 caracteres</div>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="id_jornada_0" class="form-label">
                                    <i class="bi bi-clock me-1"></i>Jornada:
                                </label>
                                <input type="text" class="form-control" id="id_jornada_0" name="id_jornada[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_jornada'] ?? '') : ''; ?>"
                                       placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                                <div class="form-text">Mínimo 3 caracteres</div>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="id_ciudad_0" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Ciudad:
                                </label>
                                <select class="form-select" id="id_ciudad_0" name="id_ciudad[]" required>
                                    <option value="">Seleccione una ciudad</option>
                                    <?php foreach ($municipios as $municipio): ?>
                                        <option value="<?php echo $municipio['id_municipio']; ?>" 
                                            <?php echo (!empty($datos_existentes) && $datos_existentes[0]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($municipio['municipio']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="anno_0" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Año:
                                </label>
                                <input type="number" class="form-control" id="anno_0" name="anno[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['anno'] ?? '') : ''; ?>"
                                       placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                                <div class="form-text">Año de estudio</div>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="titulos_0" class="form-label">
                                    <i class="bi bi-award me-1"></i>Títulos:
                                </label>
                                <input type="text" class="form-control" id="titulos_0" name="titulos[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['titulos'] ?? '') : ''; ?>"
                                       placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                                <div class="form-text">Mínimo 3 caracteres</div>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="id_resultado_0" class="form-label">
                                    <i class="bi bi-check-circle me-1"></i>Resultado:
                                </label>
                                <input type="text" class="form-control" id="id_resultado_0" name="id_resultado[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['id_resultado'] ?? '') : ''; ?>"
                                       placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                                <div class="form-text">Mínimo 3 caracteres</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estudios adicionales si existen datos -->
                    <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
                        <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
                            <div class="estudio-item" data-estudio="<?php echo $i; ?>">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-estudio" onclick="removeEstudio(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h6><i class="fas fa-mortarboard me-2"></i>Estudio #<?php echo $i + 1; ?></h6>
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="centro_estudios_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-building me-1"></i>Centro de Estudios:
                                        </label>
                                        <input type="text" class="form-control" id="centro_estudios_<?php echo $i; ?>" name="centro_estudios[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['centro_estudios']); ?>"
                                               placeholder="Ej: Universidad Nacional" minlength="3" required>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="id_jornada_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-clock me-1"></i>Jornada:
                                        </label>
                                        <input type="text" class="form-control" id="id_jornada_<?php echo $i; ?>" name="id_jornada[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['id_jornada']); ?>"
                                               placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="id_ciudad_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-geo-alt me-1"></i>Ciudad:
                                        </label>
                                        <select class="form-select" id="id_ciudad_<?php echo $i; ?>" name="id_ciudad[]" required>
                                            <option value="">Seleccione una ciudad</option>
                                            <?php foreach ($municipios as $municipio): ?>
                                                <option value="<?php echo $municipio['id_municipio']; ?>" 
                                                    <?php echo ($datos_existentes[$i]['id_ciudad'] == $municipio['id_municipio']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($municipio['municipio']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="anno_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-calendar me-1"></i>Año:
                                        </label>
                                        <input type="number" class="form-control" id="anno_<?php echo $i; ?>" name="anno[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['anno']); ?>"
                                               placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="titulos_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-award me-1"></i>Títulos:
                                        </label>
                                        <input type="text" class="form-control" id="titulos_<?php echo $i; ?>" name="titulos[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['titulos']); ?>"
                                               placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="id_resultado_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-check-circle me-1"></i>Resultado:
                                        </label>
                                        <input type="text" class="form-control" id="id_resultado_<?php echo $i; ?>" name="id_resultado[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['id_resultado']); ?>"
                                               placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarEstudio">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Otro Estudio
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../gasto/gasto.php" class="btn btn-secondary btn-lg">
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
let estudioCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;

document.getElementById('btnAgregarEstudio').addEventListener('click', function() {
    const container = document.getElementById('estudios-container');
    const nuevoEstudio = document.createElement('div');
    nuevoEstudio.className = 'estudio-item';
    nuevoEstudio.setAttribute('data-estudio', estudioCounter);
    
    nuevoEstudio.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm btn-remove-estudio" onclick="removeEstudio(this)">
            <i class="fas fa-times"></i>
        </button>
        <h6><i class="fas fa-mortarboard me-2"></i>Estudio #${estudioCounter + 1}</h6>
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="centro_estudios_${estudioCounter}" class="form-label">
                    <i class="bi bi-building me-1"></i>Centro de Estudios:
                </label>
                <input type="text" class="form-control" id="centro_estudios_${estudioCounter}" name="centro_estudios[]" 
                       placeholder="Ej: Universidad Nacional" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_jornada_${estudioCounter}" class="form-label">
                    <i class="bi bi-clock me-1"></i>Jornada:
                </label>
                <input type="text" class="form-control" id="id_jornada_${estudioCounter}" name="id_jornada[]" 
                       placeholder="Ej: Diurna, Nocturna" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_ciudad_${estudioCounter}" class="form-label">
                    <i class="bi bi-geo-alt me-1"></i>Ciudad:
                </label>
                <select class="form-select" id="id_ciudad_${estudioCounter}" name="id_ciudad[]" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?php echo $municipio['id_municipio']; ?>">
                            <?php echo htmlspecialchars($municipio['municipio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="anno_${estudioCounter}" class="form-label">
                    <i class="bi bi-calendar me-1"></i>Año:
                </label>
                <input type="number" class="form-control" id="anno_${estudioCounter}" name="anno[]" 
                       placeholder="2024" min="1900" max="<?php echo date('Y') + 10; ?>" required>
                <div class="form-text">Año de estudio</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="titulos_${estudioCounter}" class="form-label">
                    <i class="bi bi-award me-1"></i>Títulos:
                </label>
                <input type="text" class="form-control" id="titulos_${estudioCounter}" name="titulos[]" 
                       placeholder="Ej: Ingeniero, Licenciado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
            
            <div class="col-md-2 mb-3">
                <label for="id_resultado_${estudioCounter}" class="form-label">
                    <i class="bi bi-check-circle me-1"></i>Resultado:
                </label>
                <input type="text" class="form-control" id="id_resultado_${estudioCounter}" name="id_resultado[]" 
                       placeholder="Ej: Aprobado, Graduado" minlength="3" required>
                <div class="form-text">Mínimo 3 caracteres</div>
            </div>
        </div>
    `;
    
    container.appendChild(nuevoEstudio);
    estudioCounter++;
});

function removeEstudio(button) {
    const estudioItem = button.closest('.estudio-item');
    estudioItem.remove();
    
    // Renumerar los estudios restantes
    const estudios = document.querySelectorAll('.estudio-item');
    estudios.forEach((estudio, index) => {
        const titulo = estudio.querySelector('h6');
        titulo.innerHTML = `<i class="fas fa-mortarboard me-2"></i>Estudio #${index + 1}`;
    });
}
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