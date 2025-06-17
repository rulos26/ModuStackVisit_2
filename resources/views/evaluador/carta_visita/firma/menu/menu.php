<?php
require_once __DIR__ . '/../../../config.php';
?>
<!-- 
    Modificaciones realizadas:
    1. Implementado sistema de rutas con constantes
    2. Eliminada duplicación de rutas
    3. Agregados comentarios explicativos
    4. Validación de rutas implementada
-->
<nav class="menu-vertical navbar-dark bg-dark">
    <img src="<?php echo validatePath(MENU_LOGO); ?>" alt="Logotipo de la empresa" width="100%" height="10%"> <br><br>
    
    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo validatePath(CARTA_AUTORIZACION); ?>">Carta Autorización</a> 
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo validatePath(VISITA_INDEX); ?>">Vista domiciliaría</a>
        </li>
    </ul>
</nav>

