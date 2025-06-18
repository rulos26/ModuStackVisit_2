<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
?>
<div class="container mt-4">
    <div class="alert alert-success">
        <h4 class="alert-heading">Evaluación Visita Domiciliaria</h4>
        <p>Esta es la sección personalizada para la evaluación de la visita domiciliaria. Aquí puedes mostrar formularios, resultados o cualquier información relevante para este módulo.</p>
    </div>
</div>
<div class="text-center mt-4">
    <a href="visita/index.php" class="btn btn-primary btn-lg">
        <i class="bi bi-play-fill me-2"></i>Empezar
    </a>
</div>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 2) . '/layout/dashboard.php';
?>