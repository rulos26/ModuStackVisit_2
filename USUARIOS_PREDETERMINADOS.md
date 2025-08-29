# 🔧 **SISTEMA DE USUARIOS PREDETERMINADOS**

**Fecha:** <?php echo date('Y-m-d H:i:s'); ?>  
**Archivo:** `app/Controllers/LoginController.php`  
**Tipo:** Sistema de Usuarios Automático

---

## 🎯 **DESCRIPCIÓN**

Se ha implementado un sistema automático que verifica y crea 3 usuarios predeterminados con hashes de contraseña correctos cada vez que se instancia el `LoginController`. Esto permite un acceso inmediato al sistema sin necesidad de configuración manual.

---

## 👥 **USUARIOS PREDETERMINADOS**

### **1. Superadministrador**
- **Usuario:** `root`
- **Contraseña:** `root`
- **Rol:** 3 (Superadministrador)
- **Nombre:** Super Administrador
- **Cédula:** 30000001
- **Correo:** root@empresa.com
- **Acceso:** Completo al sistema

### **2. Administrador**
- **Usuario:** `admin`
- **Contraseña:** `admin`
- **Rol:** 1 (Administrador)
- **Nombre:** Administrador
- **Cédula:** 30000002
- **Correo:** admin@empresa.com
- **Acceso:** Gestión de usuarios y evaluaciones

### **3. Cliente/Evaluador**
- **Usuario:** `cliente`
- **Contraseña:** `cliente`
- **Rol:** 2 (Evaluador)
- **Nombre:** Cliente
- **Cédula:** 30000003
- **Correo:** cliente@empresa.com
- **Acceso:** Evaluaciones y reportes

---

## 🔧 **IMPLEMENTACIÓN TÉCNICA**

### **Método ensureDefaultUsers():**
```php
private function ensureDefaultUsers() {
    $this->debugLog("Verificando usuarios predeterminados...");
    $this->debugConsole("🔍 VERIFICANDO USUARIOS PREDETERMINADOS", ['accion' => 'inicio']);
    
    $defaultUsers = [
        [
            'usuario' => 'root',
            'password' => 'root',
            'rol' => 3, // Superadministrador
            'nombre' => 'Super Administrador',
            'cedula' => '30000001',
            'correo' => 'root@empresa.com'
        ],
        // ... otros usuarios
    ];
    
    foreach ($defaultUsers as $userData) {
        $this->createUserIfNotExists($userData);
    }
}
```

### **Método createUserIfNotExists():**
```php
private function createUserIfNotExists($userData) {
    $usuario = $userData['usuario'];
    
    // Verificar si el usuario ya existe
    $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1');
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        // Usuario ya existe - saltar creación
        return;
    }
    
    // Crear hash de contraseña correcto
    $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Insertar usuario en la base de datos
    // ... lógica de inserción
}
```

---

## 🚀 **CARACTERÍSTICAS DEL SISTEMA**

### **✅ Funcionalidades:**
- **Creación Automática:** Los usuarios se crean automáticamente al instanciar LoginController
- **Verificación Inteligente:** Solo crea usuarios que no existen
- **Hashes Correctos:** Usa `password_hash()` con `PASSWORD_DEFAULT`
- **Compatibilidad:** Funciona con y sin la columna `fecha_creacion`
- **Debug Completo:** Logs detallados de todo el proceso
- **Login Directo:** Permite acceso inmediato sin configuración

### **🔍 Proceso de Verificación:**
1. **Inicio:** Al instanciar LoginController
2. **Verificación:** Comprueba si cada usuario existe
3. **Creación:** Crea usuarios faltantes con hashes correctos
4. **Logging:** Registra todo el proceso
5. **Debug:** Envía información a consola JavaScript

---

## 📋 **CÓMO USAR**

### **1. Ejecutar el Script Principal:**
```
http://localhost/ModuStackVisit_2/tests/Unit/CrearUsuariosPredeterminados.php
```

### **2. Login Directo:**
- El script proporciona botones para login directo
- Cada usuario tiene su propio botón con credenciales
- El sistema redirige automáticamente al dashboard correspondiente

### **3. Verificación Manual:**
- El script verifica que todos los usuarios existan
- Comprueba que los hashes sean válidos
- Muestra información detallada de cada usuario

---

## 🧪 **SCRIPT DE PRUEBA**

### **Archivo:** `tests/Unit/CrearUsuariosPredeterminados.php`

Este script proporciona:

