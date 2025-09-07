# Módulo Explorador de Imágenes

## Descripción
Módulo completo para explorar y gestionar imágenes en el servidor, con interfaz similar al explorador de Windows. Solo accesible para superadministradores.

## Características Implementadas

### ✅ Navegación
- **Vista tipo explorador de Windows**: Interfaz intuitiva con cuadrícula de archivos
- **Navegación por carpetas**: Doble clic para entrar a carpetas
- **Breadcrumb**: Muestra la ruta actual (public/images > eventos > enero)
- **Botón Atrás**: Navegación hacia carpetas anteriores
- **Botón Recargar**: Actualiza el contenido de la carpeta actual

### ✅ Visualización
- **Miniaturas de imágenes**: Las imágenes se muestran como thumbnails
- **Iconos por tipo**: Carpetas (verde), imágenes (azul), archivos (gris)
- **Información de archivos**: Tamaño y fecha de modificación
- **Vista de cuadrícula**: Layout responsivo tipo Windows Explorer

### ✅ Gestión de Archivos
- **Eliminación de imágenes**: Botón eliminar con confirmación
- **Confirmación de eliminación**: Modal de confirmación antes de borrar
- **Eliminación AJAX**: Sin recargar la página
- **Feedback visual**: Mensajes de éxito/error

### ✅ Seguridad
- **Autenticación requerida**: Solo usuarios logueados
- **Validación de rol**: Solo superadministradores (rol = 3)
- **Validación de rutas**: Previene acceso fuera de public/images
- **Sanitización de entrada**: Protección contra path traversal
- **Logging**: Registro de todas las operaciones

### ✅ Experiencia de Usuario
- **Interfaz moderna**: Bootstrap 5 con iconos
- **Responsive**: Funciona en desktop y móvil
- **Animaciones**: Transiciones suaves y hover effects
- **Mensajes informativos**: Feedback claro al usuario
- **Estadísticas**: Contador de elementos por carpeta

## Archivos Creados

### 1. Controlador Principal
**`app/Controllers/ExploradorImagenesController.php`**
- Maneja toda la lógica del explorador
- Validaciones de seguridad
- Operaciones de archivos
- Generación de breadcrumbs

### 2. Vista Principal
**`explorador_imagenes.php`**
- Interfaz completa del explorador
- JavaScript para navegación y AJAX
- Estilos CSS personalizados
- Integración con Bootstrap

### 3. Procesador AJAX
**`procesar_explorador_ajax.php`**
- Maneja peticiones AJAX
- Eliminación de archivos
- Validaciones de seguridad
- Respuestas JSON

### 4. Integración en Menú
**`resources/views/layout/menu.php`**
- Enlace agregado al menú de superadministrador
- Removido del menú de administrador

## Estructura de Carpetas

```
public/images/
├── eventos/
│   ├── enero/
│   │   └── evento1.txt
│   └── febrero/
│       └── evento2.txt
├── productos/
│   └── producto1.txt
├── usuarios/
├── header.jpg
├── logo.jpg
└── README.md
```

## Funcionalidades Técnicas

### Validaciones de Seguridad
```php
// Validación de rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    throw new Exception('Acceso denegado');
}

// Validación de ruta
private function validatePath($path) {
    $fullPath = realpath($this->basePath . '/' . $path);
    return strpos($fullPath, $this->basePath) === 0;
}
```

### Tipos de Archivo Soportados
- **Imágenes**: jpg, jpeg, png, gif, bmp, webp, svg
- **Carpetas**: Navegación completa
- **Otros archivos**: Visualización con icono genérico

### Operaciones AJAX
```javascript
// Eliminación de archivo
fetch('procesar_explorador_ajax.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=delete&path=' + encodeURIComponent(path)
})
```

## Uso del Módulo

### Acceso
1. Iniciar sesión como superadministrador (rol = 3)
2. Ir al menú lateral
3. Hacer clic en "Explorador de Imágenes"

### Navegación
1. **Entrar a carpeta**: Doble clic en la carpeta
2. **Volver atrás**: Botón "Atrás" o breadcrumb
3. **Recargar**: Botón "Recargar"

### Eliminación de Imágenes
1. Hacer hover sobre la imagen
2. Hacer clic en el botón rojo de eliminar
3. Confirmar en el modal
4. La imagen se elimina sin recargar la página

## Configuración

### Ruta Base
La ruta base está configurada en el controlador:
```php
$this->basePath = realpath(__DIR__ . '/../../public/images');
```

### Extensiones Permitidas
```php
$this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
```

## Logs y Monitoreo

Todas las operaciones se registran en los logs del sistema:
- Accesos al módulo
- Eliminaciones de archivos
- Errores de validación
- Intentos de acceso no autorizado

## Consideraciones de Seguridad

1. **Path Traversal**: Prevenido con validación de rutas
2. **Autenticación**: Verificación de sesión activa
3. **Autorización**: Solo superadministradores
4. **Sanitización**: Escape de HTML en nombres de archivos
5. **Logging**: Registro de todas las operaciones

## Mejoras Futuras Posibles

- [ ] Subida de archivos
- [ ] Renombrado de archivos
- [ ] Creación de carpetas
- [ ] Vista previa de imágenes en modal
- [ ] Búsqueda de archivos
- [ ] Filtros por tipo de archivo
- [ ] Ordenamiento por nombre/fecha/tamaño
- [ ] Selección múltiple
- [ ] Operaciones en lote

## Compatibilidad

- **PHP**: 7.4+
- **Navegadores**: Chrome, Firefox, Safari, Edge
- **Responsive**: Desktop, tablet, móvil
- **Dependencias**: Bootstrap 5, Bootstrap Icons

---

**Desarrollado por**: Sistema de Visitas  
**Versión**: 1.0  
**Fecha**: 2024