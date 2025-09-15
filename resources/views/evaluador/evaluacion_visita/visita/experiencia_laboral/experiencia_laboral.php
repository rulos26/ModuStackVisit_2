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
require_once __DIR__ . '/ExperienciaLaboralController.php';
use App\Controllers\ExperienciaLaboralController;

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
        $controller = ExperienciaLaboralController::getInstance();

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
                header('Location: ../concepto_final_evaluador/concepto_final_evaluador.php');
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
        error_log("Error en experiencia_laboral.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = ExperienciaLaboralController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Si no hay datos del formulario (POST), usar datos existentes
    if (empty($datos_formulario) && $datos_existentes !== false) {
        $datos_formulario = $datos_existentes;
    }
} catch (Exception $e) {
    error_log("Error en experiencia_laboral.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
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
    const camposMonetarios = document.querySelectorAll('input[name*="[salario]"]');
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
        const camposMonetarios = document.querySelectorAll('input[name*="[salario]"]');
        camposMonetarios.forEach(campo => {
            inicializarCleave(campo.id);
        });
        
        // Inicializar estado de campos monetarios
        inicializarEstadoCampos();
    }, 100);
});

// Validación del formulario
document.getElementById('formExperiencia').addEventListener('submit', function(event) {
    // Formatear valores monetarios antes del envío
    const camposMonetarios = document.querySelectorAll('input[name*="[salario]"]');
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
    <title>Experiencia Laboral - Dashboard Evaluador</title>
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
        .currency-input {
            position: relative;
        }
        .currency-input .form-control {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .currency-input .form-control:focus {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .currency-input .form-control.is-valid {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        .currency-input .form-control.is-invalid {
            border-color: #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        }
        .currency-tooltip {
            position: relative;
        }
        .currency-tooltip::after {
            content: "Formato: $1.500.000,50";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .currency-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
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
                            <h1 class="h3 mb-0">Experiencia Laboral</h1>
                            <p class="text-muted mb-0">Formulario de experiencia laboral del núcleo familiar</p>
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

<div class="card">
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
            <button class="btn btn-primary" id="nextBtn" type="button" disabled>
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
            <div class="col-12 d-flex justify-content-between align-items-center">
                <img src="/ModuStackVisit_2/public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid logo-empresa">
                <div class="text-muted text-end">
                    <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                    <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                </div>
            </div>
        </div>

        <!-- Nota informativa sobre campos obligatorios -->
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Información importante:</strong> Puede agregar múltiples experiencias laborales. Use el botón "Agregar Experiencia" para añadir más registros.
        </div>

                <form action="" method="POST" id="formExperiencia" novalidate autocomplete="off">
                    <!-- Contenedor de experiencias laborales -->
                    <div id="experiencias-container">
                        <?php if (!empty($datos_existentes) && is_array($datos_existentes)): ?>
                            <!-- Si hay datos existentes múltiples -->
                            <?php foreach ($datos_existentes as $index => $experiencia): ?>
                                <div class="experiencia-item border rounded p-3 mb-3" data-index="<?php echo $index; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="bi bi-briefcase me-2"></i>Experiencia Laboral #<?php echo intval($index) + 1; ?>
                                        </h6>
                                        <?php if ($index > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarExperiencia(this)">
                                                <i class="bi bi-trash me-1"></i>Eliminar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-building me-1"></i>Empresa:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][empresa]" 
                                                   value="<?php echo htmlspecialchars($experiencia['empresa'] ?? ''); ?>"
                                                   placeholder="Ej: Empresa ABC S.A." minlength="3" required>
                                            <div class="form-text">Mínimo 3 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-clock me-1"></i>Tiempo Laborado:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][tiempo]" 
                                                   value="<?php echo htmlspecialchars($experiencia['tiempo'] ?? ''); ?>"
                                                   placeholder="Ej: 2 años, 6 meses" minlength="3" required>
                                            <div class="form-text">Mínimo 3 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-person-badge me-1"></i>Cargo Desempeñado:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][cargo]" 
                                                   value="<?php echo htmlspecialchars($experiencia['cargo'] ?? ''); ?>"
                                                   placeholder="Ej: Gerente de Ventas" minlength="3" required>
                                            <div class="form-text">Mínimo 3 caracteres</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-cash me-1"></i>Salario:
                                            </label>
                                            <div class="currency-input currency-tooltip">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control" id="salario_<?php echo $index; ?>" name="experiencias[<?php echo $index; ?>][salario]" 
                                                           value="<?php echo formatearValorMonetario($experiencia['salario'] ?? ''); ?>"
                                                           placeholder="1.500.000" required>
                                                </div>
                                            </div>
                                            <div class="form-text">Salario mensual en pesos colombianos</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-box-arrow-right me-1"></i>Motivo de Retiro:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][retiro]" 
                                                   value="<?php echo htmlspecialchars($experiencia['retiro'] ?? ''); ?>"
                                                   placeholder="Ej: Renuncia voluntaria" minlength="5" required>
                                            <div class="form-text">Mínimo 5 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-chat-quote me-1"></i>Concepto Emitido:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][concepto]" 
                                                   value="<?php echo htmlspecialchars($experiencia['concepto'] ?? ''); ?>"
                                                   placeholder="Ej: Excelente trabajador" minlength="5" required>
                                            <div class="form-text">Mínimo 5 caracteres</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-person me-1"></i>Nombre del Contacto:
                                            </label>
                                            <input type="text" class="form-control" name="experiencias[<?php echo $index; ?>][nombre]" 
                                                   value="<?php echo htmlspecialchars($experiencia['nombre'] ?? ''); ?>"
                                                   placeholder="Ej: Juan Pérez" minlength="3" required>
                                            <div class="form-text">Mínimo 3 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-telephone me-1"></i>Número de Contacto:
                                            </label>
                                            <input type="number" class="form-control" name="experiencias[<?php echo $index; ?>][numero]" 
                                                   value="<?php echo htmlspecialchars($experiencia['numero'] ?? ''); ?>"
                                                   placeholder="Ej: 3001234567" min="1000000" required>
                                            <div class="form-text">Mínimo 7 dígitos</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Experiencia inicial (si no hay datos existentes) -->
                            <div class="experiencia-item border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">
                                        <i class="bi bi-briefcase me-2"></i>Experiencia Laboral #1
                                    </h6>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-building me-1"></i>Empresa:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][empresa]" 
                                               placeholder="Ej: Empresa ABC S.A." minlength="3" required>
                                        <div class="form-text">Mínimo 3 caracteres</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-clock me-1"></i>Tiempo Laborado:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][tiempo]" 
                                               placeholder="Ej: 2 años, 6 meses" minlength="3" required>
                                        <div class="form-text">Mínimo 3 caracteres</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-person-badge me-1"></i>Cargo Desempeñado:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][cargo]" 
                                               placeholder="Ej: Gerente de Ventas" minlength="3" required>
                                        <div class="form-text">Mínimo 3 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-cash me-1"></i>Salario:
                                        </label>
                                        <div class="currency-input currency-tooltip">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" id="salario_0" name="experiencias[0][salario]" 
                                                       placeholder="1.500.000" required>
                                            </div>
                                        </div>
                                        <div class="form-text">Salario mensual en pesos colombianos</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-box-arrow-right me-1"></i>Motivo de Retiro:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][retiro]" 
                                               placeholder="Ej: Renuncia voluntaria" minlength="5" required>
                                        <div class="form-text">Mínimo 5 caracteres</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-chat-quote me-1"></i>Concepto Emitido:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][concepto]" 
                                               placeholder="Ej: Excelente trabajador" minlength="5" required>
                                        <div class="form-text">Mínimo 5 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-person me-1"></i>Nombre del Contacto:
                                        </label>
                                        <input type="text" class="form-control" name="experiencias[0][nombre]" 
                                               placeholder="Ej: Juan Pérez" minlength="3" required>
                                        <div class="form-text">Mínimo 3 caracteres</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-telephone me-1"></i>Número de Contacto:
                                        </label>
                                        <input type="number" class="form-control" name="experiencias[0][numero]" 
                                               placeholder="Ej: 3001234567" min="1000000" required>
                                        <div class="form-text">Mínimo 7 dígitos</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botón para agregar nueva experiencia -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarExperiencia">
                                <i class="bi bi-plus-circle me-2"></i>Agregar Experiencia Laboral
                            </button>
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
    
    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let experienciaCounter = <?php echo !empty($datos_existentes) && is_array($datos_existentes) ? count($datos_existentes) : 1; ?>;

    document.getElementById('btnAgregarExperiencia').addEventListener('click', function() {
        const container = document.getElementById('experiencias-container');
        const nuevaExperiencia = document.createElement('div');
        nuevaExperiencia.className = 'experiencia-item border rounded p-3 mb-3';
        nuevaExperiencia.setAttribute('data-index', experienciaCounter);
        
        nuevaExperiencia.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                    <i class="bi bi-briefcase me-2"></i>Experiencia Laboral #${experienciaCounter + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExperiencia(this)">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </button>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-building me-1"></i>Empresa:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][empresa]" 
                           placeholder="Ej: Empresa ABC S.A." minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-clock me-1"></i>Tiempo Laborado:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][tiempo]" 
                           placeholder="Ej: 2 años, 6 meses" minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-person-badge me-1"></i>Cargo Desempeñado:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][cargo]" 
                           placeholder="Ej: Gerente de Ventas" minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-cash me-1"></i>Salario:
                    </label>
                    <div class="currency-input currency-tooltip">
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="salario_${experienciaCounter}" name="experiencias[${experienciaCounter}][salario]" 
                                   placeholder="1.500.000" required>
                        </div>
                    </div>
                    <div class="form-text">Salario mensual en pesos colombianos</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-box-arrow-right me-1"></i>Motivo de Retiro:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][retiro]" 
                           placeholder="Ej: Renuncia voluntaria" minlength="5" required>
                    <div class="form-text">Mínimo 5 caracteres</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="bi bi-chat-quote me-1"></i>Concepto Emitido:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][concepto]" 
                           placeholder="Ej: Excelente trabajador" minlength="5" required>
                    <div class="form-text">Mínimo 5 caracteres</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="bi bi-person me-1"></i>Nombre del Contacto:
                    </label>
                    <input type="text" class="form-control" name="experiencias[${experienciaCounter}][nombre]" 
                           placeholder="Ej: Juan Pérez" minlength="3" required>
                    <div class="form-text">Mínimo 3 caracteres</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="bi bi-telephone me-1"></i>Número de Contacto:
                    </label>
                    <input type="number" class="form-control" name="experiencias[${experienciaCounter}][numero]" 
                           placeholder="Ej: 3001234567" min="1000000" required>
                    <div class="form-text">Mínimo 7 dígitos</div>
                </div>
            </div>
        `;
        
        container.appendChild(nuevaExperiencia);
        
        // Inicializar Cleave.js para el nuevo campo de salario
        inicializarCleave(`salario_${experienciaCounter}`);
        
        experienciaCounter++;
        
        // Scroll suave hacia la nueva experiencia
        nuevaExperiencia.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    
    function removeExperiencia(boton) {
        const experiencia = boton.closest('.experiencia-item');
        const container = document.getElementById('experiencias-container');
        const experiencias = container.querySelectorAll('.experiencia-item');
        
        // No permitir eliminar si solo queda una experiencia
        if (experiencias.length <= 1) {
            alert('Debe mantener al menos una experiencia laboral.');
            return;
        }
        
        if (confirm('¿Está seguro de que desea eliminar esta experiencia laboral?')) {
            experiencia.remove();
            actualizarNumeracion();
        }
    }
    
    function actualizarNumeracion() {
        const experiencias = document.querySelectorAll('.experiencia-item');
        experiencias.forEach((experiencia, index) => {
            const titulo = experiencia.querySelector('h6');
            titulo.innerHTML = `<i class="bi bi-briefcase me-2"></i>Experiencia Laboral #${index + 1}`;
            
            // Actualizar los nombres de los campos
            const inputs = experiencia.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/experiencias\[\d+\]/, `experiencias[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }
    </script>
</body>
</html>
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