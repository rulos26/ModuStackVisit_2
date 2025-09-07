<?php
/**
 * PLANTILLA BASE PARA VISTAS DEL WIZARD
 * Sistema de Visitas Domiciliarias
 * 
 * Uso:
 * 1. Incluir este archivo al inicio de cada vista
 * 2. Definir las variables específicas de cada paso
 * 3. Personalizar el contenido del formulario
 */

// Variables que deben definirse en cada vista
$wizard_step = $wizard_step ?? 1;
$wizard_title = $wizard_title ?? 'Título del Paso';
$wizard_subtitle = $wizard_subtitle ?? 'Descripción del paso';
$wizard_icon = $wizard_icon ?? 'fas fa-circle';
$wizard_form_id = $wizard_form_id ?? 'wizardForm';
$wizard_form_action = $wizard_form_action ?? '';
$wizard_previous_url = $wizard_previous_url ?? '../index.php';
$wizard_next_url = $wizard_next_url ?? '../index.php';

// Definir los pasos del wizard
$wizard_steps = [
    1 => ['icon' => 'fas fa-user', 'title' => 'Paso 1', 'description' => 'Datos Básicos'],
    2 => ['icon' => 'fas fa-id-card', 'title' => 'Paso 2', 'description' => 'Información Personal'],
    3 => ['icon' => 'fas fa-building', 'title' => 'Paso 3', 'description' => 'Cámara de Comercio'],
    4 => ['icon' => 'fas fa-heartbeat', 'title' => 'Paso 4', 'description' => 'Salud'],
    5 => ['icon' => 'fas fa-users', 'title' => 'Paso 5', 'description' => 'Composición Familiar'],
    6 => ['icon' => 'fas fa-heart', 'title' => 'Paso 6', 'description' => 'Información de Pareja'],
    7 => ['icon' => 'fas fa-home', 'title' => 'Paso 7', 'description' => 'Tipo de Vivienda'],
    8 => ['icon' => 'fas fa-tools', 'title' => 'Paso 8', 'description' => 'Estado de Vivienda'],
    9 => ['icon' => 'fas fa-couch', 'title' => 'Paso 9', 'description' => 'Inventario de Enseres'],
    10 => ['icon' => 'fas fa-bolt', 'title' => 'Paso 10', 'description' => 'Servicios Públicos'],
    11 => ['icon' => 'fas fa-university', 'title' => 'Paso 11', 'description' => 'Cuentas Bancarias'],
    12 => ['icon' => 'fas fa-exclamation-triangle', 'title' => 'Paso 12', 'description' => 'Tiene Pasivo'],
    13 => ['icon' => 'fas fa-credit-card', 'title' => 'Paso 13', 'description' => 'Pasivos'],
    14 => ['icon' => 'fas fa-hand-holding-usd', 'title' => 'Paso 14', 'description' => 'Aportante'],
    15 => ['icon' => 'fas fa-chart-line', 'title' => 'Paso 15', 'description' => 'Data Crédito'],
    16 => ['icon' => 'fas fa-flag', 'title' => 'Paso 16', 'description' => 'Reportado'],
    17 => ['icon' => 'fas fa-money-bill-wave', 'title' => 'Paso 17', 'description' => 'Ingresos Mensuales'],
    18 => ['icon' => 'fas fa-receipt', 'title' => 'Paso 18', 'description' => 'Gastos'],
    19 => ['icon' => 'fas fa-graduation-cap', 'title' => 'Paso 19', 'description' => 'Estudios'],
    20 => ['icon' => 'fas fa-gavel', 'title' => 'Paso 20', 'description' => 'Información Judicial'],
    21 => ['icon' => 'fas fa-briefcase', 'title' => 'Paso 21', 'description' => 'Experiencia Laboral'],
    22 => ['icon' => 'fas fa-clipboard-check', 'title' => 'Paso 22', 'description' => 'Concepto Final'],
    23 => ['icon' => 'fas fa-camera', 'title' => 'Paso 23', 'description' => 'Registro Fotos']
];
?>

