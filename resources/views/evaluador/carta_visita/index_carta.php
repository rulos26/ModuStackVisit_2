<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ob_start();
?>
<div class="container mt-4">
    <!-- Mensaje de redirección desde búsqueda de documento -->
    <?php if (isset($_GET['reason']) && $_GET['reason'] === 'documento_no_encontrado'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                <div>
                    <h5 class="alert-heading mb-2">
                        <i class="bi bi-search me-2"></i>Documento No Encontrado
                    </h5>
                    <p class="mb-2">
                        <strong>Número de documento buscado:</strong> 
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($_GET['cedula'] ?? 'N/A'); ?></span>
                    </p>
                    <p class="mb-0">
                        <strong>Razón de la redirección:</strong> 
                        No se encontró ninguna cédula asociada con carta de autorización en el sistema.
                    </p>
                    <hr class="my-3">
                    <p class="mb-0">
                        <strong>¿Qué hacer ahora?</strong><br>
                        Debe registrar primero la <strong>Carta de Autorización</strong> para este documento antes de poder continuar con la evaluación.
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Mensaje de sesión si existe -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="alert alert-info">
                <h4 class="alert-heading">
                    <i class="bi bi-file-earmark-text me-2"></i>Carta de Autorización
                </h4>
                <p>Esta es la sección para la gestión de la carta de autorización. Aquí puedes registrar nuevos documentos que no estén en el sistema.</p>
                
                <?php if (isset($_GET['reason']) && $_GET['reason'] === 'documento_no_encontrado'): ?>
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="text-primary">
                            <i class="bi bi-info-circle me-2"></i>Proceso Requerido:
                        </h6>
                        <ol class="mb-0">
                            <li>Complete el formulario de <strong>Carta de Autorización</strong></li>
                            <li>Una vez registrada, podrá continuar con la evaluación</li>
                            <li>El sistema creará automáticamente el registro de evaluado</li>
                        </ol>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="carta_autorizacion/carta_autorizacion.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-play-fill me-2"></i>
                    <?php echo (isset($_GET['reason']) && $_GET['reason'] === 'documento_no_encontrado') ? 'Registrar Carta de Autorización' : 'Empezar'; ?>
                </a>
                
                <?php if (isset($_GET['reason']) && $_GET['reason'] === 'documento_no_encontrado'): ?>
                    <div class="mt-3">
                        <a href="../../evaluacion_visita/visita/index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Búsqueda de Documento
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$contenido = ob_get_clean();
$theme = 'evaluador';
include dirname(__DIR__, 2) . '/layout/dashboard.php';
?>