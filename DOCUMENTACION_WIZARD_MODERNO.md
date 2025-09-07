# 🧙‍♂️ DOCUMENTACIÓN - WIZARD MODERNO Y RESPONSIVE

## 📋 Descripción General

Se ha implementado un sistema de wizard moderno y responsive para el flujo de trabajo de visitas domiciliarias. El nuevo sistema proporciona una experiencia de usuario mejorada con navegación intuitiva, validación en tiempo real y diseño adaptativo para múltiples dispositivos.

## 🎨 Características Principales

### ✨ Diseño Moderno
- **Gradientes y sombras**: Diseño visual atractivo con gradientes y efectos de sombra
- **Iconografía consistente**: Uso de Font Awesome para iconos uniformes
- **Animaciones suaves**: Transiciones y animaciones CSS para mejor UX
- **Tipografía mejorada**: Fuentes modernas y legibles

### 📱 Responsive Design
- **Mobile First**: Optimizado para dispositivos móviles
- **Breakpoints adaptativos**: 
  - Desktop: > 768px
  - Tablet: 768px - 480px
  - Mobile: < 480px
- **Navegación flexible**: Adaptación automática del layout según el dispositivo

### 🔄 Funcionalidades Avanzadas
- **Validación en tiempo real**: Feedback inmediato al usuario
- **Persistencia de datos**: Guardado automático en localStorage
- **Navegación inteligente**: Botones habilitados/deshabilitados según validación
- **Campos condicionales**: Mostrar/ocultar campos según selecciones
- **Formateo automático**: Formateo de teléfonos y cédulas

## 📁 Archivos Creados

### 1. `public/css/wizard-styles.css`
Archivo CSS principal con todos los estilos del wizard:
- Variables CSS para consistencia
- Estilos responsive
- Animaciones y transiciones
- Modo oscuro (opcional)

### 2. `public/js/wizard.js`
Clase JavaScript principal que maneja:
- Navegación entre pasos
- Validación de formularios
- Persistencia de datos
- Manejo de campos condicionales
- Utilidades de formateo

### 3. `resources/views/evaluador/evaluacion_visita/visita/wizard-template.php`
Plantilla base reutilizable para todas las vistas del wizard.

## 🚀 Implementación

### Paso 1: Incluir Archivos Base
```php
<link rel="stylesheet" href="../../../../../public/css/wizard-styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="../../../../../public/js/wizard.js"></script>
```

### Paso 2: Estructura HTML
```html
<div class="wizard-container">
    <div class="wizard-card">
        <!-- Header del Wizard -->
        <div class="wizard-header">
            <h1><i class="fas fa-icon me-2"></i>TÍTULO DEL PASO</h1>
            <p class="subtitle">Descripción del paso</p>
        </div>

        <!-- Barra de Progreso -->
        <div class="wizard-progress">
            <div class="wizard-steps">
                <!-- Pasos del wizard -->
            </div>
        </div>

        <!-- Contenido del Wizard -->
        <div class="wizard-content">
            <div class="wizard-step-content active">
                <!-- Formulario -->
            </div>
        </div>

        <!-- Navegación del Wizard -->
        <div class="wizard-navigation">
            <!-- Botones de navegación -->
        </div>
    </div>
</div>
```

### Paso 3: Configuración de Variables
```php
$wizard_step = 2; // Número del paso actual
$wizard_title = 'INFORMACIÓN PERSONAL';
$wizard_subtitle = 'Datos personales completos del evaluado';
$wizard_icon = 'fas fa-id-card';
$wizard_form_id = 'formInformacionPersonal';
$wizard_form_action = '';
$wizard_previous_url = '../index.php';
$wizard_next_url = '../camara_comercio/camara_comercio.php';
```

## 🎯 Vistas Modificadas

### ✅ Completadas
1. **index.php** - Paso 1: Datos Básicos
2. **informacion_personal.php** - Paso 2: Información Personal
3. **camara_comercio.php** - Paso 3: Cámara de Comercio (parcial)

### 🔄 En Progreso
4. **salud.php** - Paso 4: Salud
5. **composicion_familiar.php** - Paso 5: Composición Familiar
6. **tiene_pareja.php** - Paso 6: Información de Pareja
7. **tipo_vivienda.php** - Paso 7: Tipo de Vivienda
8. **estado_vivienda.php** - Paso 8: Estado de Vivienda
9. **inventario_enseres.php** - Paso 9: Inventario de Enseres
10. **servicios_publicos.php** - Paso 10: Servicios Públicos
11. **cuentas_bancarias.php** - Paso 11: Cuentas Bancarias
12. **tiene_pasivo.php** - Paso 12: Tiene Pasivo
13. **pasivos.php** - Paso 13: Pasivos
14. **aportante.php** - Paso 14: Aportante
15. **data_credito.php** - Paso 15: Data Crédito
16. **reportado.php** - Paso 16: Reportado
17. **ingresos_mensuales.php** - Paso 17: Ingresos Mensuales
18. **gasto.php** - Paso 18: Gastos
19. **estudios.php** - Paso 19: Estudios
20. **informacion_judicial.php** - Paso 20: Información Judicial
21. **experiencia_laboral.php** - Paso 21: Experiencia Laboral
22. **concepto_final_evaluador.php** - Paso 22: Concepto Final

