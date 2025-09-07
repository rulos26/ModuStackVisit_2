<?php

namespace App\Controllers;

use App\Database\Database;
use App\Services\LoggerService;

class ExploradorImagenesController
{
    private $db;
    private $logger;
    private $basePath;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logger = new LoggerService();
        $this->basePath = realpath(__DIR__ . '/../../public/images');
    }

    /**
     * Mostrar la vista principal del explorador
     */
    public function index()
    {
        session_start();
        
        // Verificar autenticación y rol administrador
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
            header('Location: ../../index.php');
            exit();
        }

        $rutaActual = $_GET['ruta'] ?? '';
        $rutaCompleta = $this->validarRuta($rutaActual);
        
        $contenido = $this->obtenerContenidoCarpeta($rutaCompleta);
        $breadcrumb = $this->generarBreadcrumb($rutaActual);
        
        include __DIR__ . '/../../resources/views/explorador_imagenes.php';
    }

    /**
     * Obtener contenido de una carpeta via AJAX
     */
    public function obtenerContenido()
    {
        session_start();
        
        // Verificar autenticación y rol administrador
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $rutaActual = $_GET['ruta'] ?? '';
            $rutaCompleta = $this->validarRuta($rutaActual);
            
            $contenido = $this->obtenerContenidoCarpeta($rutaCompleta);
            $breadcrumb = $this->generarBreadcrumb($rutaActual);
            
            echo json_encode([
                'success' => true,
                'contenido' => $contenido,
                'breadcrumb' => $breadcrumb,
                'ruta_actual' => $rutaActual
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $this->logger->log('Error en obtenerContenido: ' . $e->getMessage(), 'error');
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar una imagen
     */
    public function eliminarImagen()
    {
        session_start();
        
        // Verificar autenticación y rol administrador
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $rutaImagen = $_POST['ruta'] ?? '';
            
            if (empty($rutaImagen)) {
                http_response_code(400);
                echo json_encode(['error' => 'Ruta de imagen no especificada']);
                exit();
            }

            $rutaCompleta = $this->validarRuta($rutaImagen);
            
            if (!file_exists($rutaCompleta)) {
                http_response_code(404);
                echo json_encode(['error' => 'La imagen no existe']);
                exit();
            }

            if (unlink($rutaCompleta)) {
                $this->logger->log("Imagen eliminada: $rutaCompleta", 'info');
                echo json_encode(['success' => true, 'mensaje' => 'Imagen eliminada correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo eliminar la imagen']);
            }
            
        } catch (Exception $e) {
            $this->logger->log('Error en eliminarImagen: ' . $e->getMessage(), 'error');
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Validar que la ruta esté dentro de public/images
     */
    private function validarRuta($ruta)
    {
        // Limpiar la ruta
        $ruta = trim($ruta, '/');
        $ruta = str_replace('..', '', $ruta); // Eliminar intentos de salir del directorio
        
        $rutaCompleta = $this->basePath;
        
        if (!empty($ruta)) {
            $rutaCompleta .= DIRECTORY_SEPARATOR . $ruta;
        }
        
        // Verificar que la ruta esté dentro del directorio base
        $rutaReal = realpath($rutaCompleta);
        if ($rutaReal === false || strpos($rutaReal, $this->basePath) !== 0) {
            throw new Exception('Ruta no válida');
        }
        
        return $rutaReal;
    }

    /**
     * Obtener contenido de una carpeta
     */
    private function obtenerContenidoCarpeta($rutaCompleta)
    {
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
                    'ruta' => $this->obtenerRutaRelativa($rutaItem),
                    'tipo' => 'carpeta'
                ];
            } else {
                $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                
                $contenido['archivos'][] = [
                    'nombre' => $item,
                    'ruta' => $this->obtenerRutaRelativa($rutaItem),
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
    private function obtenerRutaRelativa($rutaCompleta)
    {
        $rutaRelativa = str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $rutaCompleta);
        return str_replace(DIRECTORY_SEPARATOR, '/', $rutaRelativa);
    }

    /**
     * Generar breadcrumb
     */
    private function generarBreadcrumb($rutaActual)
    {
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

    /**
     * Formatear tamaño de archivo
     */
    public static function formatearTamaño($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $potencia = floor(($bytes ? log($bytes) : 0) / log(1024));
        $potencia = min($potencia, count($unidades) - 1);
        $bytes /= pow(1024, $potencia);
        return round($bytes, 2) . ' ' . $unidades[$potencia];
    }
}
