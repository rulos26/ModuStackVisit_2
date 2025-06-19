<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class ServiciosPublicosController {
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
        
        // Validar campos de servicios públicos (todos son opcionales pero deben ser válidos si se llenan)
        $campos_servicios = [
            'agua', 'luz', 'gas', 'telefono', 'alcantarillado', 
            'internet', 'administracion', 'parqueadero'
        ];
        
        foreach ($campos_servicios as $campo) {
            if (!empty($datos[$campo]) && (!is_numeric($datos[$campo]) || $datos[$campo] < 0)) {
                $errores[] = "El campo " . str_replace('_', ' ', $campo) . " debe ser un número válido.";
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
            $agua = $datos['agua'] ?? null;
            $luz = $datos['luz'] ?? null;
            $gas = $datos['gas'] ?? null;
            $telefono = $datos['telefono'] ?? null;
            $alcantarillado = $datos['alcantarillado'] ?? null;
            $internet = $datos['internet'] ?? null;
            $administracion = $datos['administracion'] ?? null;
            $parqueadero = $datos['parqueadero'] ?? null;
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE servicios_publicos SET 
                        agua = :agua, luz = :luz, gas = :gas, telefono = :telefono,
                        alcantarillado = :alcantarillado, internet = :internet,
                        administracion = :administracion, parqueadero = :parqueadero,
                        observacion = :observacion
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':agua', $agua);
                $stmt->bindParam(':luz', $luz);
                $stmt->bindParam(':gas', $gas);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':alcantarillado', $alcantarillado);
                $stmt->bindParam(':internet', $internet);
                $stmt->bindParam(':administracion', $administracion);
                $stmt->bindParam(':parqueadero', $parqueadero);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO servicios_publicos (id_cedula, agua, luz, gas, telefono, 
                        alcantarillado, internet, administracion, parqueadero, observacion) 
                        VALUES (:id_cedula, :agua, :luz, :gas, :telefono, :alcantarillado,
                        :internet, :administracion, :parqueadero, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':agua', $agua);
                $stmt->bindParam(':luz', $luz);
                $stmt->bindParam(':gas', $gas);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':alcantarillado', $alcantarillado);
                $stmt->bindParam(':internet', $internet);
                $stmt->bindParam(':administracion', $administracion);
                $stmt->bindParam(':parqueadero', $parqueadero);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información de servicios públicos guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información de servicios públicos.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM servicios_publicos WHERE id_cedula = :id_cedula";
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