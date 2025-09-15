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
require_once __DIR__ . '/AportanteController.php';

use App\Controllers\AportanteController;

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
        $controller = AportanteController::getInstance();

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
                header('Location: ../data_credito/data_credito.php');
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
        error_log("Error en aportante.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = AportanteController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && $datos_existentes !== false) {
        $datos_formulario = $datos_existentes;
    }
} catch (Exception $e) {
    error_log("Error en aportante.php: " . $e->getMessage());
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
        .aportante-item { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            background: #f8f9fa; 
            position: relative;
        }
        .aportante-item h6 { 
            color: #495057; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #dee2e6; 
            padding-bottom: 10px; 
        }
        .btn-remove-aportante { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
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
                <i class="bi bi-people me-2"></i>
                VISITA DOMICILIARÍA - PERSONAS QUE APORTAN ECONÓMICAMENTE AL HOGAR
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
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 14</div>
                    <div class="step-description">Aportantes</div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="controls text-center mb-4">
                <a href="../pasivos/pasivos.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('formAportantes').submit();">
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
                    Ya existen <?php echo count($datos_existentes); ?> aportante(s) registrado(s) para esta cédula. Puede actualizar los datos.
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
            
            <form action="" method="POST" id="formAportantes" novalidate autocomplete="off">
                <div id="aportantes-container">
                    <!-- Aportante inicial -->
                    <div class="aportante-item" data-aportante="0">
                        <h6><i class="fas fa-user me-2"></i>Aportante #1</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_0" class="form-label">
                                    <i class="bi bi-person me-1"></i>Nombre:
                                </label>
                                <input type="text" class="form-control" id="nombre_0" name="nombre[]" 
                                       value="<?php echo !empty($datos_existentes) ? htmlspecialchars($datos_existentes[0]['nombre'] ?? '') : ''; ?>"
                                       placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                                <div class="form-text">Mínimo 3 caracteres, máximo 100</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="valor_0" class="form-label required-field">
                                    <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                                </label>
                                <div class="currency-input currency-tooltip">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="valor_0" name="valor[]" 
                                           value="<?php echo !empty($datos_formulario) && !empty($datos_formulario[0]['valor']) ? htmlspecialchars(formatearValorMonetario($datos_formulario[0]['valor'])) : ''; ?>"
                                           placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="form-text">Ingrese el valor mensual del aporte</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aportantes adicionales si existen datos -->
                    <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
                        <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
                            <div class="aportante-item" data-aportante="<?php echo $i; ?>">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-aportante" onclick="removeAportante(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h6><i class="fas fa-user me-2"></i>Aportante #<?php echo $i + 1; ?></h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_<?php echo $i; ?>" class="form-label">
                                            <i class="bi bi-person me-1"></i>Nombre:
                                        </label>
                                        <input type="text" class="form-control" id="nombre_<?php echo $i; ?>" name="nombre[]" 
                                               value="<?php echo htmlspecialchars($datos_existentes[$i]['nombre']); ?>"
                                               placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="valor_<?php echo $i; ?>" class="form-label required-field">
                                            <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                                        </label>
                                        <div class="currency-input currency-tooltip">
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control" id="valor_<?php echo $i; ?>" name="valor[]" 
                                                   value="<?php echo htmlspecialchars(formatearValorMonetario($datos_formulario[$i]['valor'])); ?>"
                                                   placeholder="0.00" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarAportante">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Otro Aportante
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../pasivos/pasivos.php" class="btn btn-secondary btn-lg">
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
// Variables para manejar aportantes dinámicos
let aportanteCounter = <?php echo !empty($datos_formulario) ? count($datos_formulario) : 1; ?>;

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
    const camposMonetarios = document.querySelectorAll('input[id*="valor_"]');
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
        const camposMonetarios = document.querySelectorAll('input[id*="valor_"]');
        camposMonetarios.forEach(campo => {
            inicializarCleave(campo.id);
        });
        
        // Inicializar estado de campos monetarios
        inicializarEstadoCampos();
    }, 100);
});

