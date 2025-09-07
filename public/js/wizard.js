/**
 * WIZARD MODERNO Y RESPONSIVE
 * Sistema de Visitas Domiciliarias
 * 
 * Funcionalidades:
 * - Navegación entre pasos
 * - Validación en tiempo real
 * - Animaciones suaves
 * - Responsive design
 * - Persistencia de datos
 */

class WizardManager {
    constructor(options = {}) {
        this.currentStep = options.currentStep || 1;
        this.totalSteps = options.totalSteps || 6;
        this.steps = [];
        this.formData = {};
        this.isValidating = false;
        this.animationDuration = 300;
        
        this.init();
    }

    init() {
        this.setupSteps();
        this.setupEventListeners();
        this.updateProgress();
        this.loadSavedData();
    }

    setupSteps() {
        // Definir los pasos del wizard basado en el flujo de trabajo
        this.steps = [
            {
                id: 1,
                title: 'Datos Básicos',
                description: 'Ingreso de cédula',
                icon: 'fas fa-user',
                url: 'index.php',
                formId: 'formDocumento'
            },
            {
                id: 2,
                title: 'Información Personal',
                description: 'Datos personales completos',
                icon: 'fas fa-id-card',
                url: 'informacion_personal/informacion_personal.php',
                formId: 'formInformacionPersonal'
            },
            {
                id: 3,
                title: 'Cámara de Comercio',
                description: 'Información empresarial',
                icon: 'fas fa-building',
                url: 'camara_comercio/camara_comercio.php',
                formId: 'formCamaraComercio'
            },
            {
                id: 4,
                title: 'Salud',
                description: 'Estado de salud',
                icon: 'fas fa-heartbeat',
                url: 'salud/salud.php',
                formId: 'formSalud'
            },
            {
                id: 5,
                title: 'Composición Familiar',
                description: 'Miembros de la familia',
                icon: 'fas fa-users',
                url: 'composición_familiar/composición_familiar.php',
                formId: 'formComposicionFamiliar'
            },
            {
                id: 6,
                title: 'Información de Pareja',
                description: 'Datos de la pareja',
                icon: 'fas fa-heart',
                url: 'informacion_pareja/tiene_pareja.php',
                formId: 'formTienePareja'
            },
            {
                id: 7,
                title: 'Tipo de Vivienda',
                description: 'Características de la vivienda',
                icon: 'fas fa-home',
                url: 'tipo_vivienda/tipo_vivienda.php',
                formId: 'formTipoVivienda'
            },
            {
                id: 8,
                title: 'Estado de Vivienda',
                description: 'Condiciones físicas',
                icon: 'fas fa-tools',
                url: 'estado_vivienda/estado_vivienda.php',
                formId: 'formEstadoVivienda'
            },
            {
                id: 9,
                title: 'Inventario de Enseres',
                description: 'Bienes del hogar',
                icon: 'fas fa-couch',
                url: 'inventario_enseres/inventario_enseres.php',
                formId: 'formInventarioEnseres'
            },
            {
                id: 10,
                title: 'Servicios Públicos',
                description: 'Servicios disponibles',
                icon: 'fas fa-bolt',
                url: 'servicios_publicos/servicios_publicos.php',
                formId: 'formServiciosPublicos'
            },
            {
                id: 11,
                title: 'Cuentas Bancarias',
                description: 'Información financiera',
                icon: 'fas fa-university',
                url: 'cuentas_bancarias/cuentas_bancarias.php',
                formId: 'formCuentasBancarias'
            },
            {
                id: 12,
                title: 'Tiene Pasivo',
                description: 'Verificación de pasivos',
                icon: 'fas fa-exclamation-triangle',
                url: 'tiene_pasivo/tiene_pasivo.php',
                formId: 'formTienePasivo'
            },
            {
                id: 13,
                title: 'Pasivos',
                description: 'Detalle de pasivos',
                icon: 'fas fa-credit-card',
                url: 'pasivos/pasivos.php',
                formId: 'formPasivos'
            },
            {
                id: 14,
                title: 'Aportante',
                description: 'Información del aportante',
                icon: 'fas fa-hand-holding-usd',
                url: 'aportante/aportante.php',
                formId: 'formAportante'
            },
            {
                id: 15,
                title: 'Data Crédito',
                description: 'Estado crediticio',
                icon: 'fas fa-chart-line',
                url: 'data_credito/data_credito.php',
                formId: 'formDataCredito'
            },
            {
                id: 16,
                title: 'Reportado',
                description: 'Estado de reporte',
                icon: 'fas fa-flag',
                url: 'reportado/reportado.php',
                formId: 'formReportado'
            },
            {
                id: 17,
                title: 'Ingresos Mensuales',
                description: 'Fuentes de ingresos',
                icon: 'fas fa-money-bill-wave',
                url: 'ingresos_mensuales/ingresos_mensuales.php',
                formId: 'formIngresosMensuales'
            },
            {
                id: 18,
                title: 'Gastos',
                description: 'Gastos mensuales',
                icon: 'fas fa-receipt',
                url: 'gasto/gasto.php',
                formId: 'formGasto'
            },
            {
                id: 19,
                title: 'Estudios',
                description: 'Formación académica',
                icon: 'fas fa-graduation-cap',
                url: 'estudios/estudios.php',
                formId: 'formEstudios'
            },
            {
                id: 20,
                title: 'Información Judicial',
                description: 'Antecedentes judiciales',
                icon: 'fas fa-gavel',
                url: 'informacion_judicial/informacion_judicial.php',
                formId: 'formInformacionJudicial'
            },
            {
                id: 21,
                title: 'Experiencia Laboral',
                description: 'Historial laboral',
                icon: 'fas fa-briefcase',
                url: 'experiencia_laboral/experiencia_laboral.php',
                formId: 'formExperienciaLaboral'
            },
            {
                id: 22,
                title: 'Concepto Final',
                description: 'Evaluación final',
                icon: 'fas fa-clipboard-check',
                url: 'concepto_final_evaluador/concepto_final_evaluador.php',
                formId: 'formConceptoFinal'
            }
        ];

        this.totalSteps = this.steps.length;
    }

