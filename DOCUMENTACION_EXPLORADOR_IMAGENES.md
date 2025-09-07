# Módulo de Explorador de Imágenes

## Descripción
Módulo completo para la exploración y gestión de imágenes en el servidor, con interfaz similar al explorador de Windows.

## Características

### 🗂️ **Exploración de Carpetas**
- Navegación por carpetas y subcarpetas dentro de `public/images`
- Vista tipo explorador de Windows con carpetas y archivos
- Navegación con doble clic o botón "entrar"
- Listado ordenado: carpetas primero, luego archivos

### 🖼️ **Visualización de Imágenes**
- Miniaturas (thumbnails) para imágenes
- Soporte para formatos: JPG, JPEG, PNG, GIF, BMP, WEBP
- Iconos para archivos que no son imágenes
- Nombres de archivos debajo de cada elemento

### 🛠️ **Acciones Disponibles**
- **Navegación**: Entrar a carpetas con doble clic
- **Regresar**: Botón "Regresar" a carpeta anterior
- **Eliminar**: Botón eliminar en cada imagen
- **Recargar**: Botón para refrescar contenido

### 🔒 **Seguridad**
- Solo usuarios autenticados con rol **Administrador (rol = 1)** pueden acceder
- Validación de rutas para evitar acceso fuera de `public/images`
- Protección contra ataques de path traversal (`../`)

### 🎨 **Experiencia de Usuario**
- Interfaz similar al explorador de Windows
- Breadcrumb para mostrar ubicación actual
- Vista en cuadrícula para carpetas y archivos
- Confirmación antes de eliminar imágenes
- Eliminación sin recargar página (AJAX)

## Estructura de Archivos

```
ModuStackVisit_2/
├── app/Controllers/
│   └── ExploradorImagenesController.php    # Controlador principal
├── resources/views/
│   ├── explorador_imagenes.php             # Vista principal
│   └── procesar_explorador.php             # Procesador AJAX
├── public/images/                          # Directorio base de imágenes
│   ├── eventos/
│   │   ├── enero/
│   │   └── febrero/
│   ├── productos/
│   └── usuarios/
├── explorador_imagenes.php                 # Punto de entrada
└── procesar_explorador_ajax.php            # Punto de entrada AJAX
```

## Instalación

### 1. **Crear Directorio Base**
```bash
mkdir -p public/images
```

### 2. **Configurar Permisos**
Asegurar que el directorio `public/images` tenga permisos de escritura:
```bash
chmod 755 public/images
```

### 3. **Crear Estructura de Ejemplo**
```bash
mkdir -p public/images/eventos/enero
mkdir -p public/images/eventos/febrero
mkdir -p public/images/productos
mkdir -p public/images/usuarios
```

## Uso

### **Acceso al Módulo**
1. Iniciar sesión como usuario con rol **Administrador (rol = 1)**
2. En el menú lateral, hacer clic en **"Explorador de Imágenes"**
3. Se abrirá la interfaz del explorador

### **Navegación**
- **Doble clic** en una carpeta para entrar
- **Breadcrumb** para navegar a carpetas anteriores
- **Botón Recargar** para refrescar el contenido

### **Gestión de Imágenes**
- **Eliminar**: Hacer clic en el botón de eliminar (🗑️) en cualquier imagen
- **Confirmar**: Escribir la confirmación requerida
- **Resultado**: La imagen se elimina del servidor y desaparece de la vista

## API Endpoints

### **GET** `/procesar_explorador.php?accion=obtener_contenido&ruta={ruta}`
Obtiene el contenido de una carpeta.

**Parámetros:**
- `ruta`: Ruta relativa desde `public/images`

**Respuesta:**
```json
{
    "success": true,
    "contenido": {
        "carpetas": [
            {
                "nombre": "eventos",
                "ruta": "eventos",
                "tipo": "carpeta"
            }
        ],
        "archivos": [
            {
                "nombre": "imagen.jpg",
                "ruta": "imagen.jpg",
                "tipo": "imagen",
                "extension": "jpg",
                "tamaño": 1024,
                "fecha_modificacion": 1640995200
            }
        ]
    },
    "breadcrumb": [
        {"nombre": "public/images", "ruta": ""},
        {"nombre": "eventos", "ruta": "eventos"}
    ],
    "ruta_actual": "eventos"
}
```

### **POST** `/procesar_explorador.php`
Elimina una imagen del servidor.

**Parámetros:**
- `accion`: "eliminar_imagen"
- `ruta`: Ruta relativa de la imagen a eliminar

**Respuesta:**
```json
{
    "success": true,
    "mensaje": "Imagen eliminada correctamente"
}
```

## Seguridad

### **Validación de Rutas**
- Todas las rutas se validan para asegurar que estén dentro de `public/images`
- Se eliminan intentos de path traversal (`../`)
- Se utiliza `realpath()` para normalizar rutas

### **Control de Acceso**
- Verificación de sesión activa
- Validación de rol de usuario (solo Administrador)
- Respuestas HTTP apropiadas para errores

### **Sanitización**
- Escape de caracteres especiales en nombres de archivos
- Validación de extensiones de archivos
- Limpieza de rutas de entrada

## Personalización

### **Formatos de Imagen Soportados**
Modificar en `ExploradorImagenesController.php`:
```php
$esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
```

### **Tamaño de Miniaturas**
Modificar en `explorador_imagenes.php`:
```css
.thumbnail {
    width: 100px;
    height: 100px;
}
```

### **Directorio Base**
Modificar en `ExploradorImagenesController.php`:
```php
$this->basePath = realpath(__DIR__ . '/../../public/images');
```

## Solución de Problemas

### **Error: "Directorio de imágenes no encontrado"**
- Verificar que existe el directorio `public/images`
- Verificar permisos del directorio

### **Error: "Ruta no válida"**
- Verificar que la ruta no contenga `../`
- Verificar que la ruta esté dentro de `public/images`

### **Error: "Acceso denegado"**
- Verificar que el usuario esté autenticado
- Verificar que el usuario tenga rol de Administrador (rol = 1)

### **Imágenes no se muestran**
- Verificar que las imágenes estén en formatos soportados
- Verificar permisos de lectura de archivos
- Verificar que las rutas sean correctas

## Mantenimiento

### **Limpieza de Archivos**
- El módulo no incluye funcionalidad de limpieza automática
- Se recomienda implementar limpieza periódica de archivos huérfanos

### **Backup**
- Realizar respaldos regulares del directorio `public/images`
- Considerar implementar versionado de imágenes

### **Monitoreo**
- Monitorear el uso de espacio en disco
- Implementar logs de eliminación de archivos
- Monitorear accesos no autorizados

## Versión
**v1.0** - Versión inicial con funcionalidades básicas de exploración y eliminación.
