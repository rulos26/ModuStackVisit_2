<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['cedula_autorizacion'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=firma&test=123');
    exit();
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
            <h5 class="card-title">Firma de Autorización</h5>
        </div>
        <div class="card-body">
            <div class="controls text-center mb-4">
                <!-- Botones de stepper eliminados para simplificar el flujo -->
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
            <form action="guardar_firma.php" method="POST" id="firmaForm">
                <div class="row mb-4">
                    <div class="col-6">
                        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 60%; height: auto;">
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFirma">
                            Firmar
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <img id="firmaImg" src="" alt="Firma digital" style="max-width: 300px; display: none;">
                    </div>
                </div>
                <!-- Modal para la firma -->
                <div class="modal fade" id="modalFirma" tabindex="-1" aria-labelledby="modalFirmaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalFirmaLabel">Firma</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <canvas id="canvas" width="800" height="400" style="border:1px solid #ccc;"></canvas>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="guardarFirmaBtn">Guardar Firma</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="firma_digital" id="firmaDigitalInput" required>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success mt-3">Finalizar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/public/js/stepper.js"></script>
<script>
// Lógica para capturar la firma en el canvas y guardarla como imagen base64
let canvas = document.getElementById('canvas');
let ctx = canvas.getContext('2d');
let drawing = false;
let lastX = 0;
let lastY = 0;

function startDrawing(e) {
    drawing = true;
    [lastX, lastY] = [e.offsetX, e.offsetY];
}
function draw(e) {
    if (!drawing) return;
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.strokeStyle = '#222';
    ctx.lineWidth = 2;
    ctx.stroke();
    [lastX, lastY] = [e.offsetX, e.offsetY];
}
function stopDrawing() {
    drawing = false;
}
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

document.getElementById('guardarFirmaBtn').addEventListener('click', function() {
    let dataURL = canvas.toDataURL();
    document.getElementById('firmaDigitalInput').value = dataURL;
    document.getElementById('firmaImg').src = dataURL;
    document.getElementById('firmaImg').style.display = 'block';
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalFirma'));
    modal.hide();
    // Desactivar el botón 'Firmar' después de guardar la firma
    document.querySelector('button[data-bs-target="#modalFirma"]').disabled = true;
});
// Validación para evitar enviar el formulario sin firma
const firmaForm = document.getElementById('firmaForm');
firmaForm.addEventListener('submit', function(e) {
    if (!document.getElementById('firmaDigitalInput').value) {
        alert('Por favor, firme antes de finalizar.');
        e.preventDefault();
    }
});
</script>

<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>