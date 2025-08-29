# 🔍 **REDIRECCIONES POR ROL - LOGINCONTROLLER**

**Fecha:** <?php echo date('Y-m-d H:i:s'); ?>  
**Archivo:** `app/Controllers/LoginController.php`  
**Método:** `getRedirectUrl($rol)`

---

## 📋 **CONFIGURACIÓN ACTUAL DE REDIRECCIONES**

### **Método getRedirectUrl() - Líneas 463-475:**

```php
private function getRedirectUrl($rol) {
    switch ($rol) {
        case 1:
            return 'resources/views/admin/dashboardAdmin.php';
        case 2:
            return 'resources/views/evaluador/dashboardEavaluador.php';
        case 3:
            return 'resources/views/superadmin/dashboardSuperAdmin.php';
        default:
            throw new \InvalidArgumentException('Rol de usuario no válido: ' . $rol);
    }
}
```

---

## 🎯 **MAPA DE REDIRECCIONES**

| Rol | Descripción | URL de Destino | Archivo |
|-----|-------------|----------------|---------|
| **1** | **Administrador** | `resources/views/admin/dashboardAdmin.php` | ✅ Existe |
| **2** | **Evaluador/Cliente** | `resources/views/evaluador/dashboardEavaluador.php` | ✅ Existe |
| **3** | **Superadministrador** | `resources/views/superadmin/dashboardSuperAdmin.php` | ✅ Existe |

---

## 🔍 **VERIFICACIÓN DE ARCHIVOS DE DESTINO**

### **✅ Archivos Existentes:**

1. **`resources/views/admin/dashboardAdmin.php`**
   - **Estado:** ✅ Existe
   - **Tamaño:** 138 bytes
   - **Descripción:** Dashboard del administrador

2. **`resources/views/evaluador/dashboardEavaluador.php`**
   - **Estado:** ✅ Existe
   - **Tamaño:** 131 bytes
   - **Descripción:** Dashboard del evaluador/cliente

3. **`resources/views/superadmin/dashboardSuperAdmin.php`**
   - **Estado:** ✅ Existe
   - **Tamaño:** 18KB
   - **Descripción:** Dashboard del superadministrador

---

## 🚀 **FLUJO DE REDIRECCIÓN**

### **Proceso Completo:**

1. **Usuario hace login exitoso**
2. **LoginController.authenticate()** procesa la autenticación
3. **Se crea la sesión** con datos del usuario
4. **Se llama getRedirectUrl($rol)** con el rol del usuario
5. **Se retorna la URL específica** según el rol
6. **Se incluye en la respuesta** de autenticación exitosa
7. **El frontend redirige** al usuario a la URL especificada

### **Ejemplo de Respuesta Exitosa:**

```php
return [
    'success' => true,
    'message' => 'Login exitoso',
    'data' => [
        'user_id' => $user['id'],
        'username' => $user['usuario'],
        'rol' => $user['rol'],
        'nombre' => $user['nombre'],
        'session_token' => $sessionToken,
        'redirect_url' => $redirectUrl  // ← URL específica por rol
    ],
    'timestamp' => time()
];
```

---

## 🧪 **SCRIPT DE VERIFICACIÓN**

### **Archivo:** `tests/Unit/VerificarRedireccionesPorRol.php`

**Funcionalidades:**
- ✅ Verifica configuración de redirecciones
- ✅ Comprueba existencia de archivos de destino
- ✅ Prueba login con usuarios reales
- ✅ Valida redirecciones correctas
- ✅ Prueba método getRedirectUrl directamente

**Cómo ejecutar:**
```
http://localhost/ModuStackVisit_2/tests/Unit/VerificarRedireccionesPorRol.php
```

---

## 👥 **USUARIOS PREDETERMINADOS Y SUS REDIRECCIONES**

### **1. Superadministrador (root/root)**
- **Rol:** 3
- **Redirección:** `resources/views/superadmin/dashboardSuperAdmin.php`
- **Funcionalidades:** Gestión completa del sistema

### **2. Administrador (admin/admin)**
- **Rol:** 1
- **Redirección:** `resources/views/admin/dashboardAdmin.php`
- **Funcionalidades:** Gestión de usuarios y evaluaciones

### **3. Cliente/Evaluador (cliente/cliente)**
- **Rol:** 2
- **Redirección:** `resources/views/evaluador/dashboardEavaluador.php`
- **Funcionalidades:** Evaluaciones y cartas de autorización

---

## 🔧 **POSIBLES MEJORAS**

### **1. Validación de Archivos:**
```php
private function getRedirectUrl($rol) {
    $url = '';
    switch ($rol) {
        case 1:
            $url = 'resources/views/admin/dashboardAdmin.php';
            break;
        case 2:
            $url = 'resources/views/evaluador/dashboardEavaluador.php';
            break;
        case 3:
            $url = 'resources/views/superadmin/dashboardSuperAdmin.php';
            break;
        default:
            throw new \InvalidArgumentException('Rol de usuario no válido: ' . $rol);
    }
    
    // Validar que el archivo existe
    if (!file_exists(__DIR__ . '/../../' . $url)) {
        throw new \RuntimeException('Archivo de destino no encontrado: ' . $url);
    }
    
    return $url;
}
```

### **2. Configuración Externa:**
```php
// En config.php
$REDIRECT_URLS = [
    1 => 'resources/views/admin/dashboardAdmin.php',
    2 => 'resources/views/evaluador/dashboardEavaluador.php',
    3 => 'resources/views/superadmin/dashboardSuperAdmin.php'
];
```

### **3. Logging de Redirecciones:**
```php
$this->logger->info('User redirected after login', [
    'usuario' => $user['usuario'],
    'rol' => $user['rol'],
    'redirect_url' => $redirectUrl
]);
```

---

## ⚠️ **CONSIDERACIONES DE SEGURIDAD**

### **1. Validación de Rol:**
- ✅ Se valida que el rol sea 1, 2 o 3
- ✅ Se lanza excepción para roles inválidos

### **2. Validación de Sesión:**
- ✅ Se crea token de sesión único
- ✅ Se establece timeout de sesión
- ✅ Se actualiza último acceso

### **3. Logging:**
- ✅ Se registra login exitoso
- ✅ Se incluye información de IP y User-Agent

---

## 📊 **ESTADO ACTUAL**

### **✅ Funcionalidades Completas:**
- [x] Redirecciones configuradas correctamente
- [x] Archivos de destino existen
- [x] Validación de roles implementada
- [x] Manejo de errores robusto
- [x] Logging de eventos de seguridad

### **🔧 Funcionalidades Opcionales:**
- [ ] Validación de existencia de archivos
- [ ] Configuración externa de URLs
- [ ] Logging detallado de redirecciones
- [ ] Métricas de uso por rol

---

## 🚀 **PRÓXIMOS PASOS**

### **1. Ejecutar Verificación:**
```
http://localhost/ModuStackVisit_2/tests/Unit/VerificarRedireccionesPorRol.php
```

### **2. Probar Login Completo:**
```
http://localhost/ModuStackVisit_2/tests/Unit/TestLoginDespuesCorreccion.php
```

### **3. Verificar Funcionamiento:**
- Probar login con cada usuario predeterminado
- Confirmar redirección a dashboard correcto
- Verificar funcionalidades específicas de cada rol

---

**Documento generado automáticamente**  
**Última actualización:** <?php echo date('Y-m-d H:i:s'); ?>
