<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit();
}

require_once __DIR__ . '/../../../../../app/Controllers/CartaAutorizacionController.php';
use App\Controllers\CartaAutorizacionController;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = CartaAutorizacionController::guardarAutorizacion(
        $_POST['cedula'],
        $_POST['nombre'],
        $_POST['direccion'],
        $_POST['localidad'],
        $_POST['barrio'],
        $_POST['telefono'],
        $_POST['celular_1'],
        $_POST['fecha'],
        $_POST['autorizacion'],
        $_POST['correo']
    );

    if ($resultado === true) {
        $_SESSION['success'] = 'Autorización guardada exitosamente.';
        header('Location: ../datos_basicos/datos_basicos.php');
        exit();
    } else {
        $_SESSION['error'] = $resultado;
    }
}

// Usar la clase Database si está disponible
require_once __DIR__ . '/../../../../../app/Database/Database.php';
use App\Database\Database;

try {
    $db = Database::getInstance()->getConnection();

    // Consultas seguras con PDO
    $municipios = $db->query("SELECT municipio FROM municipios")->fetchAll(PDO::FETCH_COLUMN);
    $tiposDocumento = $db->query("SELECT nombre FROM opc_tipo_documentos")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error de conexión: " . htmlspecialchars($e->getMessage()));
}

ob_start();
?>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title">Carta de Autorización</h5>
        </div>
        <div class="card-body">
            <div class="container px-0">
                <div class="stepper-wrapper mb-4">
                    <div class="stepper-item active">
                        <div class="step-counter">1</div>
                        <div class="step-name">Datos Básicos</div>
                    </div>
                    <div class="stepper-item">
                        <div class="step-counter">2</div>
                        <div class="step-name">Información Personal</div>
                    </div>
                    <div class="stepper-item">
                        <div class="step-counter">3</div>
                        <div class="step-name">Contacto</div>
                    </div>
                    <div class="stepper-item">
                        <div class="step-counter">4</div>
                        <div class="step-name">Autorización</div>
                    </div>
                    <div class="stepper-item">
                        <div class="step-counter">5</div>
                        <div class="step-name">Revisión</div>
                    </div>
                    <div class="stepper-item">
                        <div class="step-counter">6</div>
                        <div class="step-name">Finalización</div>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <form class="form-section" action="" method="post" autocomplete="off" id="cartaAutorizacionForm">
                <div class="row">
                    <div class="col-6">
                        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 60%; height: auto;">
                    </div>
                    <div class="col-6"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select class="form-control" name="ciudad" id="ciudad" required>
                                <option value="0" selected>Seleccione la ciudad</option>
                                <?php foreach ($municipios as $municipio): ?>
                                    <option value="<?= htmlspecialchars($municipio) ?>"><?= htmlspecialchars($municipio) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione una ciudad.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Maria Perez" required>
                            <div class="invalid-feedback">Por favor ingrese su nombre.</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="opc_tipo_documentos" class="form-label">Tipo de Documento</label>
                            <select class="form-control" name="opc_tipo_documentos" id="opc_tipo_documentos" required>
                                <option value="0" selected>Seleccione Tipo de Documento</option>
                                <?php foreach ($tiposDocumento as $tipo): ?>
                                    <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un tipo de documento.</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="number" class="form-control" id="cedula" name="cedula" placeholder="0123456789" required>
                            <div class="invalid-feedback">Por favor ingrese su cédula.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                            <div class="invalid-feedback">Por favor ingrese su dirección.</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="localidad" class="form-label">Localidad</label>
                            <input type="text" class="form-control" id="localidad" name="localidad" required>
                            <div class="invalid-feedback">Por favor ingrese su localidad.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="barrio" class="form-label">Barrio</label>
                            <input type="text" class="form-control" id="barrio" name="barrio" required>
                            <div class="invalid-feedback">Por favor ingrese su barrio.</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                            <div class="invalid-feedback">Por favor ingrese su teléfono.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="celular_1" class="form-label">Celular</label>
                            <input type="tel" class="form-control" id="celular_1" name="celular_1" required>
                            <div class="invalid-feedback">Por favor ingrese su celular.</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                            <div class="invalid-feedback">Por favor ingrese su correo electrónico.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="autorizacion" class="form-label">Autorización</label>
                            <textarea class="form-control" id="autorizacion" name="autorizacion" rows="12" required></textarea>
                            <div class="invalid-feedback">Por favor ingrese la autorización.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Autorizo</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>

<script src="/public/js/validacionCartaAutorizacion.js"></script>

<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>