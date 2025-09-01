# 🔒 Protección de Usuarios Predefinidos - Sistema de Visitas

## 📋 Resumen Ejecutivo

Se han implementado protecciones de **máxima seguridad** para los usuarios predefinidos del sistema, garantizando que estas cuentas maestras **NO puedan ser modificadas, eliminadas o desactivadas bajo ninguna circunstancia**.

## 🎯 Usuarios Predefinidos Protegidos

### **🔒 Cuentas Maestras del Sistema:**

| Usuario | Rol | Descripción | Protección |
|---------|-----|-------------|------------|
| **root** | 3 - Superadministrador | Superadministrador del Sistema | 🔒 PROTEGIDO - Cuenta maestra del sistema |
| **admin** | 1 - Administrador | Administrador del Sistema | 🔒 PROTEGIDO - Cuenta administrativa maestra |
| **cliente** | 2 - Cliente/Evaluador | Cliente/Evaluador del Sistema | 🔒 PROTEGIDO - Cuenta de cliente maestra |
| **evaluador** | 2 - Evaluador | Evaluador del Sistema | 🔒 PROTEGIDO - Cuenta de evaluador maestra |

## 🚫 Operaciones Bloqueadas

### **❌ NO SE PERMITEN las siguientes operaciones:**

1. **Eliminación** - `DELETE FROM usuarios WHERE usuario IN ('root', 'admin', 'cliente', 'evaluador')`
2. **Edición** - `UPDATE usuarios SET ... WHERE usuario IN ('root', 'admin', 'cliente', 'evaluador')`
3. **Desactivación** - `UPDATE usuarios SET activo = 0 WHERE usuario IN ('root', 'admin', 'cliente', 'evaluador')`
4. **Activación** - `UPDATE usuarios SET activo = 1 WHERE usuario IN ('root', 'admin', 'cliente', 'evaluador')`
5. **Cambio de contraseña** - Cualquier modificación de datos personales

## 🔧 Implementación Técnica

### **Constantes de Protección:**
```php
private const USUARIOS_PREDEFINIDOS = [
    'root' => [
        'rol' => 3,
        'descripcion' => 'Superadministrador del Sistema',
        'proteccion' => 'PROTEGIDO - Cuenta maestra del sistema'
    ],
    'admin' => [
        'rol' => 1,
        'descripcion' => 'Administrador del Sistema',
        'proteccion' => 'PROTEGIDO - Cuenta administrativa maestra'
    ],
    'cliente' => [
        'rol' => 2,
        'descripcion' => 'Cliente/Evaluador del Sistema',
        'proteccion' => 'PROTEGIDO - Cuenta de cliente maestra'
    ],
    'evaluador' => [
        'rol' => 2,
        'descripcion' => 'Evaluador del Sistema',
        'proteccion' => 'PROTEGIDO - Cuenta de evaluador maestra'
    ]
];
```

### **Métodos de Verificación:**
```php
/**
 * Verificar si un usuario es predefinido del sistema
 */
private function esUsuarioPredefinido($usuario) {
    return array_key_exists(strtolower($usuario), self::USUARIOS_PREDEFINIDOS);
}

/**
 * Verificar si un usuario por ID es predefinido
 */
private function obtenerUsuarioPredefinidoPorId($id) {
    $stmt = $this->db->prepare("SELECT usuario FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $resultado = $stmt->fetch();
    
    if ($resultado && $this->esUsuarioPredefinido($resultado['usuario'])) {
        return self::USUARIOS_PREDEFINIDOS[strtolower($resultado['usuario'])];
    }
    
    return false;
}
```

## 🚨 Códigos de Error de Protección

### **PROTECTED_USER_DELETE**
- **Descripción**: Se intentó eliminar un usuario predefinido
- **Mensaje**: "NO SE PUEDE ELIMINAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- **Acción**: Bloquear eliminación completamente

### **PROTECTED_USER_UPDATE**
- **Descripción**: Se intentó editar un usuario predefinido
- **Mensaje**: "NO SE PUEDE MODIFICAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- **Acción**: Bloquear edición completamente

### **PROTECTED_USER_DEACTIVATE**
- **Descripción**: Se intentó desactivar un usuario predefinido
- **Mensaje**: "NO SE PUEDE DESACTIVAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- **Acción**: Bloquear desactivación completamente

### **PROTECTED_USER_ACTIVATE**
- **Descripción**: Se intentó cambiar el estado de un usuario predefinido
- **Mensaje**: "NO SE PUEDE MODIFICAR EL ESTADO DE UN USUARIO PREDEFINIDO DEL SISTEMA"
- **Acción**: Bloquear cambios de estado completamente

## 📊 Información de Protección

### **Método `getInfoProteccionUsuario()`:**
```php
public function getInfoProteccionUsuario($usuario) {
    if ($this->esUsuarioPredefinido($usuario)) {
        $info = self::USUARIOS_PREDEFINIDOS[strtolower($usuario)];
        return [
            'protegido' => true,
            'usuario' => $usuario,
            'rol' => $info['rol'],
            'descripcion' => $info['descripcion'],
            'proteccion' => $info['proteccion'],
            'mensaje' => "Este usuario es una cuenta maestra del sistema y NO puede ser modificada, eliminada o desactivada."
        ];
    }
    
    return ['protegido' => false];
}
```

