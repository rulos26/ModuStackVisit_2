<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Services/LoggerService.php';

use App\Database\Database;
use App\Services\LoggerService;
use PDOException;

class TablasPrincipalesController {
    
    private $db;
    private $logger;
    
    // Tabla principal de usuarios evaluados
    private const TABLA_EVALUADOS = 'evaluados';
    
    // Tablas relacionadas que se verifican al eliminar un usuario
    private const TABLAS_RELACIONADAS = [
        'autorizaciones',
        'camara_comercio', 
        'composicion_familiar',
        'concepto_final_evaluador',
        'cuentas_bancarias',
        'data_credito',
        'estados_salud',
        'estado_vivienda',
        'estudios',
        'evidencia_fotografica',
        'experiencia_laboral',
        'firmas',
        'foto_perfil_autorizacion',
        'gasto',
        'informacion_judicial',
        'informacion_pareja',
        'ingresos_mensuales',
        'inventario_enseres',
        'pasivos',
        'patrimonio',
        'servicios_publicos',
        'tipo_vivienda',
        'ubicacion',
        'ubicacion_autorizacion',
        'ubicacion_foto',
        'foto_perfil_visita'
    ];
    
    // Tablas que contienen archivos físicos
    private const TABLAS_CON_ARCHIVOS = [
        'firmas' => ['ruta', 'nombre'],
        'foto_perfil_autorizacion' => ['ruta', 'nombre'],
        'foto_perfil_visita' => ['ruta', 'nombre'],
        'ubicacion_autorizacion' => ['ruta', 'nombre'],
        'ubicacion_foto' => ['ruta', 'nombre']
    ];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new LoggerService();
    }
    
    /**
     * Obtener todos los usuarios evaluados
     * @return array
     */
    public function obtenerUsuariosEvaluados() {
        try {
            // Primero verificar si la tabla existe
            if (!$this->tablaExiste(self::TABLA_EVALUADOS)) {
                return ['error' => 'La tabla "evaluados" no existe en la base de datos'];
            }
            
            // Verificar la estructura de la tabla
            $stmt = $this->db->prepare("DESCRIBE " . self::TABLA_EVALUADOS);
            $stmt->execute();
            $columnas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Verificar si las columnas necesarias existen
            $columnasExistentes = array_column($columnas, 'Field');
            $columnasRequeridas = ['id_cedula', 'nombres', 'apellidos'];
            
            foreach ($columnasRequeridas as $columna) {
                if (!in_array($columna, $columnasExistentes)) {
                    return ['error' => "La columna '$columna' no existe en la tabla evaluados. Columnas disponibles: " . implode(', ', $columnasExistentes)];
                }
            }
            
            // Obtener los usuarios
            $stmt = $this->db->prepare("SELECT id_cedula, nombres, apellidos FROM " . self::TABLA_EVALUADOS . " ORDER BY nombres, apellidos");
            $stmt->execute();
            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $this->logger->info('Usuarios evaluados obtenidos', [
                'total_usuarios' => count($usuarios)
            ]);
            
            return $usuarios;
            
        } catch (PDOException $e) {
            $this->logger->error('Error al obtener usuarios evaluados', [
                'error' => $e->getMessage(),
                'tabla' => self::TABLA_EVALUADOS
            ]);
            return ['error' => 'Error al obtener usuarios evaluados: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar en qué tablas existe información para un id_cedula
     * @param int $idCedula
     * @return array
     */
    public function verificarTablasConDatos($idCedula) {
        $tablasConDatos = [];
        
        try {
            // Verificar tabla evaluados
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . self::TABLA_EVALUADOS . " WHERE id_cedula = :id_cedula");
            $stmt->bindParam(':id_cedula', $idCedula, \PDO::PARAM_INT);
            $stmt->execute();
            $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            if ($total > 0) {
                $tablasConDatos['evaluados'] = $total;
            }
            
            // Verificar tablas relacionadas
            foreach (self::TABLAS_RELACIONADAS as $tabla) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM `$tabla` WHERE id_cedula = :id_cedula");
                $stmt->bindParam(':id_cedula', $idCedula, \PDO::PARAM_INT);
                $stmt->execute();
                $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
                if ($total > 0) {
                    $tablasConDatos[$tabla] = $total;
                }
            }
            
            return $tablasConDatos;
            
        } catch (PDOException $e) {
            $this->logger->error('Error al verificar tablas con datos', [
                'id_cedula' => $idCedula,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al verificar tablas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar archivos físicos asociados a un id_cedula
     * @param int $idCedula
     * @return array
     */
    private function eliminarArchivosFisicos($idCedula) {
        $archivosEliminados = [];
        $errores = [];
        
        try {
            foreach (self::TABLAS_CON_ARCHIVOS as $tabla => $columnas) {
                $rutaCol = $columnas[0];
                $nombreCol = $columnas[1];
                
                $stmt = $this->db->prepare("SELECT `$rutaCol`, `$nombreCol` FROM `$tabla` WHERE id_cedula = :id_cedula");
                $stmt->bindParam(':id_cedula', $idCedula, \PDO::PARAM_INT);
                $stmt->execute();
                $archivos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach ($archivos as $archivo) {
                    $rutaCompleta = $archivo[$rutaCol] . '/' . $archivo[$nombreCol];
                    $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/' . $rutaCompleta;
                    
                    if (file_exists($rutaAbsoluta)) {
                        if (unlink($rutaAbsoluta)) {
                            $archivosEliminados[] = $rutaCompleta;
                        } else {
                            $errores[] = "No se pudo eliminar: $rutaCompleta";
                        }
                    }
                }
            }
            
            return [
                'archivos_eliminados' => $archivosEliminados,
                'errores' => $errores
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('Error al eliminar archivos físicos', [
                'id_cedula' => $idCedula,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al eliminar archivos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar usuario y todos sus datos relacionados
     * @param int $idCedula
     * @return array
     */
    public function eliminarUsuarioCompleto($idCedula) {
        try {
            // Primero eliminar archivos físicos
            $resultadoArchivos = $this->eliminarArchivosFisicos($idCedula);
            
            // Iniciar transacción
            $this->db->beginTransaction();
            
            $registrosEliminados = 0;
            $tablasProcesadas = [];
            
            // Eliminar de tabla evaluados
            $stmt = $this->db->prepare("DELETE FROM " . self::TABLA_EVALUADOS . " WHERE id_cedula = :id_cedula");
            $stmt->bindParam(':id_cedula', $idCedula, \PDO::PARAM_INT);
            $stmt->execute();
            $eliminados = $stmt->rowCount();
            if ($eliminados > 0) {
                $registrosEliminados += $eliminados;
                $tablasProcesadas['evaluados'] = $eliminados;
            }
            
            // Eliminar de tablas relacionadas
            foreach (self::TABLAS_RELACIONADAS as $tabla) {
                $stmt = $this->db->prepare("DELETE FROM `$tabla` WHERE id_cedula = :id_cedula");
                $stmt->bindParam(':id_cedula', $idCedula, \PDO::PARAM_INT);
                $stmt->execute();
                $eliminados = $stmt->rowCount();
                if ($eliminados > 0) {
                    $registrosEliminados += $eliminados;
                    $tablasProcesadas[$tabla] = $eliminados;
                }
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            $this->logger->info('Usuario eliminado completamente', [
                'id_cedula' => $idCedula,
                'registros_eliminados' => $registrosEliminados,
                'tablas_procesadas' => $tablasProcesadas,
                'archivos_eliminados' => count($resultadoArchivos['archivos_eliminados'] ?? [])
            ]);
            
            return [
                'success' => true,
                'mensaje' => "Usuario eliminado exitosamente. Se eliminaron $registrosEliminados registros y " . count($resultadoArchivos['archivos_eliminados'] ?? []) . " archivos.",
                'id_cedula' => $idCedula,
                'registros_eliminados' => $registrosEliminados,
                'tablas_procesadas' => $tablasProcesadas,
                'archivos_eliminados' => $resultadoArchivos['archivos_eliminados'] ?? [],
                'errores_archivos' => $resultadoArchivos['errores'] ?? []
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->logger->error('Error al eliminar usuario completo', [
                'id_cedula' => $idCedula,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al eliminar usuario: ' . $e->getMessage()];
        }
    }
    
    /**
     * Vaciar todas las tablas (TRUNCATE)
     * @return array
     */
    public function vaciarTodasLasTablas() {
        try {
            // Primero eliminar todos los archivos físicos
            $archivosEliminados = [];
            $errores = [];
            
            foreach (self::TABLAS_CON_ARCHIVOS as $tabla => $columnas) {
                $rutaCol = $columnas[0];
                $nombreCol = $columnas[1];
                
                $stmt = $this->db->prepare("SELECT `$rutaCol`, `$nombreCol` FROM `$tabla`");
                $stmt->execute();
                $archivos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach ($archivos as $archivo) {
                    $rutaCompleta = $archivo[$rutaCol] . '/' . $archivo[$nombreCol];
                    $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/' . $rutaCompleta;
                    
                    if (file_exists($rutaAbsoluta)) {
                        if (unlink($rutaAbsoluta)) {
                            $archivosEliminados[] = $rutaCompleta;
                        } else {
                            $errores[] = "No se pudo eliminar: $rutaCompleta";
                        }
                    }
                }
            }
            
            // Iniciar transacción
            $this->db->beginTransaction();
            
            $tablasTruncadas = [];
            
            // Truncar tabla evaluados
            $stmt = $this->db->prepare("TRUNCATE TABLE " . self::TABLA_EVALUADOS);
            $stmt->execute();
            $tablasTruncadas[] = self::TABLA_EVALUADOS;
            
            // Truncar tablas relacionadas
            foreach (self::TABLAS_RELACIONADAS as $tabla) {
                $stmt = $this->db->prepare("TRUNCATE TABLE `$tabla`");
                $stmt->execute();
                $tablasTruncadas[] = $tabla;
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            $this->logger->warning('Todas las tablas vaciadas', [
                'tablas_truncadas' => $tablasTruncadas,
                'archivos_eliminados' => count($archivosEliminados),
                'usuario' => $_SESSION['username'] ?? 'unknown'
            ]);
            
            return [
                'success' => true,
                'mensaje' => "Todas las tablas han sido vaciadas exitosamente. Se eliminaron " . count($archivosEliminados) . " archivos físicos.",
                'tablas_truncadas' => $tablasTruncadas,
                'archivos_eliminados' => $archivosEliminados,
                'errores_archivos' => $errores
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->logger->error('Error al vaciar todas las tablas', [
                'error' => $e->getMessage(),
                'usuario' => $_SESSION['username'] ?? 'unknown'
            ]);
            return ['error' => 'Error al vaciar tablas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar si una tabla existe
     * @param string $nombreTabla
     * @return bool
     */
    private function tablaExiste($nombreTabla) {
        try {
            $stmt = $this->db->prepare("SHOW TABLES LIKE :tabla");
            $stmt->bindParam(':tabla', $nombreTabla);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
