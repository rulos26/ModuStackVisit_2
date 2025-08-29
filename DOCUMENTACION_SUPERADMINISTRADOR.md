# DOCUMENTACIÓN DEL ROL SUPERADMINISTRADOR

## 📋 **RESUMEN**

El **Superadministrador** es el rol de más alto nivel en el sistema de gestión de visitas domiciliarias. Tiene acceso completo a todas las funcionalidades del sistema y puede gestionar usuarios, configuraciones, logs y respaldos.

---

## 🔐 **CARACTERÍSTICAS DEL ROL**

### **Nivel de Acceso:**
- **Rol ID:** 3
- **Nivel:** Máximo (Superior a Administrador y Evaluador)
- **Acceso:** Completo al sistema

### **Funcionalidades Principales:**
1. **Gestión de Usuarios** - Crear, editar, eliminar usuarios de todos los roles
2. **Dashboard Estadístico** - Ver estadísticas generales del sistema
3. **Logs del Sistema** - Acceso a logs de actividad
4. **Auditoría** - Reportes de auditoría del sistema
5. **Respaldos** - Crear y gestionar respaldos de la base de datos
6. **Configuración** - Configurar parámetros del sistema
7. **Reportes** - Generar reportes avanzados

---

## 🚀 **INSTALACIÓN Y CONFIGURACIÓN**

### **1. Crear el Usuario Superadministrador**

Ejecutar el script de prueba:
```
http://localhost/ModuStackVisit_2/tests/Unit/CrearSuperAdminTest.php
```

### **2. Credenciales por Defecto:**
- **Usuario:** `superadmin`
- **Contraseña:** `SuperAdmin123!`
- **Cédula:** `30000003`
- **Email:** `superadmin@empresa.com`

### **3. Acceso al Sistema:**
1. Ir a la página de login principal
2. Ingresar las credenciales del superadministrador
3. Será redirigido automáticamente al dashboard de superadministrador

---

## 📁 **ESTRUCTURA DE ARCHIVOS**

### **Controladores:**
```
app/Controllers/
└── SuperAdminController.php          # Controlador principal
```

### **Vistas:**
```
resources/views/superadmin/
├── dashboardSuperAdmin.php           # Dashboard principal
├── gestion_usuarios.php              # Gestión de usuarios
├── configuracion_sistema.php         # Configuración del sistema
├── logs_sistema.php                  # Logs del sistema
├── auditoria.php                     # Auditoría
├── respaldo.php                      # Respaldos
└── reportes.php                      # Reportes
```

### **Scripts de Prueba:**
```
tests/Unit/
└── CrearSuperAdminTest.php           # Crear usuario superadmin
```

---

## 🎯 **FUNCIONALIDADES DETALLADAS**

### **1. Dashboard Principal**
- **Estadísticas en Tiempo Real:**
  - Total de usuarios por rol
  - Total de evaluaciones
  - Total de cartas de autorización
  - Evaluadores activos

- **Gráficos Interactivos:**
  - Evaluaciones por mes (gráfico de líneas)
  - Distribución de usuarios (gráfico de dona)

- **Acciones Rápidas:**
  - Crear usuario
  - Crear respaldo
  - Ver logs
  - Configuración

### **2. Gestión de Usuarios**
- **Operaciones CRUD:**
  - ✅ Crear usuarios de cualquier rol
  - ✅ Editar información de usuarios
  - ✅ Eliminar usuarios
  - ✅ Listar todos los usuarios

- **Campos Gestionables:**
  - Nombre completo
  - Cédula
  - Usuario
  - Correo electrónico
  - Rol (1=Admin, 2=Evaluador, 3=Superadmin)
  - Contraseña

### **3. Estadísticas del Sistema**
- **Consultas Optimizadas:**
  - Usuarios por rol
  - Evaluaciones totales
  - Cartas de autorización
  - Evaluaciones por mes (últimos 6 meses)

### **4. Logs y Auditoría**
- **Logs del Sistema:**
  - Actividad de usuarios
  - Errores del sistema
  - Acciones administrativas

- **Reportes de Auditoría:**
  - Cambios en datos
  - Acciones por usuario
  - Filtros por fecha

### **5. Respaldos**
- **Funcionalidades:**
  - Crear respaldos automáticos
  - Descargar respaldos
  - Programar respaldos
  - Restaurar desde respaldo

### **6. Configuración del Sistema**
- **Parámetros Configurables:**
  - Configuración de email
  - Parámetros de seguridad
  - Configuración de respaldos
  - Configuración de logs

