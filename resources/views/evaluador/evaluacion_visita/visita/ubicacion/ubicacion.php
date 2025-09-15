<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay una sesión activa (revisamos todas las posibles fuentes de la cédula)
$id_cedula = $_SESSION['id_cedula'] ?? $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;

if (!$id_cedula) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=ubicacion&test=123');
    exit();
}

require_once __DIR__ . '/UbicacionController.php';
use App\Controllers\UbicacionController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = UbicacionController::getInstance();
        
        // Validar que se recibieron las coordenadas
        if (!isset($_POST['latituds']) || !isset($_POST['longituds']) || 
            empty($_POST['latituds']) || empty($_POST['longituds'])) {
            $_SESSION['error'] = "No se recibieron las coordenadas de ubicación.";
        } else {
            $latitud = $_POST['latituds'];
            $longitud = $_POST['longituds'];
            
            $resultado = $controller->guardar($id_cedula, $latitud, $longitud);
            
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                // Aseguramos que la cédula se mantenga en sesión para el siguiente paso
                $_SESSION['id_cedula'] = $id_cedula;
                
                echo '<script>
                    window.location.href = "../perfil/perfil.php";
                </script>';
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        }
    } catch (Exception $e) {
        error_log("Error en ubicacion.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = UbicacionController::getInstance();
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
} catch (Exception $e) {
    error_log("Error en ubicacion.php: " . $e->getMessage());
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
.location-info { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; }
.coordinates { font-family: 'Courier New', monospace; font-size: 1.1rem; font-weight: bold; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-geo-alt me-2"></i>
                UBICACIÓN EN TIEMPO REAL
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
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="step-title">Paso 21</div>
                    <div class="step-description">Concepto Final</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="step-title">Paso 22</div>
                    <div class="step-description">Ubicación</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../concepto_final_evaluador/concepto_final_evaluador.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formUbicacion').submit();">
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
                    Ya existe ubicación registrada para esta cédula. Puede actualizar los datos.
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
            
            <form id="formUbicacion" action="" method="POST">
                <div class="row mb-4">
                   
                    <div class="col-6 d-flex align-items-center justify-content-center">
                        <div class="location-info text-center">
                            <h4><i class="fas fa-map-marker-alt me-2"></i>Ubicación Actual</h4>
                            <div class="coordinates mb-3">
                                <p><i class="fas fa-latitude me-2"></i>Latitud: <span id="latitud" class="text-primary">Obteniendo...</span></p>
                                <p><i class="fas fa-longitude me-2"></i>Longitud: <span id="longitud" class="text-primary">Obteniendo...</span></p>
                            </div>
                            <div class="progress mt-3" style="height: 50px; width: 80%; margin: 0 auto;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%; font-size: 16px; font-weight: bold;" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <span id="countdown">0 %</span>
                                </div>
                            </div>
                            <p class="mt-3 text-muted" style="font-size: 16px;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Generando ubicación...
                            </p>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="latituds" name="latituds">
                <input type="hidden" id="longituds" name="longituds">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Función para obtener la ubicación automáticamente al cargar la página
function obtenerUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitud = position.coords.latitude;
            var longitud = position.coords.longitude;
            
            document.getElementById('latitud').innerText = latitud.toFixed(6);
            document.getElementById('longitud').innerText = longitud.toFixed(6);
            document.getElementById('latituds').value = latitud;
            document.getElementById('longituds').value = longitud;
            
            iniciarContador();
        }, function(error) {
            console.error('Error al obtener ubicación:', error);
            document.getElementById('latitud').innerText = 'Error';
            document.getElementById('longitud').innerText = 'Error';
            document.querySelector('.text-muted').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No se pudo obtener la ubicación.';
        });
    } else {
        document.getElementById('latitud').innerText = 'No soportado';
        document.getElementById('longitud').innerText = 'No soportado';
        document.querySelector('.text-muted').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>La geolocalización no está disponible en este navegador.';
    }
}

// Función para el contador regresivo
function iniciarContador() {
    let tiempoRestante = 10;
    const progressBar = document.getElementById('progressBar');
    const countdown = document.getElementById('countdown');
    const total = 10;
    
    const intervalo = setInterval(() => {
        tiempoRestante--;
        const porcentaje = Math.round(((total - tiempoRestante) / total) * 100);
        
        progressBar.style.width = porcentaje + '%';
        progressBar.setAttribute('aria-valuenow', porcentaje);
        countdown.textContent = porcentaje;
        
        if (tiempoRestante <= 0) {
            clearInterval(intervalo);
            document.querySelector('.text-muted').innerHTML = '<i class="fas fa-check-circle me-2"></i>¡Ubicación generada! Enviando datos...';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            
            setTimeout(() => {
                document.getElementById('formUbicacion').submit();
            }, 1000);
        }
    }, 1000);
}

// Iniciar cuando se carga la página
window.onload = obtenerUbicacion;
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