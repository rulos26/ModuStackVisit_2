<div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end min-vh-100" style="width: 260px;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4 fw-bold">Menú</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php
        // Asegurarse de que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener el rol del usuario de la sesión
        $rol = $_SESSION['rol'] ?? null;

        // --- Menú para Evaluador (Rol 2) o Administrador (Rol 1) ---
        if ($rol == 2 || $rol == 1) {
        ?>
            <li class="nav-item">
                <a href="/ModuStackVisit_2/resources/views/evaluador/carta_visita/index_carta.php" class="nav-link link-dark">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Carta de autorización
                </a>
            </li>
            <li>
                <a href="/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/index_evaluacion.php" class="nav-link link-dark">
                    <i class="bi bi-house-door me-2"></i>
                    Evaluación visita domiciliaria
                </a>
            </li>
        <?php
        }

        // --- Menú exclusivo para Administrador (Rol 1) ---
        if ($rol == 1) {
        ?>
            <hr>
            <li class="nav-item">
                <span class="nav-link link-dark fw-bold text-primary">
                    <i class="bi bi-shield-lock me-2"></i>
                    Administración
                </span>
            </li>
            <li>
                <a href="/ModuStackVisit_2/resources/views/admin/usuario_carta/index.php" class="nav-link link-dark">
                    <i class="bi bi-envelope me-2"></i>
                    Usuarios Carta
                </a>
            </li>
            <li>
                <a href="/ModuStackVisit_2/resources/views/admin/usuario_evaluacion/index.php" class="nav-link link-dark">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Usuarios Evaluación
                </a>
            </li>
        <?php
        }
        ?>
    </ul>
    <hr>
    <div class="text-muted small">&copy; <?php echo date('Y'); ?> Mi Sistema</div>
</div> 