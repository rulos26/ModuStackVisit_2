<?php
// Redirigir al nuevo wizard
header('Location: ../inventario_enseres_wizard.php');
exit();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InventarioEnseresController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../servicios_publicos/servicios_publicos.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en inventario_enseres.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = InventarioEnseresController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Obtener opciones para los select
    $parametros = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en inventario_enseres.php: " . $e->getMessage());
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
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-box-seam me-2"></i>
                VISITA DOMICILIARÍA - INVENTARIO DE ENSERES
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-box-seam"></i></div>
                    <div class="step-title">Paso 9</div>
                    <div class="step-description">Inventario de Enseres</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../estado_vivienda/estado_vivienda.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formInventarioEnseres').submit();">
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
                    Ya existe información del inventario de enseres registrada para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formInventarioEnseres" novalidate autocomplete="off">
                        <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="televisor_cant" class="form-label">
                            <i class="bi bi-tv me-1"></i>Televisores:
                        </label>
                                <select class="form-select" id="televisor_cant" name="televisor_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['televisor_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="dvd_cant" class="form-label">
                            <i class="bi bi-disc me-1"></i>DVDs:
                        </label>
                                <select class="form-select" id="dvd_cant" name="dvd_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['dvd_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="teatro_casa_cant" class="form-label">
                            <i class="bi bi-speaker me-1"></i>Teatros en Casa:
                        </label>
                                <select class="form-select" id="teatro_casa_cant" name="teatro_casa_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['teatro_casa_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                    </div>
                            </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="equipo_sonido_cant" class="form-label">
                            <i class="bi bi-music-note me-1"></i>Equipos de Sonido:
                        </label>
                                <select class="form-select" id="equipo_sonido_cant" name="equipo_sonido_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['equipo_sonido_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="computador_cant" class="form-label">
                            <i class="bi bi-laptop me-1"></i>Computadores:
                        </label>
                                <select class="form-select" id="computador_cant" name="computador_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['computador_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="impresora_cant" class="form-label">
                            <i class="bi bi-printer me-1"></i>Impresoras:
                        </label>
                                <select class="form-select" id="impresora_cant" name="impresora_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['impresora_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                    </div>
                            </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="movil_cant" class="form-label">
                            <i class="bi bi-phone me-1"></i>Dispositivos Móviles:
                        </label>
                                <select class="form-select" id="movil_cant" name="movil_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['movil_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="estufa_cant" class="form-label">
                            <i class="bi bi-fire me-1"></i>Estufas:
                        </label>
                                <select class="form-select" id="estufa_cant" name="estufa_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['estufa_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="nevera_cant" class="form-label">
                            <i class="bi bi-snow me-1"></i>Neveras:
                        </label>
                                <select class="form-select" id="nevera_cant" name="nevera_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['nevera_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                    </div>
                            </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="lavadora_cant" class="form-label">
                            <i class="bi bi-water me-1"></i>Lavadoras:
                        </label>
                                <select class="form-select" id="lavadora_cant" name="lavadora_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['lavadora_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="microondas_cant" class="form-label">
                            <i class="bi bi-lightning me-1"></i>Microondas:
                        </label>
                                <select class="form-select" id="microondas_cant" name="microondas_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['microondas_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                    <div class="col-md-4 mb-3">
                        <label for="moto_cant" class="form-label">
                            <i class="bi bi-bicycle me-1"></i>Motos:
                        </label>
                                <select class="form-select" id="moto_cant" name="moto_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['moto_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="carro_cant" class="form-label">
                            <i class="bi bi-car-front me-1"></i>Carros:
                        </label>
                                <select class="form-select" id="carro_cant" name="carro_cant">
                            <option value="">Seleccione</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php echo ($datos_existentes && $datos_existentes['carro_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                                </select>
                            </div>
                            </div>   

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="observacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Observación:
                        </label>
                        <textarea class="form-control" id="observacion" name="observacion" 
                                  rows="4" maxlength="1000"><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                        <div class="form-text">Máximo 1000 caracteres. Mínimo 10 caracteres si se llena.</div>
                        </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../estado_vivienda/estado_vivienda.php" class="btn btn-secondary btn-lg">
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