<?php
ob_start();
?>
<div class="container mt-4">
    <div class="alert alert-info">
        <h4 class="alert-heading">Carta de Autorización</h4>
        <p>Esta es la sección personalizada para la gestión de la carta de autorización. Aquí puedes mostrar formularios, tablas o cualquier contenido específico relacionado con este módulo.</p>
    </div>
</div>
<?php
$contenido = ob_get_clean();
include __DIR__ . '/../layout/dashboard.php';