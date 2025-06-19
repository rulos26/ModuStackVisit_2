<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class GastoController {
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
        
        // Verificar que se recibieron todos los campos necesarios
        $campos_requeridos = [
            'alimentacion_val' => 'Alimentación',
            'educacion_val' => 'Educación',
            'salud_val' => 'Salud',
            'recreacion_val' => 'Recreación',
            'cuota_creditos_val' => 'Cuota de Créditos',
            'arriendo_val' => 'Arriendo',
            'servicios_publicos_val' => 'Servicios Públicos',
            'otros_val' => 'Otros'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                $errores[] = "El campo '$nombre' es obligatorio.";
            } else {
                // Validar que sea un número válido
                $valor = str_replace(['$', ',', '.'], '', $datos[$campo]);
                if (!is_numeric($valor) || $valor < 0) {
                    $errores[] = "El campo '$nombre' debe ser un número válido mayor o igual a 0.";
                }
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM gasto WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Preparar los valores limpios
            $alimentacion_val = str_replace(['$', ',', '.'], '', $datos['alimentacion_val']);
            $educacion_val = str_replace(['$', ',', '.'], '', $datos['educacion_val']);
            $salud_val = str_replace(['$', ',', '.'], '', $datos['salud_val']);
            $recreacion_val = str_replace(['$', ',', '.'], '', $datos['recreacion_val']);
            $cuota_creditos_val = str_replace(['$', ',', '.'], '', $datos['cuota_creditos_val']);
            $arriendo_val = str_replace(['$', ',', '.'], '', $datos['arriendo_val']);
            $servicios_publicos_val = str_replace(['$', ',', '.'], '', $datos['servicios_publicos_val']);
            $otros_val = str_replace(['$', ',', '.'], '', $datos['otros_val']);
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO gasto (id_cedula, alimentacion_val, educacion_val, salud_val, recreacion_val, cuota_creditos_val, arriendo_val, servicios_publicos_val, otros_val) 
                    VALUES (:id_cedula, :alimentacion_val, :educacion_val, :salud_val, :recreacion_val, :cuota_creditos_val, :arriendo_val, :servicios_publicos_val, :otros_val)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':alimentacion_val', $alimentacion_val);
            $stmt->bindParam(':educacion_val', $educacion_val);
            $stmt->bindParam(':salud_val', $salud_val);
            $stmt->bindParam(':recreacion_val', $recreacion_val);
            $stmt->bindParam(':cuota_creditos_val', $cuota_creditos_val);
            $stmt->bindParam(':arriendo_val', $arriendo_val);
            $stmt->bindParam(':servicios_publicos_val', $servicios_publicos_val);
            $stmt->bindParam(':otros_val', $otros_val);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Gastos mensuales guardados exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar los gastos mensuales.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM gasto WHERE id_cedula = :id_cedula ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
} 