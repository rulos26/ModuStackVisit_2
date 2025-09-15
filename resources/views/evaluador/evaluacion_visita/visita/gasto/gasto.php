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
require_once __DIR__ . '/GastoController.php';

use App\Controllers\GastoController;

// Función para formatear valores monetarios
function formatearValorMonetario($valor) {
    if (empty($valor) || $valor === 'N/A' || !is_numeric($valor)) {
        return '';
    }
    
    // Convertir a número
    $numero = floatval($valor);
    
    // Formatear con separadores de miles y símbolo de peso colombiano
    return '$' . number_format($numero, 0, ',', '.');
}

// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = GastoController::getInstance();

        // Sanitizar y validar datos de entrada
        $datos = $controller->sanitizarDatos($_POST);

        // Validar datos
        $errores = $controller->validarDatos($datos);
        
        // Guardar los datos del formulario para mantenerlos en caso de error
        $datos_formulario = $datos;
        
        if (empty($errores)) {
            // Intentar guardar los datos
            $resultado = $controller->guardar($datos);

            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];

                // Siempre redirigir a la siguiente pantalla después de guardar/actualizar exitosamente
                header('Location: ../estudios/estudios.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            // Procesar errores para mostrarlos en campos específicos
            foreach ($errores as $error) {
                $_SESSION['error'] = $error;
            }
        }
    } catch (Exception $e) {
        error_log("Error en gasto.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = GastoController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && $datos_existentes !== false) {
        $datos_formulario = $datos_existentes;
    }
} catch (Exception $e) {
    error_log("Error en gasto.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<!-- Puedes usar este código como base para tu formulario y menú responsive -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Responsive y Menú</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
        /* Menú horizontal en desktop */
        @media (min-width: 992px) {
            .navbar-desktop {
                display: flex !important;
            }
            .navbar-mobile {
                display: none !important;
            }
        }
        /* Menú hamburguesa en móvil/tablet */
        @media (max-width: 991.98px) {
            .navbar-desktop {
                display: none !important;
            }
            .navbar-mobile {
                display: block !important;
            }
        }
        /* Ajuste para observaciones */
        .obs-row {
            flex-wrap: wrap;
        }
        .obs-col {
            flex: 1 0 100%;
            max-width: 100%;
        }
        /* Forzar 4 columnas desde 1440px (ajustado para pantallas grandes) */
        @media (min-width: 1440px) {
            .form-responsive-row > [class*="col-"] {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
        /* Bootstrap row display flex fix para forzar columnas */
        .form-responsive-row {
            display: flex;
            flex-wrap: wrap;
        }
        /* Ajuste para imagen de logo que no carga */
        .logo-empresa {
            max-width: 300px;
            min-width: 120px;
            height: auto;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        /* Mejorar visual de la card */
        .card {
            box-shadow: 0 2px 16px 0 rgba(0,0,0,0.07);
        }
        /* Pasos */
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
        .form-text.error-message {
            color: #dc3545;
            font-weight: 500;
        }
        .form-text.success-message {
            color: #198754;
            font-weight: 500;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        /* Estilos para campos de moneda */
        .currency-input {
            position: relative;
        }
        .currency-input .form-control {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 15px;
            font-weight: 600;
            color: #495057;
            transition: all 0.3s ease;
        }
        .currency-input .form-control:focus {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
            transform: translateY(-1px);
        }
        .currency-input .form-control:not(:valid):not(:invalid):not(.is-valid):not(.is-invalid) {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .currency-input .form-control.is-valid {
            background: linear-gradient(135deg, #d1edff 0%, #b3d9ff 100%);
            border-color: #198754;
            color: #0f5132;
        }
        .currency-input .form-control.is-invalid {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
            border-color: #dc3545;
            color: #721c24;
        }
        .currency-tooltip {
            position: relative;
        }
        .currency-tooltip::after {
            content: "Formato: $1.500.000,50";
            position: absolute;
            bottom: -25px;
            left: 0;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        .currency-tooltip:hover::after {
            opacity: 1;
        }
</style>

</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-cash-coin me-2"></i>
                VISITA DOMICILIARÍA - GASTOS O DEUDAS MENSUALES DEL NÚCLEO FAMILIAR
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-cash-coin"></i></div>
                    <div class="step-title">Paso 17</div>
                    <div class="step-description">Gastos Mensuales</div>
                </div>
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
                    Ya existe información de gastos mensuales registrada para esta cédula. Puede actualizar los datos.
                </div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid logo-empresa">
                </div>
                <div class="col-md-6 text-end">
                    <div class="text-muted">
                        <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                        <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Nota informativa sobre campos obligatorios -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Información importante:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios y deben ser completados antes de continuar.
            </div>
            
            <form action="" method="POST" id="formGastos" novalidate autocomplete="off">
                <div class="row">
                    <!-- Campo Alimentación -->
                    <div class="col-md-4 mb-3">
                        <label for="alimentacion_val" class="form-label required-field">
                            <i class="bi bi-egg-fried me-1"></i>Alimentación:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="alimentacion_val" name="alimentacion_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['alimentacion_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en alimentación mensual</div>
                    </div>
                    
                    <!-- Campo Educación -->
                    <div class="col-md-4 mb-3">
                        <label for="educacion_val" class="form-label required-field">
                            <i class="bi bi-book me-1"></i>Educación:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="educacion_val" name="educacion_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['educacion_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en educación mensual</div>
                    </div>
                    
                    <!-- Campo Salud -->
                    <div class="col-md-4 mb-3">
                        <label for="salud_val" class="form-label required-field">
                            <i class="bi bi-heart-pulse me-1"></i>Salud:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="salud_val" name="salud_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['salud_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en salud mensual</div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Campo Recreación -->
                    <div class="col-md-4 mb-3">
                        <label for="recreacion_val" class="form-label required-field">
                            <i class="bi bi-emoji-smile me-1"></i>Recreación:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="recreacion_val" name="recreacion_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['recreacion_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en recreación mensual</div>
                    </div>
                    
                    <!-- Campo Cuota de Créditos -->
                    <div class="col-md-4 mb-3">
                        <label for="cuota_creditos_val" class="form-label required-field">
                            <i class="bi bi-credit-card me-1"></i>Cuota de Créditos:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="cuota_creditos_val" name="cuota_creditos_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['cuota_creditos_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Cuotas de créditos mensuales</div>
                    </div>
                    
                    <!-- Campo Arriendo -->
                    <div class="col-md-4 mb-3">
                        <label for="arriendo_val" class="form-label required-field">
                            <i class="bi bi-house me-1"></i>Arriendo:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="arriendo_val" name="arriendo_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['arriendo_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en arriendo mensual</div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Campo Servicios Públicos -->
                    <div class="col-md-4 mb-3">
                        <label for="servicios_publicos_val" class="form-label required-field">
                            <i class="bi bi-lightning-charge me-1"></i>Servicios Públicos:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="servicios_publicos_val" name="servicios_publicos_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['servicios_publicos_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Gastos en servicios públicos</div>
                    </div>
                    
                    <!-- Campo Otros -->
                    <div class="col-md-4 mb-3">
                        <label for="otros_val" class="form-label required-field">
                            <i class="bi bi-plus-circle me-1"></i>Otros:
                        </label>
                        <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="otros_val" name="otros_val" 
                                       value="<?php echo !empty($datos_formulario) ? htmlspecialchars(formatearValorMonetario($datos_formulario['otros_val'] ?? '')) : ''; ?>"
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-text">Otros gastos mensuales</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../ingresos_mensuales/ingresos_mensuales.php" class="btn btn-secondary btn-lg">
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

    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Cleave.js para formato de moneda -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
</body>
</html>

<script>
// Variables para Cleave.js
let cleaveInstances = {};

// Función para inicializar Cleave.js en un campo
function inicializarCleave(campoId) {
    if (cleaveInstances[campoId]) {
        cleaveInstances[campoId].destroy();
    }
    
    const campo = document.getElementById(campoId);
    if (campo) {
        cleaveInstances[campoId] = new Cleave(campo, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.',
            numeralDecimalScale: 2,
            prefix: '$ ',
            onValueChanged: function(e) {
                const input = e.target;
                // Remover clases de validación previas
                input.classList.remove('is-invalid', 'is-valid');
                
                // Validar formato monetario
                if (validarFormatoMonetario(input.value)) {
                    input.classList.add('is-valid');
                } else if (input.value.trim() !== '') {
                    input.classList.add('is-invalid');
                }
            }
        });
    }
}

// Función para validar formato monetario colombiano
function validarFormatoMonetario(valor) {
    if (!valor || valor.trim() === '') return false;
    
    // Remover prefijo $ y espacios
    let valorLimpio = valor.replace(/^\$\s*/, '').trim();
    
    // Patrón para formato colombiano: 1.500.000,50 o 1500000,50
    const patronColombiano = /^(\d{1,3}(\.\d{3})*|\d+)(,\d{1,2})?$/;
    
    return patronColombiano.test(valorLimpio);
}

// Función para formatear valor para envío
function formatearValorParaEnvio(valor) {
    if (!valor || valor.trim() === '') return '';
    
    // Remover prefijo $ y espacios
    let valorLimpio = valor.replace(/^\$\s*/, '').trim();
    
    // Reemplazar punto por nada (separador de miles) y coma por punto (decimal)
    valorLimpio = valorLimpio.replace(/\./g, '').replace(',', '.');
    
    return valorLimpio;
}

// Función para inicializar estado de campos monetarios
function inicializarEstadoCampos() {
    const camposMonetarios = document.querySelectorAll('input[id$="_val"]');
    camposMonetarios.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            campo.classList.add('is-valid');
        }
    });
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Cleave.js para campos monetarios existentes
    setTimeout(function() {
        const camposMonetarios = document.querySelectorAll('input[id$="_val"]');
        camposMonetarios.forEach(campo => {
            inicializarCleave(campo.id);
        });
        
        // Inicializar estado de campos monetarios
        inicializarEstadoCampos();
    }, 100);
});

// Validación del formulario
document.getElementById('formGastos').addEventListener('submit', function(event) {
    const camposObligatorios = ['alimentacion_val', 'educacion_val', 'salud_val', 'recreacion_val', 'cuota_creditos_val', 'arriendo_val', 'servicios_publicos_val', 'otros_val'];
    
    // Validar campos obligatorios
    for (const campoId of camposObligatorios) {
        const elemento = document.getElementById(campoId);
        if (!elemento.value || elemento.value.trim() === '') {
            event.preventDefault();
            const label = elemento.closest('.mb-3').querySelector('label');
            const labelText = label ? label.innerText.replace('*', '').trim() : campoId;
            alert(`El campo "${labelText}" es obligatorio.`);
            elemento.focus();
            return;
        }
        
        // Validación específica para campos monetarios
        if (!validarFormatoMonetario(elemento.value)) {
            event.preventDefault();
            const label = elemento.closest('.mb-3').querySelector('label');
            const labelText = label ? label.innerText.replace('*', '').trim() : campoId;
            alert(`El campo "${labelText}" debe tener un formato válido (ej: $1.500.000,50).`);
            elemento.focus();
            return;
        }
    }
    
    // Formatear valores monetarios antes del envío
    const camposMonetarios = document.querySelectorAll('input[id$="_val"]');
    camposMonetarios.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            campo.value = formatearValorParaEnvio(campo.value);
        }
    });
});
</script>

<?php
$contenido = ob_get_clean();

// Verificar si la sesión ya está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../../../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../../../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos Mensuales - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
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
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .invalid-feedback {
            font-size: 0.875rem;
        }
        .valid-feedback {
            font-size: 0.875rem;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Gastos Mensuales</h1>
                            <p class="text-muted mb-0">Formulario de gastos del núcleo familiar</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>