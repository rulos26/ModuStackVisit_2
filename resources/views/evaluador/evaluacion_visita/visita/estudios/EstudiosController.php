<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class EstudiosController {
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
        $campos_requeridos = [
            'centro_estudios' => 'Centro de Estudios',
            'id_jornada' => 'Jornada',
            'id_ciudad' => 'Ciudad',
            'anno' => 'Año',
            'titulos' => 'Títulos',
            'id_resultado' => 'Resultado'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || !is_array($datos[$campo])) {
                $errores[] = "Debe proporcionar al menos un registro de $nombre.";
                return $errores;
            }
        }
        
        // Verificar que todos los arrays tengan la misma longitud
        $longitud = count($datos['centro_estudios']);
        foreach ($campos_requeridos as $campo => $nombre) {
            if (count($datos[$campo]) !== $longitud) {
                $errores[] = "Todos los campos deben tener la misma cantidad de registros.";
                return $errores;
            }
        }
        
        // Validar cada conjunto de datos
        for ($i = 0; $i < $longitud; $i++) {
            $numero_registro = $i + 1;
            
            // Validar centro de estudios (mínimo 3 caracteres)
            if (empty($datos['centro_estudios'][$i]) || strlen(trim($datos['centro_estudios'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: El centro de estudios debe tener al menos 3 caracteres.";
            }
            
            // Validar jornada (mínimo 3 caracteres)
            if (empty($datos['id_jornada'][$i]) || strlen(trim($datos['id_jornada'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: La jornada debe tener al menos 3 caracteres.";
            }
            
            // Validar ciudad (debe ser un número válido)
            if (empty($datos['id_ciudad'][$i]) || !is_numeric($datos['id_ciudad'][$i]) || $datos['id_ciudad'][$i] < 1) {
                $errores[] = "Registro $numero_registro: Debe seleccionar una ciudad válida.";
            }
            
            // Validar año (debe ser un número válido entre 1900 y año actual + 10)
            $anno_actual = date('Y');
            if (empty($datos['anno'][$i]) || !is_numeric($datos['anno'][$i]) || 
                $datos['anno'][$i] < 1900 || $datos['anno'][$i] > ($anno_actual + 10)) {
                $errores[] = "Registro $numero_registro: El año debe ser un número válido entre 1900 y " . ($anno_actual + 10) . ".";
            }
            
            // Validar títulos (mínimo 3 caracteres)
            if (empty($datos['titulos'][$i]) || strlen(trim($datos['titulos'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: Los títulos deben tener al menos 3 caracteres.";
            }
            
            // Validar resultado (mínimo 3 caracteres)
            if (empty($datos['id_resultado'][$i]) || strlen(trim($datos['id_resultado'][$i])) < 3) {
                $errores[] = "Registro $numero_registro: El resultado debe tener al menos 3 caracteres.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM estudios WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar los nuevos registros
            $sql = "INSERT INTO estudios (id_cedula, centro_estudios, id_jornada, id_ciudad, anno, titulos, id_resultado) 
                    VALUES (:id_cedula, :centro_estudios, :id_jornada, :id_ciudad, :anno, :titulos, :id_resultado)";
            $stmt = $this->db->prepare($sql);
            
            $registros_insertados = 0;
            $longitud = count($datos['centro_estudios']);
            
            for ($i = 0; $i < $longitud; $i++) {
                $centro_estudios = $datos['centro_estudios'][$i];
                $id_jornada = $datos['id_jornada'][$i];
                $id_ciudad = $datos['id_ciudad'][$i];
                $anno = $datos['anno'][$i];
                $titulos = $datos['titulos'][$i];
                $id_resultado = $datos['id_resultado'][$i];
                
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':centro_estudios', $centro_estudios);
                $stmt->bindParam(':id_jornada', $id_jornada);
                $stmt->bindParam(':id_ciudad', $id_ciudad);
                $stmt->bindParam(':anno', $anno);
                $stmt->bindParam(':titulos', $titulos);
                $stmt->bindParam(':id_resultado', $id_resultado);
                
                if ($stmt->execute()) {
                    $registros_insertados++;
                }
            }
            
            if ($registros_insertados > 0) {
                return [
                    'success' => true, 
                    'message' => "Se guardaron exitosamente $registros_insertados estudio(s)."
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar ningún estudio.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM estudios WHERE id_cedula = :id_cedula ORDER BY id";
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