<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class PatrimonioController {
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
        
        // Validar si tiene patrimonio
        if (!isset($datos['tiene_patrimonio']) || empty($datos['tiene_patrimonio'])) {
            $errores[] = "Debe seleccionar si tiene patrimonio o no.";
        }
        
        // Si tiene patrimonio (valor != '1'), validar los campos específicos
        if (isset($datos['tiene_patrimonio']) && $datos['tiene_patrimonio'] != '1') {
            // Validar valor de vivienda (debe ser un número positivo)
            if (!empty($datos['valor_vivienda'])) {
                $valor_vivienda = str_replace(['$', ',', '.'], '', $datos['valor_vivienda']);
                if (!is_numeric($valor_vivienda) || $valor_vivienda < 0) {
                    $errores[] = "El valor de la vivienda debe ser un número válido mayor o igual a 0.";
                }
            }
            
            // Validar dirección (mínimo 10 caracteres si se llena)
            if (!empty($datos['direccion']) && strlen(trim($datos['direccion'])) < 10) {
                $errores[] = "La dirección debe tener al menos 10 caracteres.";
            }
            
            // Validar vehículo (mínimo 3 caracteres si se llena)
            if (!empty($datos['id_vehiculo']) && strlen(trim($datos['id_vehiculo'])) < 3) {
                $errores[] = "El vehículo debe tener al menos 3 caracteres.";
            }
            
            // Validar marca (mínimo 2 caracteres si se llena)
            if (!empty($datos['id_marca']) && strlen(trim($datos['id_marca'])) < 2) {
                $errores[] = "La marca debe tener al menos 2 caracteres.";
            }
            
            // Validar modelo (mínimo 2 caracteres si se llena)
            if (!empty($datos['id_modelo']) && strlen(trim($datos['id_modelo'])) < 2) {
                $errores[] = "El modelo debe tener al menos 2 caracteres.";
            }
            
            // Validar ahorro (debe ser un número positivo si se llena)
            if (!empty($datos['id_ahorro'])) {
                $ahorro = str_replace(['$', ',', '.'], '', $datos['id_ahorro']);
                if (!is_numeric($ahorro) || $ahorro < 0) {
                    $errores[] = "El ahorro debe ser un número válido mayor o igual a 0.";
                }
            }
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
            $tiene_patrimonio = $datos['tiene_patrimonio'];
            
            // Si no tiene patrimonio (valor = '1'), guardar con valores N/A
            if ($tiene_patrimonio == '1') {
                $valor_vivienda = 'N/A';
                $direccion = 'N/A';
                $id_vehiculo = 'N/A';
                $id_marca = 'N/A';
                $id_modelo = 'N/A';
                $id_ahorro = 'N/A';
                $otros = 'N/A';
                $observacion = !empty($datos['observacion']) ? $datos['observacion'] : 'N/A';
            } else {
                // Si tiene patrimonio, usar los datos del formulario
                $valor_vivienda = $datos['valor_vivienda'] ?? '';
                $direccion = $datos['direccion'] ?? '';
                $id_vehiculo = $datos['id_vehiculo'] ?? '';
                $id_marca = $datos['id_marca'] ?? '';
                $id_modelo = $datos['id_modelo'] ?? '';
                $id_ahorro = $datos['id_ahorro'] ?? '';
                $otros = $datos['otros'] ?? '';
                $observacion = $datos['observacion'] ?? '';
            }

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE patrimonio SET 
                        valor_vivienda = :valor_vivienda, direccion = :direccion, 
                        id_vehiculo = :id_vehiculo, id_marca = :id_marca, 
                        id_modelo = :id_modelo, id_ahorro = :id_ahorro, 
                        otros = :otros, observacion = :observacion
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':valor_vivienda', $valor_vivienda);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':id_vehiculo', $id_vehiculo);
                $stmt->bindParam(':id_marca', $id_marca);
                $stmt->bindParam(':id_modelo', $id_modelo);
                $stmt->bindParam(':id_ahorro', $id_ahorro);
                $stmt->bindParam(':otros', $otros);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO patrimonio (id_cedula, valor_vivienda, direccion, 
                        id_vehiculo, id_marca, id_modelo, id_ahorro, otros, observacion) 
                        VALUES (:id_cedula, :valor_vivienda, :direccion, :id_vehiculo, 
                        :id_marca, :id_modelo, :id_ahorro, :otros, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':valor_vivienda', $valor_vivienda);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':id_vehiculo', $id_vehiculo);
                $stmt->bindParam(':id_marca', $id_marca);
                $stmt->bindParam(':id_modelo', $id_modelo);
                $stmt->bindParam(':id_ahorro', $id_ahorro);
                $stmt->bindParam(':otros', $otros);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información de patrimonio guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información de patrimonio.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM patrimonio WHERE id_cedula = :id_cedula";
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
                'parametro' => 'opc_parametro'
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