---

## 🔧 **CONFIGURACIÓN TÉCNICA**

### **Base de Datos:**
```sql
-- Verificar que existe la columna fecha_creacion en la tabla usuarios
ALTER TABLE usuarios ADD COLUMN fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Crear índices para optimizar consultas
CREATE INDEX idx_usuarios_rol ON usuarios(rol);
CREATE INDEX idx_usuarios_fecha ON usuarios(fecha_creacion);
```

### **Tablas Requeridas:**
```sql
-- Tabla para logs del sistema (opcional)
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(255),
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45)
);

-- Tabla para auditoría (opcional)
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(50),
    tabla_afectada VARCHAR(100),
    datos_anteriores JSON,
    datos_nuevos JSON,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para configuración del sistema (opcional)
CREATE TABLE configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE,
    valor TEXT,
    descripcion TEXT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🛡️ **SEGURIDAD**

### **Validaciones Implementadas:**
- ✅ Verificación de rol en cada vista
- ✅ Prepared statements para todas las consultas
- ✅ Hash seguro de contraseñas (bcrypt)
- ✅ Validación de entrada de datos
- ✅ Sanitización de datos de salida

### **Recomendaciones de Seguridad:**
1. **Cambiar contraseña por defecto** después del primer acceso
2. **Usar contraseñas fuertes** (mínimo 12 caracteres)
3. **Habilitar autenticación de dos factores** (futuro)
4. **Revisar logs regularmente**
5. **Crear respaldos frecuentes**

---

## 📊 **ESTADÍSTICAS Y MÉTRICAS**

### **Métricas Disponibles:**
- **Usuarios:**
  - Total por rol
  - Nuevos usuarios por mes
  - Usuarios activos

- **Evaluaciones:**
  - Total de evaluaciones
  - Evaluaciones por mes
  - Evaluaciones por evaluador

- **Sistema:**
  - Uso de recursos
  - Errores del sistema
  - Tiempo de respuesta

---

## 🔄 **FLUJO DE TRABAJO**

### **1. Acceso Inicial:**
```
Login → Verificación de Rol → Dashboard Superadmin
```

### **2. Gestión de Usuarios:**
```
Dashboard → Gestión de Usuarios → CRUD Usuarios
```

### **3. Monitoreo del Sistema:**
```
Dashboard → Logs/Auditoría → Análisis de Actividad
```

### **4. Mantenimiento:**
```
Dashboard → Respaldos → Crear/Descargar Respaldo
```

---

## 🚨 **TROUBLESHOOTING**

### **Problemas Comunes:**

#### **1. Error de Acceso Denegado:**
- Verificar que el usuario tenga rol = 3
- Verificar que la sesión esté activa
- Limpiar caché del navegador

#### **2. Error en Consultas de Estadísticas:**
- Verificar que existan las tablas requeridas
- Verificar permisos de base de datos
- Revisar logs de errores

#### **3. Error al Crear Usuarios:**
- Verificar que la tabla usuarios tenga la estructura correcta
- Verificar que no existan usuarios duplicados
- Revisar restricciones de base de datos

### **Logs de Error:**
Los errores se registran en:
- `error_log` del servidor web
- Tabla `logs_sistema` (si existe)
- Archivos de log del sistema

---

## 📈 **ROADMAP FUTURO**

### **Funcionalidades Planificadas:**
1. **Autenticación de dos factores**
2. **Notificaciones en tiempo real**
3. **API REST para integraciones**
4. **Dashboard móvil responsive**
5. **Reportes avanzados con gráficos**
6. **Sistema de alertas automáticas**
7. **Integración con servicios externos**

### **Mejoras de Seguridad:**
1. **Rate limiting** para prevenir ataques
2. **Auditoría de IP** y geolocalización
3. **Encriptación de datos sensibles**
4. **Backup automático en la nube**

---

## 📞 **SOPORTE**

### **Contacto:**
- **Desarrollador:** Sistema de Análisis Automatizado
- **Fecha de Creación:** Agosto 2024
- **Versión:** 1.0

### **Recursos Adicionales:**
- [Documentación del Sistema Principal](../INFORME_OPTIMIZACION_PROYECTO.md)
- [Guía de Instalación](../README.md)
- [Manual de Usuario](../MANUAL_USUARIO.md)

---

**Nota:** Este rol debe ser usado con responsabilidad ya que tiene acceso completo al sistema. Se recomienda cambiar las credenciales por defecto inmediatamente después de la instalación.
