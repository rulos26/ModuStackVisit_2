<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['id_cedula'])) {
    header("Location: /login.php");
    exit();
}

require_once __DIR__ . "/ExperienciaLaboralController.php"; // corregir ruta
$controller = new ExperienciaLaboralController();

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    try {
        $accion = $_POST['accion'];
        $id_experiencia = $_POST['id_experiencia'] ?? null;
        $datos = $controller->sanitizarDatos($_POST);

        if ($accion === 'guardar') {
            if ($id_experiencia) {
                $controller->actualizarExperiencia($id_experiencia, $datos);
                $_SESSION['mensaje'] = "¡Experiencia actualizada correctamente!";
            } else {
                $controller->guardarExperiencia($datos);
                $_SESSION['mensaje'] = "¡Experiencia guardada correctamente!";
            }
        } elseif ($accion === 'eliminar' && $id_experiencia) {
            $controller->eliminarExperiencia($id_experiencia);
            $_SESSION['mensaje'] = "¡Experiencia eliminada correctamente!";
        }
    } catch (Exception $e) {
        error_log("Error en experiencia_laboral: " . $e->getMessage());
        $_SESSION['error'] = "Ocurrió un error inesperado. Intente más tarde.";
    }
    header("Location: experiencia_laboral.php");
    exit();
}

$id_cedula = $_SESSION['id_cedula'];
$experiencias = $controller->obtenerExperienciasPorCedula($id_cedula);

ob_start();
?>
<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-briefcase"></i> Experiencia Laboral</h2>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" id="form-experiencia">
        <input type="hidden" name="accion" id="accion" value="guardar">
        <input type="hidden" name="id_experiencia" id="id_experiencia" value="">

        <div id="experiencias-container">
            <div class="experiencia border rounded p-3 mb-3 bg-light">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-building"></i> Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa[]" class="form-control" required minlength="2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-briefcase"></i> Cargo</label>
                        <input type="text" name="cargo[]" class="form-control" required minlength="2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-calendar"></i> Fecha Inicio</label>
                        <input type="date" name="fecha_inicio[]" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-calendar-check"></i> Fecha Fin</label>
                        <input type="date" name="fecha_fin[]" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-envelope"></i> Correo</label>
                        <input type="email" name="correo[]" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-phone"></i> Número de Contacto</label>
                        <input type="text" name="numero_contacto[]" class="form-control" required pattern="\d{7,}">
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm mt-2 eliminar-experiencia" aria-label="Eliminar experiencia">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </div>
        </div>

        <button type="button" id="agregar-experiencia" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Agregar Experiencia
        </button>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Experiencias
        </button>
    </form>

    <hr>

    <h3><i class="fas fa-list"></i> Experiencias Registradas</h3>
    <?php if (!empty($experiencias)): ?>
        <ul class="list-group mt-3">
            <?php foreach ($experiencias as $exp): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($exp['cargo']) ?></strong> en
                    <?= htmlspecialchars($exp['nombre_empresa']) ?> (<?= htmlspecialchars($exp['fecha_inicio']) ?> - <?= htmlspecialchars($exp['fecha_fin']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se han registrado experiencias laborales.</p>
    <?php endif; ?>
</div>

<script>
document.getElementById('agregar-experiencia').addEventListener('click', function() {
    const container = document.getElementById('experiencias-container');
    const clone = container.firstElementChild.cloneNode(true);
    clone.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(clone);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.eliminar-experiencia')) {
        const experiencia = e.target.closest('.experiencia');
        if (document.querySelectorAll('.experiencia').length > 1) {
            experiencia.remove();
        } else {
            alert('Debe haber al menos una experiencia.');
        }
    }
});
</script>
<?php
$contenido = ob_get_clean();

// Incluir dashboard
$dashboard_path = realpath(__DIR__ . '/../../../../../layout/dashboard.php');
if ($dashboard_path && file_exists($dashboard_path)) {
    include $dashboard_path;
} else {
    echo $contenido;
}
?>