## 🛠️ Clases CSS Principales

### Contenedores
- `.wizard-container` - Contenedor principal
- `.wizard-card` - Card del wizard
- `.wizard-header` - Header con título
- `.wizard-progress` - Barra de progreso
- `.wizard-content` - Contenido principal
- `.wizard-navigation` - Navegación

### Pasos
- `.wizard-step` - Paso individual
- `.wizard-step.active` - Paso activo
- `.wizard-step.completed` - Paso completado
- `.wizard-step-icon` - Icono del paso
- `.wizard-step-title` - Título del paso
- `.wizard-step-description` - Descripción del paso

### Formularios
- `.wizard-form` - Formulario del wizard
- `.form-group` - Grupo de campo
- `.form-label` - Etiqueta del campo
- `.form-control` - Campo de entrada
- `.form-select` - Campo de selección

### Botones
- `.wizard-btn` - Botón base
- `.wizard-btn-primary` - Botón primario
- `.wizard-btn-secondary` - Botón secundario
- `.wizard-btn-success` - Botón de éxito

### Alertas
- `.wizard-alert` - Alerta base
- `.wizard-alert-success` - Alerta de éxito
- `.wizard-alert-danger` - Alerta de error
- `.wizard-alert-warning` - Alerta de advertencia
- `.wizard-alert-info` - Alerta informativa

## 🔧 JavaScript API

### Clase WizardManager
```javascript
// Inicialización
const wizard = new WizardManager({
    currentStep: 1,
    totalSteps: 22
});

// Métodos principales
wizard.nextStep();           // Ir al siguiente paso
wizard.prevStep();           // Ir al paso anterior
wizard.setCurrentStep(5);    // Establecer paso específico
wizard.validateForm(form);   // Validar formulario
wizard.saveFormData();       // Guardar datos
wizard.clearSavedData();     // Limpiar datos guardados
```

### Utilidades
```javascript
// Formateo de teléfonos
wizardUtils.formatPhone(input);

// Formateo de cédulas
wizardUtils.formatCedula(input);

// Validación de cédula
wizardUtils.validateCedula(cedula);

// Cálculo de edad
wizardUtils.calculateAge(birthDate);
```

## 📱 Responsive Breakpoints

### Desktop (> 768px)
- Layout horizontal completo
- Barra de progreso con 6 pasos visibles
- Formularios en múltiples columnas

### Tablet (768px - 480px)
- Layout adaptativo
- Barra de progreso con 3-4 pasos visibles
- Formularios en 2 columnas

### Mobile (< 480px)
- Layout vertical
- Barra de progreso en columna
- Formularios en 1 columna
- Botones de navegación apilados

## 🎨 Personalización

### Variables CSS
```css
:root {
    --primary-color: #4361ee;
    --success-color: #2ecc71;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
```

### Temas
- **Claro**: Por defecto
- **Oscuro**: Activado con `@media (prefers-color-scheme: dark)`

## 🔍 Validaciones Implementadas

### Campos de Texto
- Longitud mínima/máxima
- Patrones regex
- Caracteres permitidos

### Campos Numéricos
- Rango mínimo/máximo
- Validación de números
- Formateo automático

### Campos de Email
- Formato de email válido
- Dominio verificado

### Campos de Teléfono
- Formato colombiano
- Longitud correcta
- Solo números

### Campos Condicionales
- Mostrar/ocultar según selección
- Validación condicional
- Limpieza automática

## 🚀 Próximos Pasos

### Implementación Restante
1. **Completar todas las vistas** usando la plantilla base
2. **Probar responsividad** en diferentes dispositivos
3. **Optimizar rendimiento** de animaciones
4. **Agregar tests** de funcionalidad
5. **Documentar casos de uso** específicos

### Mejoras Futuras
- **Modo offline** con Service Workers
- **Progreso persistente** en base de datos
- **Validaciones del servidor** mejoradas
- **Integración con APIs** externas
- **Analytics** de uso del wizard

## 📞 Soporte

Para dudas o problemas con la implementación del wizard:
1. Revisar esta documentación
2. Verificar la consola del navegador
3. Comprobar que todos los archivos estén incluidos
4. Validar la estructura HTML

---

**Versión**: 1.0  
**Fecha**: Diciembre 2024  
**Autor**: Sistema de Visitas Domiciliarias v2.0
