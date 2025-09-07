<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
?>
<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="fas fa-home me-2"></i>VISITA DOMICILIARÍA</h1>
            <p class="subtitle">Sistema de Evaluación Integral</p>
        </div>

        <!-- Barra de Progreso -->
        <div class="wizard-progress">
            <div class="wizard-steps">
                <div class="wizard-step active">
                    <div class="wizard-step-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="wizard-step-title">Paso 1</div>
                    <div class="wizard-step-description">Datos Básicos</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="wizard-step-title">Paso 2</div>
                    <div class="wizard-step-description">Información Personal</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="wizard-step-title">Paso 3</div>
                    <div class="wizard-step-description">Cámara de Comercio</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="wizard-step-title">Paso 4</div>
                    <div class="wizard-step-description">Salud</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="wizard-step-title">Paso 5</div>
                    <div class="wizard-step-description">Composición Familiar</div>
                </div>
                <div class="wizard-step">
                    <div class="wizard-step-icon">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="wizard-step-title">Paso 6</div>
                    <div class="wizard-step-description">Finalización</div>
                </div>
            </div>
        </div>

        <!-- Contenido del Wizard -->
        <div class="wizard-content">
            <div class="wizard-step-content active">
                <!-- Información del Evaluado -->
                <div class="wizard-evaluado-info">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="wizard-logo">
                        </div>
                        <div class="col-md-6 wizard-evaluado-details">
                            <div class="detail-item">
                                <span class="detail-label">Fecha:</span> <?php echo date('d/m/Y'); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Sistema:</span> Visitas Domiciliarias v2.0
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerta de Protección de Datos -->
                <div class="wizard-alert wizard-alert-info">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>PROTECCIÓN DE DATOS PERSONALES:</strong><br>
                        Al suministrar sus datos personales en este formulario AUTORIZA su tratamiento de forma expresa y voluntaria a la empresa Grupo de Tareas Empresariales. Le informamos que estos serán tratados conforme a lo previsto en la ley 1581 del 2012 y serán incluidos en una base de datos cuyo responsable es Grupo de Tareas Empresariales. La finalidad de la recolección es la gestión de siniestros y el trámite de reclamos ante las compañías de seguros. Usted podrá revocar su autorización en cualquier momento, consultar su información personal y ejercer sus derechos de conocer, actualizar, rectificar, corregir, suprimir o revocar su autorización enviando un email a: grpte@hotmail.com
                    </div>
                </div>

                <!-- Formulario -->
                <form action="session.php" method="POST" id="formDocumento" class="wizard-form" autocomplete="off">
                    <div class="form-group">
                        <label for="id_cedula" class="form-label">
                            <i class="fas fa-id-card"></i>
                            Número de Documento:
                        </label>
                        <input type="number" class="form-control" id="id_cedula" name="id_cedula" 
                               required min="1" autocomplete="off" placeholder="Ingrese su número de cédula">
                        <div class="invalid-feedback">
                            Por favor ingrese un número de documento válido.
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Ingrese su número de cédula sin puntos ni espacios
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <button type="button" class="wizard-btn wizard-btn-secondary" disabled>
                <i class="fas fa-arrow-left"></i>
                Anterior
            </button>
            <div class="text-center">
                <small class="text-muted">Paso 1 de 22</small>
            </div>
            <button type="button" class="wizard-btn wizard-btn-primary wizard-btn-next" id="btnEnviar" disabled>
                Empezar
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
<script src="../../../../../public/js/wizard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real del campo de cédula
    const inputCedula = document.getElementById('id_cedula');
    const btnEnviar = document.getElementById('btnEnviar');
    const form = document.getElementById('formDocumento');
    
    // Función de validación
    function validateCedula() {
        const value = inputCedula.value.trim();
        const isValid = value.length >= 6 && parseInt(value) > 0;
        
        if (isValid) {
            inputCedula.classList.remove('is-invalid');
            inputCedula.classList.add('is-valid');
            btnEnviar.disabled = false;
        } else {
            inputCedula.classList.remove('is-valid');
            inputCedula.classList.add('is-invalid');
            btnEnviar.disabled = true;
        }
        
        return isValid;
    }
    
    // Event listeners
    inputCedula.addEventListener('input', validateCedula);
    inputCedula.addEventListener('blur', validateCedula);
    
    // Formatear cédula mientras se escribe
    inputCedula.addEventListener('input', function() {
        if (window.wizardUtils) {
            window.wizardUtils.formatCedula(this);
        }
    });
    
    // Manejar envío del formulario
    btnEnviar.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (validateCedula()) {
            // Mostrar animación de carga
            btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            btnEnviar.disabled = true;
            
            // Enviar formulario
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
    
    // Validación inicial
    validateCedula();
    
    // Auto-focus en el campo de cédula
    inputCedula.focus();
});
</script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>