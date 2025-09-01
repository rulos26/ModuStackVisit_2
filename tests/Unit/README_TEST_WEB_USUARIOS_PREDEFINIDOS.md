# 🔧 Test Web Automatizado para Usuarios Predefinidos

## 📋 Descripción

Este sistema de test automatizado valida la creación y protección de usuarios maestros del sistema ModuStack. Está diseñado para funcionar en entornos de producción de manera segura y profesional.

## 🎯 Objetivos del Test

### **Usuarios a Validar:**
- **root** (Superadministrador) - Rol 3
- **admin** (Administrador) - Rol 1  
- **cliente** (Cliente) - Rol 2
- **evaluador** (Evaluador) - Rol 4

### **Validaciones Principales:**
1. ✅ **Creación Correcta**: Verificar que cada perfil se cree correctamente
2. ✅ **Roles Asignados**: Confirmar que los roles estén correctamente asignados
3. ✅ **Protección Activa**: Validar que los usuarios maestros no puedan ser modificados
4. ✅ **Restricciones**: Verificar límites de roles únicos (1 Admin, 1 Superadmin)

## 🏗️ Arquitectura del Sistema

### **Archivos Principales:**

#### 1. **`TestWebUsuariosPredefinidos.php`**
- **Interfaz web** con diseño moderno y responsive
- **Bootstrap 5** para estilos profesionales
- **JavaScript ES6+** para funcionalidad interactiva
- **Sistema de logs** en tiempo real

#### 2. **`TestWebUsuariosPredefinidosAPI.php`**
- **API PHP** que implementa la lógica del test
- **Validaciones de seguridad** para entorno de producción
- **Manejo de errores** robusto
- **Respuestas JSON** estructuradas

### **Características de Seguridad:**
- ✅ Headers de seguridad HTTP
- ✅ Validación de IP (configurable)
- ✅ Timeout de ejecución (5 minutos máximo)
- ✅ Límite de memoria (256MB)
- ✅ Manejo seguro de excepciones

## 🚀 Cómo Usar el Test

### **1. Acceso al Test**
```
URL: https://tudominio.com/ModuStackVisit_2/tests/Unit/TestWebUsuariosPredefinidos.php
```

### **2. Opciones Disponibles**

#### **🚀 Test Completo**
- Ejecuta todas las validaciones del sistema
- 8 pasos de verificación
- Reporte detallado de resultados

#### **🛡️ Test de Protección**
- Valida únicamente el sistema de protección
- 3 pasos de verificación
- Enfoque en seguridad

#### **📋 Ver Logs del Sistema**
- Muestra logs recientes del sistema
- Últimos 50 registros
- Información en tiempo real

### **3. Flujo de Ejecución**

```
1. Preparación de interfaz
2. Ejecución de test via API
3. Procesamiento de resultados
4. Visualización de reporte
5. Acciones disponibles
```

## 📊 Estructura de Respuesta de la API

### **Respuesta Exitosa:**
```json
{
    "success": true,
    "steps": [
        {
            "step": 1,
            "title": "Verificar conexión a base de datos",
            "success": true,
            "message": "Conexión a base de datos exitosa",
            "details": "Base de datos conectada correctamente"
        }
    ],
    "summary": {
        "total_steps": 8,
        "successful_steps": 8,
        "failed_steps": 0,
        "success_rate": "100%",
        "status": "COMPLETADO EXITOSAMENTE"
    }
}
```

### **Respuesta con Errores:**
```json
{
    "success": false,
    "steps": [...],
    "summary": {...},
    "errors": [
        "Error de conexión a la base de datos"
    ]
}
```

## 🔧 Configuración para Producción

### **1. Seguridad de IP (Opcional)**
En `TestWebUsuariosPredefinidosAPI.php`:
```php
// Descomentar para restringir acceso por IP
// $allowedIPs = ['IP1', 'IP2', 'IP3'];
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Acceso no autorizado']);
//     exit;
// }
```

### **2. Configuración de Base de Datos**
Verificar `app/Config/config.php`:
```php
'database' => [
    'host' => 'tu_host',
    'dbname' => 'tu_base_de_datos',
    'username' => 'tu_usuario',
    'password' => 'tu_contraseña',
    'charset' => 'utf8mb4'
]
```

### **3. Permisos de Archivos**
```bash
chmod 644 tests/Unit/TestWebUsuariosPredefinidos.php
chmod 644 tests/Unit/TestWebUsuariosPredefinidosAPI.php
chmod 755 tests/Unit/
```

## 📋 Pasos del Test Completo

### **Paso 1: Verificar Conexión a Base de Datos**
- Conecta a la base de datos
- Ejecuta query de prueba
- Valida respuesta

### **Paso 2: Validar Estructura de Tablas**
- Verifica existencia de tabla `usuarios`
- Valida columnas requeridas
- Confirma estructura correcta

### **Paso 3: Crear Usuarios Predeterminados**
- Llama `LoginController->initializeDefaultUsers()`
- Crea usuarios si no existen
- Verifica hashes de contraseñas

