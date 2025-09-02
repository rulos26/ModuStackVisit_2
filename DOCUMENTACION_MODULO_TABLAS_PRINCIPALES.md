# 📊 Módulo de Gestión de Tablas Principales

## 📋 Descripción General

El **Módulo de Gestión de Tablas Principales** es una herramienta avanzada diseñada exclusivamente para Superadministradores del sistema. Permite administrar y gestionar las tablas principales del programa, incluyendo operaciones de eliminación selectiva por cédula de usuario y truncamiento completo de tablas.

## 🎯 Objetivos del Módulo

- **Gestión Centralizada**: Administrar todas las tablas principales del sistema desde una interfaz unificada
- **Eliminación Selectiva**: Eliminar registros específicos por número de cédula en tablas individuales o múltiples
- **Limpieza de Datos**: Truncar tablas completas para mantenimiento del sistema
- **Monitoreo**: Obtener estadísticas detalladas de cada tabla y del sistema en general
- **Seguridad**: Garantizar que solo Superadministradores puedan acceder a estas funciones críticas

## 🏗️ Arquitectura del Sistema

### **Componentes Principales**

1. **`TablasPrincipalesController`** - Controlador principal con lógica de negocio
2. **`procesar_tablas_principales.php`** - Script de procesamiento de operaciones
3. **`gestion_tablas_principales.php`** - Interfaz de usuario principal
4. **Integración con menú** - Enlace en el menú del Superadministrador

### **Estructura de Archivos**

```
app/Controllers/
├── TablasPrincipalesController.php          # Controlador principal

resources/views/superadmin/
├── procesar_tablas_principales.php         # Script de procesamiento
├── gestion_tablas_principales.php          # Vista principal

resources/views/layout/
└── menu.php                                # Menú actualizado

tests/Unit/
└── TestModuloTablasPrincipales.php         # Script de pruebas
```

## 🗄️ Tablas Principales del Sistema

### **Tabla Central: `usuarios`**
- **Propósito**: Gestión de usuarios y autenticación del sistema
- **Columna Cédula**: `cedula`
- **Descripción**: Tabla principal de usuarios y autenticación

### **Tablas Relacionadas (por `id_cedula` o `cedula`)**

| Tabla | Columna Cédula | Descripción |
|-------|----------------|-------------|
| `aportante` | `id_cedula` | Información de aportantes del usuario |
| `autorizaciones` | `cedula` | Autorizaciones del usuario |
| `camara_comercio` | `id_cedula` | Información de cámara de comercio |
| `composicion_familiar` | `id_cedula` | Composición familiar del usuario |
| `concepto_final_evaluador` | `id_cedula` | Concepto final del evaluador |
| `cuentas_bancarias` | `id_cedula` | Cuentas bancarias del usuario |
| `data_credito` | `id_cedula` | Datos de crédito del usuario |
| `evidencia_fotografica` | `id_cedula` | Evidencia fotográfica del usuario |
| `experiencia_laboral` | `id_cedula` | Experiencia laboral del usuario |
| `firmas` | `id_cedula` | Firmas del usuario |
| `foto_perfil_autorizacion` | `id_cedula` | Fotos de perfil para autorización |
| `foto_perfil_visita` | `id_cedula` | Fotos de perfil para visita |
| `gasto` | `id_cedula` | Gastos del usuario |
| `informacion_judicial` | `id_cedula` | Información judicial del usuario |
| `informacion_pareja` | `id_cedula` | Información de pareja del usuario |
| `ingresos_mensuales` | `id_cedula` | Ingresos mensuales del usuario |
| `inventario_enseres` | `id_cedula` | Inventario de enseres del usuario |
| `ubicacion` | `id_cedula` | Ubicación del usuario |
| `ubicacion_autorizacion` | `id_cedula` | Ubicación para autorización |
| `ubicacion_foto` | `id_cedula` | Fotos de ubicación del usuario |

### **Tabla Especial: `formularios`**
- **Propósito**: Gestión de formularios del sistema
- **Columna Cédula**: No aplica (tabla de configuración)
- **Descripción**: Formularios del sistema (sin cédula)

## ⚙️ Funcionalidades Principales

