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
    $_SESSION['success'] = 'Autorización guardada exitosamente.';
    $_SESSION['cedula_autorizacion'] = $_POST['cedula'];
    header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/firma/firma.php');

    if ($resultado === true) {
        $_SESSION['success'] = 'Autorización guardada exitosamente.';
        $_SESSION['cedula_autorizacion'] = $_POST['cedula'];
        header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/firma/firma.php');
        exit();
    } else {
        //$_SESSION['error'] = $resultado;
        header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/firma/firma.php');
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
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.steps-horizontal {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    width: 100%;
    gap: 0.5rem;
}
.step-horizontal {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}
.step-horizontal:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 24px;
    left: 50%;
    width: 100%;
    height: 4px;
    background: #e0e0e0;
    z-index: 0;
    transform: translateX(50%);
}
.step-horizontal .step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #888;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    border: 2px solid #e0e0e0;
    z-index: 1;
    transition: all 0.3s;
}
.step-horizontal.active .step-icon {
    background: #4361ee;
    border-color: #4361ee;
    color: #fff;
    box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
}
.step-horizontal.complete .step-icon {
    background: #2ecc71;
    border-color: #2ecc71;
    color: #fff;
}
.step-horizontal .step-title {
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 0.2rem;
}
.step-horizontal .step-description {
    font-size: 0.85rem;
    color: #888;
    text-align: center;
}
.step-horizontal.active .step-title,
.step-horizontal.active .step-description {
    color: #4361ee;
}
.step-horizontal.complete .step-title,
.step-horizontal.complete .step-description {
    color: #2ecc71;
}
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title">Carta de Autorización</h5>
        </div>
        <div class="card-body">
            <div class="steps-horizontal mb-4">
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-user"></i></div>
                    <div class="step-title">Paso 1</div>
                    <div class="step-description">Datos Básicos</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                    <div class="step-title">Paso 2</div>
                    <div class="step-description">Información Personal</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-phone"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Contacto</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Autorización</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-search"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Revisión</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Finalización</div>
                </div>
            </div>
            <div class="controls text-center mb-4">
                <button class="btn btn-secondary me-2" id="prevBtn" type="button">Anterior</button>
                <button class="btn btn-primary" id="nextBtn" type="button">Siguiente</button>
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

<script src="../../../../../public/js/validacionCartaAutorizacion.js"></script>
<script src="/public/js/stepper.js"></script>

<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>