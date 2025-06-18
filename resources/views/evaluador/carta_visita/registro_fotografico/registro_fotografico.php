<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) && !isset($_SESSION['cedula_autorizacion'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=registro_fotografico&test=123');
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
            <h5 class="card-title">Registro Fotográfico</h5>
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
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-phone"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Contacto</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Autorización</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-pen-nib"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Firma</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
            </div>
            <div class="controls text-center mb-4">
                <!-- Aquí podrías poner botones de navegación si lo deseas -->
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
            <form id="fotoForm" method="POST" enctype="multipart/form-data" action="guardar_foto.php">
                <div class="row mb-4">
                    <div class="col-6">
                        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 60%; height: auto;">
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-primary" id="captureButton">
                            <i class="fas fa-camera"></i> Tomar Foto
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <div id="contadorFoto" style="font-size:2rem; color:#4361ee; margin-bottom:1rem; display:none;"></div>
                        <video id="video" width="400" height="300" autoplay style="display:none;"></video>
                        <canvas id="canvas" width="400" height="300" style="display:none;"></canvas>
                        <img id="fotoPreview" src="" alt="Foto tomada" style="max-width: 300px; display: none;">
                    </div>
                </div>
                <input type="hidden" name="foto_digital" id="fotoDigitalInput" required>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Lógica para capturar la foto automáticamente al cargar la página con contador
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const fotoPreview = document.getElementById('fotoPreview');
const fotoDigitalInput = document.getElementById('fotoDigitalInput');
const fotoForm = document.getElementById('fotoForm');
const contadorFoto = document.getElementById('contadorFoto');

let stream = null;

function tomarFotoAutomatica() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = canvas.toDataURL('image/png');
    fotoDigitalInput.value = imageData;
    fotoPreview.src = imageData;
    fotoPreview.style.display = 'block';
    video.style.display = 'none';
    contadorFoto.style.display = 'none';
    // Detener la cámara
    if (stream) stream.getTracks().forEach(track => track.stop());
    // Enviar el formulario automáticamente
    setTimeout(function() {
        fotoForm.submit();
    }, 400);
}

window.addEventListener('DOMContentLoaded', function() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(s) {
            stream = s;
            video.srcObject = stream;
            video.style.display = 'block';
            video.play();
            video.onloadeddata = function() {
                let tiempo = 3;
                contadorFoto.textContent = 'La foto se tomará en ' + tiempo + '...';
                contadorFoto.style.display = 'block';
                let intervalo = setInterval(function() {
                    tiempo--;
                    if (tiempo > 0) {
                        contadorFoto.textContent = 'La foto se tomará en ' + tiempo + '...';
                    } else {
                        clearInterval(intervalo);
                        tomarFotoAutomatica();
                    }
                }, 1000);
            };
        })
        .catch(function(error) {
            alert('Error al acceder a la cámara: ' + error);
        });
});
</script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>