<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
include dirname(__DIR__, 2) . '/layout/dashboard.php';
?>