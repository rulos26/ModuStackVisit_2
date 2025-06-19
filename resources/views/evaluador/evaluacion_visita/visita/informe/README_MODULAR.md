# SISTEMA DE INFORMES MODULARIZADO

## Descripción
Este sistema de generación de informes de visita domiciliaria ha sido completamente modularizado para mejorar la mantenibilidad, escalabilidad y facilidad de desarrollo.

## Estructura de Archivos

```
informe/
├── modules/                          # Directorio de módulos
│   ├── BaseModule.php               # Clase base para todos los módulos
│   ├── PerfilModule.php             # Módulo de información personal
│   ├── CamaraComercioModule.php     # Módulo de cámara de comercio
│   ├── EstadoSaludModule.php        # Módulo de estado de salud
│   ├── ComposicionFamiliarModule.php # Módulo de composición familiar
│   ├── InformacionParejaModule.php  # Módulo de información de pareja
│   └── InventarioModule.php         # Módulo de inventario
├── sql/                             # Consultas SQL originales (mantenidas)
├── InformeModular.php               # Archivo principal modularizado
├── index.php                        # Archivo original (mantenido)
└── README_MODULAR.md                # Este archivo
```

## Características del Sistema Modular

### 1. **Clase Base (BaseModule.php)**
- Proporciona funcionalidad común para todos los módulos
- Manejo de errores centralizado
- Validación de datos estándar
- Métodos auxiliares reutilizables

### 2. **Módulos Específicos**
Cada módulo extiende `BaseModule` y maneja una sección específica del informe:
- **PerfilModule**: Información personal y foto
- **CamaraComercioModule**: Datos empresariales
- **EstadoSaludModule**: Información médica
- **ComposicionFamiliarModule**: Datos familiares
- **InformacionParejaModule**: Información de pareja
- **InventarioModule**: Inventario de enseres

### 3. **Ventajas de la Modularización**

#### ✅ **Mantenibilidad**
- Cada módulo es independiente
- Cambios en un módulo no afectan otros
- Código más limpio y organizado

#### ✅ **Escalabilidad**
- Fácil agregar nuevos módulos
- Reutilización de código
- Estructura consistente

#### ✅ **Debugging**
- Errores aislados por módulo
- Logging específico
- Testing individual

#### ✅ **Reutilización**
- Módulos pueden usarse en otros informes
- Configuración centralizada
- Estilos consistentes

## Cómo Usar el Sistema

### 1. **Generar Informe Modular**
```php
// Usar el archivo modularizado
require_once 'InformeModular.php';
```

### 2. **Agregar Nuevo Módulo**
1. Crear nuevo archivo en `modules/`
2. Extender `BaseModule`
3. Implementar métodos `obtenerDatos()` y `generarSeccion()`
4. Registrar en `InformeVisitaDomiciliariaModular::inicializarModulos()`

### 3. **Ejemplo de Nuevo Módulo**
```php
class NuevoModulo extends BaseModule {
    private $datos;
    
    protected function obtenerDatos() {
        // Lógica para obtener datos
    }
    
    public function generarSeccion() {
        // Lógica para generar HTML
    }
}
```

## Configuración

### Constantes Globales
```php
define('REPORT_VERSION', '3.0');
define('REPORT_CODE', 'PSI-FR-11');
define('REPORT_VALIDITY', 'Enero 2024');
```

### Estilos CSS
Los estilos están centralizados en `ConfiguracionInforme`:
- Tablas estándar
- Headers consistentes
- Bordes uniformes

## Logging y Errores

### Sistema de Logging
- Archivo: `informe_errors.log`
- Niveles: INFO, ERROR
- Timestamps automáticos

### Manejo de Errores
- Try-catch en ejecución principal
- Validación de datos en cada módulo
- Valores por defecto para datos faltantes

## Migración desde Sistema Original

### 1. **Mantener Compatibilidad**
- El archivo `index.php` original se mantiene
- Los archivos SQL se conservan
- Transición gradual posible

### 2. **Ventajas de Migración**
- Código más limpio
- Menos warnings de TCPDF
- Mejor organización
- Fácil mantenimiento

## Próximos Pasos

### 1. **Módulos Pendientes**
- TipoViviendaModule
- ServiciosModule
- PatrimonioModule
- CuentasBancariasModule
- PasivosModule
- AportanteModule
- IngresosModule
- GastosModule
- EstudiosModule
- ExperienciaLaboralModule
- InformacionJudicialModule
- ConceptoFinalModule
- EvidenciasFotograficasModule

### 2. **Mejoras Futuras**
- Sistema de plantillas (Blade/Twig)
- Cache de datos
- Validación avanzada
- Testing automatizado
- API REST para datos

## Contacto y Soporte
Para dudas o mejoras, contactar al equipo de desarrollo.

---
**Versión**: 3.0  
**Fecha**: Enero 2024  
**Autor**: Sistema de Informes 