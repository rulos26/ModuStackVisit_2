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
        
        // Verificar si se están enviando múltiples experiencias
        if (isset($datos['experiencias']) && is_array($datos['experiencias'])) {
            // Validar múltiples experiencias
            foreach ($datos['experiencias'] as $index => $experiencia) {
                $errores = array_merge($errores, $this->validarExperiencia($experiencia, $index + 1));
            }
        } else {
            // Validar experiencia única (compatibilidad con versión anterior)
            $errores = array_merge($errores, $this->validarExperiencia($datos, 1));
        }
        
        return $errores;
    }
    
    private function validarExperiencia($experiencia, $numero) {
        $errores = [];
        $prefijo = "Experiencia #{$numero}: ";
        
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
            if (!isset($experiencia[$campo]) || empty(trim($experiencia[$campo]))) {
                $errores[] = $prefijo . "El campo '$nombre' es requerido.";
            }
        }
        
        // Validar empresa (mínimo 3 caracteres)
        if (isset($experiencia['empresa']) && strlen(trim($experiencia['empresa'])) < 3) {
            $errores[] = $prefijo . "El nombre de la empresa debe tener al menos 3 caracteres.";
        }
        
        // Validar tiempo laborado (mínimo 3 caracteres)
        if (isset($experiencia['tiempo']) && strlen(trim($experiencia['tiempo'])) < 3) {
            $errores[] = $prefijo . "El tiempo laborado debe tener al menos 3 caracteres.";
        }
        
        // Validar cargo (mínimo 3 caracteres)
        if (isset($experiencia['cargo']) && strlen(trim($experiencia['cargo'])) < 3) {
            $errores[] = $prefijo . "El cargo desempeñado debe tener al menos 3 caracteres.";
        }
        
        // Validar salario (debe ser un número positivo)
        if (isset($experiencia['salario'])) {
            if (!is_numeric($experiencia['salario']) || $experiencia['salario'] < 0) {
                $errores[] = $prefijo . "El salario debe ser un número válido mayor o igual a 0.";
            }
        }
        
        // Validar motivo de retiro (mínimo 5 caracteres)
        if (isset($experiencia['retiro']) && strlen(trim($experiencia['retiro'])) < 5) {
            $errores[] = $prefijo . "El motivo de retiro debe tener al menos 5 caracteres.";
        }
        
        // Validar concepto emitido (mínimo 5 caracteres)
        if (isset($experiencia['concepto']) && strlen(trim($experiencia['concepto'])) < 5) {
            $errores[] = $prefijo . "El concepto emitido debe tener al menos 5 caracteres.";
        }
        
        // Validar nombre del contacto (mínimo 3 caracteres)
        if (isset($experiencia['nombre']) && strlen(trim($experiencia['nombre'])) < 3) {
            $errores[] = $prefijo . "El nombre del contacto debe tener al menos 3 caracteres.";
        }
        
        // Validar número de contacto (debe ser un número válido)
        if (isset($experiencia['numero'])) {
            if (!is_numeric($experiencia['numero']) || $experiencia['numero'] < 1000000) {
                $errores[] = $prefijo . "El número de contacto debe ser un número válido de al menos 7 dígitos.";
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
            
            // Verificar si se están enviando múltiples experiencias
            if (isset($datos['experiencias']) && is_array($datos['experiencias'])) {
                // Guardar múltiples experiencias
                $experiencias_guardadas = 0;
                $sql = "INSERT INTO experiencia_laboral (
                    id_cedula, empresa, tiempo, cargo, salario, retiro, concepto, nombre, numero
                ) VALUES (
                    :id_cedula, :empresa, :tiempo, :cargo, :salario, :retiro, :concepto, :nombre, :numero
                )";
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($datos['experiencias'] as $experiencia) {
                    $stmt->bindParam(':id_cedula', $id_cedula);
                    $stmt->bindParam(':empresa', $experiencia['empresa']);
                    $stmt->bindParam(':tiempo', $experiencia['tiempo']);
                    $stmt->bindParam(':cargo', $experiencia['cargo']);
                    $stmt->bindParam(':salario', $experiencia['salario']);
                    $stmt->bindParam(':retiro', $experiencia['retiro']);
                    $stmt->bindParam(':concepto', $experiencia['concepto']);
                    $stmt->bindParam(':nombre', $experiencia['nombre']);
                    $stmt->bindParam(':numero', $experiencia['numero']);
                    
                    if ($stmt->execute()) {
                        $experiencias_guardadas++;
                    }
                }
                
                if ($experiencias_guardadas > 0) {
                    return [
                        'success' => true, 
                        'message' => "Se guardaron {$experiencias_guardadas} experiencia(s) laboral(es) exitosamente."
                    ];
                } else {
                    return ['success' => false, 'message' => 'No se pudo guardar ninguna experiencia laboral.'];
                }
            } else {
                // Guardar experiencia única (compatibilidad con versión anterior)
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
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM experiencia_laboral WHERE id_cedula = :id_cedula ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Si no hay resultados, devolver null para compatibilidad
            if (empty($resultados)) {
                return null;
            }
            
            // Si solo hay un resultado, devolverlo como array asociativo para compatibilidad
            if (count($resultados) === 1) {
                return $resultados[0];
            }
            
            // Si hay múltiples resultados, devolver el array completo
            return $resultados;
        } catch (PDOException $e) {
            return null;
        }
    }
} 