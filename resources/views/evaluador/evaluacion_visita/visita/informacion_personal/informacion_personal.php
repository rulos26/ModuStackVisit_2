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
require_once __DIR__ . '/InformacionPersonalController.php';

use App\Controllers\InformacionPersonalController;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = InformacionPersonalController::getInstance();

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
                header('Location: ../camara_comercio/camara_comercio.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en informacion_personal.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

try {
    // Obtener instancia del controlador
    $controller = InformacionPersonalController::getInstance();

    // Obtener datos existentes si los hay
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);

    // Obtener opciones para los select boxes
    $opciones = [
        'tipo_documentos' => $controller->obtenerOpciones('tipo_documentos'),
        'municipios' => $controller->obtenerOpciones('municipios'),
        'rh' => $controller->obtenerOpciones('rh'),
        'estaturas' => $controller->obtenerOpciones('estaturas'),
        'pesos' => $controller->obtenerOpciones('pesos'),
        'estado_civil' => $controller->obtenerOpciones('estado_civil'),
        'estratos' => $controller->obtenerOpciones('estratos')
    ];
} catch (Exception $e) {
    error_log("Error en informacion_personal.php: " . $e->getMessage());
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
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid px-2">
        <div class="card mt-4 w-100" style="max-width:100%; border-radius: 0;">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-fill me-2"></i>
                    VISITA DOMICILIARÍA - INFORMACIÓN PERSONAL
                </h5>
            </div>
            <div class="card-body">
                <!-- Indicador de pasos -->
                <div class="steps-horizontal mb-4">
                    <div class="step-horizontal active">
                        <div class="step-icon"><i class="fas fa-id-card"></i></div>
                        <div class="step-title">Paso 2</div>
                        <div class="step-description">Información Personal</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-building"></i></div>
                        <div class="step-title">Paso 3</div>
                        <div class="step-description">Cámara de Comercio</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-camera"></i></div>
                        <div class="step-title">Paso 4</div>
                        <div class="step-description">Registro Fotográfico</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="step-title">Paso 5</div>
                        <div class="step-description">Ubicación</div>
                    </div>
                    <div class="step-horizontal">
                        <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                        <div class="step-title">Paso 6</div>
                        <div class="step-description">Finalización</div>
                    </div>
                </div>

                <!-- Controles de navegación -->
                <div class="controls text-center mb-4">
                    <a href="../index.php" class="btn btn-secondary me-2">
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
                    <strong>Información importante:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios y deben ser completados antes de continuar.
                </div>

                <form action="" method="POST" id="formInformacionPersonal" novalidate autocomplete="off">
                    <!-- Fila 1: Documento y Tipo -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_cedula" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Número de Documento:
                            </label>
                            <input type="number" class="form-control" id="id_cedula" name="id_cedula"
                                value="<?php echo htmlspecialchars($id_cedula); ?>" readonly>
                            <div class="form-text">Documento de identidad</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_tipo_documentos" class="form-label required-field">
                                <i class="bi bi-card-list me-1"></i>Tipo de Documento:
                            </label>
                            <select class="form-select" id="id_tipo_documentos" name="id_tipo_documentos" required>
                                <option value="">Seleccione tipo de documento</option>
                                <?php foreach ($opciones['tipo_documentos'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_tipo_documentos'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el tipo de documento.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="cedula_expedida" class="form-label required-field">
                                <i class="bi bi-geo-alt me-1"></i>Cédula expedida en:
                            </label>
                            <select class="form-select" id="cedula_expedida" name="cedula_expedida" required>
                                <option value="">Seleccione municipio</option>
                                <?php foreach ($opciones['municipios'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['cedula_expedida'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['municipio']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el municipio de expedición.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="nombres" class="form-label required-field">
                                <i class="bi bi-person me-1"></i>Nombres:
                            </label>
                            <input type="text" class="form-control" id="nombres" name="nombres"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['nombres'] ?? '') : ''; ?>"
                                required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese nombres válidos (solo letras).</div>
                        </div>
                    </div>
                    <!-- Fila 2: Apellidos, Edad, Fecha y Lugar -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="apellidos" class="form-label required-field">
                                <i class="bi bi-person me-1"></i>Apellidos:
                            </label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['apellidos'] ?? '') : ''; ?>"
                                required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese apellidos válidos (solo letras).</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="edad" class="form-label required-field">
                                <i class="bi bi-calendar me-1"></i>Edad:
                            </label>
                            <input type="number" class="form-control" id="edad" name="edad"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['edad'] ?? '') : ''; ?>"
                                required min="18" max="120">
                            <div class="invalid-feedback">La edad debe estar entre 18 y 120 años.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="fecha_expedicion" class="form-label required-field">
                                <i class="bi bi-calendar-date me-1"></i>Fecha de Expedición:
                            </label>
                            <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['fecha_expedicion'] ?? '') : ''; ?>"
                                required max="<?php echo date('Y-m-d'); ?>">
                            <div class="invalid-feedback">Por favor ingrese una fecha válida.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="lugar_nacimiento" class="form-label required-field">
                                <i class="bi bi-geo-alt me-1"></i>Lugar de Nacimiento:
                            </label>
                            <select class="form-select" id="lugar_nacimiento" name="lugar_nacimiento" required>
                                <option value="">Seleccione municipio</option>
                                <?php foreach ($opciones['municipios'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['lugar_nacimiento'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['municipio']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el lugar de nacimiento.</div>
                        </div>
                    </div>
                    <!-- Fila 3: Teléfonos y RH -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="celular_1" class="form-label required-field">
                                <i class="bi bi-phone me-1"></i>Celular 1:
                            </label>
                            <input type="tel" class="form-control" id="celular_1" name="celular_1"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_1'] ?? '') : ''; ?>"
                                required pattern="^\+?\d{1,3}\s?\(?\d{2,4}\)?[\s-]?\d{3,4}[\s-]?\d{4}$"
                                placeholder="+1 (997) 998-9661">
                            <div class="invalid-feedback">
                                Ingrese un número válido. Ejemplo: +1 (997) 998-9661 o 3001234567
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="celular_2" class="form-label">
                                <i class="bi bi-phone me-1"></i>Celular 2:
                            </label>
                            <input type="tel" class="form-control" id="celular_2" name="celular_2"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['celular_2'] ?? '') : ''; ?>"
                                pattern="^\+?\d{1,3}\s?\(?\d{2,4}\)?[\s-]?\d{3,4}[\s-]?\d{4}$"
                                placeholder="+1 (997) 998-9661">
                            <div class="invalid-feedback">
                                Ingrese un número válido. Ejemplo: +1 (997) 998-9661 o 3001234567
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone me-1"></i>Teléfono:
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['telefono'] ?? '') : ''; ?>"
                                pattern="^\+?\d{1,3}\s?\(?\d{2,4}\)?[\s-]?\d{3,4}[\s-]?\d{4}$"
                                placeholder="+1 (436) 685-5062">
                            <div class="invalid-feedback">
                                Ingrese un número válido. Ejemplo: +1 (436) 685-5062 o 1234567
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_rh" class="form-label required-field">
                                <i class="bi bi-droplet me-1"></i>Tipo de RH:
                            </label>
                            <select class="form-select" id="id_rh" name="id_rh" required>
                                <option value="">Seleccione tipo de sangre</option>
                                <?php foreach ($opciones['rh'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_rh'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el tipo de sangre.</div>
                        </div>
                    </div>
                    <!-- Fila 4: Estatura, Peso, Estado Civil y Hace Cuánto -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_estatura" class="form-label required-field">
                                <i class="bi bi-arrows-vertical me-1"></i>Estatura:
                            </label>
                            <select class="form-select" id="id_estatura" name="id_estatura" required>
                                <option value="">Seleccione estatura</option>
                                <?php foreach ($opciones['estaturas'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_estatura'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione la estatura.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="peso_kg" class="form-label required-field">
                                <i class="bi bi-weight me-1"></i>Peso (kg):
                            </label>
                            <select class="form-select" id="peso_kg" name="peso_kg" required>
                                <option value="">Seleccione peso</option>
                                <?php foreach ($opciones['pesos'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['peso_kg'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el peso.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_estado_civil" class="form-label required-field">
                                <i class="bi bi-heart me-1"></i>Estado Civil:
                            </label>
                            <select class="form-select" id="id_estado_civil" name="id_estado_civil" required>
                                <option value="">Seleccione estado civil</option>
                                <?php foreach ($opciones['estado_civil'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_estado_civil'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado civil.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="hacer_cuanto" class="form-label">
                                <i class="bi bi-clock me-1"></i>Hace cuánto tiempo:
                            </label>
                            <input type="number" class="form-control" id="hacer_cuanto" name="hacer_cuanto"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['hacer_cuanto'] ?? '') : ''; ?>"
                                min="0" max="50" placeholder="Años">
                            <div class="form-text">Años en el estado civil actual</div>
                        </div>
                        </div>

                    <!-- Fila 5: Número de Hijos, Dirección, Ciudad y Localidad -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="numero_hijos" class="form-label">
                                <i class="bi bi-people me-1"></i>Número de Hijos:
                            </label>
                            <input type="number" class="form-control" id="numero_hijos" name="numero_hijos"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['numero_hijos'] ?? '') : ''; ?>"
                                min="0" max="20">
                            <div class="invalid-feedback">El número de hijos debe estar entre 0 y 20.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="direccion" class="form-label required-field">
                                <i class="bi bi-geo-alt me-1"></i>Dirección:
                            </label>
                            <input type="text" class="form-control" id="direccion" name="direccion"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['direccion'] ?? '') : ''; ?>"
                                required maxlength="200">
                            <div class="invalid-feedback">Por favor ingrese la dirección.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_ciudad" class="form-label required-field">
                                <i class="bi bi-building me-1"></i>Ciudad:
                            </label>
                            <select class="form-select" id="id_ciudad" name="id_ciudad" required>
                                <option value="">Seleccione ciudad</option>
                                <?php foreach ($opciones['municipios'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id_municipio']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_ciudad'] == $opcion['id_municipio']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['municipio']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione la ciudad.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="localidad" class="form-label required-field">
                                <i class="bi bi-geo-alt me-1"></i>Localidad:
                            </label>
                            <input type="text" class="form-control" id="localidad" name="localidad"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['localidad'] ?? '') : ''; ?>"
                                required maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese la localidad.</div>
                        </div>
                        </div>

                    <!-- Fila 6: Barrio, Estrato, Correo y Cargo -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="barrio" class="form-label required-field">
                                <i class="bi bi-house me-1"></i>Barrio:
                            </label>
                            <input type="text" class="form-control" id="barrio" name="barrio"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['barrio'] ?? '') : ''; ?>"
                                required maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese el barrio.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="id_estrato" class="form-label required-field">
                                <i class="bi bi-layers me-1"></i>Estrato:
                            </label>
                            <select class="form-select" id="id_estrato" name="id_estrato" required>
                                <option value="">Seleccione estrato</option>
                                <?php foreach ($opciones['estratos'] as $opcion): ?>
                                    <option value="<?php echo htmlspecialchars($opcion['id']); ?>"
                                        <?php echo ($datos_existentes && $datos_existentes['id_estrato'] == $opcion['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opcion['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estrato.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="correo" class="form-label required-field">
                                <i class="bi bi-envelope me-1"></i>Correo Electrónico:
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['correo'] ?? '') : ''; ?>"
                                required maxlength="100">
                            <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="cargo" class="form-label">
                                <i class="bi bi-briefcase me-1"></i>Cargo:
                            </label>
                            <input type="text" class="form-control" id="cargo" name="cargo"
                                value="<?php echo $datos_existentes ? htmlspecialchars($datos_existentes['cargo'] ?? '') : ''; ?>"
                                maxlength="100">
                            <div class="form-text">Cargo o profesión actual</div>
                        </div>
                    </div>

                    <!-- Fila 7: Campos booleanos -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="tiene_multa_simit" class="form-label required-field">
                                <i class="bi bi-exclamation-triangle me-1"></i>Tiene Multa en SIMIT:
                            </label>
                            <select class="form-select" id="tiene_multa_simit" name="tiene_multa_simit" required>
                                <option value="">Seleccione una opción</option>
                                <option value="1" <?php echo ($datos_existentes && $datos_existentes['tiene_multa_simit'] == '1') ? 'selected' : ''; ?>>Sí</option>
                                <option value="0" <?php echo ($datos_existentes && $datos_existentes['tiene_multa_simit'] == '0') ? 'selected' : ''; ?>>No</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione si tiene multa en SIMIT.</div>
                            <div class="valid-feedback">Campo completado correctamente.</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="tiene_tarjeta_militar" class="form-label required-field">
                                <i class="bi bi-shield-check me-1"></i>Tiene Tarjeta Militar:
                            </label>
                            <select class="form-select" id="tiene_tarjeta_militar" name="tiene_tarjeta_militar" required>
                                <option value="">Seleccione una opción</option>
                                <option value="1" <?php echo ($datos_existentes && $datos_existentes['tiene_tarjeta_militar'] == '1') ? 'selected' : ''; ?>>Sí</option>
                                <option value="0" <?php echo ($datos_existentes && $datos_existentes['tiene_tarjeta_militar'] == '0') ? 'selected' : ''; ?>>No</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione si tiene tarjeta militar.</div>
                            <div class="valid-feedback">Campo completado correctamente.</div>
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
                            <a href="../index.php" class="btn btn-secondary btn-lg">
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
            const form = document.getElementById('formInformacionPersonal');
            const camposObligatorios = [
                'id_tipo_documentos', 'cedula_expedida', 'nombres', 'apellidos', 'edad',
                'fecha_expedicion', 'lugar_nacimiento', 'celular_1', 'id_rh', 'id_estatura',
                'peso_kg', 'id_estado_civil', 'direccion', 'id_ciudad', 'localidad', 'barrio',
                'id_estrato', 'correo', 'tiene_multa_simit', 'tiene_tarjeta_militar'
            ];
            
            // Función para validar un campo
            function validarCampo(campo) {
                const elemento = document.getElementById(campo);
                if (!elemento) return true;
                
                const valor = elemento.value.trim();
                let esValido = true;
                
                // Validación especial para campos booleanos
                if (campo === 'tiene_multa_simit' || campo === 'tiene_tarjeta_militar') {
                    esValido = valor !== '';
                } else {
                    esValido = valor !== '' && valor !== '0';
                }
                
                // Aplicar clases de validación
                if (esValido) {
                    elemento.classList.remove('is-invalid');
                    elemento.classList.add('is-valid');
                } else {
                    elemento.classList.remove('is-valid');
                    elemento.classList.add('is-invalid');
                }
                
                return esValido;
            }
            
            // Validar todos los campos obligatorios
            function validarFormulario() {
                let esValido = true;
                camposObligatorios.forEach(campo => {
                    if (!validarCampo(campo)) {
                        esValido = false;
                    }
                });
                return esValido;
            }
            
            // Agregar event listeners a los campos
            camposObligatorios.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    elemento.addEventListener('blur', () => validarCampo(campo));
                    elemento.addEventListener('change', () => validarCampo(campo));
                }
            });
            
            // Validar formulario al enviar
            form.addEventListener('submit', function(e) {
                if (!validarFormulario()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Mostrar mensaje de error
                    const mensajeError = document.createElement('div');
                    mensajeError.className = 'alert alert-danger mt-3';
                    mensajeError.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Por favor complete todos los campos obligatorios antes de continuar.';
                    
                    // Remover mensaje anterior si existe
                    const mensajeAnterior = form.querySelector('.alert-danger');
                    if (mensajeAnterior) {
                        mensajeAnterior.remove();
                    }
                    
                    form.appendChild(mensajeError);
                    
                    // Scroll al primer campo inválido
                    const primerCampoInvalido = form.querySelector('.is-invalid');
                    if (primerCampoInvalido) {
                        primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        primerCampoInvalido.focus();
                    }
                }
                
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
    <title>Información Personal - Dashboard Evaluador</title>
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
        .logo-empresa {
            max-width: 200px;
            height: auto;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
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
                            <h1 class="h3 mb-0">Información Personal</h1>
                            <p class="text-muted mb-0">Formulario de datos personales para evaluación</p>
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