### **1. Estadísticas del Sistema**
- **Estadísticas Generales**: Total de tablas, registros y tablas con cédula
- **Estadísticas por Tabla**: Registros totales, cédulas únicas, registros sin cédula
- **Información de Tabla**: Nombre, descripción, columna de cédula

### **2. Eliminación por Cédula**
- **Eliminación Individual**: Eliminar registros de una cédula específica en una tabla
- **Eliminación Masiva**: Eliminar registros de una cédula en TODAS las tablas relacionadas
- **Validaciones**: Verificación de existencia de registros antes de eliminar

### **3. Truncamiento de Tablas**
- **Truncamiento Individual**: Eliminar TODOS los registros de una tabla específica
- **Confirmación Doble**: Requiere confirmación explícita del usuario
- **Logging**: Registro de todas las operaciones de truncamiento

## 🔐 Sistema de Seguridad

### **Control de Acceso**
- **Solo Superadministradores**: Rol 3 exclusivamente
- **Verificación de Sesión**: Validación de autenticación
- **HTTP 403**: Respuesta de acceso denegado para usuarios no autorizados

### **Confirmaciones de Seguridad**
- **Eliminación por Cédula**: Confirmación estándar del navegador
- **Truncamiento**: Confirmación explícita con texto específico
- **Eliminación Masiva**: Confirmación con texto específico

### **Logging y Auditoría**
- **Todas las Operaciones**: Registro de eliminaciones y truncamientos
- **Información del Usuario**: Usuario que ejecutó la operación
- **Timestamp**: Fecha y hora de la operación
- **Detalles**: Tabla, cédula, registros afectados

## 🎨 Interfaz de Usuario

### **Diseño Responsivo**
- **Bootstrap 5**: Framework CSS moderno y responsivo
- **Bootstrap Icons**: Iconografía consistente
- **Gradientes**: Diseño visual atractivo y profesional

### **Componentes de la Interfaz**
- **Sidebar de Navegación**: Menú lateral con enlaces del sistema
- **Selector de Tabla**: Dropdown para elegir tabla a gestionar
- **Tarjetas de Estadísticas**: Visualización clara de datos
- **Botones de Acción**: Acciones claramente diferenciadas por color
- **Modales de Confirmación**: Ventanas emergentes para confirmaciones críticas

### **Colores y Estilos**
- **Azul**: Información y estadísticas
- **Amarillo**: Advertencias y eliminación por cédula
- **Rojo**: Operaciones críticas (truncamiento, eliminación masiva)
- **Verde**: Operaciones exitosas

## 🚀 Instalación y Configuración

### **Requisitos Previos**
- PHP 7.4 o superior
- Base de datos MySQL/MariaDB
- Sistema de autenticación funcional
- Rol de Superadministrador configurado

### **Pasos de Instalación**
1. **Verificar Archivos**: Asegurar que todos los archivos estén en su ubicación correcta
2. **Permisos**: Verificar permisos de lectura/escritura en directorios
3. **Base de Datos**: Confirmar que todas las tablas principales existan
4. **Menú**: Verificar que el enlace esté visible en el menú del Superadministrador

### **Verificación de Instalación**
- Ejecutar `tests/Unit/TestModuloTablasPrincipales.php`
- Verificar que no haya errores en la consola del navegador
- Confirmar acceso desde el menú del Superadministrador

## 📖 Guía de Uso

### **Acceso al Módulo**
1. Iniciar sesión como Superadministrador
2. Navegar al menú lateral
3. Hacer clic en "Tablas Principales"

### **Ver Estadísticas Generales**
1. Hacer clic en "Estadísticas Generales"
2. Revisar el resumen del sistema
3. Analizar totales y distribución de datos

### **Gestionar una Tabla Específica**
1. Seleccionar tabla del dropdown
2. Revisar información y estadísticas de la tabla
3. Elegir acción a realizar:
   - **Eliminar por Cédula**: Ingresar número de cédula y confirmar
   - **Truncar Tabla**: Confirmar con texto específico

### **Eliminación Masiva por Cédula**
1. Ingresar número de cédula en el campo correspondiente
2. Hacer clic en "Eliminación Masiva"
3. Confirmar con texto específico
4. Revisar resultados de la operación

