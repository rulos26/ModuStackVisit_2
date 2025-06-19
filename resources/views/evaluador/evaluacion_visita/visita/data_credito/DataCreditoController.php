<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class DataCreditoController {
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
        if (!isset($datos['entidad']) || !is_array($datos['entidad'])) {
            $errores[] = "Debe proporcionar al menos una entidad.";
            return $errores;
        }
        
        if (!isset($datos['cuotas']) || !is_array($datos['cuotas'])) {
            $errores[] = "Debe proporcionar al menos un número de cuotas.";
            return $errores;
        }
        
        if (!isset($datos['pago_mensual']) || !is_array($datos['pago_mensual'])) {
            $errores[] = "Debe proporcionar al menos un valor de pago mensual.";
            return $errores;
        }
        
        if (!isset($datos['deuda']) || !is_array($datos['deuda'])) {
            $errores[] = "Debe proporcionar al menos un valor de deuda total.";
            return $errores;
        }
        
        // Verificar que todos los arrays tengan la misma longitud
        $longitud = count($datos['entidad']);
        if (count($datos['cuotas']) !== $longitud || 
            count($datos['pago_mensual']) !== $longitud || 
            count($datos['deuda']) !== $longitud) {
            $errores[] = "Todos los campos deben tener la misma cantidad de registros.";
            return $errores;
        }
        
        // Validar cada conjunto de datos
        for ($i = 0; $i < $longitud; $i++) {
            $numero_registro = $i + 1;
            
            // Validar entidad (mínimo 3 caracteres)
            if (empty($datos['entidad'][$i]) || strlen(trim($datos['entidad'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: La entidad debe tener al menos 3 caracteres.";
            }
            
            // Validar cuotas (debe ser un número positivo)
            $cuotas = str_replace(['$', ',', '.'], '', $datos['cuotas'][$i]);
            if (empty($datos['cuotas'][$i]) || !is_numeric($cuotas) || $cuotas < 0) {
                $errores[] = "Registro $numero_registro: El número de cuotas debe ser un número válido mayor o igual a 0.";
            }
            
            // Validar pago mensual (debe ser un número positivo)
            $pago_mensual = str_replace(['$', ',', '.'], '', $datos['pago_mensual'][$i]);
            if (empty($datos['pago_mensual'][$i]) || !is_numeric($pago_mensual) || $pago_mensual < 0) {
                $errores[] = "Registro $numero_registro: El pago mensual debe ser un número válido mayor o igual a 0.";
            }
            
            // Validar deuda total (debe ser un número positivo)
            $deuda = str_replace(['$', ',', '.'], '', $datos['deuda'][$i]);
            if (empty($datos['deuda'][$i]) || !is_numeric($deuda) || $deuda < 0) {
                $errores[] = "Registro $numero_registro: La deuda total debe ser un número válido mayor o igual a 0.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM data_credito WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar los nuevos registros
            $sql = "INSERT INTO data_credito (id_cedula, entidad, cuotas, pago_mensual, deuda) VALUES (:id_cedula, :entidad, :cuotas, :pago_mensual, :deuda)";
            $stmt = $this->db->prepare($sql);
            
            $registros_insertados = 0;
            $longitud = count($datos['entidad']);
            
            for ($i = 0; $i < $longitud; $i++) {
                $entidad = $datos['entidad'][$i];
                $cuotas = str_replace(['$', ',', '.'], '', $datos['cuotas'][$i]);
                $pago_mensual = str_replace(['$', ',', '.'], '', $datos['pago_mensual'][$i]);
                $deuda = str_replace(['$', ',', '.'], '', $datos['deuda'][$i]);
                
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':entidad', $entidad);
                $stmt->bindParam(':cuotas', $cuotas);
                $stmt->bindParam(':pago_mensual', $pago_mensual);
                $stmt->bindParam(':deuda', $deuda);
                
                if ($stmt->execute()) {
                    $registros_insertados++;
                }
            }
            
            if ($registros_insertados > 0) {
                return [
                    'success' => true, 
                    'message' => "Se guardaron exitosamente $registros_insertados reporte(s) de data crédito."
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar ningún reporte de data crédito.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function guardarSinReportes() {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Eliminar registros existentes
            $sql_delete = "DELETE FROM data_credito WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar registro con valores N/A
            $sql = "INSERT INTO data_credito (id_cedula, entidad, cuotas, pago_mensual, deuda) VALUES (:id_cedula, 'N/A', 'N/A', 'N/A', 'N/A')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Información de data crédito guardada exitosamente (sin reportes).'];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la información de data crédito.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM data_credito WHERE id_cedula = :id_cedula ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
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