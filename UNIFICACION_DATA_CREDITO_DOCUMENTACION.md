# Unificación de Vistas Data Crédito - Documentación

## Resumen
Se ha unificado exitosamente las vistas `data_credito.php` y `reportado.php` en una sola vista `data_credito.php`, siguiendo el mismo patrón implementado anteriormente para `tiene_pasivo.php` y `pasivos.php`.

## Problemas Identificados
1. **Experiencia fragmentada**: El usuario tenía que navegar entre dos vistas separadas
2. **Duplicación de código**: Lógica similar en ambas vistas
3. **Navegación confusa**: Redirecciones innecesarias entre vistas
4. **Mantenimiento complejo**: Dos archivos que gestionar para la misma funcionalidad

## Solución Implementada

### 1. Vista Unificada (`data_credito.php`)

#### **Dashboard Verde Integrado**
- Aplicado el mismo diseño de dashboard verde usado en `informacion_personal.php`
- Sidebar con navegación y gradiente verde (`linear-gradient(135deg, #11998e 0%, #38ef7d 100%)`)
- Header responsivo con información del usuario y fecha
- Indicador de pasos horizontal actualizado

#### **Pregunta Inicial**
```html
<select class="form-select" id="reportado_centrales" name="reportado_centrales" required onchange="toggleCamposReportes()">
    <option value="">Seleccione una opción</option>
    <option value="0">No</option>
    <option value="1">Sí</option>
</select>
```

#### **Campos Dinámicos**
- Los campos detallados se muestran/ocultan dinámicamente usando JavaScript
- CSS con transiciones suaves para mejor UX
- Formulario completo para reportes detallados cuando se selecciona "Sí"

#### **Formato de Moneda con Cleave.js**
- Integración de Cleave.js para formateo automático de campos monetarios
- Formato colombiano: `$ 1.500.000,50`
- Validación en tiempo real con feedback visual
- Tooltips informativos para guiar al usuario

### 2. Controlador Actualizado (`DataCreditoController.php`)

#### **Método `validarDatos()` Mejorado**
```php
public function validarDatos($datos) {
    $errores = [];
    
    // Primero validar si está reportado
    if (!isset($datos['reportado_centrales']) || empty($datos['reportado_centrales'])) {
        $errores[] = "Debe seleccionar si está reportado en centrales de riesgo.";
        return $errores;
    }
    
    // Si no está reportado, no validar campos detallados
    if ($datos['reportado_centrales'] == '0') {
        return $errores;
    }
    
    // Si está reportado, validar campos detallados
    if ($datos['reportado_centrales'] == '1') {
        // ... validación de campos detallados
    }
    
    return $errores;
}
```

#### **Método `guardar()` Unificado**
```php
public function guardar($datos) {
    $reportado_centrales = $datos['reportado_centrales'];
    
    // Si el usuario indica que NO está reportado
    if ($reportado_centrales == '0') {
        // Eliminar registros existentes e insertar 'N/A'
        $sql_delete = "DELETE FROM data_credito WHERE id_cedula = :id_cedula";
        $sql = "INSERT INTO data_credito (id_cedula, entidad, cuotas, pago_mensual, deuda) 
                VALUES (:id_cedula, 'N/A', 'N/A', 'N/A', 'N/A')";
    }
    
    // Si el usuario indica que SÍ está reportado
    if ($reportado_centrales == '1') {
        // Eliminar registros existentes e insertar reportes detallados
        // ... lógica para múltiples reportes
    }
}
```

### 3. JavaScript Integrado

#### **Funciones Principales**
- `toggleCamposReportes()`: Muestra/oculta campos dinámicos
- `inicializarCleave()`: Configura formateo monetario
- `validarFormatoMonetario()`: Valida formato colombiano
- `formatearValorParaEnvio()`: Convierte formato para backend
- `removeReporte()`: Elimina reportes dinámicos

#### **Event Listeners**
- Inicialización automática de Cleave.js en campos monetarios
- Validación en tiempo real con feedback visual
- Manejo de formularios dinámicos
- Prevención de envío con errores

### 4. Archivos Eliminados
- ❌ `reportado.php` - Vista eliminada (funcionalidad integrada)

### 5. Redirecciones Actualizadas
- ✅ `vali.php` - Actualizada redirección de `reportado.php` a `data_credito.php`

## Beneficios de la Unificación

### **Experiencia de Usuario**
- ✅ **Flujo continuo**: Todo en una sola pantalla
- ✅ **Navegación simplificada**: Sin redirecciones innecesarias
- ✅ **Feedback visual**: Campos monetarios con formato automático
- ✅ **Validación en tiempo real**: Errores mostrados inmediatamente

### **Mantenimiento**
- ✅ **Código más limpio**: Sin duplicación
- ✅ **Un solo archivo**: Más fácil de mantener
- ✅ **Lógica centralizada**: Controlador unificado
- ✅ **Consistencia**: Mismo patrón que otros módulos

### **Funcionalidad**
- ✅ **Formato monetario**: Cleave.js con formato colombiano
- ✅ **Campos dinámicos**: Agregar/eliminar reportes
- ✅ **Validación robusta**: Cliente y servidor
- ✅ **Persistencia de datos**: Manejo correcto de errores

## Estructura Final

```
data_credito/
├── data_credito.php          # Vista unificada (mantenida)
├── DataCreditoController.php # Controlador actualizado
└── reportado.php            # ❌ Eliminado
```

## Patrón de Unificación

Esta implementación sigue el mismo patrón exitoso usado en:
- `tiene_pasivo.php` + `pasivos.php` → `pasivos.php` unificado
- `tiene_pareja.php` + formulario detallado → `tiene_pareja.php` unificado

## Próximos Pasos Recomendados

1. **Probar la funcionalidad** con diferentes escenarios
2. **Verificar redirecciones** desde otros módulos
3. **Validar formato monetario** con diferentes valores
4. **Confirmar persistencia** de datos en la base de datos

## Conclusión

La unificación de `data_credito.php` y `reportado.php` ha sido exitosa, proporcionando:
- Mejor experiencia de usuario
- Código más mantenible
- Funcionalidad robusta
- Consistencia con el resto del sistema

El patrón implementado puede ser replicado en otros módulos que tengan la misma estructura de "pregunta inicial + formulario detallado".
