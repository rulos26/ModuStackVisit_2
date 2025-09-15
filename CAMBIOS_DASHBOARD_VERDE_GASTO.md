# Cambios Dashboard Verde y Formato Monetario - gasto.php

## Resumen
Se aplicaron los cambios del dashboard verde de evaluador y el formato monetario con Cleave.js a la vista `gasto.php`, siguiendo el patrón implementado en `informacion_personal.php` y `pasivos.php`.

## Cambios Realizados

### 1. **PHP - Función de Formato Monetario**
```php
// Función para formatear valores monetarios
function formatearValorMonetario($valor) {
    if (empty($valor) || $valor === 'N/A' || !is_numeric($valor)) {
        return '';
    }
    
    // Convertir a número
    $numero = floatval($valor);
    
    // Formatear con separadores de miles y símbolo de peso colombiano
    return '$' . number_format($numero, 0, ',', '.');
}
```

### 2. **PHP - Manejo de Datos del Formulario**
```php
// Guardar los datos del formulario para mantenerlos en caso de error
$datos_formulario = $datos;

// Si no hay datos del formulario (POST), usar datos existentes
if (empty($datos_formulario) && $datos_existentes !== false) {
    $datos_formulario = $datos_existentes;
}
```

### 3. **HTML - Estructura del Dashboard Verde**
- **Sidebar Verde**: Implementado con gradiente `linear-gradient(135deg, #11998e 0%, #38ef7d 100%)`
- **Navegación**: Enlaces a Dashboard, Carta de Autorización, Evaluación Visita Domiciliaria
- **Header**: Título "Gastos Mensuales" con información del usuario
- **Contenido Principal**: Área de contenido con fondo `#f8f9fa`

### 4. **HTML - Campos Monetarios con Formato**
```html
<!-- Ejemplo para Alimentación -->
<div class="mb-3">
    <label for="alimentacion_val" class="form-label required-field">Alimentación</label>
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="text" 
               class="form-control" 
               id="alimentacion_val" 
               name="alimentacion_val" 
               value="<?php echo formatearValorMonetario($datos_formulario['alimentacion_val'] ?? ''); ?>"
               required>
    </div>
    <div class="form-text">Ingrese el valor en pesos colombianos</div>
</div>
```

### 5. **CSS - Estilos del Dashboard**
```css
.sidebar {
    min-height: 100vh;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.sidebar .nav-link {
    color: rgba(255,255,255,0.9);
    border-radius: 8px;
    margin: 2px 0;
    transition: all 0.3s ease;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    color: white;
    background: rgba(255,255,255,0.2);
    transform: translateX(5px);
}
.main-content {
    background-color: #f8f9fa;
    min-height: 100vh;
}
```

### 6. **CSS - Estilos para Campos Monetarios**
```css
.currency-input {
    position: relative;
}

.currency-input .form-control {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.currency-input .form-control:focus {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-color: #11998e;
    box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
}

.currency-input .form-control.is-valid {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

.currency-input .form-control.is-invalid {
    border-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}

.currency-tooltip {
    position: relative;
}

.currency-tooltip::after {
    content: "Formato: $1.500.000,50";
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
}

.currency-tooltip:hover::after {
    opacity: 1;
    visibility: visible;
}
```

### 7. **JavaScript - Cleave.js para Formato Monetario**
```javascript
// Variables para Cleave.js
let cleaveInstances = {};

// Función para inicializar Cleave.js en un campo
function inicializarCleave(campoId) {
    if (cleaveInstances[campoId]) {
        cleaveInstances[campoId].destroy();
    }
    
    const campo = document.getElementById(campoId);
    if (campo) {
        cleaveInstances[campoId] = new Cleave(campo, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.',
            numeralDecimalScale: 2,
            prefix: '$ ',
            onValueChanged: function(e) {
                const input = e.target;
                // Remover clases de validación previas
                input.classList.remove('is-invalid', 'is-valid');
                
                // Validar formato monetario
                if (validarFormatoMonetario(input.value)) {
                    input.classList.add('is-valid');
                } else if (input.value.trim() !== '') {
                    input.classList.add('is-invalid');
                }
            }
        });
    }
}
```