    setupEventListeners() {
        // Botones de navegación
        document.addEventListener('click', (e) => {
            if (e.target.matches('.wizard-btn-next, .wizard-btn-primary')) {
                e.preventDefault();
                this.nextStep();
            }
            
            if (e.target.matches('.wizard-btn-prev, .wizard-btn-secondary')) {
                e.preventDefault();
                this.prevStep();
            }
        });

        // Validación en tiempo real
        document.addEventListener('input', (e) => {
            if (e.target.matches('.wizard-form input, .wizard-form select, .wizard-form textarea')) {
                this.validateField(e.target);
                this.saveFormData();
            }
        });

        // Cambios en selects para campos condicionales
        document.addEventListener('change', (e) => {
            if (e.target.matches('.wizard-form select')) {
                this.handleConditionalFields(e.target);
            }
        });

        // Prevenir envío accidental del formulario
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.wizard-form')) {
                e.preventDefault();
                this.submitForm(e.target);
            }
        });

        // Navegación por teclado
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                this.nextStep();
            }
            
            if (e.key === 'Backspace' && e.ctrlKey) {
                e.preventDefault();
                this.prevStep();
            }
        });
    }

    updateProgress() {
        const progressSteps = document.querySelectorAll('.wizard-step');
        
        progressSteps.forEach((step, index) => {
            const stepNumber = index + 1;
            const stepElement = step;
            
            // Remover clases existentes
            stepElement.classList.remove('active', 'completed');
            
            if (stepNumber < this.currentStep) {
                stepElement.classList.add('completed');
            } else if (stepNumber === this.currentStep) {
                stepElement.classList.add('active');
            }
        });

        // Actualizar barra de progreso
        const progressBar = document.querySelector('.wizard-progress-bar');
        if (progressBar) {
            const progress = (this.currentStep / this.totalSteps) * 100;
            progressBar.style.width = `${progress}%`;
        }
    }

    nextStep() {
        if (this.isValidating) return;
        
        const currentForm = document.querySelector('.wizard-form');
        if (currentForm && !this.validateForm(currentForm)) {
            this.showAlert('Por favor complete todos los campos obligatorios.', 'warning');
            return;
        }

        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.saveProgress();
            this.navigateToStep();
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.saveProgress();
            this.navigateToStep();
        }
    }

    navigateToStep() {
        const currentStepData = this.steps.find(step => step.id === this.currentStep);
        if (currentStepData) {
            // Mostrar animación de transición
            this.showTransition();
            
            setTimeout(() => {
                window.location.href = currentStepData.url;
            }, this.animationDuration);
        }
    }

    validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const isRequired = field.hasAttribute('required');
        
        // Remover clases de validación anteriores
        field.classList.remove('is-valid', 'is-invalid');
        
        // Validación básica
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validaciones específicas por tipo
        switch (fieldType) {
            case 'email':
                if (value && !this.isValidEmail(value)) {
                    field.classList.add('is-invalid');
                    return false;
                }
                break;
                
            case 'tel':
                if (value && !this.isValidPhone(value)) {
                    field.classList.add('is-invalid');
                    return false;
                }
                break;
                
            case 'number':
                if (value && isNaN(value)) {
                    field.classList.add('is-invalid');
                    return false;
                }
                break;
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

    handleConditionalFields(selectElement) {
        const value = selectElement.value;
        const fieldName = selectElement.name;
        
        // Buscar campos condicionales relacionados
        const conditionalFields = document.querySelectorAll(`[data-depends-on="${fieldName}"]`);
        
        conditionalFields.forEach(field => {
            const dependsOnValue = field.getAttribute('data-depends-value');
            const fieldContainer = field.closest('.wizard-conditional-fields') || field;
            
            if (value === dependsOnValue) {
                fieldContainer.classList.add('show');
                fieldContainer.style.display = 'block';
                
                // Hacer el campo requerido si es necesario
                if (field.hasAttribute('data-required-when-visible')) {
                    field.setAttribute('required', 'required');
                }
            } else {
                fieldContainer.classList.remove('show');
                fieldContainer.style.display = 'none';
                
                // Limpiar el valor y remover required
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = false;
                } else {
                    field.value = '';
                }
                field.removeAttribute('required');
            }
        });
    }

    submitForm(form) {
        if (!this.validateForm(form)) {
            this.showAlert('Por favor complete todos los campos obligatorios.', 'warning');
            return;
        }
        
        this.isValidating = true;
        this.showLoading();
        
        // Simular envío del formulario
        setTimeout(() => {
            form.submit();
        }, 1000);
    }

    saveFormData() {
        const form = document.querySelector('.wizard-form');
        if (!form) return;
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        this.formData = { ...this.formData, ...data };
        localStorage.setItem('wizardFormData', JSON.stringify(this.formData));
    }

    loadSavedData() {
        const savedData = localStorage.getItem('wizardFormData');
        if (savedData) {
            this.formData = JSON.parse(savedData);
            this.populateForm();
        }
        
        const savedProgress = localStorage.getItem('wizardProgress');
        if (savedProgress) {
            this.currentStep = parseInt(savedProgress);
        }
    }

    populateForm() {
        const form = document.querySelector('.wizard-form');
        if (!form) return;
        
        Object.keys(this.formData).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = field.value === this.formData[key];
                } else {
                    field.value = this.formData[key];
                }
                
                // Validar el campo después de poblar
                this.validateField(field);
            }
        });
    }

    saveProgress() {
        localStorage.setItem('wizardProgress', this.currentStep.toString());
    }

    showAlert(message, type = 'info') {
        const alertContainer = document.querySelector('.wizard-alerts') || this.createAlertContainer();
        
        const alert = document.createElement('div');
        alert.className = `wizard-alert wizard-alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-${this.getAlertIcon(type)}"></i>
            <span>${message}</span>
            <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    }

    createAlertContainer() {
        const container = document.createElement('div');
        container.className = 'wizard-alerts';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        container.style.maxWidth = '400px';
        
        document.body.appendChild(container);
        return container;
    }

    getAlertIcon(type) {
        const icons = {
            success: 'check-circle',
            danger: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    showLoading() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'wizard-loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="wizard-loading">
                <div class="wizard-spinner"></div>
                <p>Procesando...</p>
            </div>
        `;
        
        loadingOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        `;
        
        document.body.appendChild(loadingOverlay);
    }

    showTransition() {
        const transitionOverlay = document.createElement('div');
        transitionOverlay.className = 'wizard-transition';
        transitionOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        transitionOverlay.innerHTML = `
            <div style="text-align: center; color: white;">
                <div class="wizard-spinner" style="border-top-color: white; margin: 0 auto 20px;"></div>
                <h3>Navegando al siguiente paso...</h3>
            </div>
        `;
        
        document.body.appendChild(transitionOverlay);
        
        // Animar entrada
        setTimeout(() => {
            transitionOverlay.style.opacity = '1';
        }, 10);
    }

    // Métodos de utilidad
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^[0-9]{7,10}$/;
        return phoneRegex.test(phone.replace(/\D/g, ''));
    }

    // Método público para limpiar datos guardados
    clearSavedData() {
        localStorage.removeItem('wizardFormData');
        localStorage.removeItem('wizardProgress');
        this.formData = {};
        this.currentStep = 1;
    }

    // Método público para obtener datos del formulario
    getFormData() {
        return this.formData;
    }

    // Método público para establecer el paso actual
    setCurrentStep(step) {
        if (step >= 1 && step <= this.totalSteps) {
            this.currentStep = step;
            this.updateProgress();
            this.saveProgress();
        }
    }
}

// Inicializar el wizard cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Detectar el paso actual basado en la URL
    const currentPath = window.location.pathname;
    let currentStep = 1;
    
    // Mapear URLs a pasos
    const urlToStep = {
        'index.php': 1,
        'informacion_personal.php': 2,
        'camara_comercio.php': 3,
        'salud.php': 4,
        'composición_familiar.php': 5,
        'tiene_pareja.php': 6,
        'tipo_vivienda.php': 7,
        'estado_vivienda.php': 8,
        'inventario_enseres.php': 9,
        'servicios_publicos.php': 10,
        'cuentas_bancarias.php': 11,
        'tiene_pasivo.php': 12,
        'pasivos.php': 13,
        'aportante.php': 14,
        'data_credito.php': 15,
        'reportado.php': 16,
        'ingresos_mensuales.php': 17,
        'gasto.php': 18,
        'estudios.php': 19,
        'informacion_judicial.php': 20,
        'experiencia_laboral.php': 21,
        'concepto_final_evaluador.php': 22
    };
    
    // Encontrar el paso actual
    for (const [url, step] of Object.entries(urlToStep)) {
        if (currentPath.includes(url)) {
            currentStep = step;
            break;
        }
    }
    
    // Inicializar el wizard
    window.wizard = new WizardManager({
        currentStep: currentStep,
        totalSteps: 22
    });
    
    // Hacer el wizard disponible globalmente
    window.WizardManager = WizardManager;
});

// Funciones de utilidad globales
window.wizardUtils = {
    // Formatear número de teléfono
    formatPhone: function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 7) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
        }
        input.value = value;
    },
    
    // Formatear número de cédula
    formatCedula: function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 7) {
            value = value.replace(/(\d{1,3})(\d{3})(\d{3})/, '$1.$2.$3');
        }
        input.value = value;
    },
    
    // Validar cédula colombiana
    validateCedula: function(cedula) {
        if (!cedula || cedula.length < 6) return false;
        
        const cleanCedula = cedula.replace(/\D/g, '');
        if (cleanCedula.length < 6 || cleanCedula.length > 10) return false;
        
        return true;
    },
    
    // Calcular edad a partir de fecha de nacimiento
    calculateAge: function(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }
};
