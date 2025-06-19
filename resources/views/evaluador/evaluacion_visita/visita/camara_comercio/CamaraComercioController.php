<?php
namespace App\Controllers;

// Actualizar la ruta de la base de datos para la nueva ubicación
require_once __DIR__ . '/../../../../../../app/Database/Database.php';

use App\Database\Database;
use PDOException;
use Exception;

class CamaraComercioController {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Sanitiza los datos de entrada
     */
    public function sanitizarDatos($datos) {
        $sanitizados = [];
        
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $sanitizados[$clave] = trim(strip_tags($valor));
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        
        // Debug: log de datos sanitizados
        error_log('DEBUG CamaraComercioController: Datos sanitizados: ' . json_encode($sanitizados));
        
        return $sanitizados;
    }
    
    /**
     * Valida los datos de entrada
     */
    public function validarDatos($datos) {
        $errores = [];
        
        // Validar cédula
        if (empty($datos['id_cedula']) || !is_numeric($datos['id_cedula'])) {
            $errores[] = 'La cédula es obligatoria y debe ser numérica.';
        }
        
        // Validar si tiene cámara de comercio
        if (!isset($datos['tiene_camara'])) {
            $errores[] = 'Debe indicar si tiene cámara de comercio.';
        }
        
        // Si tiene cámara de comercio, validar campos adicionales
        if (isset($datos['tiene_camara']) && $datos['tiene_camara'] !== '1') {
            if (empty($datos['nombre'])) {
                $errores[] = 'El nombre de la empresa es obligatorio.';
            }
            
            if (empty($datos['razon'])) {
                $errores[] = 'La razón social es obligatoria.';
            }
            
            if (empty($datos['actividad'])) {
                $errores[] = 'La actividad es obligatoria.';
            }
        }
        
        return $errores;
    }
    
    /**
     * Guarda o actualiza la información de cámara de comercio
     */
    public function guardar($datos) {
        try {
            // Debug: log de datos recibidos
            error_log('DEBUG CamaraComercioController: Datos recibidos para guardar: ' . json_encode($datos));
            
            // Verificar si ya existe un registro para esta cédula
            $existe = $this->obtenerPorCedula($datos['id_cedula']);
            
            if ($existe) {
                // Actualizar registro existente
                $resultado = $this->actualizar($datos);
                if ($resultado) {
                    return [
                        'success' => true,
                        'message' => 'Información de cámara de comercio actualizada exitosamente.',
                        'action' => 'updated'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al actualizar la información de cámara de comercio.'
                    ];
                }
            } else {
                // Crear nuevo registro
                $resultado = $this->crear($datos);
                if ($resultado) {
                    return [
                        'success' => true,
                        'message' => 'Información de cámara de comercio guardada exitosamente.',
                        'action' => 'created'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al guardar la información de cámara de comercio.'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log('ERROR CamaraComercioController: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Crea un nuevo registro
     */
    private function crear($datos) {
        try {
            $sql = "INSERT INTO camara_comercio (
                id_cedula, tiene_camara, nombre, razon, activdad, observacion, fecha_creacion
            ) VALUES (
                :id_cedula, :tiene_camara, :nombre, :razon, :actividad, :observacion, NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            $stmt->bindParam(':id_cedula', $datos['id_cedula']);
            $stmt->bindParam(':tiene_camara', $datos['tiene_camara']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':razon', $datos['razon']);
            $stmt->bindParam(':actividad', $datos['actividad']);
            $stmt->bindParam(':observacion', $datos['observacion']);
            
            $resultado = $stmt->execute();
            
            error_log('DEBUG CamaraComercioController: Resultado crear: ' . var_export($resultado, true));
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController crear: ' . $e->getMessage());
            throw new Exception('Error al crear registro: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un registro existente
     */
    private function actualizar($datos) {
        try {
            $sql = "UPDATE camara_comercio SET 
                tiene_camara = :tiene_camara,
                nombre = :nombre,
                razon = :razon,
                activdad = :actividad,
                observacion = :observacion,
                fecha_actualizacion = NOW()
                WHERE id_cedula = :id_cedula";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            $stmt->bindParam(':id_cedula', $datos['id_cedula']);
            $stmt->bindParam(':tiene_camara', $datos['tiene_camara']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':razon', $datos['razon']);
            $stmt->bindParam(':actividad', $datos['actividad']);
            $stmt->bindParam(':observacion', $datos['observacion']);
            
            $resultado = $stmt->execute();
            
            error_log('DEBUG CamaraComercioController: Resultado actualizar: ' . var_export($resultado, true));
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController actualizar: ' . $e->getMessage());
            throw new Exception('Error al actualizar registro: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene información de cámara de comercio por cédula
     */
    public function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT * FROM camara_comercio WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController obtenerPorCedula: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene opciones para los select boxes
     */
    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'parametros' => 'opc_parametro'
            ];
            
            if (!isset($tablas[$tipo])) {
                throw new Exception("Tipo de opción no válido: $tipo");
            }
            
            $tabla = $tablas[$tipo];
            $sql = "SELECT * FROM $tabla ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController obtenerOpciones: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verifica si existe información para una cédula
     */
    public function existeInformacion($cedula) {
        try {
            $sql = "SELECT COUNT(*) FROM camara_comercio WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController existeInformacion: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina información de cámara de comercio por cédula
     */
    public function eliminar($cedula) {
        try {
            $sql = "DELETE FROM camara_comercio WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController eliminar: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de cámara de comercio
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_registros,
                        COUNT(CASE WHEN tiene_camara = 'Si' THEN 1 END) as con_camara,
                        COUNT(CASE WHEN tiene_camara = 'No' THEN 1 END) as sin_camara
                    FROM camara_comercio";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR CamaraComercioController obtenerEstadisticas: ' . $e->getMessage());
            return [];
        }
    }
} 