<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class CuentasBancariasController {
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
        if (!isset($datos['id_entidad']) || !is_array($datos['id_entidad'])) {
            $errores[] = "Debe proporcionar al menos una entidad bancaria.";
            return $errores;
        }
        
        if (!isset($datos['id_tipo_cuenta']) || !is_array($datos['id_tipo_cuenta'])) {
            $errores[] = "Debe proporcionar al menos un tipo de cuenta.";
            return $errores;
        }
        
        if (!isset($datos['id_ciudad']) || !is_array($datos['id_ciudad'])) {
            $errores[] = "Debe seleccionar al menos una ciudad.";
            return $errores;
        }
        
        // Verificar que todos los arrays tengan la misma longitud
        $longitud = count($datos['id_entidad']);
        if (count($datos['id_tipo_cuenta']) !== $longitud || count($datos['id_ciudad']) !== $longitud) {
            $errores[] = "Todos los campos deben tener la misma cantidad de registros.";
            return $errores;
        }
        
        // Validar cada conjunto de datos
        for ($i = 0; $i < $longitud; $i++) {
            $numero_registro = $i + 1;
            
            // Validar entidad (mínimo 3 caracteres)
            if (empty($datos['id_entidad'][$i]) || strlen(trim($datos['id_entidad'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: La entidad debe tener al menos 3 caracteres.";
            }
            
            // Validar tipo de cuenta (mínimo 3 caracteres)
            if (empty($datos['id_tipo_cuenta'][$i]) || strlen(trim($datos['id_tipo_cuenta'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: El tipo de cuenta debe tener al menos 3 caracteres.";
            }
            
            // Validar ciudad (debe ser un número válido)
            if (empty($datos['id_ciudad'][$i]) || !is_numeric($datos['id_ciudad'][$i]) || $datos['id_ciudad'][$i] < 1) {
                $errores[] = "Registro $numero_registro: Debe seleccionar una ciudad válida.";
            }
            
            // Validar observaciones (opcional pero si se llena debe tener mínimo 10 caracteres)
            if (isset($datos['observaciones'][$i]) && !empty($datos['observaciones'][$i])) {
                if (strlen(trim($datos['observaciones'][$i])) < 10) {
                    $errores[] = "Registro $numero_registro: Las observaciones deben tener al menos 10 caracteres.";
                }
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM cuentas_bancarias WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar los nuevos registros
            $sql = "INSERT INTO cuentas_bancarias (id_cedula, id_entidad, id_tipo_cuenta, id_ciudad, observaciones) 
                    VALUES (:id_cedula, :id_entidad, :id_tipo_cuenta, :id_ciudad, :observaciones)";
            $stmt = $this->db->prepare($sql);
            
            $registros_insertados = 0;
            $longitud = count($datos['id_entidad']);
            
            for ($i = 0; $i < $longitud; $i++) {
                $id_entidad = $datos['id_entidad'][$i];
                $id_tipo_cuenta = $datos['id_tipo_cuenta'][$i];
                $id_ciudad = $datos['id_ciudad'][$i];
                $observaciones = isset($datos['observaciones'][$i]) ? $datos['observaciones'][$i] : '';
                
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':id_entidad', $id_entidad);
                $stmt->bindParam(':id_tipo_cuenta', $id_tipo_cuenta);
                $stmt->bindParam(':id_ciudad', $id_ciudad);
                $stmt->bindParam(':observaciones', $observaciones);
                
                if ($stmt->execute()) {
                    $registros_insertados++;
                }
            }
            
            if ($registros_insertados > 0) {
                return [
                    'success' => true, 
                    'message' => "Se guardaron exitosamente $registros_insertados cuenta(s) bancaria(s)."
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar ninguna cuenta bancaria.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM cuentas_bancarias WHERE id_cedula = :id_cedula ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerMunicipios() {
        try {
            $sql = "SELECT id_municipio, municipio FROM municipios ORDER BY municipio";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
} 