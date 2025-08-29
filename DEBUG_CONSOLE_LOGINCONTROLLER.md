# 🔍 **DEBUG DE CONSOLA JAVASCRIPT - LOGINCONTROLLER**

**Fecha:** <?php echo date('Y-m-d H:i:s'); ?>  
**Archivo:** `app/Controllers/LoginController.php`  
**Tipo:** Debug de Consola JavaScript

---

## 🎯 **DESCRIPCIÓN**

Se ha implementado un sistema de debug de consola JavaScript que permite rastrear en tiempo real la ejecución del `LoginController.php`. Este sistema envía mensajes detallados a la consola del navegador para facilitar el debugging y monitoreo del proceso de autenticación.

---

## 🚀 **CARACTERÍSTICAS DEL DEBUG**

### **✅ Funcionalidades:**
- **Grupos de Console:** Cada paso del proceso se agrupa en la consola
- **Timestamps:** Cada mensaje incluye la fecha y hora exacta
- **Datos Estructurados:** Información detallada en formato JSON
- **Stack Trace:** Rastreo completo de la ejecución
- **Emojis:** Iconos para identificar rápidamente cada tipo de evento
- **Logs Duales:** Tanto en consola como en archivo de log

### **🔍 Tipos de Mensajes:**
- **🚀 INICIO AUTENTICACIÓN:** Comienzo del proceso
- **🔍 VALIDANDO ENTRADA:** Validación de datos de entrada
- **🔒 VERIFICANDO RATE LIMITING:** Verificación de bloqueos
- **🔍 BUSCANDO USUARIO EN BD:** Búsqueda en base de datos
- **🔐 VERIFICANDO CONTRASEÑA:** Verificación de credenciales
- **👤 VERIFICANDO ESTADO ACTIVO:** Verificación de estado del usuario
- **🔑 CREANDO SESIÓN:** Creación de sesión de usuario
- **🎉 AUTENTICACIÓN EXITOSA:** Login exitoso
- **❌ ERRORES:** Diferentes tipos de errores

---

## 📋 **CÓMO USAR EL DEBUG**

### **1. Abrir las Herramientas de Desarrollador:**
```bash
# En el navegador:
F12 → Console
```

### **2. Ejecutar el Script de Prueba:**
```
http://localhost/ModuStackVisit_2/tests/Unit/TestLoginControllerDebugConsole.php
```

### **3. Observar los Mensajes en Consola:**
Los mensajes aparecerán agrupados y con información detallada.

---

## 🔧 **IMPLEMENTACIÓN TÉCNICA**

### **Método debugConsole():**
```php
private function debugConsole($message, $data = []) {
    // Crear script JavaScript para consola
    $script = "<script>";
    $script .= "console.group('🔍 LOGINCONTROLLER DEBUG: " . addslashes($message) . "');";
    $script .= "console.log('📅 Timestamp:', '" . date('Y-m-d H:i:s') . "');";
    
    if (!empty($data)) {
        $script .= "console.log('📊 Data:', " . json_encode($data) . ");";
    }
    
    $script .= "console.trace('📍 Stack Trace');";
    $script .= "console.groupEnd();";
    $script .= "</script>";
    
    // Enviar al navegador
    echo $script;
    
    // También escribir al log de debug
    $this->debugLog("CONSOLE DEBUG: $message - " . json_encode($data));
}
```

### **Puntos de Debug Implementados:**

#### **1. Inicio de Autenticación:**
```php
$this->debugConsole("🚀 INICIO AUTENTICACIÓN", [
    'usuario' => $usuario,
    'password_length' => strlen($password),
    'timestamp' => date('Y-m-d H:i:s')
]);
```

#### **2. Validación de Entrada:**
```php
$this->debugConsole("🔍 VALIDANDO ENTRADA", ['usuario' => $usuario]);
$this->debugConsole("✅ VALIDACIÓN COMPLETADA", [
    'valida' => $validation['valid'],
    'mensaje' => $validation['message']
]);
```

#### **3. Verificación de Rate Limiting:**
```php
$this->debugConsole("🔒 VERIFICANDO RATE LIMITING", ['usuario' => $usuario]);
$this->debugConsole("🚫 CUENTA BLOQUEADA", [
    'usuario' => $usuario,
    'razon' => 'ACCOUNT_LOCKED',
    'mensaje' => 'Cuenta temporalmente bloqueada. Intente en 15 minutos.'
]);
```

#### **4. Búsqueda de Usuario:**
```php
$this->debugConsole("🔍 BUSCANDO USUARIO EN BD", ['usuario' => $usuario]);
$this->debugConsole("✅ USUARIO ENCONTRADO", [
    'id' => $user['id'],
    'rol' => $user['rol'],
    'activo' => $user['activo'] ?? 'NULL'
]);
```

