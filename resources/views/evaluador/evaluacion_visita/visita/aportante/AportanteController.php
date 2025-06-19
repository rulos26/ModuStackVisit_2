<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class AportanteController {
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
            if (is_array($valor)) {
                $sanitizados[$clave] = [];
                foreach ($valor as $index => $item) {
                    if (is_string($item)) {
                        $sanitizados[$clave][$index] = trim(strip_tags($item));
                    } else {
                        $sanitizados[$clave][$index] = $item;
                    }
                }
            } else {
                if (is_string($valor)) {
                    $sanitizados[$clave] = trim(strip_tags($valor));
                } else {
                    $sanitizados[$clave] = $valor;
                }
            }
        }
        return $sanitizados;
    }

    public function validarDatos($datos) {
        $errores = [];
        
        // Verificar que se recibieron los arrays necesarios
        if (!isset($datos['nombre']) || !is_array($datos['nombre'])) {
            $errores[] = "Debe proporcionar al menos un nombre de aportante.";
            return $errores;
        }
        
        if (!isset($datos['valor']) || !is_array($datos['valor'])) {
            $errores[] = "Debe proporcionar al menos un valor de aporte.";
            return $errores;
        }
        
        // Verificar que todos los arrays tengan la misma longitud
        $longitud = count($datos['nombre']);
        if (count($datos['valor']) !== $longitud) {
            $errores[] = "Todos los campos deben tener la misma cantidad de registros.";
            return $errores;
        }
        
        // Validar cada conjunto de datos
        for ($i = 0; $i < $longitud; $i++) {
            $numero_registro = $i + 1;
            
            // Validar nombre (mínimo 3 caracteres, máximo 100)
            if (empty($datos['nombre'][$i]) || strlen(trim($datos['nombre'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: El nombre debe tener al menos 3 caracteres.";
            } elseif (strlen(trim($datos['nombre'][$i])) > 100) {
                $errores[] = "Registro $numero_registro: El nombre no puede exceder 100 caracteres.";
            }
            
            // Validar valor (debe ser un número positivo)
            $valor = str_replace(['$', ',', '.'], '', $datos['valor'][$i]);
            if (empty($datos['valor'][$i]) || !is_numeric($valor) || $valor < 0) {
                $errores[] = "Registro $numero_registro: El valor debe ser un número válido mayor o igual a 0.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM aportante WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar los nuevos registros
            $sql = "INSERT INTO aportante (id_cedula, nombre, valor) VALUES (:id_cedula, :nombre, :valor)";
            $stmt = $this->db->prepare($sql);
            
            $registros_insertados = 0;
            $longitud = count($datos['nombre']);
            
            for ($i = 0; $i < $longitud; $i++) {
                $nombre = $datos['nombre'][$i];
                $valor = str_replace(['$', ',', '.'], '', $datos['valor'][$i]);
                
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':valor', $valor);
                
                if ($stmt->execute()) {
                    $registros_insertados++;
                }
            }
            
            if ($registros_insertados > 0) {
                return [
                    'success' => true, 
                    'message' => "Se guardaron exitosamente $registros_insertados aportante(s)."
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar ningún aportante.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM aportante WHERE id_cedula = :id_cedula ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
} 