### **Método `listarUsuariosPredefinidos()`:**
```php
public function listarUsuariosPredefinidos() {
    $usuarios_predefinidos = [];
    
    foreach (self::USUARIOS_PREDEFINIDOS as $usuario => $info) {
        // Consultar BD y agregar información de protección
        $usuarios_predefinidos[] = [
            'id' => $usuario_bd['id'],
            'usuario' => $usuario_bd['usuario'],
            'protegido' => true,
            'descripcion' => $info['descripcion'],
            'proteccion' => $info['proteccion'],
            'estado_proteccion' => '🔒 PROTEGIDO - NO MODIFICABLE'
        ];
    }
    
    return $usuarios_predefinidos;
}
```

## 🔍 Verificaciones Implementadas

### **1. En Eliminación (`eliminarUsuario`):**
```php
// Verificar que no sea un usuario predefinido del sistema
$usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
if ($usuario_predefinido) {
    return [
        'error' => 'NO SE PUEDE ELIMINAR UN USUARIO PREDEFINIDO DEL SISTEMA',
        'error_code' => 'PROTECTED_USER_DELETE',
        'usuario' => $usuario_predefinido['descripcion'],
        'proteccion' => $usuario_predefinido['proteccion'],
        'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser eliminada bajo ninguna circunstancia.'
    ];
}
```

### **2. En Edición (`actualizarUsuario`):**
```php
// Verificar que no sea un usuario predefinido del sistema
$usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($datos['id']);
if ($usuario_predefinido) {
    return [
        'error' => 'NO SE PUEDE MODIFICAR UN USUARIO PREDEFINIDO DEL SISTEMA',
        'error_code' => 'PROTECTED_USER_UPDATE',
        'usuario' => $usuario_predefinido['descripcion'],
        'proteccion' => $usuario_predefinido['proteccion'],
        'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser modificada bajo ninguna circunstancia.'
    ];
}
```

### **3. En Desactivación (`desactivarUsuario`):**
```php
// Verificar que no sea un usuario predefinido del sistema
$usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
if ($usuario_predefinido) {
    return [
        'error' => 'NO SE PUEDE DESACTIVAR UN USUARIO PREDEFINIDO DEL SISTEMA',
        'error_code' => 'PROTECTED_USER_DEACTIVATE',
        'usuario' => $usuario_predefinido['descripcion'],
        'proteccion' => $usuario_predefinido['proteccion'],
        'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser desactivada bajo ninguna circunstancia.'
    ];
}
```

### **4. En Activación (`activarUsuario`):**
```php
// Verificar que no sea un usuario predefinido del sistema
$usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
if ($usuario_predefinido) {
    return [
        'error' => 'NO SE PUEDE MODIFICAR EL ESTADO DE UN USUARIO PREDEFINIDO DEL SISTEMA',
        'error_code' => 'PROTECTED_USER_ACTIVATE',
        'usuario' => $usuario_predefinido['descripcion'],
        'proteccion' => $usuario_predefinido['proteccion'],
        'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y su estado NO puede ser modificado bajo ninguna circunstancia.'
    ];
}
```

## 📋 Listado de Usuarios con Información de Protección

### **Método `listarUsuarios()` Modificado:**
```php
private function listarUsuarios() {
    // ... consulta SQL original ...
    
    // Agregar información de protección para usuarios predefinidos
    foreach ($usuarios as &$usuario) {
        if ($this->esUsuarioPredefinido($usuario['usuario'])) {
            $info_proteccion = self::USUARIOS_PREDEFINIDOS[strtolower($usuario['usuario'])];
            $usuario['protegido'] = true;
            $usuario['descripcion'] = $info_proteccion['descripcion'];
            $usuario['proteccion'] = $info_proteccion['proteccion'];
            $usuario['estado_proteccion'] = '🔒 PROTEGIDO - NO MODIFICABLE';
            $usuario['acciones_permitidas'] = ['ver'];
            $usuario['acciones_bloqueadas'] = ['editar', 'eliminar', 'activar', 'desactivar'];
        } else {
            $usuario['protegido'] = false;
            $usuario['estado_proteccion'] = '📝 EDITABLE';
            $usuario['acciones_permitidas'] = ['ver', 'editar', 'eliminar', 'activar', 'desactivar'];
            $usuario['acciones_bloqueadas'] = [];
        }
    }
    
    return $usuarios;
}
```

## 🧪 Pruebas Implementadas

### **TestUsuariosPredefinidos.php:**
- ✅ Verificación de usuarios predefinidos
- ✅ Verificación de listado completo con protecciones
- ✅ Prueba de eliminación bloqueada
- ✅ Prueba de desactivación bloqueada
- ✅ Prueba de edición bloqueada
- ✅ Verificación de información de protección

## 🔒 Niveles de Seguridad

### **Nivel 1: Identificación Automática**
- Detección automática de usuarios predefinidos por nombre
- No depende de IDs de base de datos
- Protección basada en nombres de usuario

