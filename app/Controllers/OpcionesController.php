<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Services/LoggerService.php';

use App\Database\Database;
use App\Services\LoggerService;
use PDO;
use PDOException;

class OpcionesController {
    private $db;
    private $logger;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new LoggerService();
    }
    
    /**
     * Obtener todas las opciones de una tabla específica
     * @param string $tabla
     * @return array
     */
    public function obtenerOpciones($tabla) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$tabla} ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->error("Error al obtener opciones de {$tabla}", ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Obtener una opción específica por ID
     * @param string $tabla
     * @param int $id
     * @return array|false
     */
    public function obtenerOpcionPorId($tabla, $id) {
        try {
            $idColumn = $this->obtenerColumnaId($tabla);
            $stmt = $this->db->prepare("SELECT * FROM {$tabla} WHERE {$idColumn} = :id LIMIT 1");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->error("Error al obtener opción de {$tabla}", ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Crear una nueva opción
     * @param string $tabla
     * @param array $datos
     * @return array
     */
    public function crearOpcion($tabla, $datos) {
        try {
            $idColumn = $this->obtenerColumnaId($tabla);
            $sql = "INSERT INTO {$tabla} (nombre) VALUES (:nombre)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $nuevoId = $this->db->lastInsertId();
                $this->logger->info("Nueva opción creada en {$tabla}", [
                    'id' => $nuevoId,
                    'nombre' => $datos['nombre']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Opción creada exitosamente',
                    'id' => $nuevoId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear la opción'
                ];
            }
        } catch (PDOException $e) {
            $this->logger->error("Error al crear opción en {$tabla}", [
                'datos' => $datos,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error interno del sistema'
            ];
        }
    }
    
    /**
     * Actualizar una opción existente
     * @param string $tabla
     * @param int $id
     * @param array $datos
     * @return array
     */
    public function actualizarOpcion($tabla, $id, $datos) {
        try {
            $idColumn = $this->obtenerColumnaId($tabla);
            $sql = "UPDATE {$tabla} SET nombre = :nombre WHERE {$idColumn} = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->logger->info("Opción actualizada en {$tabla}", [
                    'id' => $id,
                    'nombre' => $datos['nombre']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Opción actualizada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la opción'
                ];
            }
        } catch (PDOException $e) {
            $this->logger->error("Error al actualizar opción en {$tabla}", [
                'id' => $id,
                'datos' => $datos,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error interno del sistema'
            ];
        }
    }
    
    /**
     * Eliminar una opción
     * @param string $tabla
     * @param int $id
     * @return array
     */
    public function eliminarOpcion($tabla, $id) {
        try {
            // Verificar si la opción está siendo utilizada en otras tablas
            if ($this->opcionEnUso($tabla, $id)) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar esta opción porque está siendo utilizada en el sistema'
                ];
            }
            
            $idColumn = $this->obtenerColumnaId($tabla);
            $sql = "DELETE FROM {$tabla} WHERE {$idColumn} = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->logger->info("Opción eliminada de {$tabla}", ['id' => $id]);
                
                return [
                    'success' => true,
                    'message' => 'Opción eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar la opción'
                ];
            }
        } catch (PDOException $e) {
            $this->logger->error("Error al eliminar opción de {$tabla}", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error interno del sistema'
            ];
        }
    }
    
    /**
     * Verificar si una opción está siendo utilizada
     * @param string $tabla
     * @param int $id
     * @return bool
     */
    private function opcionEnUso($tabla, $id) {
        // Mapeo de tablas de opciones y sus referencias
        $referencias = [
            'opc_concepto_final' => ['concepto_final_evaluador'],
            'opc_concepto_seguridad' => ['concepto_final_evaluador'],
            'opc_conviven' => ['composicion_familiar'],
            'opc_cuenta' => ['informacion_financiera'],
            'opc_entidad' => ['informacion_financiera'],
            'opc_estados' => ['informacion_personal'],
            'opc_estado_civiles' => ['informacion_personal'],
            'opc_estado_vivienda' => ['informacion_vivienda'],
            'opc_estaturas' => ['informacion_personal'],
            'opc_estratos' => ['informacion_vivienda'],
            'opc_genero' => ['informacion_personal'],
            'opc_informacion_judicial' => ['informacion_personal'],
            'opc_inventario_enseres' => ['inventario_enseres'],
            'opc_jornada' => ['experiencia_laboral'],
            'opc_marca' => ['vehiculos'],
            'opc_modelo' => ['vehiculos'],
            'opc_nivel_academico' => ['informacion_academica'],
            'opc_num_hijos' => ['informacion_personal'],
            'opc_ocupacion' => ['experiencia_laboral'],
            'opc_parametro' => ['parametros_sistema'],
            'opc_parentesco' => ['composicion_familiar'],
            'opc_peso' => ['informacion_personal'],
            'opc_propiedad' => ['informacion_vivienda'],
            'opc_resultado' => ['evaluaciones'],
            'opc_rh' => ['informacion_personal'],
            'opc_sector' => ['experiencia_laboral'],
            'opc_servicios_publicos' => ['informacion_vivienda'],
            'opc_tipo_cuenta' => ['informacion_financiera'],
            'opc_tipo_documentos' => ['documentos'],
            'opc_tipo_inversion' => ['informacion_financiera'],
            'opc_tipo_vivienda' => ['informacion_vivienda'],
            'opc_vehiculo' => ['vehiculos'],
            'opc_viven' => ['informacion_personal']
        ];
        
        if (!isset($referencias[$tabla])) {
            return false; // No hay referencias conocidas
        }
        
        $idColumn = $this->obtenerColumnaId($tabla);
        $referenciaColumn = $this->obtenerColumnaReferencia($tabla);
        
        foreach ($referencias[$tabla] as $tablaReferencia) {
            try {
                // Verificar si la tabla de referencia existe
                $stmt = $this->db->prepare("SHOW TABLES LIKE :tabla");
                $stmt->bindParam(':tabla', $tablaReferencia);
                $stmt->execute();
                
                if ($stmt->fetch()) {
                    // Verificar si hay registros que usen esta opción
                    $sql = "SELECT COUNT(*) as total FROM {$tablaReferencia} WHERE {$referenciaColumn} = :id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($resultado['total'] > 0) {
                        return true; // La opción está en uso
                    }
                }
            } catch (PDOException $e) {
                // Si hay error, asumimos que no está en uso
                continue;
            }
        }
        
        return false;
    }
    
    /**
     * Obtener el nombre de la columna ID de una tabla
     * @param string $tabla
     * @return string
     */
    public function obtenerColumnaId($tabla) {
        $columnasId = [
            'opc_concepto_final' => 'id_concepto_final',
            'opc_concepto_seguridad' => 'id_concepto_seguridad',
            'opc_conviven' => 'id_conviven',
            'opc_cuenta' => 'id',
            'opc_entidad' => 'id_entidad',
            'opc_estados' => 'id',
            'opc_estado_civiles' => 'id',
            'opc_estado_vivienda' => 'id_estado',
            'opc_estaturas' => 'id',
            'opc_estratos' => 'id',
            'opc_genero' => 'id',
            'opc_informacion_judicial' => 'id',
            'opc_inventario_enseres' => 'id',
            'opc_jornada' => 'id_jornada',
            'opc_marca' => 'id_marca',
            'opc_modelo' => 'id_modelo',
            'opc_nivel_academico' => 'id',
            'opc_num_hijos' => 'id',
            'opc_ocupacion' => 'id',
            'opc_parametro' => 'id',
            'opc_parentesco' => 'id',
            'opc_peso' => 'id',
            'opc_propiedad' => 'id',
            'opc_resultado' => 'id_resultado',
            'opc_rh' => 'id',
            'opc_sector' => 'id',
            'opc_servicios_publicos' => 'id',
            'opc_tipo_cuenta' => 'id_tipo_cuenta',
            'opc_tipo_documentos' => 'id',
            'opc_tipo_inversion' => 'id_tipo_inversion',
            'opc_tipo_vivienda' => 'id',
            'opc_vehiculo' => 'id_vehiculo',
            'opc_viven' => 'id_vive_candidato'
        ];
        
        return $columnasId[$tabla] ?? 'id';
    }
    
    /**
     * Obtener el nombre de la columna de referencia
     * @param string $tabla
     * @return string
     */
    private function obtenerColumnaReferencia($tabla) {
        $columnasReferencia = [
            'opc_concepto_final' => 'concepto_final',
            'opc_concepto_seguridad' => 'concepto_seguridad',
            'opc_conviven' => 'conviven',
            'opc_cuenta' => 'tipo_cuenta',
            'opc_entidad' => 'entidad',
            'opc_estados' => 'estado',
            'opc_estado_civiles' => 'estado_civil',
            'opc_estado_vivienda' => 'estado_vivienda',
            'opc_estaturas' => 'estatura',
            'opc_estratos' => 'estrato',
            'opc_genero' => 'genero',
            'opc_informacion_judicial' => 'informacion_judicial',
            'opc_inventario_enseres' => 'tipo_enser',
            'opc_jornada' => 'jornada',
            'opc_marca' => 'marca',
            'opc_modelo' => 'modelo',
            'opc_nivel_academico' => 'nivel_academico',
            'opc_num_hijos' => 'num_hijos',
            'opc_ocupacion' => 'ocupacion',
            'opc_parametro' => 'parametro',
            'opc_parentesco' => 'parentesco',
            'opc_peso' => 'peso',
            'opc_propiedad' => 'propiedad',
            'opc_resultado' => 'resultado',
            'opc_rh' => 'rh',
            'opc_sector' => 'sector',
            'opc_servicios_publicos' => 'servicio_publico',
            'opc_tipo_cuenta' => 'tipo_cuenta',
            'opc_tipo_documentos' => 'tipo_documento',
            'opc_tipo_inversion' => 'tipo_inversion',
            'opc_tipo_vivienda' => 'tipo_vivienda',
            'opc_vehiculo' => 'tipo_vehiculo',
            'opc_viven' => 'vive_candidato'
        ];
        
        return $columnasReferencia[$tabla] ?? 'id';
    }
    
    /**
     * Obtener estadísticas de una tabla
     * @param string $tabla
     * @return array
     */
    public function obtenerEstadisticas($tabla) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$tabla}");
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'total' => $total,
                'tabla' => $tabla
            ];
        } catch (PDOException $e) {
            $this->logger->error("Error al obtener estadísticas de {$tabla}", ['error' => $e->getMessage()]);
            return ['total' => 0, 'tabla' => $tabla];
        }
    }
    
    /**
     * Validar datos de entrada
     * @param array $datos
     * @return array
     */
    public function validarDatos($datos) {
        $errores = [];
        
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        } elseif (strlen($datos['nombre']) > 50) {
            $errores[] = 'El nombre no puede exceder 50 caracteres';
        }
        
        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }
}