### **Paso 4: Verificar Roles Asignados**
- Confirma rol 3 para root
- Confirma rol 1 para admin
- Confirma rol 2 para cliente
- Confirma rol 4 para evaluador

### **Paso 5: Validar Protección de Usuarios**
- Verifica sistema de protección
- Confirma usuarios marcados como protegidos
- Valida restricciones activas

### **Paso 6: Probar Operaciones CRUD**
- Test de listado de usuarios
- Verifica funcionalidad básica
- Valida respuestas del sistema

### **Paso 7: Verificar Restricciones**
- Confirma solo 1 Administrador
- Confirma solo 1 Superadministrador
- Valida límites de roles únicos

### **Paso 8: Generar Reporte Final**
- Compila resultados
- Calcula estadísticas
- Prepara resumen ejecutivo

## 🛡️ Sistema de Protección

### **Usuarios Protegidos:**
- **root** - Superadministrador
- **admin** - Administrador
- **cliente** - Cliente
- **evaluador** - Evaluador

### **Operaciones Bloqueadas:**
- ❌ **Eliminación**: No se pueden eliminar
- ❌ **Edición**: No se pueden modificar
- ❌ **Desactivación**: No se pueden desactivar
- ❌ **Activación**: No se pueden activar

### **Códigos de Error:**
- `PROTECTED_USER_DELETE` - Intento de eliminación
- `PROTECTED_USER_UPDATE` - Intento de edición
- `PROTECTED_USER_DEACTIVATE` - Intento de desactivación
- `PROTECTED_USER_ACTIVATE` - Intento de activación

## 📱 Características de la Interfaz

### **Diseño Responsive:**
- ✅ **Desktop**: Layout completo con todas las opciones
- ✅ **Tablet**: Adaptación automática de columnas
- ✅ **Mobile**: Diseño optimizado para pantallas pequeñas

### **Elementos Visuales:**
- 🎨 **Gradientes** modernos
- 🔄 **Animaciones** suaves
- 📊 **Barras de progreso** interactivas
- 🎯 **Iconos** descriptivos
- 🎨 **Colores** semánticos (verde=éxito, rojo=error)

### **Funcionalidades JavaScript:**
- 🔄 **Async/Await** para operaciones API
- 📝 **Logs en tiempo real**
- 🎯 **Manejo de errores** robusto
- 📊 **Actualización dinámica** de resultados

## 🚨 Solución de Problemas

### **Error: "Autoloader no encontrado"**
```bash
# Verificar que existe vendor/autoload.php
ls -la vendor/autoload.php

# Si no existe, ejecutar Composer
composer install
```

### **Error: "Clase no encontrada"**
```bash
# Verificar namespace en archivos
grep -r "namespace App" app/

# Verificar autoload en composer.json
cat composer.json | grep autoload
```

### **Error: "Conexión a base de datos fallida"**
```bash
# Verificar configuración
cat app/Config/config.php

# Probar conexión manual
mysql -u usuario -p -h host base_de_datos
```

### **Error: "Permisos denegados"**
```bash
# Verificar permisos de archivos
ls -la tests/Unit/

# Ajustar permisos si es necesario
chmod 644 tests/Unit/*.php
```

## 📈 Métricas y Reportes

### **Estadísticas del Test:**
- **Tasa de Éxito**: Porcentaje de pasos exitosos
- **Tiempo de Ejecución**: Duración total del test
- **Errores Detectados**: Lista de problemas encontrados
- **Estado del Sistema**: Resumen general de salud

### **Logs del Sistema:**
- **Timestamp**: Hora exacta de cada evento
- **Nivel**: Info, Success, Warning, Error
- **Mensaje**: Descripción detallada del evento
- **Contexto**: Información adicional relevante

## 🔄 Mantenimiento y Actualizaciones

### **Recomendaciones:**
1. **Ejecutar regularmente** para monitorear salud del sistema
2. **Revisar logs** para detectar patrones de error
3. **Actualizar configuraciones** según cambios en el sistema
4. **Backup de resultados** para análisis histórico

### **Personalización:**
- Modificar `$allowedIPs` para restricciones de acceso
- Ajustar timeouts según necesidades del servidor
- Agregar nuevos pasos de validación según requerimientos
- Personalizar estilos CSS para branding corporativo

## 📞 Soporte y Contacto

### **Para Reportes de Bugs:**
- Revisar logs del sistema
- Verificar configuración de base de datos
- Confirmar permisos de archivos
- Validar versión de PHP (requerida: 7.4+)

### **Para Nuevas Funcionalidades:**
- Documentar requerimientos
- Crear casos de prueba
- Implementar en API y frontend
- Validar en entorno de desarrollo

---

## 📝 Changelog

### **Versión 1.0 (2025-01-29)**
- ✅ Implementación inicial del test web
- ✅ API PHP para validaciones
- ✅ Interfaz responsive con Bootstrap 5
- ✅ Sistema de logs en tiempo real
- ✅ Validaciones de seguridad para producción
- ✅ Soporte completo para 4 roles del sistema

---

**Desarrollado por:** Sistema ModuStack  
**Versión:** 1.0  
**Última actualización:** 2025-01-29  
**Licencia:** MIT