## ⚠️ Consideraciones de Seguridad

### **Operaciones Irreversibles**
- **Truncamiento**: Elimina TODOS los registros de una tabla
- **Eliminación Masiva**: Afecta múltiples tablas simultáneamente
- **Sin Backup**: No hay sistema automático de respaldo

### **Recomendaciones**
- **Backup Manual**: Realizar respaldo antes de operaciones críticas
- **Pruebas**: Probar en ambiente de desarrollo primero
- **Auditoría**: Revisar logs después de operaciones importantes
- **Limitación de Acceso**: Solo usuarios autorizados deben tener acceso

### **Validaciones Implementadas**
- **Verificación de Rol**: Solo Superadministradores
- **Confirmaciones Explícitas**: Texto específico requerido
- **Validación de Datos**: Verificación de formato de cédula
- **Manejo de Errores**: Respuestas claras y informativas

## 🔧 Mantenimiento y Soporte

### **Logs del Sistema**
- **Ubicación**: Directorio `logs/` del sistema
- **Formato**: Timestamp, operación, usuario, detalles
- **Retención**: Configurable según políticas de la organización

### **Monitoreo**
- **Estadísticas Regulares**: Revisar estadísticas del sistema periódicamente
- **Logs de Operaciones**: Monitorear operaciones críticas
- **Rendimiento**: Verificar impacto en rendimiento de la base de datos

### **Actualizaciones**
- **Versiones**: Mantener actualizado el framework y dependencias
- **Parches de Seguridad**: Aplicar actualizaciones de seguridad
- **Backup**: Respaldo regular de la base de datos

## 🐛 Solución de Problemas

### **Problemas Comunes**

#### **Error de Acceso Denegado**
- **Causa**: Usuario no es Superadministrador
- **Solución**: Verificar rol del usuario en la sesión

#### **Tabla No Encontrada**
- **Causa**: Tabla no existe en la base de datos
- **Solución**: Verificar estructura de la base de datos

#### **Error de Conexión**
- **Causa**: Problemas de conectividad con la base de datos
- **Solución**: Verificar configuración y estado de la base de datos

#### **Confirmación Incorrecta**
- **Causa**: Texto de confirmación no coincide exactamente
- **Solución**: Escribir exactamente el texto requerido

### **Debug y Diagnóstico**
- **Consola del Navegador**: Revisar errores JavaScript
- **Logs del Servidor**: Verificar logs de PHP y base de datos
- **Script de Prueba**: Ejecutar `TestModuloTablasPrincipales.php`

## 📈 Mejoras Futuras

### **Funcionalidades Planificadas**
- **Sistema de Backup**: Backup automático antes de operaciones críticas
- **Historial de Operaciones**: Interfaz para revisar operaciones anteriores
- **Rollback**: Capacidad de revertir operaciones (limitada)
- **Notificaciones**: Alertas por email para operaciones críticas

### **Optimizaciones Técnicas**
- **Cache de Estadísticas**: Mejorar rendimiento de consultas
- **Paginación**: Para tablas con muchos registros
- **Búsqueda Avanzada**: Filtros y búsquedas complejas
- **API REST**: Interfaz programática para integraciones

### **Mejoras de UX**
- **Tutorial Interactivo**: Guía paso a paso para nuevos usuarios
- **Modo Oscuro**: Tema alternativo para la interfaz
- **Responsive Avanzado**: Mejor experiencia en dispositivos móviles
- **Accesibilidad**: Mejoras para usuarios con discapacidades

## 📞 Soporte Técnico

### **Contacto**
- **Desarrollador**: Sistema de Visitas
- **Versión**: 1.0
- **Fecha**: 2024

### **Recursos Adicionales**
- **Documentación**: Este archivo y comentarios en el código
- **Scripts de Prueba**: `tests/Unit/TestModuloTablasPrincipales.php`
- **Logs del Sistema**: Directorio `logs/` para diagnóstico

---

**⚠️ IMPORTANTE**: Este módulo proporciona acceso directo a operaciones críticas de la base de datos. Úselo con precaución y siempre realice respaldos antes de operaciones importantes.