### 8. **JavaScript - Validación de Formato Monetario**
```javascript
// Función para validar formato monetario colombiano
function validarFormatoMonetario(valor) {
    if (!valor || valor.trim() === '') return false;
    
    // Remover prefijo $ y espacios
    let valorLimpio = valor.replace(/^\$\s*/, '').trim();
    
    // Patrón para formato colombiano: 1.500.000,50 o 1500000,50
    const patronColombiano = /^(\d{1,3}(\.\d{3})*|\d+)(,\d{1,2})?$/;
    
    return patronColombiano.test(valorLimpio);
}

// Función para formatear valor para envío
function formatearValorParaEnvio(valor) {
    if (!valor || valor.trim() === '') return '';
    
    // Remover prefijo $ y espacios
    let valorLimpio = valor.replace(/^\$\s*/, '').trim();
    
    // Reemplazar punto por nada (separador de miles) y coma por punto (decimal)
    valorLimpio = valorLimpio.replace(/\./g, '').replace(',', '.');
    
    return valorLimpio;
}
```

### 9. **JavaScript - Inicialización y Validación del Formulario**
```javascript
// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Cleave.js para campos monetarios existentes
    setTimeout(function() {
        const camposMonetarios = document.querySelectorAll('input[id$="_val"]');
        camposMonetarios.forEach(campo => {
            inicializarCleave(campo.id);
        });
        
        // Inicializar estado de campos monetarios
        inicializarEstadoCampos();
    }, 100);
});

// Validación del formulario
document.getElementById('formGastos').addEventListener('submit', function(event) {
    const camposObligatorios = ['alimentacion_val', 'educacion_val', 'salud_val', 'recreacion_val', 'cuota_creditos_val', 'arriendo_val', 'servicios_publicos_val', 'otros_val'];
    
    // Validar campos obligatorios
    for (const campoId of camposObligatorios) {
        const elemento = document.getElementById(campoId);
        if (!elemento.value || elemento.value.trim() === '') {
            event.preventDefault();
            const label = elemento.closest('.mb-3').querySelector('label');
            const labelText = label ? label.innerText.replace('*', '').trim() : campoId;
            alert(`El campo "${labelText}" es obligatorio.`);
            elemento.focus();
            return;
        }
        
        // Validación específica para campos monetarios
        if (!validarFormatoMonetario(elemento.value)) {
            event.preventDefault();
            const label = elemento.closest('.mb-3').querySelector('label');
            const labelText = label ? label.innerText.replace('*', '').trim() : campoId;
            alert(`El campo "${labelText}" debe tener un formato válido (ej: $1.500.000,50).`);
            elemento.focus();
            return;
        }
    }
    
    // Formatear valores monetarios antes del envío
    const camposMonetarios = document.querySelectorAll('input[id$="_val"]');
    camposMonetarios.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            campo.value = formatearValorParaEnvio(campo.value);
        }
    });
});
```

## Campos Monetarios Actualizados

Los siguientes campos ahora tienen formato monetario con Cleave.js:

1. **Alimentación** (`alimentacion_val`)
2. **Educación** (`educacion_val`)
3. **Salud** (`salud_val`)
4. **Recreación** (`recreacion_val`)
5. **Cuota Créditos** (`cuota_creditos_val`)
6. **Arriendo** (`arriendo_val`)
7. **Servicios Públicos** (`servicios_publicos_val`)
8. **Otros** (`otros_val`)

## Características del Formato Monetario

- **Prefijo**: `$` (peso colombiano)
- **Separador de miles**: `.` (punto)
- **Separador decimal**: `,` (coma)
- **Escala decimal**: 2 dígitos máximo
- **Ejemplo**: `$1.500.000,50`

## Validaciones Implementadas

1. **Campos obligatorios**: Todos los campos monetarios son requeridos
2. **Formato monetario**: Validación del formato colombiano
3. **Valores numéricos**: Solo acepta números válidos
4. **Feedback visual**: Campos válidos en verde, inválidos en rojo
5. **Tooltips**: Información sobre el formato esperado

## Beneficios

1. **Consistencia**: Mismo diseño y funcionalidad que otras vistas
2. **Usabilidad**: Formato automático de moneda en tiempo real
3. **Validación**: Prevención de errores de formato
4. **Experiencia**: Interfaz moderna y profesional
5. **Accesibilidad**: Tooltips informativos y feedback visual

## Archivos Modificados

- `resources/views/evaluador/evaluacion_visita/visita/gasto/gasto.php`

## Dependencias Agregadas

- **Cleave.js**: `https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js`
- **Bootstrap Icons**: `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css`
- **Font Awesome**: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css`

## Notas Técnicas

- Se mantiene la compatibilidad con el controlador existente
- Los valores se formatean automáticamente para envío al servidor
- Se preserva la funcionalidad de validación del lado del servidor
- El diseño es completamente responsive
- Se implementó manejo de errores y estados de validación
