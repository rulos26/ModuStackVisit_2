<?php
ob_start();
?>
<div class="container mt-4">
    <div class="alert alert-success">
        <h4 class="alert-heading">Evaluación Visita Domiciliaria</h4>
        <p>Esta es la sección personalizada para la evaluación de la visita domiciliaria. Aquí puedes mostrar formularios, resultados o cualquier información relevante para este módulo.</p>
    </div>
</div>
<?php
$contenido = ob_get_clean();
include __DIR__ . '/../layout/dashboard.php';
?>