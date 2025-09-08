
<!-- Rediseño profesional y moderno para informacion_personal.php -->
<!-- filepath: c:\xampp\htdocs\ModuStackVisit_2\resources\views\evaluador\evaluacion_visita\visita\informacion_personal\informacion_personal.php -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}
require_once __DIR__ . '/InformacionPersonalController.php';
use App\Controllers\InformacionPersonalController;
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body { font-family: 'Montserrat', Arial, sans-serif; background: #f4f6fb; }
.card-wizard {
    border: none;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(67,97,238,0.08);
    padding: 36px 36px 18px 36px;
    background: #fff;
}
.wizard-steps {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 24px;
    gap: 8px;
}
.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
}
.wizard-step .step-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e7f3fe;
    color: #4361ee;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    margin-bottom: 4px;
    border: 2px solid #e7f3fe;
    transition: background 0.2s, border 0.2s;
}
.wizard-step.active .step-icon {
    background: #4361ee;
    color: #fff;
    border: 2px solid #4361ee;
}
.wizard-step.complete .step-icon {
    background: #4bb543;
    color: #fff;
    border: 2px solid #4bb543;
}
.wizard-step .step-title {
    font-size: 0.95em;
    color: #888;
    text-align: center;
    font-weight: 500;
}
.wizard-step.active .step-title {
    color: #4361ee;
    font-weight: 600;
}
@media (max-width: 600px) {
    .card-wizard { padding: 16px 4px 10px 4px; }
    .wizard-step .step-title { font-size: 0.85em; }
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card-wizard">
                <!-- Wizard Steps Header -->
                <div class="wizard-steps mb-4">
                    <div class="wizard-step active">
                        <span class="step-icon"><i class="bi bi-person"></i></span>
                        <span class="step-title">Información Personal</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-briefcase"></i></span>
                        <span class="step-title">Cámara de Comercio</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-heart-pulse"></i></span>
                        <span class="step-title">Salud</span>
                    </div>
                    <div class="wizard-step">
                        <span class="step-icon"><i class="bi bi-people"></i></span>
                        <span class="step-title">Composición Familiar</span>
                    </div>
                    <!-- ...agrega más pasos según el flujo real... -->
                </div>
                <h4 class="mb-3 text-center">Información Personal</h4>
                <!-- Mensajes de alerta (éxito, error, advertencia) -->
                <?php if (isset($mensaje_exito)): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= $mensaje_exito ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($mensaje_error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= $mensaje_error ?>
                    </div>
                <?php endif; ?>
                <!-- Formulario -->
                <form id="formInformacionPersonal" method="POST" autocomplete="off">
                    <!-- ...aquí va tu formulario existente... -->
                    <!-- Ejemplo de campo -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                    </div>
                    <!-- ...agrega los demás campos aquí... -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </a>
                        <button type="submit" class="btn btn-primary" id="nextBtn">
                            Guardar y continuar <i class="bi bi-arrow-right-circle ms-2"></i>
                        </button>
                    </div>
                </form>
                <div class="card-footer text-end mt-4" style="color:#b0b0b0;font-size:0.93em;">
                    © 2024 V0.01
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('formInformacionPersonal').addEventListener('submit', function(e) {
    if(!confirm('¿Está seguro de que desea guardar la información?')) {
        e.preventDefault();
    }
});
</script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>