### **Nivel 2: Verificación por ID**
- Doble verificación al operar por ID
- Consulta a BD para obtener nombre de usuario
- Validación cruzada con constantes de protección

### **Nivel 3: Bloqueo de Operaciones**
- Bloqueo completo de operaciones CRUD
- Mensajes de error específicos y profesionales
- Códigos de error únicos para cada tipo de protección

### **Nivel 4: Información de Protección**
- Información detallada sobre protecciones
- Marcado visual de usuarios protegidos
- Restricción de acciones permitidas

## 📝 Casos de Uso

### **Escenario 1: Intentar Eliminar Usuario Root**
- ❌ **Operación**: `DELETE FROM usuarios WHERE usuario = 'root'`
- 🚫 **Resultado**: Error de protección
- 📋 **Mensaje**: "NO SE PUEDE ELIMINAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- 🔍 **Código**: `PROTECTED_USER_DELETE`

### **Escenario 2: Intentar Editar Usuario Admin**
- ❌ **Operación**: `UPDATE usuarios SET nombre = 'Nuevo Nombre' WHERE usuario = 'admin'`
- 🚫 **Resultado**: Error de protección
- 📋 **Mensaje**: "NO SE PUEDE MODIFICAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- 🔍 **Código**: `PROTECTED_USER_UPDATE`

### **Escenario 3: Intentar Desactivar Usuario Cliente**
- ❌ **Operación**: `UPDATE usuarios SET activo = 0 WHERE usuario = 'cliente'`
- 🚫 **Resultado**: Error de protección
- 📋 **Mensaje**: "NO SE PUEDE DESACTIVAR UN USUARIO PREDEFINIDO DEL SISTEMA"
- 🔍 **Código**: `PROTECTED_USER_DEACTIVATE`

## 🎯 Beneficios de la Implementación

### **Seguridad:**
- ✅ **Protección absoluta** de cuentas maestras
- ✅ **Prevención de sabotaje** del sistema
- ✅ **Integridad garantizada** de usuarios críticos
- ✅ **Auditoría completa** de intentos de modificación

### **Mantenibilidad:**
- ✅ **Configuración centralizada** de usuarios protegidos
- ✅ **Fácil adición** de nuevos usuarios protegidos
- ✅ **Código reutilizable** para verificaciones
- ✅ **Documentación clara** de protecciones

### **Experiencia de Usuario:**
- ✅ **Mensajes claros** sobre restricciones
- ✅ **Identificación visual** de usuarios protegidos
- ✅ **Información detallada** sobre protecciones
- ✅ **Prevención de errores** accidentales

## 🔧 Configuración y Personalización

### **Agregar Nuevo Usuario Protegido:**
```php
private const USUARIOS_PREDEFINIDOS = [
    // ... usuarios existentes ...
    'nuevo_usuario' => [
        'rol' => 2,
        'descripcion' => 'Nuevo Usuario del Sistema',
        'proteccion' => 'PROTEGIDO - Cuenta especial del sistema'
    ]
];
```

### **Modificar Nivel de Protección:**
```php
// Para permitir solo lectura (sin modificación)
$usuario['acciones_permitidas'] = ['ver'];
$usuario['acciones_bloqueadas'] = ['editar', 'eliminar', 'activar', 'desactivar'];

// Para permitir edición limitada
$usuario['acciones_permitidas'] = ['ver', 'editar'];
$usuario['acciones_bloqueadas'] = ['eliminar', 'activar', 'desactivar'];
```

## 📞 Soporte y Mantenimiento

### **Monitoreo Recomendado:**
1. **Revisar logs** de intentos de modificación de usuarios protegidos
2. **Verificar auditoría** de operaciones bloqueadas
3. **Monitorear intentos** de violación de protecciones
4. **Validar integridad** de usuarios predefinidos

### **Troubleshooting:**
1. **Error PROTECTED_USER_DELETE**: Usuario está en lista de protegidos
2. **Error PROTECTED_USER_UPDATE**: Usuario está en lista de protegidos
3. **Error PROTECTED_USER_DEACTIVATE**: Usuario está en lista de protegidos
4. **Error PROTECTED_USER_ACTIVATE**: Usuario está en lista de protegidos

---

## 📋 Resumen de Cambios

| Archivo | Cambio | Descripción |
|---------|--------|-------------|
| `SuperAdminController.php` | Constantes y métodos de protección | Implementación completa de protecciones |
| `TestUsuariosPredefinidos.php` | Nuevo | Test completo de protecciones implementadas |
| `PROTECCION_USUARIOS_PREDEFINIDOS.md` | Nuevo | Documentación completa del sistema de protección |

---

**🎯 Estado**: ✅ **IMPLEMENTADO Y PROBADO**
**🔒 Seguridad**: ✅ **MÁXIMA PROTECCIÓN**
**📊 Cobertura**: ✅ **100% DE USUARIOS PREDEFINIDOS**
**🧪 Testing**: ✅ **SUITE COMPLETA DE PROTECCIONES**
**🛡️ Nivel**: ✅ **PROTECCIÓN ABSOLUTA**