#### **Funcionalidades:**
- ✅ Verificación automática de usuarios
- ✅ Creación de usuarios faltantes
- ✅ Validación de hashes de contraseña
- ✅ Login directo con botones
- ✅ Información detallada de cada usuario
- ✅ Interfaz visual atractiva
- ✅ Debug de consola JavaScript

#### **Características:**
- **Verificación Automática:** Comprueba que todos los usuarios existan
- **Validación de Hashes:** Verifica que las contraseñas funcionen correctamente
- **Login Directo:** Botones para acceso inmediato
- **Información Detallada:** Muestra datos completos de cada usuario
- **Debug Visual:** Interfaz clara y organizada

---

## 🔐 **SEGURIDAD**

### **✅ Medidas Implementadas:**
- **Hashes Seguros:** Usa `password_hash()` con `PASSWORD_DEFAULT`
- **Verificación Robusta:** `password_verify()` para validación
- **Logs de Seguridad:** Registro de creación de usuarios
- **Validación de Entrada:** Sanitización de datos
- **Rate Limiting:** Protección contra ataques de fuerza bruta

### **⚠️ Consideraciones:**
- Los usuarios predeterminados son para desarrollo/pruebas
- En producción, cambiar las contraseñas inmediatamente
- Los usuarios se crean automáticamente solo si no existen
- El sistema es compatible con usuarios existentes

---

## 📊 **EJEMPLO DE SALIDA**

### **Usuario Creado Exitosamente:**
```
✅ Usuario: root (Superadministrador)
ID: 15
Nombre: Super Administrador
Rol: 3 - Superadministrador
Cédula: 30000001
Correo: root@empresa.com
Activo: SÍ
Hash Length: 60 caracteres
Hash Preview: $2y$10$Hs7CUR2O8be.FTUXkCBYdekOmeK7BJr.BepWIzPj.bh...
✅ Hash de contraseña válido
```

### **Debug en Consola:**
```javascript
🔍 LOGINCONTROLLER DEBUG: 🔍 VERIFICANDO USUARIOS PREDETERMINADOS
📅 Timestamp: 2025-08-29 16:45:30
📊 Data: {"accion": "inicio"}
📍 Stack Trace: [Call Stack]

🔍 LOGINCONTROLLER DEBUG: ✅ USUARIO CREADO
📅 Timestamp: 2025-08-29 16:45:30
📊 Data: {
  "usuario": "root",
  "rol": 3,
  "hash_length": 60,
  "hash_preview": "$2y$10$Hs7CUR2O8be.FTUXkCBYdekOmeK7BJr.BepWIzPj.bh..."
}
📍 Stack Trace: [Call Stack]
```

---

## 🔗 **ARCHIVOS RELACIONADOS**

### **Archivos Modificados:**
- `app/Controllers/LoginController.php` - Implementación del sistema automático

### **Archivos de Prueba:**
- `tests/Unit/CrearUsuariosPredeterminados.php` - Script principal de verificación

### **Archivos de Log:**
- `logs/debug.log` - Logs de creación de usuarios
- `logs/app.log` - Logs de seguridad

---

## 🎯 **VENTAJAS DEL SISTEMA**

### **✅ Beneficios:**
1. **Acceso Inmediato:** No requiere configuración manual
2. **Hashes Correctos:** Contraseñas compatibles con el sistema
3. **Verificación Automática:** Solo crea usuarios faltantes
4. **Debug Completo:** Rastreo detallado del proceso
5. **Login Directo:** Acceso con un clic
6. **Compatibilidad:** Funciona con estructura de BD existente

### **🔍 Casos de Uso:**
- **Desarrollo:** Acceso rápido para testing
- **Demo:** Presentación del sistema
- **Instalación:** Configuración inicial automática
- **Recuperación:** Restauración de usuarios básicos

---

## 🚀 **PRÓXIMOS PASOS**

1. **Ejecutar el script principal:**
   ```
   http://localhost/ModuStackVisit_2/tests/Unit/CrearUsuariosPredeterminados.php
   ```

2. **Verificar usuarios creados**

3. **Hacer login directo con cualquiera de los usuarios**

4. **Abrir herramientas de desarrollador (F12) para ver debug**

5. **Probar funcionalidades de cada rol**

---

## 📝 **NOTAS IMPORTANTES**

- **Desarrollo:** Los usuarios predeterminados son ideales para desarrollo
- **Producción:** Cambiar contraseñas en entorno de producción
- **Seguridad:** El sistema incluye rate limiting y logs de seguridad
- **Compatibilidad:** Funciona con usuarios existentes
- **Debug:** Sistema completo de debug para troubleshooting

---

**Documento generado automáticamente**  
**Última actualización:** <?php echo date('Y-m-d H:i:s'); ?>
