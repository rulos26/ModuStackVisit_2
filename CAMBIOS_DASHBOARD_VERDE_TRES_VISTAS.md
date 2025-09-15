# Cambios Dashboard Verde y Formato Monetario - Tres Vistas

## Resumen
Se aplicaron los cambios del dashboard verde de evaluador y el formato monetario con Cleave.js a las tres vistas: `estudios.php`, `informacion_judicial.php` y `experiencia_laboral.php`, siguiendo el patrón implementado en `informacion_personal.php` y `pasivos.php`.

## Vistas Modificadas

### 1. **estudios.php**
- **Ubicación**: `resources/views/evaluador/evaluacion_visita/visita/estudios/estudios.php`
- **Cambios**: Dashboard verde + formato monetario (preparado para futuros campos monetarios)

### 2. **informacion_judicial.php**
- **Ubicación**: `resources/views/evaluador/evaluacion_visita/visita/informacion_judicial/informacion_judicial.php`
- **Cambios**: Dashboard verde (sin campos monetarios en esta vista)

### 3. **experiencia_laboral.php**
- **Ubicación**: `resources/views/evaluador/evaluacion_visita/visita/experiencia_laboral/experiencia_laboral.php`
- **Cambios**: Dashboard verde + formato monetario para campos de salario

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
// Variables para manejar errores y datos
$errores_campos = [];
$datos_formulario = [];

// Guardar los datos del formulario para mantenerlos en caso de error
$datos_formulario = $datos;

// Si no hay datos del formulario (POST), usar datos existentes
if (empty($datos_formulario) && $datos_existentes !== false) {
    $datos_formulario = $datos_existentes;
}
```

### 3. **HTML - Estructura del Dashboard Verde**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Nombre Vista] - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">[Nombre Vista]</h1>
                            <p class="text-muted mb-0">[Descripción]</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

### 4. **CSS - Estilos del Dashboard Verde**
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
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}
.btn-primary {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
}
```

### 5. **CSS - Estilos para Campos Monetarios (experiencia_laboral.php)**
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

### 6. **JavaScript - Cleave.js para Formato Monetario (estudios.php y experiencia_laboral.php)**
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

// Función para inicializar estado de campos monetarios
function inicializarEstadoCampos() {
    const camposMonetarios = document.querySelectorAll('input[id$="_val"], input[name*="[salario]"]');
    camposMonetarios.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            campo.classList.add('is-valid');
        }
    });
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Cleave.js para campos monetarios existentes
    setTimeout(function() {
        const camposMonetarios = document.querySelectorAll('input[id$="_val"], input[name*="[salario]"]');
        camposMonetarios.forEach(campo => {
            inicializarCleave(campo.id);
        });
        
        // Inicializar estado de campos monetarios
        inicializarEstadoCampos();
    }, 100);
});

// Validación del formulario
document.getElementById('formEstudios').addEventListener('submit', function(event) {
    // Formatear valores monetarios antes del envío
    const camposMonetarios = document.querySelectorAll('input[id$="_val"], input[name*="[salario]"]');
    camposMonetarios.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            campo.value = formatearValorParaEnvio(campo.value);
        }
    });
});
```

### 7. **HTML - Campos Monetarios con Formato (experiencia_laboral.php)**
```html
<div class="col-md-4 mb-3">
    <label class="form-label">
        <i class="bi bi-cash me-1"></i>Salario:
    </label>
    <div class="currency-input currency-tooltip">
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" id="salario_0" name="experiencias[0][salario]" 
                   value="<?php echo formatearValorMonetario($experiencia['salario'] ?? ''); ?>"
                   placeholder="1.500.000" required>
        </div>
    </div>
    <div class="form-text">Salario mensual en pesos colombianos</div>
</div>
```

### 8. **JavaScript - Actualización para Experiencia Laboral**
```javascript
// Inicializar Cleave.js para el nuevo campo de salario
inicializarCleave(`salario_${experienciaCounter}`);

// Actualizar el HTML del campo de salario en experiencias dinámicas
<div class="currency-input currency-tooltip">
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="text" class="form-control" id="salario_${experienciaCounter}" name="experiencias[${experienciaCounter}][salario]" 
               placeholder="1.500.000" required>
    </div>
</div>
```

## Características Implementadas

### ✅ **Dashboard Verde**
- **Sidebar verde** con gradiente `linear-gradient(135deg, #11998e 0%, #38ef7d 100%)`
- **Navegación** a otras secciones del sistema
- **Header** con información del usuario
- **Diseño responsive** y moderno

### ✅ **Formato Monetario (estudios.php y experiencia_laboral.php)**
- **Cleave.js** para formato automático en tiempo real
- **Validación** del formato colombiano (`$1.500.000,50`)
- **Feedback visual** (verde para válido, rojo para inválido)
- **Tooltips informativos** sobre el formato esperado
- **Formateo automático** antes del envío al servidor

### ✅ **Campos Actualizados**
- **estudios.php**: Preparado para futuros campos monetarios
- **informacion_judicial.php**: Solo dashboard verde (sin campos monetarios)
- **experiencia_laboral.php**: Campo de salario con formato monetario

### ✅ **Funcionalidades JavaScript**
- **Inicialización automática** de Cleave.js en campos existentes
- **Validación en tiempo real** del formato monetario
- **Formateo automático** antes del envío
- **Gestión de instancias** de Cleave.js para campos dinámicos

### ✅ **Mejoras de UX**
- **Transiciones suaves** en hover y focus
- **Feedback visual** inmediato
- **Tooltips informativos**
- **Validación en tiempo real**
- **Formato consistente** en toda la aplicación

## Beneficios

1. **Consistencia Visual**: Todas las vistas ahora tienen el mismo diseño verde del dashboard
2. **Mejor UX**: Formato monetario automático y validación en tiempo real
3. **Mantenibilidad**: Código estructurado y reutilizable
4. **Accesibilidad**: Tooltips y feedback visual claro
5. **Responsive**: Diseño adaptable a diferentes tamaños de pantalla

## Archivos Modificados

1. `resources/views/evaluador/evaluacion_visita/visita/estudios/estudios.php`
2. `resources/views/evaluador/evaluacion_visita/visita/informacion_judicial/informacion_judicial.php`
3. `resources/views/evaluador/evaluacion_visita/visita/experiencia_laboral/experiencia_laboral.php`

## Dependencias Agregadas

- **Cleave.js**: `https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js`
- **Bootstrap 5**: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`
- **Bootstrap Icons**: `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css`
- **Font Awesome**: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css`

## Notas Técnicas

- **Formato monetario**: `$1.500.000,50` (peso colombiano)
- **Validación**: Regex para formato colombiano
- **Conversión**: Automática antes del envío al servidor
- **Compatibilidad**: Funciona con campos dinámicos y estáticos
- **Performance**: Inicialización diferida para evitar conflictos
