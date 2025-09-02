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
    
    // Tablas principales del sistema
    private const TABLAS_PRINCIPALES = [
        'usuarios' => [
            'nombre' => 'Usuarios del Sistema',
            'columna_cedula' => 'cedula',
            'descripcion' => 'Tabla principal de usuarios y autenticación'
        ],
        'aportante' => [
            'nombre' => 'Aportantes',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Información de aportantes del usuario'
        ],
        'autorizaciones' => [
            'nombre' => 'Autorizaciones',
            'columna_cedula' => 'cedula',
            'descripcion' => 'Autorizaciones del usuario'
        ],
        'camara_comercio' => [
            'nombre' => 'Cámara de Comercio',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Información de cámara de comercio'
        ],
        'composicion_familiar' => [
            'nombre' => 'Composición Familiar',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Composición familiar del usuario'
        ],
        'concepto_final_evaluador' => [
            'nombre' => 'Concepto Final Evaluador',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Concepto final del evaluador'
        ],
        'cuentas_bancarias' => [
            'nombre' => 'Cuentas Bancarias',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Cuentas bancarias del usuario'
        ],
        'data_credito' => [
            'nombre' => 'Data Crédito',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Datos de crédito del usuario'
        ],
        'evidencia_fotografica' => [
            'nombre' => 'Evidencia Fotográfica',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Evidencia fotográfica del usuario'
        ],
        'experiencia_laboral' => [
            'nombre' => 'Experiencia Laboral',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Experiencia laboral del usuario'
        ],
        'firmas' => [
            'nombre' => 'Firmas',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Firmas del usuario'
        ],
        'formularios' => [
            'nombre' => 'Formularios',
            'columna_cedula' => null,
            'descripcion' => 'Formularios del sistema (sin cédula)'
        ],
        'foto_perfil_autorizacion' => [
            'nombre' => 'Fotos Perfil Autorización',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Fotos de perfil para autorización'
        ],
        'foto_perfil_visita' => [
            'nombre' => 'Fotos Perfil Visita',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Fotos de perfil para visita'
        ],
        'gasto' => [
            'nombre' => 'Gastos',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Gastos del usuario'
        ],
        'informacion_judicial' => [
            'nombre' => 'Información Judicial',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Información judicial del usuario'
        ],
        'informacion_pareja' => [
            'nombre' => 'Información de Pareja',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Información de pareja del usuario'
        ],
        'ingresos_mensuales' => [
            'nombre' => 'Ingresos Mensuales',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Ingresos mensuales del usuario'
        ],
        'inventario_enseres' => [
            'nombre' => 'Inventario de Enseres',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Inventario de enseres del usuario'
        ],
        'ubicacion' => [
            'nombre' => 'Ubicación',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Ubicación del usuario'
        ],
        'ubicacion_autorizacion' => [
            'nombre' => 'Ubicación Autorización',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Ubicación para autorización'
        ],
        'ubicacion_foto' => [
            'nombre' => 'Fotos de Ubicación',
            'columna_cedula' => 'id_cedula',
            'descripcion' => 'Fotos de ubicación del usuario'
        ]
    ];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new LoggerService();
    }
    
    /**
     * Obtener todas las tablas principales
     * @return array
     */
    public function obtenerTablasPrincipales() {
        return self::TABLAS_PRINCIPALES;
    }
    
    /**
     * Obtener estadísticas de una tabla específica
     * @param string $nombreTabla
     * @return array
     */
    public function obtenerEstadisticasTabla($nombreTabla) {
        if (!isset(self::TABLAS_PRINCIPALES[$nombreTabla])) {
            return ['error' => 'Tabla no encontrada'];
        }
        
        try {
            $tabla = self::TABLAS_PRINCIPALES[$nombreTabla];
            $columnaCedula = $tabla['columna_cedula'];
            
            // Contar total de registros
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM `$nombreTabla`");
            $stmt->execute();
            $totalRegistros = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            $estadisticas = [
                'tabla' => $nombreTabla,
                'nombre' => $tabla['nombre'],
                'total_registros' => $totalRegistros,
                'tiene_cedula' => !is_null($columnaCedula),
                'columna_cedula' => $columnaCedula,
                'descripcion' => $tabla['descripcion']
            ];
            
            // Si tiene columna de cédula, obtener estadísticas adicionales
            if ($columnaCedula) {
                // Contar cédulas únicas
                $stmt = $this->db->prepare("SELECT COUNT(DISTINCT `$columnaCedula`) as cedulas_unicas FROM `$nombreTabla` WHERE `$columnaCedula` IS NOT NULL");
                $stmt->execute();
                $cedulasUnicas = $stmt->fetch(\PDO::FETCH_ASSOC)['cedulas_unicas'];
                
                $estadisticas['cedulas_unicas'] = $cedulasUnicas;
                $estadisticas['registros_sin_cedula'] = $totalRegistros - $cedulasUnicas;
            }
            
            return $estadisticas;
            
        } catch (PDOException $e) {
            $this->logger->error('Error al obtener estadísticas de tabla', [
                'tabla' => $nombreTabla,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al obtener estadísticas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar registros por cédula en una tabla específica
     * @param string $nombreTabla
     * @param int $cedula
     * @return array
     */
    public function eliminarRegistrosPorCedula($nombreTabla, $cedula) {
        if (!isset(self::TABLAS_PRINCIPALES[$nombreTabla])) {
            return ['error' => 'Tabla no encontrada'];
        }
        
        $tabla = self::TABLAS_PRINCIPALES[$nombreTabla];
        $columnaCedula = $tabla['columna_cedula'];
        
        if (!$columnaCedula) {
            return ['error' => 'Esta tabla no tiene columna de cédula'];
        }
        
        try {
            // Verificar si existen registros
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM `$nombreTabla` WHERE `$columnaCedula` = :cedula");
            $stmt->bindParam(':cedula', $cedula, \PDO::PARAM_INT);
            $stmt->execute();
            $totalRegistros = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            if ($totalRegistros == 0) {
                return ['error' => 'No se encontraron registros para la cédula especificada'];
            }
            
            // Eliminar registros
            $stmt = $this->db->prepare("DELETE FROM `$nombreTabla` WHERE `$columnaCedula` = :cedula");
            $stmt->bindParam(':cedula', $cedula, \PDO::PARAM_INT);
            $stmt->execute();
            
            $registrosEliminados = $stmt->rowCount();
            
            $this->logger->info('Registros eliminados por cédula', [
                'tabla' => $nombreTabla,
                'cedula' => $cedula,
                'registros_eliminados' => $registrosEliminados
            ]);
            
            return [
                'success' => true,
                'mensaje' => "Se eliminaron $registrosEliminados registros de la tabla '$nombreTabla' para la cédula $cedula",
                'tabla' => $nombreTabla,
                'cedula' => $cedula,
                'registros_eliminados' => $registrosEliminados
            ];
            
        } catch (PDOException $e) {
            $this->logger->error('Error al eliminar registros por cédula', [
                'tabla' => $nombreTabla,
                'cedula' => $cedula,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al eliminar registros: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar registros por cédula en TODAS las tablas relacionadas
     * @param int $cedula
     * @return array
     */
    public function eliminarRegistrosPorCedulaEnTodasLasTablas($cedula) {
        $resultados = [];
        $totalRegistrosEliminados = 0;
        $tablasProcesadas = 0;
        
        foreach (self::TABLAS_PRINCIPALES as $nombreTabla => $tabla) {
            if ($tabla['columna_cedula']) {
                $resultado = $this->eliminarRegistrosPorCedula($nombreTabla, $cedula);
                $resultados[$nombreTabla] = $resultado;
                
                if (isset($resultado['registros_eliminados'])) {
                    $totalRegistrosEliminados += $resultado['registros_eliminados'];
                }
                
                $tablasProcesadas++;
            }
        }
        
        $this->logger->info('Eliminación masiva por cédula completada', [
            'cedula' => $cedula,
            'tablas_procesadas' => $tablasProcesadas,
            'total_registros_eliminados' => $totalRegistrosEliminados
        ]);
        
        return [
            'success' => true,
            'mensaje' => "Proceso completado. Se eliminaron $totalRegistrosEliminados registros en $tablasProcesadas tablas para la cédula $cedula",
            'cedula' => $cedula,
            'tablas_procesadas' => $tablasProcesadas,
            'total_registros_eliminados' => $totalRegistrosEliminados,
            'resultados_por_tabla' => $resultados
        ];
    }
    
    /**
     * Truncar una tabla específica
     * @param string $nombreTabla
     * @return array
     */
    public function truncarTabla($nombreTabla) {
        if (!isset(self::TABLAS_PRINCIPALES[$nombreTabla])) {
            return ['error' => 'Tabla no encontrada'];
        }
        
        try {
            // Obtener estadísticas antes de truncar
            $estadisticas = $this->obtenerEstadisticasTabla($nombreTabla);
            $totalRegistros = $estadisticas['total_registros'] ?? 0;
            
            // Truncar tabla
            $stmt = $this->db->prepare("TRUNCATE TABLE `$nombreTabla`");
            $stmt->execute();
            
            $this->logger->warning('Tabla truncada', [
                'tabla' => $nombreTabla,
                'registros_eliminados' => $totalRegistros,
                'usuario' => $_SESSION['username'] ?? 'unknown'
            ]);
            
            return [
                'success' => true,
                'mensaje' => "Tabla '$nombreTabla' truncada exitosamente. Se eliminaron $totalRegistros registros.",
                'tabla' => $nombreTabla,
                'registros_eliminados' => $totalRegistros
            ];
            
        } catch (PDOException $e) {
            $this->logger->error('Error al truncar tabla', [
                'tabla' => $nombreTabla,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Error al truncar tabla: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas generales del sistema
     * @return array
     */
    public function obtenerEstadisticasGenerales() {
        $estadisticas = [];
        $totalRegistrosSistema = 0;
        
        foreach (self::TABLAS_PRINCIPALES as $nombreTabla => $tabla) {
            $estadisticasTabla = $this->obtenerEstadisticasTabla($nombreTabla);
            
            if (!isset($estadisticasTabla['error'])) {
                $estadisticas[$nombreTabla] = $estadisticasTabla;
                $totalRegistrosSistema += $estadisticasTabla['total_registros'];
            }
        }
        
        return [
            'total_tablas' => count(self::TABLAS_PRINCIPALES),
            'total_registros_sistema' => $totalRegistrosSistema,
            'estadisticas_por_tabla' => $estadisticas
        ];
    }
    
    /**
     * Verificar si una tabla existe
     * @param string $nombreTabla
     * @return bool
     */
    public function tablaExiste($nombreTabla) {
        try {
            $stmt = $this->db->prepare("SHOW TABLES LIKE :tabla");
            $stmt->bindParam(':tabla', $nombreTabla);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener información de una tabla específica
     * @param string $nombreTabla
     * @return array
     */
    public function obtenerInformacionTabla($nombreTabla) {
        if (!isset(self::TABLAS_PRINCIPALES[$nombreTabla])) {
            return ['error' => 'Tabla no encontrada'];
        }
        
        if (!$this->tablaExiste($nombreTabla)) {
            return ['error' => 'La tabla no existe en la base de datos'];
        }
        
        return self::TABLAS_PRINCIPALES[$nombreTabla];
    }
}
