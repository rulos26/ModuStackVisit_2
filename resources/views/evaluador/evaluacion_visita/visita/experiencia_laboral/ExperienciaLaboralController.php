<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class ExperienciaLaboralController {
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
        $campos_requeridos = [
            'empresa' => 'Empresa',
            'tiempo' => 'Tiempo Laborado',
            'cargo' => 'Cargo Desempeñado',
            'salario' => 'Salario',
            'retiro' => 'Motivo de Retiro',
            'concepto' => 'Concepto Emitido',
            'nombre' => 'Nombre del Contacto',
            'numero' => 'Número de Contacto'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                $errores[] = "El campo '$nombre' es requerido.";
            }
        }
        
        // Validar empresa (mínimo 3 caracteres)
        if (isset($datos['empresa']) && strlen(trim($datos['empresa'])) < 3) {
            $errores[] = "El nombre de la empresa debe tener al menos 3 caracteres.";
        }
        
        // Validar tiempo laborado (mínimo 3 caracteres)
        if (isset($datos['tiempo']) && strlen(trim($datos['tiempo'])) < 3) {
            $errores[] = "El tiempo laborado debe tener al menos 3 caracteres.";
        }
        
        // Validar cargo (mínimo 3 caracteres)
        if (isset($datos['cargo']) && strlen(trim($datos['cargo'])) < 3) {
            $errores[] = "El cargo desempeñado debe tener al menos 3 caracteres.";
        }
        
        // Validar salario (debe ser un número positivo)
        if (isset($datos['salario'])) {
            if (!is_numeric($datos['salario']) || $datos['salario'] < 0) {
                $errores[] = "El salario debe ser un número válido mayor o igual a 0.";
            }
        }
        
        // Validar motivo de retiro (mínimo 5 caracteres)
        if (isset($datos['retiro']) && strlen(trim($datos['retiro'])) < 5) {
            $errores[] = "El motivo de retiro debe tener al menos 5 caracteres.";
        }
        
        // Validar concepto emitido (mínimo 5 caracteres)
        if (isset($datos['concepto']) && strlen(trim($datos['concepto'])) < 5) {
            $errores[] = "El concepto emitido debe tener al menos 5 caracteres.";
        }
        
        // Validar nombre del contacto (mínimo 3 caracteres)
        if (isset($datos['nombre']) && strlen(trim($datos['nombre'])) < 3) {
            $errores[] = "El nombre del contacto debe tener al menos 3 caracteres.";
        }
        
        // Validar número de contacto (debe ser un número válido)
        if (isset($datos['numero'])) {
            if (!is_numeric($datos['numero']) || $datos['numero'] < 1000000) {
                $errores[] = "El número de contacto debe ser un número válido de al menos 7 dígitos.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM experiencia_laboral WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO experiencia_laboral (
                id_cedula, empresa, tiempo, cargo, salario, retiro, concepto, nombre, numero
            ) VALUES (
                :id_cedula, :empresa, :tiempo, :cargo, :salario, :retiro, :concepto, :nombre, :numero
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':empresa', $datos['empresa']);
            $stmt->bindParam(':tiempo', $datos['tiempo']);
            $stmt->bindParam(':cargo', $datos['cargo']);
            $stmt->bindParam(':salario', $datos['salario']);
            $stmt->bindParam(':retiro', $datos['retiro']);
            $stmt->bindParam(':concepto', $datos['concepto']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':numero', $datos['numero']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Experiencia laboral guardada exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar la experiencia laboral.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM experiencia_laboral WHERE id_cedula = :id_cedula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
} 