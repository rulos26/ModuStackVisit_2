<?php
/**
 * MÓDULO DE PROVEEDOR DE DATOS DEL EVALUADO
 * Obtención de datos básicos del evaluado desde la base de datos
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

class EvaluadoDataProvider {
    private $mysqli;
    private $id_cedula;
    private $logger;
    
    public function __construct($mysqli, $id_cedula, $logger) {
        $this->mysqli = $mysqli;
        $this->id_cedula = $id_cedula;
        $this->logger = $logger;
    }
    
    public function obtenerDatosEvaluado() {
        try {
            $query = "
                SELECT 
                    e.*,
                    td.nombre as tipo_documento_nombre,
                    c.nombre as ciudad_nombre,
                    rh.nombre as rh_nombre,
                    est.nombre as estatura_nombre,
                    ec.nombre as estado_civil_nombre,
                    m.municipio as lugar_nacimiento_municipio
                FROM evaluados e
                LEFT JOIN opc_tipo_documentos td ON e.tipo_documento_id = td.id
                LEFT JOIN opciones c ON e.ciudad_id = c.id
                LEFT JOIN opc_rh rh ON e.rh_id = rh.id
                LEFT JOIN opc_estaturas est ON e.estatura_id = est.id
                LEFT JOIN opc_estado_civiles ec ON e.estado_civil_id = ec.id
                LEFT JOIN municipios m ON e.lugar_nacimiento_municipio_id = m.id
                WHERE e.id_cedula = ?
            ";
            
            $stmt = $this->mysqli->prepare($query);
            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . $this->mysqli->error);
            }
            
            $stmt->bind_param('s', $this->id_cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception('No se encontraron datos para la cédula: ' . $this->id_cedula);
            }
            
            $datos = $result->fetch_assoc();
            $this->logger->logInfo('Datos del evaluado obtenidos correctamente para cédula: ' . $this->id_cedula);
            
            return $datos;
            
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos del evaluado: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function validarExistenciaEvaluado() {
        try {
            $query = "SELECT id FROM evaluados WHERE id_cedula = ?";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;
            }
            return false;
        } catch (Exception $e) {
            $this->logger->logError('Error al validar existencia del evaluado: ' . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerDatosResumidos() {
        try {
            $query = "
                SELECT 
                    e.id_cedula,
                    e.nombres,
                    e.apellidos,
                    e.fecha_nacimiento,
                    td.nombre as tipo_documento
                FROM evaluados e
                LEFT JOIN opc_tipo_documentos td ON e.tipo_documento_id = td.id
                WHERE e.id_cedula = ?
            ";
            
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos resumidos: ' . $e->getMessage());
            return [];
        }
    }
}
?> 