document.getElementById('btnAgregarAportante').addEventListener('click', function() {
    const container = document.getElementById('aportantes-container');
    const nuevoAportante = document.createElement('div');
    nuevoAportante.className = 'aportante-item';
    nuevoAportante.setAttribute('data-aportante', aportanteCounter);
    
    nuevoAportante.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm btn-remove-aportante" onclick="removeAportante(this)">
            <i class="fas fa-times"></i>
        </button>
        <h6><i class="fas fa-user me-2"></i>Aportante #${aportanteCounter + 1}</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre_${aportanteCounter}" class="form-label">
                    <i class="bi bi-person me-1"></i>Nombre:
                </label>
                <input type="text" class="form-control" id="nombre_${aportanteCounter}" name="nombre[]" 
                       placeholder="Ej: Juan Pérez, María García" minlength="3" maxlength="100" required>
                <div class="form-text">Mínimo 3 caracteres, máximo 100</div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="valor_${aportanteCounter}" class="form-label">
                    <i class="bi bi-cash-stack me-1"></i>Valor del Aporte:
                </label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" id="valor_${aportanteCounter}" name="valor[]" 
                           placeholder="0.00" required>
                </div>
                <div class="form-text">Ingrese el valor mensual del aporte</div>
            </div>
        </div>
    `;
    
    container.appendChild(nuevoAportante);
    
    // Inicializar Cleave.js para los nuevos campos monetarios
    inicializarCleave(`valor_${aportanteCounter}`);
    
    aportanteCounter++;
});

function removeAportante(button) {
    const aportanteItem = button.closest('.aportante-item');
    aportanteItem.remove();
    
    // Renumerar los aportantes restantes
    const aportantes = document.querySelectorAll('.aportante-item');
    aportantes.forEach((aportante, index) => {
        const titulo = aportante.querySelector('h6');
        titulo.innerHTML = `<i class="fas fa-user me-2"></i>Aportante #${index + 1}`;
    });
}

// Validación del formulario (mejorada)
document.getElementById('formAportantes').addEventListener('submit', function(event) {
    const camposObligatorios = ['nombre', 'valor'];
    
    const aportantes = document.querySelectorAll('.aportante-item');
    for (let i = 0; i < aportantes.length; i++) {
        for (const campo of camposObligatorios) {
            const elemento = document.getElementById(`${campo}_${i}`);
            if (!elemento.value || elemento.value.trim() === '') {
                event.preventDefault();
                // Obtener el texto de la etiqueta para un mensaje más claro
                const label = elemento.closest('.mb-3').querySelector('label');
                const labelText = label ? label.innerText.replace('*', '').trim() : campo;
                alert(`El campo "${labelText}" del Aportante #${i + 1} es obligatorio.`);
                elemento.focus();
                return;
            }
            
            // Validación específica para campos monetarios
            if (campo === 'valor') {
                if (!validarFormatoMonetario(elemento.value)) {
                    event.preventDefault();
                    const label = elemento.closest('.mb-3').querySelector('label');
                    const labelText = label ? label.innerText.replace('*', '').trim() : campo;
                    alert(`El campo "${labelText}" del Aportante #${i + 1} debe tener un formato válido (ej: $1.500.000,50).`);
                    elemento.focus();
                    return;
                }
            }
        }
    }
    
    // Formatear valores monetarios antes del envío
    const camposMonetarios = document.querySelectorAll('input[id*="valor_"]');
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
    <title>Aportantes - Dashboard Evaluador</title>
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
        .aportante-item { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            background: #f8f9fa; 
            position: relative;
        }
        .aportante-item h6 { 
            color: #495057; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #dee2e6; 
            padding-bottom: 10px; 
        }
        .btn-remove-aportante { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
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
                            <h1 class="h3 mb-0">Aportantes</h1>
                            <p class="text-muted mb-0">Formulario de personas que aportan económicamente al hogar</p>
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