<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

require_once __DIR__ . '/../../../../../app/Database/Database.php';
use App\Database\Database;

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        $id_cedula = $_SESSION['id_cedula'];
        $tiene_camara = $_POST['tiene_camara'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $razon = $_POST['razon'] ?? '';
        $actividad = $_POST['actividad'] ?? '';
        $observacion = $_POST['observacion'] ?? '';

        // Insertar o actualizar registro
        $sql = "REPLACE INTO camara_comercio (id_cedula, tiene_camara, nombre, razon, actividad, observacion) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_cedula, $tiene_camara, $nombre, $razon, $actividad, $observacion]);

        $_SESSION['success'] = 'Información de Cámara de Comercio guardada exitosamente.';
        header('Location: ../salud/salud.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
    }
}

// Obtener datos existentes si los hay
$datos_existentes = [];
try {
    $db = Database::getInstance()->getConnection();
    $id_cedula = $_SESSION['id_cedula'];
    $stmt = $db->prepare("SELECT * FROM camara_comercio WHERE id_cedula = ?");
    $stmt->execute([$id_cedula]);
    $datos_existentes = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    $error_message = 'Error al cargar los datos: ' . $e->getMessage();
}

ob_start();
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.steps-horizontal { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; width: 100%; gap: 0.5rem; }
.step-horizontal { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.step-horizontal:not(:last-child)::after { content: ''; position: absolute; top: 24px; left: 50%; width: 100%; height: 4px; background: #e0e0e0; z-index: 0; transform: translateX(50%); }
.step-horizontal .step-icon { width: 48px; height: 48px; border-radius: 50%; background: #e0e0e0; color: #888; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.5rem; border: 2px solid #e0e0e0; z-index: 1; transition: all 0.3s; }
.step-horizontal.active .step-icon { background: #4361ee; border-color: #4361ee; color: #fff; box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2); }
.step-horizontal.complete .step-icon { background: #2ecc71; border-color: #2ecc71; color: #fff; }
.step-horizontal .step-title { font-weight: bold; font-size: 1rem; margin-bottom: 0.2rem; }
.step-horizontal .step-description { font-size: 0.85rem; color: #888; text-align: center; }
.step-horizontal.active .step-title, .step-horizontal.active .step-description { color: #4361ee; }
.step-horizontal.complete .step-title, .step-horizontal.complete .step-description { color: #2ecc71; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title">Cámara de Comercio</h5>
        </div>
        <div class="card-body">
            <div class="steps-horizontal mb-4">
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-user"></i></div>
                    <div class="step-title">Paso 1</div>
                    <div class="step-description">Datos Básicos</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                    <div class="step-title">Paso 2</div>
                    <div class="step-description">Información Personal</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-building"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Cámara de Comercio</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Salud</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
                <div class="step-horizontal">
                    <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Finalización</div>
                </div>
            </div>
            <div class="controls text-center mb-4">
                <a href="../informacion_personal/informacion_personal.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Anterior
                </a>
                <button class="btn btn-primary" id="nextBtn" type="button" onclick="document.getElementById('camaraComercioForm').submit();">
                    Siguiente<i class="fas fa-arrow-right ms-1"></i>
                </button>
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
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form class="form-section" action="" method="post" autocomplete="off" id="camaraComercioForm">
                <div class="row">
                    <div class="col-6">
                        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 60%; height: auto;">
                    </div>
                    <div class="col-6"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tiene_camara" class="form-label">¿Tiene Cámara de Comercio?</label>
                        <select class="form-select" id="tiene_camara" name="tiene_camara" required>
                            <option value="" selected>Seleccione una opción</option>
                            <option value="Si" <?php if (($datos_existentes['tiene_camara'] ?? '') === 'Si') echo 'selected'; ?>>Sí</option>
                            <option value="No" <?php if (($datos_existentes['tiene_camara'] ?? '') === 'No') echo 'selected'; ?>>No</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione una opción.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nombre" class="form-label">Nombre de Empresa</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="200" value="<?php echo htmlspecialchars($datos_existentes['nombre'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="razon" class="form-label">Razón Social</label>
                        <input type="text" class="form-control" id="razon" name="razon" maxlength="200" value="<?php echo htmlspecialchars($datos_existentes['razon'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="actividad" class="form-label">Actividad</label>
                        <input type="text" class="form-control" id="actividad" name="actividad" maxlength="200" value="<?php echo htmlspecialchars($datos_existentes['actividad'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="observacion" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="2" maxlength="1000"><?php echo htmlspecialchars($datos_existentes['observacion'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script src="../../../../../public/js/validacionCamaraComercio.js"></script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>