<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) && !isset($_SESSION['cedula_autorizacion'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=ubicacion&test=123');
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
            <h5 class="card-title">Ubicación en Tiempo Real</h5>
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
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Registro Fotográfico</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="step-title">Paso 7</div>
                    <div class="step-description">Ubicación</div>
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
            <form id="ubicacionForm" action="guardar.php" method="POST">
                <div class="row mb-4">
                    <div class="col-6">
                        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="img-fluid" style="max-width: 60%; height: auto;">
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-center">
                        <div>
                            <h4>Ubicación Actual</h4>
                            <p>Latitud: <span id="latitud"></span></p>
                            <p>Longitud: <span id="longitud"></span></p>
                            <div class="progress mt-3" style="height: 50px; width: 80%; margin: 0 auto;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%; font-size: 16px; font-weight: bold;" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <span id="countdown">0 %</span>%
                                </div>
                            </div>
                            <p class="mt-3 text-muted" style="font-size: 16px;">Generando ubicación...</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="latituds" name="latituds">
                <input type="hidden" id="longituds" name="longituds">
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Función para obtener la ubicación automáticamente al cargar la página
function obtenerUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitud = position.coords.latitude;
            var longitud = position.coords.longitude;
            document.getElementById('latitud').innerText = latitud;
            document.getElementById('longitud').innerText = longitud;
            document.getElementById('latituds').value = latitud;
            document.getElementById('longituds').value = longitud;
            iniciarContador();
        }, function() {
            alert('No se pudo obtener la ubicación.');
        });
    } else {
        alert('La geolocalización no está disponible en este navegador.');
    }
}
// Función para el contador regresivo
function iniciarContador() {
    let tiempoRestante = 10;
    const progressBar = document.getElementById('progressBar');
    const countdown = document.getElementById('countdown');
    const total = 10;
    const intervalo = setInterval(() => {
        tiempoRestante--;
        const porcentaje = Math.round(((total - tiempoRestante) / total) * 100);
        progressBar.style.width = porcentaje + '%';
        progressBar.setAttribute('aria-valuenow', porcentaje);
        countdown.textContent = porcentaje;
        if (tiempoRestante <= 0) {
            clearInterval(intervalo);
            document.querySelector('.text-muted').textContent = '¡Ubicación generada! Enviando datos...';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            setTimeout(() => {
                document.getElementById('ubicacionForm').submit();
            }, 1000);
        }
    }, 1000);
}
window.onload = obtenerUbicacion;
</script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>