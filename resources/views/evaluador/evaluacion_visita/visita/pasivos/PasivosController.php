<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class PasivosController {
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

    public function validarDatos($datos) {
        $errores = [];
        
        // Validar si tiene pasivos (único campo obligatorio)
        if (empty($datos['tiene_pasivos']) || $datos['tiene_pasivos'] == '') {
            $errores[] = 'Debe seleccionar si posee pasivos.';
            return $errores; // Si no selecciona, no validar más campos
        }
        
        // Si no tiene pasivos (valor 0), no validar más campos
        if ($datos['tiene_pasivos'] == '0') {
            return $errores; // No hay más validaciones necesarias
        }
        
        // Si tiene pasivos (valor 1), validar los campos adicionales
        if ($datos['tiene_pasivos'] == '1') {
            // Verificar que se recibieron los arrays necesarios
            if (!isset($datos['item']) || !is_array($datos['item'])) {
                $errores[] = "Debe proporcionar al menos un producto.";
                return $errores;
            }
        
            if (!isset($datos['id_entidad']) || !is_array($datos['id_entidad'])) {
                $errores[] = "Debe proporcionar al menos una entidad.";
                return $errores;
            }
            
            if (!isset($datos['id_tipo_inversion']) || !is_array($datos['id_tipo_inversion'])) {
                $errores[] = "Debe proporcionar al menos un tipo de inversión.";
                return $errores;
            }
            
            if (!isset($datos['id_ciudad']) || !is_array($datos['id_ciudad'])) {
                $errores[] = "Debe seleccionar al menos una ciudad.";
                return $errores;
            }
            
            if (!isset($datos['deuda']) || !is_array($datos['deuda'])) {
                $errores[] = "Debe proporcionar al menos un valor de deuda.";
                return $errores;
            }
            
            if (!isset($datos['cuota_mes']) || !is_array($datos['cuota_mes'])) {
                $errores[] = "Debe proporcionar al menos un valor de cuota mensual.";
                return $errores;
            }
            
            // Verificar que todos los arrays tengan la misma longitud
            $longitud = count($datos['item']);
            if (count($datos['id_entidad']) !== $longitud || 
                count($datos['id_tipo_inversion']) !== $longitud || 
                count($datos['id_ciudad']) !== $longitud || 
                count($datos['deuda']) !== $longitud || 
                count($datos['cuota_mes']) !== $longitud) {
                $errores[] = "Todos los campos deben tener la misma cantidad de registros.";
                return $errores;
            }
            
            // Validar cada conjunto de datos
            for ($i = 0; $i < $longitud; $i++) {
                $numero_registro = $i + 1;
                
                // Validar producto (mínimo 3 caracteres)
                if (empty($datos['item'][$i]) || strlen(trim($datos['item'][$i])) < 3) {
                    $errores[] = "Registro $numero_registro: El producto debe tener al menos 3 caracteres.";
                }
                
                // Validar entidad (mínimo 3 caracteres)
                if (empty($datos['id_entidad'][$i]) || strlen(trim($datos['id_entidad'][$i])) < 3) {
                    $errores[] = "Registro $numero_registro: La entidad debe tener al menos 3 caracteres.";
                }
                
                // Validar tipo de inversión (mínimo 3 caracteres)
                if (empty($datos['id_tipo_inversion'][$i]) || strlen(trim($datos['id_tipo_inversion'][$i])) < 3) {
                    $errores[] = "Registro $numero_registro: El tipo de inversión debe tener al menos 3 caracteres.";
                }
                
                // Validar ciudad (debe ser un número válido)
                if (empty($datos['id_ciudad'][$i]) || !is_numeric($datos['id_ciudad'][$i]) || $datos['id_ciudad'][$i] < 1) {
                    $errores[] = "Registro $numero_registro: Debe seleccionar una ciudad válida.";
                }
                
                // Validar deuda (debe ser un número positivo)
                $deuda_convertida = $this->convertirValorColombiano($datos['deuda'][$i]);
                if (empty($datos['deuda'][$i]) || $deuda_convertida < 0) {
                    $errores[] = "Registro $numero_registro: La deuda debe ser un valor válido mayor o igual a 0. Formato aceptado: $1.500.000,50 o 1500000,50";
                }
                
                // Validar cuota mensual (debe ser un número positivo)
                $cuota_convertida = $this->convertirValorColombiano($datos['cuota_mes'][$i]);
                if (empty($datos['cuota_mes'][$i]) || $cuota_convertida < 0) {
                    $errores[] = "Registro $numero_registro: La cuota mensual debe ser un valor válido mayor o igual a 0. Formato aceptado: $1.500.000,50 o 1500000,50";
                }
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $tiene_pasivos = $datos['tiene_pasivos'];
            
            // Si el usuario indica que NO tiene pasivos
            if ($tiene_pasivos == '0') {
                $existe = $this->obtenerPorCedula($id_cedula);
                
                if ($existe) {
                    // Si ya existe un registro, se actualiza para limpiar los campos
                    $sql = "UPDATE pasivos SET 
                            item = 'N/A', id_entidad = 'N/A', id_tipo_inversion = 'N/A', 
                            id_ciudad = 0, deuda = 'N/A', cuota_mes = 'N/A'
                            WHERE id_cedula = :id_cedula";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id_cedula', $id_cedula);
                } else {
                    // Si no existe, se inserta un nuevo registro con valores N/A
                    $sql = "INSERT INTO pasivos (id_cedula, item, id_entidad, id_tipo_inversion, id_ciudad, deuda, cuota_mes) 
                            VALUES (:id_cedula, 'N/A', 'N/A', 'N/A', 0, 'N/A', 'N/A')";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id_cedula', $id_cedula);
                }
                $ok = $stmt->execute();
                
                if ($ok) {
                    return ['success' => true, 'message' => 'Se ha registrado que no tiene pasivos.'];
                } else {
                    return ['success' => false, 'message' => 'Error al registrar la información.'];
                }
            }
            
            // Si tiene pasivos, guardar toda la información
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM pasivos WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar los nuevos registros
            $sql = "INSERT INTO pasivos (id_cedula, item, id_entidad, id_tipo_inversion, id_ciudad, deuda, cuota_mes) 
                    VALUES (:id_cedula, :item, :id_entidad, :id_tipo_inversion, :id_ciudad, :deuda, :cuota_mes)";
            $stmt = $this->db->prepare($sql);
            
            $registros_insertados = 0;
            $longitud = count($datos['item']);
            
            for ($i = 0; $i < $longitud; $i++) {
                $item = $datos['item'][$i];
                $id_entidad = $datos['id_entidad'][$i];
                $id_tipo_inversion = $datos['id_tipo_inversion'][$i];
                $id_ciudad = $datos['id_ciudad'][$i];
                
                // Convertir valores usando el método para formato colombiano
                $deuda = $this->convertirValorColombiano($datos['deuda'][$i]);
                $cuota_mes = $this->convertirValorColombiano($datos['cuota_mes'][$i]);
                
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':item', $item);
                $stmt->bindParam(':id_entidad', $id_entidad);
                $stmt->bindParam(':id_tipo_inversion', $id_tipo_inversion);
                $stmt->bindParam(':id_ciudad', $id_ciudad);
                $stmt->bindParam(':deuda', $deuda);
                $stmt->bindParam(':cuota_mes', $cuota_mes);
                
                if ($stmt->execute()) {
                    $registros_insertados++;
                }
            }
            
            if ($registros_insertados > 0) {
                return [
                    'success' => true, 
                    'message' => "Se guardaron exitosamente $registros_insertados pasivo(s)."
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar ningún pasivo.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function guardarSinPasivos() {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Eliminar registros existentes
            $sql_delete = "DELETE FROM pasivos WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar registro con valores N/A
            $sql = "INSERT INTO pasivos (id_cedula, item, id_entidad, id_tipo_inversion, id_ciudad, deuda, cuota_mes) 
                    VALUES (:id_cedula, 'N/A', 'N/A', 'N/A', 0, 'N/A', 'N/A')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Información de pasivos guardada exitosamente (sin pasivos).'];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la información de pasivos.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM pasivos WHERE id_cedula = :id_cedula ORDER BY id";
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