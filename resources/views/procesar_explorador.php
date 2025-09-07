<?php
// Procesador AJAX para el explorador de imágenes
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

// Función para enviar respuesta JSON
function enviarRespuesta($data) {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Función para manejar errores
function manejarError($mensaje, $codigo = 500) {
    http_response_code($codigo);
    enviarRespuesta(['error' => $mensaje]);
}

try {
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? '';
    $basePath = realpath(__DIR__ . '/../public/images');
    
    if (!$basePath) {
        manejarError('Directorio de imágenes no encontrado');
    }
    
    switch ($accion) {
        case 'obtener_contenido':
            $rutaActual = $_GET['ruta'] ?? '';
            $rutaCompleta = validarRuta($rutaActual, $basePath);
            
            $contenido = obtenerContenidoCarpeta($rutaCompleta, $basePath);
            $breadcrumb = generarBreadcrumb($rutaActual);
            
            enviarRespuesta([
                'success' => true,
                'contenido' => $contenido,
                'breadcrumb' => $breadcrumb,
                'ruta_actual' => $rutaActual
            ]);
            break;
            
        case 'eliminar_imagen':
            $rutaImagen = $_POST['ruta'] ?? '';
            
            if (empty($rutaImagen)) {
                manejarError('Ruta de imagen no especificada', 400);
            }
            
            $rutaCompleta = validarRuta($rutaImagen, $basePath);
            
            if (!file_exists($rutaCompleta)) {
                manejarError('La imagen no existe', 404);
            }
            
            if (unlink($rutaCompleta)) {
                enviarRespuesta([
                    'success' => true,
                    'mensaje' => 'Imagen eliminada correctamente'
                ]);
            } else {
                manejarError('No se pudo eliminar la imagen');
            }
            break;
            
        default:
            manejarError('Acción no válida', 400);
    }
    
} catch (Exception $e) {
    error_log("Error en procesar_explorador.php: " . $e->getMessage());
    manejarError('Error interno del servidor');
}

/**
 * Validar que la ruta esté dentro de public/images
 */
function validarRuta($ruta, $basePath) {
    // Limpiar la ruta
    $ruta = trim($ruta, '/');
    $ruta = str_replace('..', '', $ruta); // Eliminar intentos de salir del directorio
    
    $rutaCompleta = $basePath;
    
    if (!empty($ruta)) {
        $rutaCompleta .= DIRECTORY_SEPARATOR . $ruta;
    }
    
    // Verificar que la ruta esté dentro del directorio base
    $rutaReal = realpath($rutaCompleta);
    if ($rutaReal === false || strpos($rutaReal, $basePath) !== 0) {
        throw new Exception('Ruta no válida');
    }
    
    return $rutaReal;
}

/**
 * Obtener contenido de una carpeta
 */
function obtenerContenidoCarpeta($rutaCompleta, $basePath) {
    if (!is_dir($rutaCompleta)) {
        throw new Exception('La carpeta no existe');
    }

    $contenido = [
        'carpetas' => [],
        'archivos' => []
    ];

    $items = scandir($rutaCompleta);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $rutaItem = $rutaCompleta . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($rutaItem)) {
            $contenido['carpetas'][] = [
                'nombre' => $item,
                'ruta' => obtenerRutaRelativa($rutaItem, $basePath),
                'tipo' => 'carpeta'
            ];
        } else {
            $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
            
            $contenido['archivos'][] = [
                'nombre' => $item,
                'ruta' => obtenerRutaRelativa($rutaItem, $basePath),
                'tipo' => $esImagen ? 'imagen' : 'archivo',
                'extension' => $extension,
                'tamaño' => filesize($rutaItem),
                'fecha_modificacion' => filemtime($rutaItem)
            ];
        }
    }

    // Ordenar: carpetas primero, luego archivos
    usort($contenido['carpetas'], function($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
    
    usort($contenido['archivos'], function($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });

    return $contenido;
}

/**
 * Obtener ruta relativa desde public/images
 */
function obtenerRutaRelativa($rutaCompleta, $basePath) {
    $rutaRelativa = str_replace($basePath . DIRECTORY_SEPARATOR, '', $rutaCompleta);
    return str_replace(DIRECTORY_SEPARATOR, '/', $rutaRelativa);
}

/**
 * Generar breadcrumb
 */
function generarBreadcrumb($rutaActual) {
    $breadcrumb = [
        ['nombre' => 'public/images', 'ruta' => '']
    ];

    if (!empty($rutaActual)) {
        $partes = explode('/', trim($rutaActual, '/'));
        $rutaAcumulada = '';
        
        foreach ($partes as $parte) {
            if (!empty($parte)) {
                $rutaAcumulada .= ($rutaAcumulada ? '/' : '') . $parte;
                $breadcrumb[] = [
                    'nombre' => $parte,
                    'ruta' => $rutaAcumulada
                ];
            }
        }
    }

    return $breadcrumb;
}
?>

