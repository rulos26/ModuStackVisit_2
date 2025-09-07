<?php
/**
 * CONTROLADOR DEL EXPLORADOR DE IMÁGENES
 * Módulo para explorar y gestionar imágenes en el servidor
 * 
 * @author Sistema de Visitas
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

class ExploradorImagenesController {
    private $logger;
    private $basePath;
    private $allowedExtensions;
    
    public function __construct() {
        $this->logger = new Logger();
        $this->basePath = realpath(__DIR__ . '/../../public/images');
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        
        // Validar que la ruta base existe
        if (!$this->basePath) {
            throw new Exception('La ruta base de imágenes no existe');
        }
    }
    
    /**
     * Validar que el usuario tiene permisos de superadministrador
     */
    private function validateSuperAdminAccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
            throw new Exception('Acceso denegado. Solo superadministradores pueden acceder a este módulo.');
        }
    }
    
    /**
     * Validar que la ruta está dentro del directorio permitido
     */
    private function validatePath($path) {
        $fullPath = realpath($this->basePath . '/' . $path);
        
        if ($fullPath === false) {
            return false;
        }
        
        // Verificar que la ruta está dentro del directorio base
        return strpos($fullPath, $this->basePath) === 0;
    }
    
    /**
     * Obtener contenido de una carpeta
     */
    public function getFolderContent($relativePath = '') {
        try {
            $this->validateSuperAdminAccess();
            
            // Limpiar la ruta
            $relativePath = ltrim($relativePath, '/\\');
            
            // Validar la ruta
            if (!$this->validatePath($relativePath)) {
                throw new Exception('Ruta no válida o fuera del directorio permitido');
            }
            
            $fullPath = $this->basePath . ($relativePath ? '/' . $relativePath : '');
            
            if (!is_dir($fullPath)) {
                throw new Exception('La carpeta no existe');
            }
            
            $items = [];
            $directories = [];
            $files = [];
            
            // Leer el contenido del directorio
            $contents = scandir($fullPath);
            
            foreach ($contents as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                
                $itemPath = $fullPath . '/' . $item;
                $relativeItemPath = $relativePath ? $relativePath . '/' . $item : $item;
                
                if (is_dir($itemPath)) {
                    $directories[] = [
                        'name' => $item,
                        'type' => 'directory',
                        'path' => $relativeItemPath,
                        'size' => $this->getDirectorySize($itemPath),
                        'modified' => filemtime($itemPath)
                    ];
                } else {
                    $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, $this->allowedExtensions);
                    
                    $files[] = [
                        'name' => $item,
                        'type' => $isImage ? 'image' : 'file',
                        'path' => $relativeItemPath,
                        'size' => filesize($itemPath),
                        'modified' => filemtime($itemPath),
                        'extension' => $extension,
                        'isImage' => $isImage
                    ];
                }
            }
            
            // Ordenar: carpetas primero, luego archivos
            usort($directories, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            usort($files, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            $items = array_merge($directories, $files);
            
            return [
                'success' => true,
                'currentPath' => $relativePath,
                'breadcrumb' => $this->generateBreadcrumb($relativePath),
                'items' => $items,
                'totalItems' => count($items)
            ];
            
        } catch (Exception $e) {
            $this->logger->logError('Error en getFolderContent: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Eliminar un archivo
     */
    public function deleteFile($relativePath) {
        try {
            $this->validateSuperAdminAccess();
            
            // Limpiar la ruta
            $relativePath = ltrim($relativePath, '/\\');
            
            // Validar la ruta
            if (!$this->validatePath($relativePath)) {
                throw new Exception('Ruta no válida o fuera del directorio permitido');
            }
            
            $fullPath = $this->basePath . '/' . $relativePath;
            
            if (!file_exists($fullPath)) {
                throw new Exception('El archivo no existe');
            }
            
            if (is_dir($fullPath)) {
                throw new Exception('No se puede eliminar carpetas con este método');
            }
            
            // Eliminar el archivo
            if (unlink($fullPath)) {
                $this->logger->logInfo('Archivo eliminado: ' . $relativePath);
                return [
                    'success' => true,
                    'message' => 'Archivo eliminado correctamente'
                ];
            } else {
                throw new Exception('No se pudo eliminar el archivo');
            }
            
        } catch (Exception $e) {
            $this->logger->logError('Error en deleteFile: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generar breadcrumb
     */
    private function generateBreadcrumb($relativePath) {
        $breadcrumb = [
            ['name' => 'public/images', 'path' => '']
        ];
        
        if ($relativePath) {
            $parts = explode('/', $relativePath);
            $currentPath = '';
            
            foreach ($parts as $part) {
                $currentPath .= ($currentPath ? '/' : '') . $part;
                $breadcrumb[] = [
                    'name' => $part,
                    'path' => $currentPath
                ];
            }
        }
        
        return $breadcrumb;
    }
    
    /**
     * Obtener tamaño de directorio
     */
    private function getDirectorySize($path) {
        $size = 0;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Formatear tamaño de archivo
     */
    public function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Obtener información de una imagen
     */
    public function getImageInfo($relativePath) {
        try {
            $this->validateSuperAdminAccess();
            
            $relativePath = ltrim($relativePath, '/\\');
            
            if (!$this->validatePath($relativePath)) {
                throw new Exception('Ruta no válida');
            }
            
            $fullPath = $this->basePath . '/' . $relativePath;
            
            if (!file_exists($fullPath) || !is_file($fullPath)) {
                throw new Exception('El archivo no existe');
            }
            
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $this->allowedExtensions)) {
                throw new Exception('El archivo no es una imagen válida');
            }
            
            $imageInfo = getimagesize($fullPath);
            
            if ($imageInfo === false) {
                throw new Exception('No se pudo obtener información de la imagen');
            }
            
            return [
                'success' => true,
                'info' => [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'type' => $imageInfo[2],
                    'mime' => $imageInfo['mime'],
                    'size' => filesize($fullPath),
                    'modified' => filemtime($fullPath)
                ]
            ];
            
        } catch (Exception $e) {
            $this->logger->logError('Error en getImageInfo: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>