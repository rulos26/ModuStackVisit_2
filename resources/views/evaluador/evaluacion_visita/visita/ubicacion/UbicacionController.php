<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class UbicacionController {
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
        
        // Validar campos requeridos
        if (!isset($datos['latituds']) || empty($datos['latituds'])) {
            $errores[] = "La latitud es requerida.";
        }
        
        if (!isset($datos['longituds']) || empty($datos['longituds'])) {
            $errores[] = "La longitud es requerida.";
        }
        
        // Validar que sean números válidos
        if (isset($datos['latituds']) && !is_numeric($datos['latituds'])) {
            $errores[] = "La latitud debe ser un número válido.";
        }
        
        if (isset($datos['longituds']) && !is_numeric($datos['longituds'])) {
            $errores[] = "La longitud debe ser un número válido.";
        }
        
        // Validar rangos de coordenadas (aproximadamente Colombia)
        if (isset($datos['latituds']) && is_numeric($datos['latituds'])) {
            $latitud = floatval($datos['latituds']);
            if ($latitud < -4.5 || $latitud > 13.5) {
                $errores[] = "La latitud debe estar en el rango válido para Colombia (-4.5 a 13.5).";
            }
        }
        
        if (isset($datos['longituds']) && is_numeric($datos['longituds'])) {
            $longitud = floatval($datos['longituds']);
            if ($longitud < -79.5 || $longitud > -66.5) {
                $errores[] = "La longitud debe estar en el rango válido para Colombia (-79.5 a -66.5).";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'] ?? $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;
            
            if (!$id_cedula) {
                return ['success' => false, 'message' => 'No hay sesión activa o cédula no encontrada.'];
            }
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM ubicacion WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO ubicacion (id_cedula, latitud, longitud, fecha_registro) 
                    VALUES (:id_cedula, :latitud, :longitud, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':latitud', $datos['latituds']);
            $stmt->bindParam(':longitud', $datos['longituds']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Ubicación guardada exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar la ubicación.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM ubicacion WHERE id_cedula = :id_cedula ORDER BY fecha_registro DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function guardarUbicacion($id_cedula, $latitud, $longitud) {
        try {
            // Validar datos
            if (empty($latitud) || empty($longitud)) {
                return ['success' => false, 'message' => 'Latitud y longitud son requeridas.'];
            }
            
            if (!is_numeric($latitud) || !is_numeric($longitud)) {
                return ['success' => false, 'message' => 'Latitud y longitud deben ser números válidos.'];
            }
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM ubicacion WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO ubicacion (id_cedula, latitud, longitud, fecha_registro) 
                    VALUES (:id_cedula, :latitud, :longitud, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':latitud', $latitud);
            $stmt->bindParam(':longitud', $longitud);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Ubicación guardada exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar la ubicación.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
} 