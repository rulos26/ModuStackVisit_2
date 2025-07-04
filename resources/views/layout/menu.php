<?php
/**
 * @file
 * menu.php
 *
 * @brief
 * Este archivo genera la barra de menú lateral de navegación para la aplicación.
 *
 * @details
 * El contenido del menú es dinámico y se ajusta según el rol del usuario que ha iniciado sesión.
 * Utiliza la variable de sesión `$_SESSION['rol']` para determinar qué enlaces mostrar.
 * - Rol 1: Administrador.
 * - Rol 2: Evaluador.
 *
 * @author     Sistema de Visitas
 * @version    1.1
 * @date       2024
 */
?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end min-vh-100" style="width: 260px;">
    <!-- Encabezado del menú -->
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4 fw-bold">Menú</span>
    </a>
    <hr>
    <!-- Lista de enlaces de navegación -->
    <ul class="nav nav-pills flex-column mb-auto">
        <?php
        /**
         * LÓGICA DE CONTROL DE ACCESO POR ROLES
         * ------------------------------------
         * A continuación, se implementa la lógica para mostrar los elementos del menú
         * basándose en el rol del usuario almacenado en la sesión.
         */

        // Paso 1: Asegurar que la sesión esté iniciada.
        // Si no hay una sesión activa, se inicia una para poder acceder a las variables de sesión.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Paso 2: Obtener el rol del usuario desde la variable de sesión.
        // Se utiliza el operador de fusión de null (??) para evitar errores si la variable no está definida.
        // Si `$_SESSION['rol']` no existe, `$rol` será `null`.
        $rol = $_SESSION['rol'] ?? null;

        // --- MENÚ PARA EL ROL DE EVALUADOR (rol = 2) ---
        // Este bloque de menú solo se mostrará si el usuario tiene el rol de "Evaluador".
        if ($rol == 2) {
        ?>
            <hr>
            <!-- Título de la sección para el evaluador -->
            <li class="nav-item">
                <span class="nav-link link-dark fw-bold text-primary">
                    <i class="bi bi-shield-lock me-2"></i>
                    Opciones del evaluador
                </span>
            </li>
            <!-- Enlace a la sección "Carta de autorización" -->
            <li class="nav-item">
                <a href="/ModuStackVisit_2/resources/views/evaluador/carta_visita/index_carta.php" class="nav-link link-dark">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Carta de autorización
                </a>
            </li>
            <!-- Enlace a la sección "Evaluación visita domiciliaria" -->
            <li>
                <a href="/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/index_evaluacion.php" class="nav-link link-dark">
                    <i class="bi bi-house-door me-2"></i>
                    Evaluación visita domiciliaria
                </a>
            </li>
        <?php
        } // Fin del bloque para el rol de Evaluador

        // --- MENÚ PARA EL ROL DE ADMINISTRADOR (rol = 1) ---
        // Este bloque de menú solo se mostrará si el usuario tiene el rol de "Administrador".
        if ($rol == 1) {
        ?>
            <hr>
            <!-- Título de la sección para el administrador -->
            <li class="nav-item">
                <span class="nav-link link-dark fw-bold text-primary">
                    <i class="bi bi-shield-lock me-2"></i>
                    Opciones del administrador
                </span>
            </li>
            <!-- Enlace al panel de administración de usuarios de "Carta de Autorización" -->
            <li>
                <a href="/ModuStackVisit_2/resources/views/admin/usuario_carta/index.php" class="nav-link link-dark">
                    <i class="bi bi-envelope me-2"></i>
                    Usuarios Carta
                </a>
            </li>
            <!-- Enlace al panel de administración de usuarios de "Evaluación" -->
            <li>
                <a href="/ModuStackVisit_2/resources/views/admin/usuario_evaluacion/index.php" class="nav-link link-dark">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Usuarios Evaluación
                </a>
            </li>
        <?php
        } // Fin del bloque para el rol de Administrador
        ?>
    </ul>
    <hr>
    <!-- Pie de página del menú -->
    <div class="text-muted small">&copy; <?php echo date('Y'); ?> Mi Sistema</div>
</div> 