<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="<?php echo $wizard_icon; ?> me-2"></i><?php echo $wizard_title; ?></h1>
            <p class="subtitle"><?php echo $wizard_subtitle; ?></p>
        </div>

        <!-- Barra de Progreso -->
        <div class="wizard-progress">
            <div class="wizard-steps">
                <?php foreach ($wizard_steps as $step_num => $step_data): ?>
                    <div class="wizard-step <?php 
                        if ($step_num < $wizard_step) echo 'completed';
                        elseif ($step_num == $wizard_step) echo 'active';
                    ?>">
                        <div class="wizard-step-icon">
                            <i class="<?php echo $step_data['icon']; ?>"></i>
                        </div>
                        <div class="wizard-step-title"><?php echo $step_data['title']; ?></div>
                        <div class="wizard-step-description"><?php echo $step_data['description']; ?></div>
                    </div>
                <?php endforeach; ?>
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
                                <span class="detail-label">Cédula:</span> <?php echo htmlspecialchars($_SESSION['id_cedula'] ?? 'N/A'); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Usuario:</span> <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de sesión -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo $_SESSION['error']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="wizard-alert wizard-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Éxito:</strong><br>
                            <?php echo $_SESSION['success']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="wizard-alert wizard-alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Error:</strong><br>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($datos_existentes) && $datos_existentes): ?>
                    <div class="wizard-alert wizard-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Información:</strong><br>
                            Ya existe información registrada para esta cédula. Puede actualizar los datos.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $wizard_form_action; ?>" method="POST" id="<?php echo $wizard_form_id; ?>" class="wizard-form" novalidate autocomplete="off">
                    <!-- CONTENIDO DEL FORMULARIO AQUÍ -->
                    <?php 
                    // El contenido del formulario se incluye aquí
                    // Cada vista debe definir su propio contenido
                    ?>
                </form>
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <a href="<?php echo $wizard_previous_url; ?>" class="wizard-btn wizard-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Anterior
            </a>
            <div class="text-center">
                <small class="text-muted">Paso <?php echo $wizard_step; ?> de 23</small>
            </div>
            <button type="button" class="wizard-btn wizard-btn-primary wizard-btn-next" id="nextBtn" disabled>
                Siguiente
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<script src="../../../../../public/js/wizard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?php echo $wizard_form_id; ?>');
    const inputs = form.querySelectorAll('input, select, textarea');
    const nextBtn = document.getElementById('nextBtn');
    
    // Función de validación de campo
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        
        // Remover clases de validación anteriores
        field.classList.remove('is-valid', 'is-invalid');
        
        // Validación básica
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validaciones específicas por tipo
        if (field.type === 'email' && value && !isValidEmail(value)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        if (field.type === 'tel' && value && !isValidPhone(value)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        if (field.type === 'number' && value && isNaN(value)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validaciones por patrón
        if (field.pattern && value) {
            const regex = new RegExp(field.pattern);
            if (!regex.test(value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }
        
        // Validaciones por atributos
        if (field.min && value && parseFloat(value) < parseFloat(field.min)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        if (field.max && value && parseFloat(value) > parseFloat(field.max)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        if (field.maxLength && value && value.length > field.maxLength) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Si pasa todas las validaciones
        if (value || !isRequired) {
            field.classList.add('is-valid');
            return true;
        }
        
        return false;
    }
    
    // Función para verificar validez del formulario
    function checkFormValidity() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        nextBtn.disabled = !isValid;
        return isValid;
    }
    
    // Event listeners para validación en tiempo real
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
            checkFormValidity();
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
                checkFormValidity();
            }
        });
    });
    
    // Navegación con el botón siguiente
    nextBtn.addEventListener('click', function() {
        if (checkFormValidity()) {
            // Mostrar animación de carga
            nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            nextBtn.disabled = true;
            
            // Enviar formulario
            setTimeout(() => {
                form.submit();
            }, 500);
        } else {
            // Mostrar alerta
            if (window.wizard) {
                window.wizard.showAlert('Por favor complete todos los campos obligatorios antes de continuar.', 'warning');
            } else {
                alert('Por favor complete todos los campos obligatorios antes de continuar.');
            }
        }
    });
    
    // Verificar validez inicial
    checkFormValidity();
    
    // Funciones de utilidad
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[0-9]{7,10}$/;
        return phoneRegex.test(phone.replace(/\D/g, ''));
    }
});
</script>
