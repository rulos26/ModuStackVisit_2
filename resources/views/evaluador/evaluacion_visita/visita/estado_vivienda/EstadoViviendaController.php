<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class EstadoViviendaController {
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

    public function sanitizarDatos($datos) {
        $sanitizados = [];
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $sanitizados[$clave] = trim(strip_tags($valor));
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        return $sanitizados;
    }

    public function validarDatos($datos) {
        $errores = [];
        
        // Validar estado de la vivienda
        if (empty($datos['id_estado']) || $datos['id_estado'] == '0') {
            $errores[] = 'Debe seleccionar el estado de la vivienda.';
        }
        
        // Validar observación (opcional pero si se llena debe tener mínimo 10 caracteres)
        if (!empty($datos['observacion']) && strlen(trim($datos['observacion'])) < 10) {
            $errores[] = 'La observación debe tener al menos 10 caracteres.';
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $id_estado = $datos['id_estado'];
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE estado_vivienda SET 
                        id_estado = :id_estado, observacion = :observacion 
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_estado', $id_estado);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO estado_vivienda (id_cedula, id_estado, observacion) 
                        VALUES (:id_cedula, :id_estado, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':id_estado', $id_estado);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información del estado de vivienda guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información del estado de vivienda.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM estado_vivienda WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'estados' => 'opc_estados'
            ];
            
            if (!isset($tablas[$tipo])) {
                throw new Exception("Tipo de opción no válido: $tipo");
            }
            
            $tabla = $tablas[$tipo];
            $sql = "SELECT id, nombre FROM $tabla ORDER BY nombre";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
} 