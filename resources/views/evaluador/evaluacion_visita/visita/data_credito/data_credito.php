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

require_once __DIR__ . '/DataCreditoController.php';
use App\Controllers\DataCreditoController;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = DataCreditoController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        
        // Persistir datos del formulario en caso de error
        $datos_formulario = $datos;
        
        if (isset($datos['reportado_centrales'])) {
            if ($datos['reportado_centrales'] == '0') {
            // No está reportado en centrales de riesgo
            $resultado = $controller->guardarSinReportes();
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../ingresos_mensuales/ingresos_mensuales.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
                // Está reportado, validar y guardar datos detallados
                $errores = $controller->validarDatos($datos);
                
                if (empty($errores)) {
                    $resultado = $controller->guardar($datos);
                    if ($resultado['success']) {
                        $_SESSION['success'] = $resultado['message'];
                        header('Location: ../ingresos_mensuales/ingresos_mensuales.php');
            exit();
                    } else {
                        $_SESSION['error'] = $resultado['message'];
                    }
                } else {
                    $_SESSION['error'] = implode('<br>', $errores);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en data_credito.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    $controller = DataCreditoController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Determinar el valor inicial del select
    $reportado_centrales_valor = '';
    if (isset($datos_formulario['reportado_centrales'])) {
        $reportado_centrales_valor = $datos_formulario['reportado_centrales'];
    } elseif (!empty($datos_existentes)) {
        // Si existe un registro con entidad = 'N/A', significa "No reportado"
        $reportado_centrales_valor = ($datos_existentes[0]['entidad'] == 'N/A') ? '0' : '1';
    }
    
} catch (Exception $e) {
    error_log("Error en data_credito.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Crédito - Sistema de Visitas Domiciliarias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.css">
<style>
        /* Dashboard Verde - Sidebar */
        .sidebar {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main Content */
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .content-header {
            background: white;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Step Indicator */
        .steps-horizontal { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 2rem; 
            width: 100%; 
            gap: 0.5rem; 
            overflow-x: auto;
            padding: 10px 0;
        }
        
        .step-horizontal { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            flex: 1; 
            position: relative; 
            min-width: 80px;
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
            background: #11998e; 
            border-color: #11998e; 
            color: #fff; 
            box-shadow: 0 0 0 5px rgba(17, 153, 142, 0.2); 
        }
        
        .step-horizontal.complete .step-icon { 
            background: #2ecc71; 
            border-color: #2ecc71; 
            color: #fff; 
        }
        
        .step-horizontal .step-title { 
            font-weight: bold; 
            font-size: 0.8rem; 
            margin-bottom: 0.2rem; 
            text-align: center;
        }
        
        .step-horizontal .step-description { 
            font-size: 0.7rem; 
            color: #888; 
            text-align: center; 
        }
        
        .step-horizontal.active .step-title, 
        .step-horizontal.active .step-description { 
            color: #11998e; 
        }
        
        .step-horizontal.complete .step-title, 
        .step-horizontal.complete .step-description { 
            color: #2ecc71; 
        }
        
        /* Campos dinámicos */
        .campos-reportes {
            display: none;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .campos-reportes.show {
            display: block;
            opacity: 1;
        }
        
        .reporte-item { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            background: #f8f9fa; 
            position: relative;
        }
        
        .reporte-item h6 { 
            color: #495057; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #dee2e6; 
            padding-bottom: 10px; 
        }
        
        .btn-remove-reporte { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
        }
        
        /* Estilos para campos monetarios */
        .currency-input {
            position: relative;
        }
        
        .currency-tooltip {
            position: absolute;
            top: -35px;
            left: 0;
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            z-index: 1000;
        }
        
        .currency-input:hover .currency-tooltip {
            opacity: 1;
        }
        
        .currency-input .form-control {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .currency-input .form-control:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
            background: white;
        }
        
        .currency-input .form-control.is-valid {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .currency-input .form-control.is-invalid {
            border-color: #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .steps-horizontal {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .step-horizontal {
                min-width: 60px;
            }
            
            .step-horizontal .step-title {
                font-size: 0.7rem;
            }
            
            .step-horizontal .step-description {
                font-size: 0.6rem;
            }
        }
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h5 class="text-white mb-4">
                        <i class="fas fa-shield-check me-2"></i>
                        Data Crédito
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-shield-check"></i>
                            Reportes Centrales
                        </a>
                        <a class="nav-link" href="../aportante/aportante.php">
                            <i class="fas fa-arrow-left"></i>
                            Volver Aportantes
                        </a>
                        <a class="nav-link" href="../ingresos_mensuales/ingresos_mensuales.php">
                            <i class="fas fa-arrow-right"></i>
                            Siguiente: Ingresos
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-shield-check me-2 text-success"></i>
                                Data Crédito - Reportes en Centrales de Riesgo
                            </h4>
                            <p class="text-muted mb-0">Información sobre reportes en centrales de riesgo</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y'); ?>
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-id-card me-1"></i>
                                <?php echo htmlspecialchars($id_cedula); ?>
                            </small>
                        </div>
                    </div>
        </div>
                
                <div class="p-4">
                    <!-- Indicador de pasos -->
                    <div class="card mb-4">
        <div class="card-body">
                            <div class="steps-horizontal">
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
                                    <div class="step-icon"><i class="fas fa-users"></i></div>
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
                                    <div class="step-icon"><i class="fas fa-box"></i></div>
                    <div class="step-title">Paso 9</div>
                    <div class="step-description">Inventario de Enseres</div>
                </div>
                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-bolt"></i></div>
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
                                    <div class="step-icon"><i class="fas fa-people-group"></i></div>
                    <div class="step-title">Paso 14</div>
                    <div class="step-description">Aportantes</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-shield-check"></i></div>
                    <div class="step-title">Paso 15</div>
                    <div class="step-description">Data Crédito</div>
                </div>
            </div>
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
                            <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($datos_existentes)): ?>
                <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                    Ya existe información de data crédito registrada para esta cédula. Puede actualizar los datos.
                </div>
            <?php endif; ?>
            
                    <!-- Formulario Principal -->
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST" id="formDataCredito" novalidate autocomplete="off">
                                <!-- Pregunta inicial -->
            <div class="row mb-4">
                <div class="col-md-6">
                                        <label for="reportado_centrales" class="form-label">
                                            <i class="fas fa-question-circle me-1"></i>¿Se encuentra reportado en centrales de riesgo?
                                        </label>
                                        <select class="form-select" id="reportado_centrales" name="reportado_centrales" required onchange="toggleCamposReportes()">
                                            <option value="">Seleccione una opción</option>
                                            <option value="0" <?php echo ($reportado_centrales_valor == '0') ? 'selected' : ''; ?>>No</option>
                                            <option value="1" <?php echo ($reportado_centrales_valor == '1') ? 'selected' : ''; ?>>Sí</option>
                                        </select>
                                        <div class="form-text">Seleccione "No" si no está reportado, o "Sí" para continuar con el formulario detallado.</div>
                                    </div>
                                </div>
                                
                                <!-- Campos dinámicos para reportes detallados -->
                                <div id="camposReportes" class="campos-reportes">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Complete la información detallada de los reportes en centrales de riesgo.
                                    </div>
                                    
                                    <div id="reportes-container">
                                        <!-- Reporte inicial -->
                                        <div class="reporte-item" data-reporte="0">
                                            <h6><i class="fas fa-shield-check me-2"></i>Reporte #1</h6>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="entidad_0" class="form-label">
                                                        <i class="fas fa-building me-1"></i>Entidad:
                                                    </label>
                                                    <input type="text" class="form-control" id="entidad_0" name="entidad[]" 
                                                           value="<?php echo !empty($datos_existentes) && $datos_existentes[0]['entidad'] != 'N/A' ? htmlspecialchars($datos_existentes[0]['entidad'] ?? '') : ''; ?>"
                                                           placeholder="Ej: Banco de Bogotá" minlength="3" required>
                                                    <div class="form-text">Mínimo 3 caracteres</div>
                                                </div>
                                                
                                                <div class="col-md-3 mb-3">
                                                    <label for="cuotas_0" class="form-label">
                                                        <i class="fas fa-calendar-check me-1"></i>N° Cuotas:
                                                    </label>
                                                    <input type="text" class="form-control" id="cuotas_0" name="cuotas[]" 
                                                           value="<?php echo !empty($datos_existentes) && $datos_existentes[0]['cuotas'] != 'N/A' ? htmlspecialchars($datos_existentes[0]['cuotas'] ?? '') : ''; ?>"
                                                           placeholder="0" required>
                                                    <div class="form-text">Número de cuotas pendientes</div>
                                                </div>
                                                
                                                <div class="col-md-3 mb-3">
                                                    <label for="pago_mensual_0" class="form-label">
                                                        <i class="fas fa-cash-stack me-1"></i>Pago Mensual:
                                                    </label>
                                                    <div class="currency-input">
                                                        <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="text" class="form-control" id="pago_mensual_0" name="pago_mensual[]" 
                                                                   value="<?php echo !empty($datos_existentes) && $datos_existentes[0]['pago_mensual'] != 'N/A' ? formatearValorMonetario($datos_existentes[0]['pago_mensual']) : ''; ?>"
                                                                   placeholder="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-text">Valor del pago mensual</div>
                                                </div>
                                                
                                                <div class="col-md-3 mb-3">
                                                    <label for="deuda_0" class="form-label">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Total Deuda:
                                                    </label>
                                                    <div class="currency-input">
                                                        <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="text" class="form-control" id="deuda_0" name="deuda[]" 
                                                                   value="<?php echo !empty($datos_existentes) && $datos_existentes[0]['deuda'] != 'N/A' ? formatearValorMonetario($datos_existentes[0]['deuda']) : ''; ?>"
                                                                   placeholder="0" required>
                                                        </div>
                </div>
                                                    <div class="form-text">Valor total de la deuda</div>
                    </div>
                </div>
            </div>
            
                                        <!-- Reportes adicionales si existen datos -->
                                        <?php if (!empty($datos_existentes) && count($datos_existentes) > 1): ?>
                                            <?php for ($i = 1; $i < count($datos_existentes); $i++): ?>
                                                <div class="reporte-item" data-reporte="<?php echo $i; ?>">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove-reporte" onclick="removeReporte(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <h6><i class="fas fa-shield-check me-2"></i>Reporte #<?php echo $i + 1; ?></h6>
                <div class="row">
                                                        <div class="col-md-3 mb-3">
                                                            <label for="entidad_<?php echo $i; ?>" class="form-label">
                                                                <i class="fas fa-building me-1"></i>Entidad:
                                                            </label>
                                                            <input type="text" class="form-control" id="entidad_<?php echo $i; ?>" name="entidad[]" 
                                                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['entidad']); ?>"
                                                                   placeholder="Ej: Banco de Bogotá" minlength="3" required>
                                                        </div>
                                                        
                                                        <div class="col-md-3 mb-3">
                                                            <label for="cuotas_<?php echo $i; ?>" class="form-label">
                                                                <i class="fas fa-calendar-check me-1"></i>N° Cuotas:
                                                            </label>
                                                            <input type="text" class="form-control" id="cuotas_<?php echo $i; ?>" name="cuotas[]" 
                                                                   value="<?php echo htmlspecialchars($datos_existentes[$i]['cuotas']); ?>"
                                                                   placeholder="0" required>
                                                        </div>
                                                        
                                                        <div class="col-md-3 mb-3">
                                                            <label for="pago_mensual_<?php echo $i; ?>" class="form-label">
                                                                <i class="fas fa-cash-stack me-1"></i>Pago Mensual:
                                                            </label>
                                                            <div class="currency-input">
                                                                <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="text" class="form-control" id="pago_mensual_<?php echo $i; ?>" name="pago_mensual[]" 
                                                                           value="<?php echo formatearValorMonetario($datos_existentes[$i]['pago_mensual']); ?>"
                                                                           placeholder="0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-3 mb-3">
                                                            <label for="deuda_<?php echo $i; ?>" class="form-label">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Total Deuda:
                        </label>
                                                            <div class="currency-input">
                                                                <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="text" class="form-control" id="deuda_<?php echo $i; ?>" name="deuda[]" 
                                                                           value="<?php echo formatearValorMonetario($datos_existentes[$i]['deuda']); ?>"
                                                                           placeholder="0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn btn-success btn-lg me-2" id="btnAgregarReporte">
                                                <i class="fas fa-plus-circle me-2"></i>Agregar Otro Reporte
                                            </button>
                                        </div>
                    </div>
                </div>
                
                                <!-- Botones de acción -->
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <?php echo !empty($datos_existentes) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../aportante/aportante.php" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        // Variables globales
        let reporteCounter = <?php echo !empty($datos_existentes) ? count($datos_existentes) : 1; ?>;
        let cleaveInstances = {};
        
        // Función para inicializar Cleave.js en un campo
        function inicializarCleave(campoId) {
            if (cleaveInstances[campoId]) {
                cleaveInstances[campoId].destroy();
            }
            
            cleaveInstances[campoId] = new Cleave(campoId, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                prefix: '$ ',
                onValueChanged: function(e) {
                    const input = e.target;
                    if (e.target.rawValue && e.target.rawValue !== '0') {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    } else {
                        input.classList.remove('is-valid');
                        input.classList.add('is-invalid');
                    }
                }
            });
        }
        
        // Función para validar formato monetario
        function validarFormatoMonetario(valor) {
            const patron = /^\$\s?[\d.,]+$/;
            return patron.test(valor);
        }
        
        // Función para formatear valor para envío
        function formatearValorParaEnvio(valor) {
            if (!valor) return '0';
            return valor.replace(/\$\s?/g, '').replace(/\./g, '').replace(',', '.');
        }
        
        // Función para mostrar/ocultar campos de reportes
        function toggleCamposReportes() {
            const reportadoSelect = document.getElementById('reportado_centrales');
            const camposReportesDiv = document.getElementById('camposReportes');
            
            if (reportadoSelect.value === '1') {
                camposReportesDiv.classList.add('show');
            } else {
                camposReportesDiv.classList.remove('show');
                if (reportadoSelect.value === '0') {
                    document.querySelectorAll('#reportes-container input').forEach(input => {
                        input.value = '';
                        input.classList.remove('is-valid', 'is-invalid');
                    });
                }
            }
        }
        
        // Función para remover reporte
        function removeReporte(button) {
            const reporteItem = button.closest('.reporte-item');
            reporteItem.remove();
            
            const reportes = document.querySelectorAll('.reporte-item');
            reportes.forEach((reporte, index) => {
                const titulo = reporte.querySelector('h6');
                titulo.innerHTML = `<i class="fas fa-shield-check me-2"></i>Reporte #${index + 1}`;
            });
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                toggleCamposReportes();
                
                document.querySelectorAll('input[id^="pago_mensual_"], input[id^="deuda_"]').forEach(function(input) {
                    if (input.value && input.value !== '') {
                        input.classList.add('is-valid');
                    }
                    inicializarCleave('#' + input.id);
                });
            }, 100);
            
            document.getElementById('btnAgregarReporte').addEventListener('click', function() {
                const container = document.getElementById('reportes-container');
                const nuevoReporte = document.createElement('div');
                nuevoReporte.className = 'reporte-item';
                nuevoReporte.setAttribute('data-reporte', reporteCounter);
                
                nuevoReporte.innerHTML = `
                    <button type="button" class="btn btn-danger btn-sm btn-remove-reporte" onclick="removeReporte(this)">
                        <i class="fas fa-times"></i>
                    </button>
                    <h6><i class="fas fa-shield-check me-2"></i>Reporte #${reporteCounter + 1}</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="entidad_${reporteCounter}" class="form-label">
                                <i class="fas fa-building me-1"></i>Entidad:
                            </label>
                            <input type="text" class="form-control" id="entidad_${reporteCounter}" name="entidad[]" 
                                   placeholder="Ej: Banco de Bogotá" minlength="3" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cuotas_${reporteCounter}" class="form-label">
                                <i class="fas fa-calendar-check me-1"></i>N° Cuotas:
                            </label>
                            <input type="text" class="form-control" id="cuotas_${reporteCounter}" name="cuotas[]" 
                                   placeholder="0" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pago_mensual_${reporteCounter}" class="form-label">
                                <i class="fas fa-cash-stack me-1"></i>Pago Mensual:
                            </label>
                            <div class="currency-input">
                                <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="pago_mensual_${reporteCounter}" name="pago_mensual[]" 
                                           placeholder="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="deuda_${reporteCounter}" class="form-label">
                                <i class="fas fa-exclamation-triangle me-1"></i>Total Deuda:
                            </label>
                            <div class="currency-input">
                                <div class="currency-tooltip">Formato: $ 1.500.000</div>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="deuda_${reporteCounter}" name="deuda[]" 
                                           placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.appendChild(nuevoReporte);
                inicializarCleave(`#pago_mensual_${reporteCounter}`);
                inicializarCleave(`#deuda_${reporteCounter}`);
                reporteCounter++;
            });
            
            document.getElementById('formDataCredito').addEventListener('submit', function(e) {
                const reportadoSelect = document.getElementById('reportado_centrales');
                
                if (!reportadoSelect.value) {
                    e.preventDefault();
                    alert('Por favor seleccione si está reportado en centrales de riesgo.');
                    return;
                }
                
                if (reportadoSelect.value === '1') {
                    let hayErrores = false;
                    
                    document.querySelectorAll('input[id^="pago_mensual_"], input[id^="deuda_"]').forEach(function(input) {
                        if (input.value && !validarFormatoMonetario(input.value)) {
                            input.classList.add('is-invalid');
                            hayErrores = true;
                        }
                    });
                    
                    if (hayErrores) {
                        e.preventDefault();
                        alert('Por favor corrija los errores en los campos monetarios.');
                        return;
                    }
                    
                    document.querySelectorAll('input[id^="pago_mensual_"], input[id^="deuda_"]').forEach(function(input) {
                        if (input.value) {
                            input.value = formatearValorParaEnvio(input.value);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>