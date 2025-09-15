<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class SaludController {
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
        
        // Validar estado de salud
        if (empty($datos['id_estado_salud']) || $datos['id_estado_salud'] == '0') {
            $errores[] = 'Debe seleccionar el estado de salud.';
        }
        
        // Validar tipo de enfermedad
        if (empty($datos['tipo_enfermedad']) || $datos['tipo_enfermedad'] == '0') {
            $errores[] = 'Debe seleccionar si padece algún tipo de enfermedad.';
        }
        
        // Si tiene enfermedad (valor 2), validar el campo cual
        if ($datos['tipo_enfermedad'] == '2' && empty($datos['tipo_enfermedad_cual'])) {
            $errores[] = 'Debe especificar qué tipo de enfermedad padece.';
        }
        
        // Validar limitación física
        if (empty($datos['limitacion_fisica']) || $datos['limitacion_fisica'] == '0') {
            $errores[] = 'Debe seleccionar si tiene alguna limitación física.';
        }
        
        // Si tiene limitación física (valor 2), validar el campo cual
        if ($datos['limitacion_fisica'] == '2' && empty($datos['limitacion_fisica_cual'])) {
            $errores[] = 'Debe especificar qué limitación física tiene.';
        }
        
        // Validar tipo de medicamento
        if (empty($datos['tipo_medicamento']) || $datos['tipo_medicamento'] == '0') {
            $errores[] = 'Debe seleccionar el tipo de medicamento.';
        }
        
        // Si toma medicamentos (valor 2), validar el campo cual
        if ($datos['tipo_medicamento'] == '2' && empty($datos['tipo_medicamento_cual'])) {
            $errores[] = 'Debe especificar qué medicamentos toma.';
        }
        
        // Validar ingiere alcohol
        if (empty($datos['ingiere_alcohol']) || $datos['ingiere_alcohol'] == '0') {
            $errores[] = 'Debe seleccionar si ingiere alcohol.';
        }
        
        // Si ingiere alcohol (valor 2), validar el campo cual
        if ($datos['ingiere_alcohol'] == '2' && empty($datos['ingiere_alcohol_cual'])) {
            $errores[] = 'Debe especificar qué tipo de alcohol ingiere.';
        }
        
        // Validar fuma
        if (empty($datos['fuma']) || $datos['fuma'] == '0') {
            $errores[] = 'Debe seleccionar si fuma.';
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $id_estado_salud = $datos['id_estado_salud'];
            $tipo_enfermedad = $datos['tipo_enfermedad'];
            $tipo_enfermedad_cual = $datos['tipo_enfermedad_cual'] ?? '';
            $limitacion_fisica = $datos['limitacion_fisica'];
            $limitacion_fisica_cual = $datos['limitacion_fisica_cual'] ?? '';
            $tipo_medicamento = $datos['tipo_medicamento'];
            $tipo_medicamento_cual = $datos['tipo_medicamento_cual'] ?? '';
            $ingiere_alcohol = $datos['ingiere_alcohol'];
            $ingiere_alcohol_cual = $datos['ingiere_alcohol_cual'] ?? '';
            $fuma = $datos['fuma'];
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                // Determinar en qué tabla actualizar basado en dónde se encontró el registro
                $tabla = isset($existe['id_estado_salud']) ? 'estados_salud' : 'salud';
                
                $sql = "UPDATE $tabla SET 
                        id_estado_salud = :id_estado_salud, 
                        tipo_enfermedad = :tipo_enfermedad, 
                        tipo_enfermedad_cual = :tipo_enfermedad_cual, 
                        limitacion_fisica = :limitacion_fisica, 
                        limitacion_fisica_cual = :limitacion_fisica_cual, 
                        tipo_medicamento = :tipo_medicamento, 
                        tipo_medicamento_cual = :tipo_medicamento_cual, 
                        ingiere_alcohol = :ingiere_alcohol, 
                        ingiere_alcohol_cual = :ingiere_alcohol_cual, 
                        fuma = :fuma, 
                        observacion = :observacion 
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_estado_salud', $id_estado_salud);
                $stmt->bindParam(':tipo_enfermedad', $tipo_enfermedad);
                $stmt->bindParam(':tipo_enfermedad_cual', $tipo_enfermedad_cual);
                $stmt->bindParam(':limitacion_fisica', $limitacion_fisica);
                $stmt->bindParam(':limitacion_fisica_cual', $limitacion_fisica_cual);
                $stmt->bindParam(':tipo_medicamento', $tipo_medicamento);
                $stmt->bindParam(':tipo_medicamento_cual', $tipo_medicamento_cual);
                $stmt->bindParam(':ingiere_alcohol', $ingiere_alcohol);
                $stmt->bindParam(':ingiere_alcohol_cual', $ingiere_alcohol_cual);
                $stmt->bindParam(':fuma', $fuma);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
                if ($ok) {
                    return ['success'=>true, 'message'=>'Información de salud actualizada exitosamente.'];
                } else {
                    return ['success'=>false, 'message'=>'Error al actualizar la información de salud.'];
                }
            } else {
                // Intentar INSERT en estados_salud primero
                try {
                    $sql = "INSERT INTO estados_salud (id_cedula, id_estado_salud, tipo_enfermedad, tipo_enfermedad_cual, limitacion_fisica, limitacion_fisica_cual, tipo_medicamento, tipo_medicamento_cual, ingiere_alcohol, ingiere_alcohol_cual, fuma, observacion) 
                            VALUES (:id_cedula, :id_estado_salud, :tipo_enfermedad, :tipo_enfermedad_cual, :limitacion_fisica, :limitacion_fisica_cual, :tipo_medicamento, :tipo_medicamento_cual, :ingiere_alcohol, :ingiere_alcohol_cual, :fuma, :observacion)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id_cedula', $id_cedula);
                    $stmt->bindParam(':id_estado_salud', $id_estado_salud);
                    $stmt->bindParam(':tipo_enfermedad', $tipo_enfermedad);
                    $stmt->bindParam(':tipo_enfermedad_cual', $tipo_enfermedad_cual);
                    $stmt->bindParam(':limitacion_fisica', $limitacion_fisica);
                    $stmt->bindParam(':limitacion_fisica_cual', $limitacion_fisica_cual);
                    $stmt->bindParam(':tipo_medicamento', $tipo_medicamento);
                    $stmt->bindParam(':tipo_medicamento_cual', $tipo_medicamento_cual);
                    $stmt->bindParam(':ingiere_alcohol', $ingiere_alcohol);
                    $stmt->bindParam(':ingiere_alcohol_cual', $ingiere_alcohol_cual);
                    $stmt->bindParam(':fuma', $fuma);
                    $stmt->bindParam(':observacion', $observacion);
                    $ok = $stmt->execute();
                    if ($ok) {
                        return ['success'=>true, 'message'=>'Información de salud guardada exitosamente.'];
                    }
                } catch (PDOException $e) {
                    // Si falla en estados_salud, intentar en salud
                    error_log("Error en INSERT estados_salud: " . $e->getMessage());
                }
                
                // Intentar INSERT en tabla salud como respaldo
                $sql = "INSERT INTO salud (id_cedula, id_estado_salud, tipo_enfermedad, tipo_enfermedad_cual, limitacion_fisica, limitacion_fisica_cual, tipo_medicamento, tipo_medicamento_cual, ingiere_alcohol, ingiere_alcohol_cual, fuma, observacion) 
                        VALUES (:id_cedula, :id_estado_salud, :tipo_enfermedad, :tipo_enfermedad_cual, :limitacion_fisica, :limitacion_fisica_cual, :tipo_medicamento, :tipo_medicamento_cual, :ingiere_alcohol, :ingiere_alcohol_cual, :fuma, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':id_estado_salud', $id_estado_salud);
                $stmt->bindParam(':tipo_enfermedad', $tipo_enfermedad);
                $stmt->bindParam(':tipo_enfermedad_cual', $tipo_enfermedad_cual);
                $stmt->bindParam(':limitacion_fisica', $limitacion_fisica);
                $stmt->bindParam(':limitacion_fisica_cual', $limitacion_fisica_cual);
                $stmt->bindParam(':tipo_medicamento', $tipo_medicamento);
                $stmt->bindParam(':tipo_medicamento_cual', $tipo_medicamento_cual);
                $stmt->bindParam(':ingiere_alcohol', $ingiere_alcohol);
                $stmt->bindParam(':ingiere_alcohol_cual', $ingiere_alcohol_cual);
                $stmt->bindParam(':fuma', $fuma);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
                if ($ok) {
                    return ['success'=>true, 'message'=>'Información de salud guardada exitosamente.'];
                } else {
                    return ['success'=>false, 'message'=>'Error al guardar la información de salud.'];
                }
            }
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            // Intentar primero en la tabla estados_salud
            $sql = "SELECT * FROM estados_salud WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Si no se encuentra, intentar en la tabla salud
            if (!$resultado) {
                $sql = "SELECT * FROM salud WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->execute();
                $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en obtenerPorCedula: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'estados' => 'opc_estados',
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