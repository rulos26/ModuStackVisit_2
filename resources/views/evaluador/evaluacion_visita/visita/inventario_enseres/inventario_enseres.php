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
require_once __DIR__ . '/InventarioEnseresController.php';

use App\Controllers\InventarioEnseresController;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InventarioEnseresController::getInstance();

        // Sanitizar y validar datos de entrada
        $datos = $controller->sanitizarDatos($_POST);

        // Validar datos
        $errores = $controller->validarDatos($datos);

        if (empty($errores)) {
            // Intentar guardar los datos
            $resultado = $controller->guardar($datos);

            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];

                // Siempre redirigir a la siguiente pantalla después de guardar/actualizar exitosamente
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
    // Obtener instancia del controlador
    $controller = InventarioEnseresController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);

    // Obtener opciones para los select boxes
    $opciones = [
        'parametros' => $controller->obtenerOpciones('parametro')
    ];
} catch (Exception $e) {
    error_log("Error en inventario_enseres.php: " . $e->getMessage());
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
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
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
                        <div class="step-icon"><i class="fas fa-id-card"></i></div>
                        <div class="step-title">Paso 1</div>
                        <div class="step-description">Información Personal</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-building"></i></div>
                        <div class="step-title">Paso 2</div>
                        <div class="step-description">Cámara de Comercio</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                        <div class="step-title">Paso 3</div>
                        <div class="step-description">Salud</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-users"></i></div>
                        <div class="step-title">Paso 4</div>
                        <div class="step-description">Composición Familiar</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-heart"></i></div>
                        <div class="step-title">Paso 5</div>
                        <div class="step-description">Información Pareja</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-home"></i></div>
                        <div class="step-title">Paso 6</div>
                        <div class="step-description">Tipo de Vivienda</div>
                    </div>
                    <div class="step-horizontal complete">
                        <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                        <div class="step-title">Paso 7</div>
                        <div class="step-description">Estado de Vivienda</div>
                    </div>
                    <div class="step-horizontal active">
                        <div class="step-icon"><i class="fas fa-box-seam"></i></div>
                        <div class="step-title">Paso 8</div>
                        <div class="step-description">Inventario de Enseres</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-camera"></i></div>
                        <div class="step-title">Paso 9</div>
                        <div class="step-description">Registro Fotográfico</div>
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

                <div class="row mb-4">
                    <div class="col-12 text-end">
                        <div class="text-muted">
                            <small>Fecha: <?php echo date('d/m/Y'); ?></small><br>
                            <small>Cédula: <?php echo htmlspecialchars($id_cedula); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Nota informativa sobre campos obligatorios -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Información importante:</strong> Complete la información del inventario de enseres del hogar. Todos los campos son opcionales.
                </div>

                <form action="" method="POST" id="formInventarioEnseres" novalidate autocomplete="off">
                    <!-- Fila 1: Televisores, DVDs, Teatros en Casa -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="televisor_cant" class="form-label">
                                <i class="bi bi-tv me-1"></i>Televisores:
                            </label>
                            <select class="form-select" id="televisor_cant" name="televisor_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['televisor_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="dvd_cant" class="form-label">
                                <i class="bi bi-disc me-1"></i>DVDs:
                            </label>
                            <select class="form-select" id="dvd_cant" name="dvd_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['dvd_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="teatro_casa_cant" class="form-label">
                                <i class="bi bi-speaker me-1"></i>Teatros en Casa:
                            </label>
                            <select class="form-select" id="teatro_casa_cant" name="teatro_casa_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['teatro_casa_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 2: Equipos de Sonido, Computadores, Impresoras -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="equipo_sonido_cant" class="form-label">
                                <i class="bi bi-music-note me-1"></i>Equipos de Sonido:
                            </label>
                            <select class="form-select" id="equipo_sonido_cant" name="equipo_sonido_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['equipo_sonido_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="computador_cant" class="form-label">
                                <i class="bi bi-laptop me-1"></i>Computadores:
                            </label>
                            <select class="form-select" id="computador_cant" name="computador_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['computador_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="impresora_cant" class="form-label">
                                <i class="bi bi-printer me-1"></i>Impresoras:
                            </label>
                            <select class="form-select" id="impresora_cant" name="impresora_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['impresora_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 3: Dispositivos Móviles, Estufas, Neveras -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="movil_cant" class="form-label">
                                <i class="bi bi-phone me-1"></i>Dispositivos Móviles:
                            </label>
                            <select class="form-select" id="movil_cant" name="movil_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['movil_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="estufa_cant" class="form-label">
                                <i class="bi bi-fire me-1"></i>Estufas:
                            </label>
                            <select class="form-select" id="estufa_cant" name="estufa_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['estufa_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="nevera_cant" class="form-label">
                                <i class="bi bi-snow me-1"></i>Neveras:
                            </label>
                            <select class="form-select" id="nevera_cant" name="nevera_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['nevera_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 4: Lavadoras, Microondas, Motos -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="lavadora_cant" class="form-label">
                                <i class="bi bi-water me-1"></i>Lavadoras:
                            </label>
                            <select class="form-select" id="lavadora_cant" name="lavadora_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['lavadora_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="microondas_cant" class="form-label">
                                <i class="bi bi-lightning me-1"></i>Microondas:
                            </label>
                            <select class="form-select" id="microondas_cant" name="microondas_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['microondas_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="moto_cant" class="form-label">
                                <i class="bi bi-bicycle me-1"></i>Motos:
                            </label>
                            <select class="form-select" id="moto_cant" name="moto_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['moto_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 5: Carros -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="carro_cant" class="form-label">
                                <i class="bi bi-car-front me-1"></i>Carros:
                            </label>
                            <select class="form-select" id="carro_cant" name="carro_cant">
                                <option value="">Seleccione</option>
                                <?php foreach ($opciones['parametros'] as $parametro): ?>
                                    <option value="<?php echo htmlspecialchars($parametro['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['carro_cant'] == $parametro['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parametro['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Observaciones ocupa todo el ancho -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <label for="observacion" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Observaciones:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" rows="4"
                                maxlength="1000" placeholder="Ingrese observaciones adicionales..."><?php echo $datos_existentes ? htmlspecialchars($datos_existentes['observacion'] ?? '') : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres</div>
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
    <!-- Solo Bootstrap JS, no rutas locales para evitar errores de MIME -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación adicional para campos obligatorios
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formInventarioEnseres');
            
            // Validar formulario al enviar
            form.addEventListener('submit', function(e) {
                // No hay campos obligatorios en este formulario
                form.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>
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
    <title>Inventario de Enseres - Dashboard Evaluador</title>
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
                            <h1 class="h3 mb-0">Inventario de Enseres</h1>
                            <p class="text-muted mb-0">Formulario de inventario de enseres del hogar</p>
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