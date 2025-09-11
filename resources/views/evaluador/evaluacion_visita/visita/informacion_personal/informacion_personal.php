<?php
/**
 * informacion_personal_refactor.php
 * Refactorized version of informacion_peronsal_fix.php
 *
 * Improvements included:
 * - CSRF protection (token generation + verification)
 * - Escaping helper for XSS prevention (esc())
 * - Environment-driven error display (APP_ENV)
 * - Cleaner session handling and authentication check
 * - Structured form rendering with helper functions
 * - Consolidated messages rendering
 * - Clear comments and separation of concerns
 *
 * NOTE: This file assumes InformacionPersonalController.php exists in the same folder.
 * The controller should use prepared statements / parameterized queries for DB operations.
 */

declare(strict_types=1);

// === Environment & Error reporting ===
$env = getenv('APP_ENV') ?: 'production';
if ($env === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // Production: do not display errors to users
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

ob_start();

// === Session handling & auth ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple helper for escaping output
function esc($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    // random_bytes availability check
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        // fallback
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$csrf_token = $_SESSION['csrf_token'];

// Check authentication (adjust session key according to your app)
if (empty($_SESSION['id_cedula'])) {
    // Not authenticated - redirect to login
    header('Location: ../../../../../public/login.php');
    exit();
}

// Include controller (must exist)
$controller_path = __DIR__ . '/InformacionPersonalController.php';
if (!file_exists($controller_path)) {
    // Graceful fallback: set an error message and show the page, controller actions will be disabled
    $_SESSION['error'] = 'Archivo InformacionPersonalController.php no encontrado. Contacte con el administrador.';
    $controller = null;
} else {
    require_once $controller_path;
    // Use fully qualified class if namespaced in controller
    if (class_exists('App\Controllers\InformacionPersonalController')) {
        $controllerClass = 'App\Controllers\InformacionPersonalController';
    } elseif (class_exists('InformacionPersonalController')) {
        $controllerClass = 'InformacionPersonalController';
    } else {
        $controllerClass = null;
        $_SESSION['error'] = 'Clase InformacionPersonalController no encontrada.';
    }
    if ($controllerClass) {
        // Use getInstance if provided by controller
        if (method_exists($controllerClass, 'getInstance')) {
            $controller = $controllerClass::getInstance();
        } else {
            // Try constructor
            $controller = new $controllerClass();
        }
    } else {
        $controller = null;
    }
}

// Helper to build select options safely
function renderOptions(array $options, $selected = null, string $valueKey = 'id', string $labelKey = 'nombre'): string {
    $html = "";
    foreach ($options as $opt) {
        $val = $opt[$valueKey] ?? $opt[0] ?? '';
        $label = $opt[$labelKey] ?? $opt[1] ?? (string)$val;
        $sel = ((string)$val === (string)$selected) ? ' selected' : '';
        $html .= '<option value="'.esc($val).'"'.$sel.'>'.esc($label).'</option>' . PHP_EOL;
    }
    return $html;
}

// Default variables (ensure the template doesn't break if controller doesn't provide them)
$opciones = $controller->getOptions() ?? [
    'tipo_documentos' => [
        ['id' => 1, 'nombre' => 'Cédula de Ciudadanía'],
        ['id' => 2, 'nombre' => 'Tarjeta de Identidad'],
    ],
    'municipios' => [
        ['id_municipio' => 101, 'municipio' => 'Bogotá'],
        ['id_municipio' => 102, 'municipio' => 'Medellín'],
    ],
    'rh' => [
        ['id' => 1, 'nombre' => 'O+'],
        ['id' => 2, 'nombre' => 'A+'],
    ],
    'estaturas' => [],
    'pesos' => [],
    'estado_civil' => [],
    'estratos' => []
];

// Load existing data if controller provides it
$datos_existentes = $controller && method_exists($controller, 'getDatos') ? $controller->getDatos() : null;
$id_cedula = $_SESSION['id_cedula'] ?? ($datos_existentes['id_cedula'] ?? '');

// === POST handling with CSRF check ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF validation
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], (string)$postedToken)) {
        $_SESSION['error'] = 'Token CSRF inválido. Intenta recargar la página e inténtalo de nuevo.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    // Sanitize and validate using controller if available
    try {
        if ($controller && method_exists($controller, 'sanitizarDatos') && method_exists($controller, 'validarDatos')) {
            $datos = $controller->sanitizarDatos($_POST);
            $errores = $controller->validarDatos($datos);
            if (empty($errores)) {
                $resultado = $controller->guardar($datos);
                if ($resultado['success']) {
                    $_SESSION['success'] = $resultado['message'];
                    // Redirect to next step
                    header('Location: ../camara_comercio/camara_comercio.php');
                    exit();
                } else {
                    $_SESSION['error'] = $resultado['message'];
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errores);
            }
        } else {
            // Controller not available or missing methods
            $_SESSION['error'] = 'Operación no disponible: controlador no encontrado o incompleto.';
        }
    } catch (Throwable $t) {
        // Log and show a friendly error message
        error_log('Error guardando información personal: ' . $t->getMessage());
        $_SESSION['error'] = 'Ocurrió un error al procesar la solicitud. Intente de nuevo más tarde.';
    }

    // After handling POST, redirect back to avoid resubmission on reload
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// === Prepare content for output (template) ===
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Visita Domiciliaria - Información Personal</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Small enhancements */
    .card.border-primary { border-width: 2px; }
    .progress-steps { display:flex; gap: .75rem; justify-content: space-between; }
    .progress-step { text-align: center; flex:1; }
    .progress-step .circle { width:40px; height:40px; border-radius:50%; display:inline-flex; justify-content:center; align-items:center; }
    .required:after { content:' *'; color: #dc3545; }
  </style>
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="card border-primary shadow-sm">
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <div>
        <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>VISITA DOMICILIARIA - INFORMACIÓN PERSONAL</h5>
      </div>
      <div class="text-end small">
        Usuario: <?php echo esc($_SESSION['username'] ?? 'N/A'); ?>
      </div>
    </div>

    <div class="card-body">
      <!-- Progress bar -->
      <div class="mb-4">
        <div class="progress" style="height:10px;">
          <div class="progress-bar bg-success" role="progressbar" style="width:40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="progress-steps mt-2">
          <div class="progress-step">
            <div class="circle bg-primary text-white"><i class="bi bi-1-circle"></i></div>
            <div class="small fw-bold text-primary">Paso 1</div>
            <div class="text-muted small">Datos Básicos</div>
          </div>
          <div class="progress-step">
            <div class="circle bg-success text-white"><i class="bi bi-2-circle"></i></div>
            <div class="small fw-bold text-success">Paso 2</div>
            <div class="text-muted small">Información Personal</div>
          </div>
          <div class="progress-step">
            <div class="circle border border-secondary text-secondary"><i class="bi bi-3-circle"></i></div>
            <div class="small">Paso 3</div>
            <div class="text-muted small">Cámara de Comercio</div>
          </div>
          <div class="progress-step">
            <div class="circle border border-secondary text-secondary"><i class="bi bi-4-circle"></i></div>
            <div class="small">Paso 4</div>
            <div class="text-muted small">Registro Fotográfico</div>
          </div>
          <div class="progress-step">
            <div class="circle border border-secondary text-secondary"><i class="bi bi-5-circle"></i></div>
            <div class="small">Paso 5</div>
            <div class="text-muted small">Ubicación</div>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo esc($error_message); ?></div>
      <?php endif; ?>

      <?php if (!empty($datos_existentes)): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Ya existe información registrada para esta cédula. Puede actualizar los datos.</div>
      <?php endif; ?>

      <!-- Header: logo and meta -->
      <div class="row mb-4 align-items-center">
        <div class="col-md-6">
          <img src="../../../../../public/images/logo.jpg" alt="Logotipo" class="img-fluid" style="max-width: 220px;">
        </div>
        <div class="col-md-6 text-end text-muted small">
          <div>Fecha: <?php echo esc(date('d/m/Y')); ?></div>
          <div>Cédula: <?php echo esc($id_cedula); ?></div>
        </div>
      </div>

      <!-- Form -->
      <form action="<?php echo esc($_SERVER['PHP_SELF']); ?>" method="POST" id="formInformacionPersonal" class="needs-validation" novalidate autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo esc($csrf_token); ?>">

        <div class="row g-3">
          <div class="col-md-4">
            <label for="id_cedula" class="form-label required"><i class="bi bi-card-text me-1"></i>Número de Documento</label>
            <input type="text" class="form-control" id="id_cedula" name="id_cedula" value="<?php echo esc($id_cedula); ?>" readonly>
            <div class="form-text">Documento de identidad</div>
          </div>

          <div class="col-md-4">
            <label for="id_tipo_documentos" class="form-label required"><i class="bi bi-card-list me-1"></i>Tipo de Documento</label>
            <select class="form-select" id="id_tipo_documentos" name="id_tipo_documentos" required>
              <option value="">Seleccione tipo</option>
              <?php echo renderOptions($opciones['tipo_documentos'], $datos_existentes['id_tipo_documentos'] ?? null, 'id', 'nombre'); ?>
            </select>
            <div class="invalid-feedback">Por favor seleccione el tipo de documento.</div>
          </div>

          <div class="col-md-4">
            <label for="cedula_expedida" class="form-label required"><i class="bi bi-geo-alt me-1"></i>Cédula expedida en</label>
            <select class="form-select" id="cedula_expedida" name="cedula_expedida" required>
              <option value="">Seleccione municipio</option>
              <?php echo renderOptions($opciones['municipios'], $datos_existentes['cedula_expedida'] ?? null, 'id_municipio', 'municipio'); ?>
            </select>
            <div class="invalid-feedback">Por favor seleccione el municipio de expedición.</div>
          </div>

          <!-- Additional fields (nombres, apellidos, edad...) -->
          <div class="col-md-4">
            <label for="nombres" class="form-label required"><i class="bi bi-person me-1"></i>Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo esc($datos_existentes['nombres'] ?? ''); ?>" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
            <div class="invalid-feedback">Por favor ingrese nombres válidos (solo letras).</div>
          </div>

          <div class="col-md-4">
            <label for="apellidos" class="form-label required"><i class="bi bi-person me-1"></i>Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo esc($datos_existentes['apellidos'] ?? ''); ?>" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" maxlength="100">
            <div class="invalid-feedback">Por favor ingrese apellidos válidos (solo letras).</div>
          </div>

          <div class="col-md-4">
            <label for="edad" class="form-label required"><i class="bi bi-calendar me-1"></i>Edad</label>
            <input type="number" class="form-control" id="edad" name="edad" value="<?php echo esc($datos_existentes['edad'] ?? ''); ?>" required min="18" max="120">
            <div class="invalid-feedback">La edad debe estar entre 18 y 120 años.</div>
          </div>

          <!-- More fields... replicate as needed following the same pattern -->
          <div class="col-md-4">
            <label for="correo" class="form-label required"><i class="bi bi-envelope me-1"></i>Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo esc($datos_existentes['correo'] ?? ''); ?>" required maxlength="100">
            <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
          </div>

          <div class="col-12">
            <label for="observacion" class="form-label"><i class="bi bi-chat-text me-1"></i>Observaciones</label>
            <textarea class="form-control" id="observacion" name="observacion" rows="4" maxlength="1000" placeholder="Ingrese observaciones adicionales..."><?php echo esc($datos_existentes['observacion'] ?? ''); ?></textarea>
            <div class="form-text">Máximo 1000 caracteres</div>
          </div>
        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary btn-lg me-2">
            <i class="bi bi-check-circle me-2"></i><?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
          </button>
          <a href="../index.php" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left me-2"></i>Volver</a>
        </div>
      </form>
    </div>

    <div class="card-footer text-body-secondary small">
      <div class="d-flex justify-content-between">
        <span>© <?php echo date('Y'); ?> - Sistema de Visitas Domiciliarias</span>
        <span>Usuario: <?php echo esc($_SESSION['username'] ?? 'N/A'); ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Minimal JS for Bootstrap validation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (function () {
    'use strict'
    // Bootstrap custom validation
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
      .forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
  })()
</script>
</body>
</html>
