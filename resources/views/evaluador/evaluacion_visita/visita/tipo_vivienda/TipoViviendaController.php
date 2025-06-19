<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class TipoViviendaController {
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
        
        // Validar tipo de vivienda
        if (empty($datos['id_tipo_vivienda']) || $datos['id_tipo_vivienda'] == '0') {
            $errores[] = 'Debe seleccionar el tipo de vivienda.';
        }
        
        // Validar sector
        if (empty($datos['id_sector']) || $datos['id_sector'] == '0') {
            $errores[] = 'Debe seleccionar el sector.';
        }
        
        // Validar propietario
        if (empty($datos['id_propietario']) || $datos['id_propietario'] == '0') {
            $errores[] = 'Debe seleccionar el propietario.';
        }
        
        // Validar número de familia
        if (empty($datos['numero_de_familia']) || !is_numeric($datos['numero_de_familia']) || $datos['numero_de_familia'] < 1 || $datos['numero_de_familia'] > 50) {
            $errores[] = 'El número de hogares debe estar entre 1 y 50.';
        }
        
        // Validar personas núcleo familiar
        if (empty($datos['personas_nucleo_familiar']) || !is_numeric($datos['personas_nucleo_familiar']) || $datos['personas_nucleo_familiar'] < 1 || $datos['personas_nucleo_familiar'] > 100) {
            $errores[] = 'El número de personas del núcleo familiar debe estar entre 1 y 100.';
        }
        
        // Validar tiempo en el sector
        if (empty($datos['tiempo_sector'])) {
            $errores[] = 'Debe seleccionar el tiempo de residencia en el sector.';
        } else {
            $fecha_actual = date('Y-m-d');
            if ($datos['tiempo_sector'] > $fecha_actual) {
                $errores[] = 'La fecha de residencia no puede ser futura.';
            }
        }
        
        // Validar número de pisos
        if (empty($datos['numero_de_pisos']) || !is_numeric($datos['numero_de_pisos']) || $datos['numero_de_pisos'] < 1 || $datos['numero_de_pisos'] > 50) {
            $errores[] = 'El número de pisos debe estar entre 1 y 50.';
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
            $id_tipo_vivienda = $datos['id_tipo_vivienda'];
            $id_sector = $datos['id_sector'];
            $id_propietario = $datos['id_propietario'];
            $numero_de_familia = $datos['numero_de_familia'];
            $personas_nucleo_familiar = $datos['personas_nucleo_familiar'];
            $tiempo_sector = $datos['tiempo_sector'];
            $numero_de_pisos = $datos['numero_de_pisos'];
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE tipo_vivienda SET 
                        id_tipo_vivienda = :id_tipo_vivienda, id_sector = :id_sector, 
                        id_propietario = :id_propietario, numero_de_familia = :numero_de_familia, 
                        personas_nucleo_familiar = :personas_nucleo_familiar, tiempo_sector = :tiempo_sector, 
                        numero_de_pisos = :numero_de_pisos, observacion = :observacion 
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_tipo_vivienda', $id_tipo_vivienda);
                $stmt->bindParam(':id_sector', $id_sector);
                $stmt->bindParam(':id_propietario', $id_propietario);
                $stmt->bindParam(':numero_de_familia', $numero_de_familia);
                $stmt->bindParam(':personas_nucleo_familiar', $personas_nucleo_familiar);
                $stmt->bindParam(':tiempo_sector', $tiempo_sector);
                $stmt->bindParam(':numero_de_pisos', $numero_de_pisos);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO tipo_vivienda (id_cedula, id_tipo_vivienda, id_sector, id_propietario, numero_de_familia, personas_nucleo_familiar, tiempo_sector, numero_de_pisos, observacion) 
                        VALUES (:id_cedula, :id_tipo_vivienda, :id_sector, :id_propietario, :numero_de_familia, :personas_nucleo_familiar, :tiempo_sector, :numero_de_pisos, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':id_tipo_vivienda', $id_tipo_vivienda);
                $stmt->bindParam(':id_sector', $id_sector);
                $stmt->bindParam(':id_propietario', $id_propietario);
                $stmt->bindParam(':numero_de_familia', $numero_de_familia);
                $stmt->bindParam(':personas_nucleo_familiar', $personas_nucleo_familiar);
                $stmt->bindParam(':tiempo_sector', $tiempo_sector);
                $stmt->bindParam(':numero_de_pisos', $numero_de_pisos);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información de tipo de vivienda guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información de tipo de vivienda.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM tipo_vivienda WHERE id_cedula = :id_cedula";
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
                'tipo_vivienda' => 'opc_tipo_vivienda',
                'sector' => 'opc_sector',
                'propiedad' => 'opc_propiedad'
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