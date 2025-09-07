# Carpeta de Imágenes del Sistema

Esta carpeta contiene todas las imágenes del sistema de visitas.

## Estructura de carpetas:

- **eventos/**: Imágenes relacionadas con eventos
  - enero/: Eventos de enero
  - febrero/: Eventos de febrero
- **productos/**: Imágenes de productos
- **usuarios/**: Imágenes de perfil de usuarios

## Uso del Explorador de Imágenes

El explorador de imágenes permite:
- Navegar por las carpetas y subcarpetas
- Ver miniaturas de las imágenes
- Eliminar imágenes (solo superadministradores)
- Ver información de archivos

## Seguridad

- Solo usuarios con rol de superadministrador (rol = 3) pueden acceder
- Las rutas están validadas para evitar acceso fuera de esta carpeta
- Todas las operaciones están registradas en logs
