<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class IngresosMensualesController {
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

    /**
     * Convierte un valor en formato colombiano a número decimal
     * Formato esperado: $1.500.000,50 o 1500000,50 o 1500000.50
     */
    private function convertirValorColombiano($valor) {
        if (empty($valor) || $valor === 'N/A') {
            return 0;
        }
        
        // Remover símbolo de peso
        $valor = str_replace('$', '', $valor);
        
        // Remover espacios
        $valor = trim($valor);
        
        // Si tiene coma como separador decimal (formato colombiano)
        if (strpos($valor, ',') !== false) {
            // Separar parte entera y decimal
            $partes = explode(',', $valor);
            if (count($partes) === 2) {
                // Remover puntos de la parte entera (separadores de miles)
                $parte_entera = str_replace('.', '', $partes[0]);
                $parte_decimal = $partes[1];
                return floatval($parte_entera . '.' . $parte_decimal);
            }
        }
        
        // Si tiene punto como separador decimal (formato internacional)
        if (strpos($valor, '.') !== false) {
            // Verificar si es separador decimal o de miles
            $partes = explode('.', $valor);
            if (count($partes) === 2 && strlen($partes[1]) <= 2) {
                // Es separador decimal (máximo 2 decimales)
                $parte_entera = str_replace('.', '', $partes[0]);
                return floatval($parte_entera . '.' . $partes[1]);
            } else {
                // Es separador de miles, remover todos los puntos
                return floatval(str_replace('.', '', $valor));
            }
        }
        
        // Si no tiene separadores, convertir directamente
        return floatval($valor);
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
            'salario_val' => 'Salario',
            'pension_val' => 'Pensión',
            'arriendo_val' => 'Arriendo',
            'trabajo_independiente_val' => 'Trabajo Independiente',
            'otros_val' => 'Otros'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                $errores[] = "El campo '$nombre' es obligatorio.";
            } else {
                // Validar que sea un número válido usando formato colombiano
                $valor_convertido = $this->convertirValorColombiano($datos[$campo]);
                if ($valor_convertido < 0) {
                    $errores[] = "El campo '$nombre' debe ser un valor válido mayor o igual a 0. Formato aceptado: $1.500.000,50 o 1500000,50";
                }
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM ingresos_mensuales WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Preparar los valores usando formato colombiano
            $salario_val = $this->convertirValorColombiano($datos['salario_val']);
            $pension_val = $this->convertirValorColombiano($datos['pension_val']);
            $arriendo_val = $this->convertirValorColombiano($datos['arriendo_val']);
            $trabajo_independiente_val = $this->convertirValorColombiano($datos['trabajo_independiente_val']);
            $otros_val = $this->convertirValorColombiano($datos['otros_val']);
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO ingresos_mensuales (id_cedula, salario_val, pension_val, arriendo_val, trabajo_independiente_val, otros_val) 
                    VALUES (:id_cedula, :salario_val, :pension_val, :arriendo_val, :trabajo_independiente_val, :otros_val)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':salario_val', $salario_val);
            $stmt->bindParam(':pension_val', $pension_val);
            $stmt->bindParam(':arriendo_val', $arriendo_val);
            $stmt->bindParam(':trabajo_independiente_val', $trabajo_independiente_val);
            $stmt->bindParam(':otros_val', $otros_val);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Ingresos mensuales guardados exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar los ingresos mensuales.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM ingresos_mensuales WHERE id_cedula = :id_cedula ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
} 