#### **5. Verificación de Contraseña:**
```php
$this->debugConsole("🔐 VERIFICANDO CONTRASEÑA", [
    'usuario' => $usuario,
    'hash_preview' => substr($user['password'], 0, 20) . "...",
    'hash_length' => strlen($user['password'])
]);
```

#### **6. Creación de Sesión:**
```php
$this->debugConsole("🔑 CREANDO SESIÓN", ['usuario' => $usuario]);
$this->debugConsole("✅ SESIÓN CREADA", [
    'usuario' => $usuario,
    'token_preview' => substr($sessionData['session_token'], 0, 10) . "...",
    'rol' => $sessionData['rol'],
    'redirect_url' => $sessionData['redirect_url']
]);
```

---

## 🧪 **SCRIPT DE PRUEBA**

### **Archivo:** `tests/Unit/TestLoginControllerDebugConsole.php`

Este script proporciona una interfaz web para probar el debug de consola:

#### **Funcionalidades:**
- ✅ Carga automática del autoloader
- ✅ Verificación de clases
- ✅ Instanciación del LoginController
- ✅ Formulario para pruebas de autenticación
- ✅ Pruebas con credenciales correctas
- ✅ Pruebas con credenciales incorrectas
- ✅ Pruebas con usuarios inexistentes
- ✅ Interfaz visual atractiva
- ✅ Instrucciones detalladas

#### **Cómo Usar:**
1. Abrir el script en el navegador
2. Abrir las herramientas de desarrollador (F12)
3. Ir a la pestaña Console
4. Ejecutar una de las pruebas
5. Observar los mensajes de debug en consola

---

## 📊 **EJEMPLO DE SALIDA EN CONSOLA**

```javascript
🔍 LOGINCONTROLLER DEBUG: 🚀 INICIO AUTENTICACIÓN
📅 Timestamp: 2025-08-29 16:30:15
📊 Data: {
  "usuario": "root",
  "password_length": 4,
  "timestamp": "2025-08-29 16:30:15"
}
📍 Stack Trace: [Call Stack]

🔍 LOGINCONTROLLER DEBUG: 🔍 VALIDANDO ENTRADA
📅 Timestamp: 2025-08-29 16:30:15
📊 Data: {"usuario": "root"}
📍 Stack Trace: [Call Stack]

🔍 LOGINCONTROLLER DEBUG: ✅ VALIDACIÓN COMPLETADA
📅 Timestamp: 2025-08-29 16:30:15
📊 Data: {
  "valida": true,
  "mensaje": ""
}
📍 Stack Trace: [Call Stack]

🔍 LOGINCONTROLLER DEBUG: 🔒 VERIFICANDO RATE LIMITING
📅 Timestamp: 2025-08-29 16:30:15
📊 Data: {"usuario": "root"}
📍 Stack Trace: [Call Stack]

🔍 LOGINCONTROLLER DEBUG: 🚫 CUENTA BLOQUEADA
📅 Timestamp: 2025-08-29 16:30:15
📊 Data: {
  "usuario": "root",
  "razon": "ACCOUNT_LOCKED",
  "mensaje": "Cuenta temporalmente bloqueada. Intente en 15 minutos."
}
📍 Stack Trace: [Call Stack]
```

---

## 🔗 **ARCHIVOS RELACIONADOS**

### **Archivos Modificados:**
- `app/Controllers/LoginController.php` - Implementación del debug de consola

### **Archivos de Prueba:**
- `tests/Unit/TestLoginControllerDebugConsole.php` - Script de prueba principal

### **Archivos de Log:**
- `logs/debug.log` - Logs de debug (incluye mensajes de consola)

---

## 🎯 **VENTAJAS DEL DEBUG DE CONSOLA**

### **✅ Beneficios:**
1. **Tiempo Real:** Ver la ejecución paso a paso
2. **Información Detallada:** Datos estructurados y completos
3. **Fácil Identificación:** Emojis y grupos para navegación rápida
4. **Stack Trace:** Rastreo completo de la ejecución
5. **No Intrusivo:** No interfiere con la funcionalidad normal
6. **Dual Logging:** Tanto en consola como en archivo

### **🔍 Casos de Uso:**
- **Desarrollo:** Debugging durante el desarrollo
- **Testing:** Verificación de flujos de autenticación
- **Monitoreo:** Seguimiento de problemas en producción
- **Auditoría:** Rastreo de intentos de login

---

## 🚀 **PRÓXIMOS PASOS**

1. **Ejecutar el script de prueba:**
   ```
   http://localhost/ModuStackVisit_2/tests/Unit/TestLoginControllerDebugConsole.php
   ```

2. **Abrir las herramientas de desarrollador (F12)**

3. **Ir a la pestaña Console**

4. **Ejecutar las pruebas de autenticación**

5. **Observar los mensajes de debug en tiempo real**

---

**Documento generado automáticamente**  
**Última actualización:** <?php echo date('Y-m-d H:i:s'); ?>
