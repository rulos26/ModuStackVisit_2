<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class ExperienciaLaboralController {
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
            } elseif (is_array($valor)) {
                // Manejar arrays anidados (como 'experiencias')
                $sanitizados[$clave] = [];
                foreach ($valor as $sub_clave => $sub_valor) {
                    if (is_array($sub_valor)) {
                        $sanitizados[$clave][$sub_clave] = [];
                        foreach ($sub_valor as $campo => $campo_valor) {
                            if (is_string($campo_valor)) {
                                $sanitizados[$clave][$sub_clave][$campo] = trim(strip_tags($campo_valor));
                            } else {
                                $sanitizados[$clave][$sub_clave][$campo] = $campo_valor;
                            }
                        }
                    } else {
                        if (is_string($sub_valor)) {
                            $sanitizados[$clave][$sub_clave] = trim(strip_tags($sub_valor));
                        } else {
                            $sanitizados[$clave][$sub_clave] = $sub_valor;
                        }
                    }
                }
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        return $sanitizados;
    }

    public function validarDatos($datos) {
        $errores = [];
        
        // Verificar si se están enviando múltiples experiencias
        if (isset($datos['experiencias']) && is_array($datos['experiencias'])) {
            // Validar múltiples experiencias
            foreach ($datos['experiencias'] as $index => $experiencia) {
                $errores = array_merge($errores, $this->validarExperiencia($experiencia, intval($index) + 1));
            }
        } else {
            // Validar experiencia única (compatibilidad con versión anterior)
            $errores = array_merge($errores, $this->validarExperiencia($datos, 1));
        }
        
        return $errores;
    }
    
    private function validarExperiencia($experiencia, $numero) {
        $errores = [];
        $prefijo = "Experiencia #{$numero}: ";
        
        // Validar campos requeridos
        $campos_requeridos = [
            'empresa' => 'Empresa',
            'tiempo' => 'Tiempo Laborado',
            'cargo' => 'Cargo Desempeñado',
            'salario' => 'Salario',
            'retiro' => 'Motivo de Retiro',
            'concepto' => 'Concepto Emitido',
            'nombre' => 'Nombre del Contacto',
            'numero' => 'Número de Contacto'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($experiencia[$campo]) || empty(trim($experiencia[$campo]))) {
                $errores[] = $prefijo . "El campo '$nombre' es requerido.";
            }
        }
        
        // Validar empresa (mínimo 3 caracteres)
        if (isset($experiencia['empresa']) && strlen(trim($experiencia['empresa'])) < 3) {
            $errores[] = $prefijo . "El nombre de la empresa debe tener al menos 3 caracteres.";
        }
        
        // Validar tiempo laborado (mínimo 3 caracteres)
        if (isset($experiencia['tiempo']) && strlen(trim($experiencia['tiempo'])) < 3) {
            $errores[] = $prefijo . "El tiempo laborado debe tener al menos 3 caracteres.";
        }
        
        // Validar cargo (mínimo 3 caracteres)
        if (isset($experiencia['cargo']) && strlen(trim($experiencia['cargo'])) < 3) {
            $errores[] = $prefijo . "El cargo desempeñado debe tener al menos 3 caracteres.";
        }
        
        // Validar salario (debe ser un número positivo)
        if (isset($experiencia['salario'])) {
            if (!is_numeric($experiencia['salario']) || $experiencia['salario'] < 0) {
                $errores[] = $prefijo . "El salario debe ser un número válido mayor o igual a 0.";
            }
        }
        
        // Validar motivo de retiro (mínimo 5 caracteres)
        if (isset($experiencia['retiro']) && strlen(trim($experiencia['retiro'])) < 5) {
            $errores[] = $prefijo . "El motivo de retiro debe tener al menos 5 caracteres.";
        }
        
        // Validar concepto emitido (mínimo 5 caracteres)
        if (isset($experiencia['concepto']) && strlen(trim($experiencia['concepto'])) < 5) {
            $errores[] = $prefijo . "El concepto emitido debe tener al menos 5 caracteres.";
        }
        
        // Validar nombre del contacto (mínimo 3 caracteres)
        if (isset($experiencia['nombre']) && strlen(trim($experiencia['nombre'])) < 3) {
            $errores[] = $prefijo . "El nombre del contacto debe tener al menos 3 caracteres.";
        }
        
        // Validar número de contacto (debe ser un número válido)
        if (isset($experiencia['numero'])) {
            if (!is_numeric($experiencia['numero']) || $experiencia['numero'] < 1000000) {
                $errores[] = $prefijo . "El número de contacto debe ser un número válido de al menos 7 dígitos.";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Manejar eliminaciones específicas si se proporcionan
            if (isset($datos['experiencias_eliminadas']) && is_array($datos['experiencias_eliminadas'])) {
                $this->eliminarExperienciasEspecificas($id_cedula, $datos['experiencias_eliminadas']);
            }
            
            // Obtener experiencias existentes para comparar
            $experiencias_existentes = $this->obtenerPorCedula($id_cedula);
            $indices_existentes = array_keys($experiencias_existentes);
            
            // Verificar si se están enviando múltiples experiencias
            if (isset($datos['experiencias']) && is_array($datos['experiencias'])) {
                // Guardar múltiples experiencias
                $experiencias_guardadas = 0;
                $experiencias_actualizadas = 0;
                
                foreach ($datos['experiencias'] as $indice => $experiencia) {
                    // Asegurar que el salario sea un número
                    $salario = is_numeric($experiencia['salario']) ? floatval($experiencia['salario']) : 0;
                    $numero = is_numeric($experiencia['numero']) ? intval($experiencia['numero']) : 0;
                    
                    // Verificar si es una experiencia existente o nueva
                    if (isset($experiencias_existentes[$indice])) {
                        // Actualizar experiencia existente
                        $sql = "UPDATE experiencia_laboral SET 
                                empresa = :empresa, 
                                tiempo = :tiempo, 
                                cargo = :cargo, 
                                salario = :salario, 
                                retiro = :retiro, 
                                concepto = :concepto, 
                                nombre = :nombre, 
                                numero = :numero 
                                WHERE id_cedula = :id_cedula AND id = :id";
                        
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id', $experiencias_existentes[$indice]['id']);
                        $stmt->bindParam(':id_cedula', $id_cedula);
                        $stmt->bindParam(':empresa', $experiencia['empresa']);
                        $stmt->bindParam(':tiempo', $experiencia['tiempo']);
                        $stmt->bindParam(':cargo', $experiencia['cargo']);
                        $stmt->bindParam(':salario', $salario);
                        $stmt->bindParam(':retiro', $experiencia['retiro']);
                        $stmt->bindParam(':concepto', $experiencia['concepto']);
                        $stmt->bindParam(':nombre', $experiencia['nombre']);
                        $stmt->bindParam(':numero', $numero);
                        
                        if ($stmt->execute()) {
                            $experiencias_actualizadas++;
                        }
                    } else {
                        // Insertar nueva experiencia
                        $sql = "INSERT INTO experiencia_laboral (
                            id_cedula, empresa, tiempo, cargo, salario, retiro, concepto, nombre, numero
                        ) VALUES (
                            :id_cedula, :empresa, :tiempo, :cargo, :salario, :retiro, :concepto, :nombre, :numero
                        )";
                        
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_cedula', $id_cedula);
                        $stmt->bindParam(':empresa', $experiencia['empresa']);
                        $stmt->bindParam(':tiempo', $experiencia['tiempo']);
                        $stmt->bindParam(':cargo', $experiencia['cargo']);
                        $stmt->bindParam(':salario', $salario);
                        $stmt->bindParam(':retiro', $experiencia['retiro']);
                        $stmt->bindParam(':concepto', $experiencia['concepto']);
                        $stmt->bindParam(':nombre', $experiencia['nombre']);
                        $stmt->bindParam(':numero', $numero);
                        
                        if ($stmt->execute()) {
                            $experiencias_guardadas++;
                        }
                    }
                }
                
                // Manejar observaciones laborales
                $observacion_guardada = false;
                if (isset($datos['observacion_laboral']) && !empty(trim($datos['observacion_laboral']))) {
                    // Eliminar observación existente
                    $sql_delete_obs = "DELETE FROM observaciones_laborales WHERE id_cedula = :id_cedula";
                    $stmt_delete_obs = $this->db->prepare($sql_delete_obs);
                    $stmt_delete_obs->bindParam(':id_cedula', $id_cedula);
                    $stmt_delete_obs->execute();
                    
                    // Insertar nueva observación
                    $sql_obs = "INSERT INTO observaciones_laborales (id_cedula, observacion) VALUES (:id_cedula, :observacion)";
                    $stmt_obs = $this->db->prepare($sql_obs);
                    $stmt_obs->bindParam(':id_cedula', $id_cedula);
                    $stmt_obs->bindParam(':observacion', $datos['observacion_laboral']);
                    
                    if ($stmt_obs->execute()) {
                        $observacion_guardada = true;
                    }
                } else {
                    // Si no hay observación, eliminar la existente
                    $sql_delete_obs = "DELETE FROM observaciones_laborales WHERE id_cedula = :id_cedula";
                    $stmt_delete_obs = $this->db->prepare($sql_delete_obs);
                    $stmt_delete_obs->bindParam(':id_cedula', $id_cedula);
                    $stmt_delete_obs->execute();
                }
                
                $total_procesadas = $experiencias_guardadas + $experiencias_actualizadas;
                if ($total_procesadas > 0) {
                    $mensaje = "Se procesaron {$total_procesadas} experiencia(s) laboral(es): ";
                    if ($experiencias_guardadas > 0) {
                        $mensaje .= "{$experiencias_guardadas} nueva(s)";
                    }
                    if ($experiencias_actualizadas > 0) {
                        $mensaje .= ($experiencias_guardadas > 0 ? ", " : "") . "{$experiencias_actualizadas} actualizada(s)";
                    }
                    if ($observacion_guardada) {
                        $mensaje .= " y la observación laboral";
                    }
                    $mensaje .= ".";
                    
                    return [
                        'success' => true, 
                        'message' => $mensaje
                    ];
                } else {
                    return ['success' => false, 'message' => 'No se pudo guardar ninguna experiencia laboral.'];
                }
            } else {
                // Guardar experiencia única (compatibilidad con versión anterior)
                $sql = "INSERT INTO experiencia_laboral (
                    id_cedula, empresa, tiempo, cargo, salario, retiro, concepto, nombre, numero
                ) VALUES (
                    :id_cedula, :empresa, :tiempo, :cargo, :salario, :retiro, :concepto, :nombre, :numero
                )";
                
                // Asegurar que el salario sea un número
                $salario = is_numeric($datos['salario']) ? floatval($datos['salario']) : 0;
                $numero = is_numeric($datos['numero']) ? intval($datos['numero']) : 0;
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':empresa', $datos['empresa']);
                $stmt->bindParam(':tiempo', $datos['tiempo']);
                $stmt->bindParam(':cargo', $datos['cargo']);
                $stmt->bindParam(':salario', $salario);
                $stmt->bindParam(':retiro', $datos['retiro']);
                $stmt->bindParam(':concepto', $datos['concepto']);
                $stmt->bindParam(':nombre', $datos['nombre']);
                $stmt->bindParam(':numero', $numero);
                
                if ($stmt->execute()) {
                    // Manejar observaciones laborales para experiencia única
                    $observacion_guardada = false;
                    if (isset($datos['observacion_laboral']) && !empty(trim($datos['observacion_laboral']))) {
                        // Eliminar observación existente
                        $sql_delete_obs = "DELETE FROM observaciones_laborales WHERE id_cedula = :id_cedula";
                        $stmt_delete_obs = $this->db->prepare($sql_delete_obs);
                        $stmt_delete_obs->bindParam(':id_cedula', $id_cedula);
                        $stmt_delete_obs->execute();
                        
                        // Insertar nueva observación
                        $sql_obs = "INSERT INTO observaciones_laborales (id_cedula, observacion) VALUES (:id_cedula, :observacion)";
                        $stmt_obs = $this->db->prepare($sql_obs);
                        $stmt_obs->bindParam(':id_cedula', $id_cedula);
                        $stmt_obs->bindParam(':observacion', $datos['observacion_laboral']);
                        
                        if ($stmt_obs->execute()) {
                            $observacion_guardada = true;
                        }
                    } else {
                        // Si no hay observación, eliminar la existente
                        $sql_delete_obs = "DELETE FROM observaciones_laborales WHERE id_cedula = :id_cedula";
                        $stmt_delete_obs = $this->db->prepare($sql_delete_obs);
                        $stmt_delete_obs->bindParam(':id_cedula', $id_cedula);
                        $stmt_delete_obs->execute();
                    }
                    
                    $mensaje = 'Experiencia laboral guardada exitosamente';
                    if ($observacion_guardada) {
                        $mensaje .= ' y la observación laboral';
                    }
                    $mensaje .= '.';
                    
                    return [
                        'success' => true, 
                        'message' => $mensaje
                    ];
                } else {
                    return ['success' => false, 'message' => 'No se pudo guardar la experiencia laboral.'];
                }
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM experiencia_laboral WHERE id_cedula = :id_cedula ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Obtener observación laboral
            $sql_obs = "SELECT observacion FROM observaciones_laborales WHERE id_cedula = :id_cedula LIMIT 1";
            $stmt_obs = $this->db->prepare($sql_obs);
            $stmt_obs->bindParam(':id_cedula', $id_cedula);
            $stmt_obs->execute();
            $observacion = $stmt_obs->fetch(\PDO::FETCH_ASSOC);
            
            // Si no hay resultados de experiencias, devolver null para compatibilidad
            if (empty($resultados)) {
                // Si no hay experiencias pero sí hay observación, devolver array con la observación
                if ($observacion) {
                    return ['observacion_laboral' => $observacion['observacion']];
                }
                return null;
            }
            
            // Agregar la observación al array de resultados si existe
            if ($observacion) {
                $resultados['observacion_laboral'] = $observacion['observacion'];
            }
            
            // Si solo hay un resultado, devolverlo como array asociativo para compatibilidad
            if (count($resultados) === 1) {
                return $resultados[0];
            }
            
            // Si hay múltiples resultados, devolver el array completo
            return $resultados;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Elimina experiencias laborales específicas por índice
     */
    public function eliminarExperienciasEspecificas($id_cedula, $indices_eliminar) {
        try {
            // Obtener todas las experiencias existentes
            $experiencias_existentes = $this->obtenerPorCedula($id_cedula);
            
            if (empty($experiencias_existentes)) {
                return true; // No hay nada que eliminar
            }
            
            // Convertir a array indexado si es necesario
            if (!is_array($experiencias_existentes) || !isset($experiencias_existentes[0])) {
                $experiencias_existentes = [$experiencias_existentes];
            }
            
            $experiencias_eliminadas = 0;
            
            foreach ($indices_eliminar as $indice) {
                if (isset($experiencias_existentes[$indice])) {
                    $experiencia = $experiencias_existentes[$indice];
                    
                    // Eliminar por ID específico si existe
                    if (isset($experiencia['id'])) {
                        $sql = "DELETE FROM experiencia_laboral WHERE id = :id AND id_cedula = :id_cedula";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id', $experiencia['id']);
                        $stmt->bindParam(':id_cedula', $id_cedula);
                        
                        if ($stmt->execute()) {
                            $experiencias_eliminadas++;
                        }
                    } else {
                        // Si no hay ID, eliminar por coincidencia de datos
                        $sql = "DELETE FROM experiencia_laboral WHERE 
                                id_cedula = :id_cedula AND 
                                empresa = :empresa AND 
                                cargo = :cargo AND 
                                salario = :salario";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_cedula', $id_cedula);
                        $stmt->bindParam(':empresa', $experiencia['empresa']);
                        $stmt->bindParam(':cargo', $experiencia['cargo']);
                        $stmt->bindParam(':salario', $experiencia['salario']);
                        
                        if ($stmt->execute()) {
                            $experiencias_eliminadas++;
                        }
                    }
                }
            }
            
            return $experiencias_eliminadas > 0;
            
        } catch (PDOException $e) {
            error_log("Error al eliminar experiencias específicas: " . $e->getMessage());
            return false;
        }
    }
} 