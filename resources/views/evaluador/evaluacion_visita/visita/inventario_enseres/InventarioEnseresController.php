<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class InventarioEnseresController {
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
        
        // Validar campos de enseres (todos son opcionales pero deben ser válidos si se llenan)
        $campos_enseres = [
            'televisor_cant', 'dvd_cant', 'teatro_casa_cant', 'equipo_sonido_cant',
            'computador_cant', 'impresora_cant', 'movil_cant', 'estufa_cant',
            'nevera_cant', 'lavadora_cant', 'microondas_cant', 'moto_cant', 'carro_cant'
        ];
        
        foreach ($campos_enseres as $campo) {
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
            $televisor_cant = $datos['televisor_cant'] ?? null;
            $dvd_cant = $datos['dvd_cant'] ?? null;
            $teatro_casa_cant = $datos['teatro_casa_cant'] ?? null;
            $equipo_sonido_cant = $datos['equipo_sonido_cant'] ?? null;
            $computador_cant = $datos['computador_cant'] ?? null;
            $impresora_cant = $datos['impresora_cant'] ?? null;
            $movil_cant = $datos['movil_cant'] ?? null;
            $estufa_cant = $datos['estufa_cant'] ?? null;
            $nevera_cant = $datos['nevera_cant'] ?? null;
            $lavadora_cant = $datos['lavadora_cant'] ?? null;
            $microondas_cant = $datos['microondas_cant'] ?? null;
            $moto_cant = $datos['moto_cant'] ?? null;
            $carro_cant = $datos['carro_cant'] ?? null;
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE inventario_enseres SET 
                        televisor_cant = :televisor_cant, dvd_cant = :dvd_cant, 
                        teatro_casa_cant = :teatro_casa_cant, equipo_sonido_cant = :equipo_sonido_cant,
                        computador_cant = :computador_cant, impresora_cant = :impresora_cant,
                        movil_cant = :movil_cant, estufa_cant = :estufa_cant,
                        nevera_cant = :nevera_cant, lavadora_cant = :lavadora_cant,
                        microondas_cant = :microondas_cant, moto_cant = :moto_cant,
                        carro_cant = :carro_cant, observacion = :observacion
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':televisor_cant', $televisor_cant);
                $stmt->bindParam(':dvd_cant', $dvd_cant);
                $stmt->bindParam(':teatro_casa_cant', $teatro_casa_cant);
                $stmt->bindParam(':equipo_sonido_cant', $equipo_sonido_cant);
                $stmt->bindParam(':computador_cant', $computador_cant);
                $stmt->bindParam(':impresora_cant', $impresora_cant);
                $stmt->bindParam(':movil_cant', $movil_cant);
                $stmt->bindParam(':estufa_cant', $estufa_cant);
                $stmt->bindParam(':nevera_cant', $nevera_cant);
                $stmt->bindParam(':lavadora_cant', $lavadora_cant);
                $stmt->bindParam(':microondas_cant', $microondas_cant);
                $stmt->bindParam(':moto_cant', $moto_cant);
                $stmt->bindParam(':carro_cant', $carro_cant);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO inventario_enseres (id_cedula, televisor_cant, dvd_cant, teatro_casa_cant, 
                        equipo_sonido_cant, computador_cant, impresora_cant, movil_cant, estufa_cant,
                        nevera_cant, lavadora_cant, microondas_cant, moto_cant, carro_cant, observacion) 
                        VALUES (:id_cedula, :televisor_cant, :dvd_cant, :teatro_casa_cant, :equipo_sonido_cant,
                        :computador_cant, :impresora_cant, :movil_cant, :estufa_cant, :nevera_cant,
                        :lavadora_cant, :microondas_cant, :moto_cant, :carro_cant, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':televisor_cant', $televisor_cant);
                $stmt->bindParam(':dvd_cant', $dvd_cant);
                $stmt->bindParam(':teatro_casa_cant', $teatro_casa_cant);
                $stmt->bindParam(':equipo_sonido_cant', $equipo_sonido_cant);
                $stmt->bindParam(':computador_cant', $computador_cant);
                $stmt->bindParam(':impresora_cant', $impresora_cant);
                $stmt->bindParam(':movil_cant', $movil_cant);
                $stmt->bindParam(':estufa_cant', $estufa_cant);
                $stmt->bindParam(':nevera_cant', $nevera_cant);
                $stmt->bindParam(':lavadora_cant', $lavadora_cant);
                $stmt->bindParam(':microondas_cant', $microondas_cant);
                $stmt->bindParam(':moto_cant', $moto_cant);
                $stmt->bindParam(':carro_cant', $carro_cant);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información del inventario de enseres guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información del inventario de enseres.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM inventario_enseres WHERE id_cedula = :id_cedula";
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