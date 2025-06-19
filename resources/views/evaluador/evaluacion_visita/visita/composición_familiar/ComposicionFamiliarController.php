<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class ComposicionFamiliarController {
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
                $sanitizados[$clave] = array_map(function($item) {
                    return is_string($item) ? trim(strip_tags($item)) : $item;
                }, $valor);
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
        
        // Validar que haya al menos un miembro de familia
        if (empty($datos['nombre']) || !is_array($datos['nombre'])) {
            $errores[] = 'Debe agregar al menos un miembro de la familia.';
            return $errores;
        }
        
        $nombres = $datos['nombre'];
        $parentescos = $datos['id_parentesco'] ?? [];
        $edades = $datos['edad'] ?? [];
        $ocupaciones = $datos['id_ocupacion'] ?? [];
        $telefonos = $datos['telefono'] ?? [];
        $conviven = $datos['id_conviven'] ?? [];
        $observaciones = $datos['observacion'] ?? [];
        
        // Validar cada miembro de la familia
        for ($i = 0; $i < count($nombres); $i++) {
            $num = $i + 1;
            
            // Validar nombre
            if (empty($nombres[$i])) {
                $errores[] = "El nombre del miembro $num es obligatorio.";
            } elseif (strlen($nombres[$i]) < 2) {
                $errores[] = "El nombre del miembro $num debe tener al menos 2 caracteres.";
            }
            
            // Validar parentesco
            if (empty($parentescos[$i]) || $parentescos[$i] == '0') {
                $errores[] = "Debe seleccionar el parentesco del miembro $num.";
            }
            
            // Validar edad
            if (empty($edades[$i])) {
                $errores[] = "La edad del miembro $num es obligatoria.";
            } elseif (!is_numeric($edades[$i]) || $edades[$i] < 0 || $edades[$i] > 120) {
                $errores[] = "La edad del miembro $num debe estar entre 0 y 120 años.";
            }
            
            // Validar ocupación (opcional)
            if (!empty($ocupaciones[$i]) && $ocupaciones[$i] == '0') {
                $errores[] = "Debe seleccionar una ocupación válida para el miembro $num o dejarlo vacío.";
            }
            
            // Validar teléfono
            if (empty($telefonos[$i])) {
                $errores[] = "El teléfono del miembro $num es obligatorio.";
            } elseif (!preg_match('/^[0-9]{7,10}$/', $telefonos[$i])) {
                $errores[] = "El teléfono del miembro $num debe tener entre 7 y 10 dígitos.";
            }
            
            // Validar conviven
            if (empty($conviven[$i]) || $conviven[$i] == '0') {
                $errores[] = "Debe seleccionar si convive el miembro $num.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $this->eliminarPorCedula($id_cedula);
            
            $nombres = $datos['nombre'];
            $parentescos = $datos['id_parentesco'] ?? [];
            $edades = $datos['edad'] ?? [];
            $ocupaciones = $datos['id_ocupacion'] ?? [];
            $telefonos = $datos['telefono'] ?? [];
            $conviven = $datos['id_conviven'] ?? [];
            $observaciones = $datos['observacion'] ?? [];
            
            $registros_exitosos = 0;
            
            // Insertar cada miembro de la familia
            for ($i = 0; $i < count($nombres); $i++) {
                if (!empty($nombres[$i])) {
                    $sql = "INSERT INTO composicion_familiar (id_cedula, nombre, id_parentesco, edad, id_ocupacion, telefono, id_conviven, observacion) 
                            VALUES (:id_cedula, :nombre, :id_parentesco, :edad, :id_ocupacion, :telefono, :id_conviven, :observacion)";
                    
                    $stmt = $this->db->prepare($sql);
                    
                    // Crear variables temporales para bindParam
                    $nombre = $nombres[$i];
                    $parentesco = $parentescos[$i];
                    $edad = $edades[$i];
                    $ocupacion = !empty($ocupaciones[$i]) ? $ocupaciones[$i] : null;
                    $telefono = $telefonos[$i];
                    $conviven_val = $conviven[$i];
                    $observacion = $observaciones[$i] ?? '';
                    
                    $stmt->bindParam(':id_cedula', $id_cedula);
                    $stmt->bindParam(':nombre', $nombre);
                    $stmt->bindParam(':id_parentesco', $parentesco);
                    $stmt->bindParam(':edad', $edad);
                    $stmt->bindParam(':id_ocupacion', $ocupacion);
                    $stmt->bindParam(':telefono', $telefono);
                    $stmt->bindParam(':id_conviven', $conviven_val);
                    $stmt->bindParam(':observacion', $observacion);
                    
                    if ($stmt->execute()) {
                        $registros_exitosos++;
                    }
                }
            }
            
            if ($registros_exitosos > 0) {
                return [
                    'success' => true, 
                    'message' => "Información de composición familiar guardada exitosamente. Se registraron $registros_exitosos miembros."
                ];
            } else {
                return [
                    'success' => false, 
                    'message' => 'No se pudo guardar ningún registro de composición familiar.'
                ];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM composicion_familiar WHERE id_cedula = :id_cedula ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function eliminarPorCedula($id_cedula) {
        try {
            $sql = "DELETE FROM composicion_familiar WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'parentesco' => 'opc_parentesco',
                'ocupacion' => 'opc_ocupacion',
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