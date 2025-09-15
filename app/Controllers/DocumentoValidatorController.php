<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;
use Exception;

class DocumentoValidatorController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Valida y procesa el ingreso de documento según el flujo optimizado
     * 
     * @param string $cedula Número de documento a validar
     * @return array Resultado de la validación con mensaje y acción
     */
    public function validarDocumento($cedula) {
        try {
            // Paso 1: Validar formato del documento
            $validacionFormato = $this->validarFormatoDocumento($cedula);
            if (!$validacionFormato['valido']) {
                return [
                    'success' => false,
                    'message' => $validacionFormato['mensaje'],
                    'action' => 'error',
                    'redirect' => null
                ];
            }
            
            // Paso 2: Buscar en tabla evaluados
            $evaluado = $this->buscarEnEvaluados($cedula);
            if ($evaluado) {
                return [
                    'success' => true,
                    'message' => 'Evaluado encontrado. Redirigiendo a Información Personal…',
                    'action' => 'evaluado_existente',
                    'redirect' => 'informacion_personal/informacion_personal.php',
                    'data' => $evaluado
                ];
            }
            
            // Paso 3: Buscar en tabla autorizaciones
            $autorizacion = $this->buscarEnAutorizaciones($cedula);
            if ($autorizacion) {
                // Crear evaluado desde autorización
                $evaluadoCreado = $this->crearEvaluadoDesdeAutorizacion($autorizacion);
                if ($evaluadoCreado) {
                    return [
                        'success' => true,
                        'message' => 'Se creó el evaluado a partir de la carta de autorización. Continúe con Información Personal.',
                        'action' => 'evaluado_creado',
                        'redirect' => 'informacion_personal/informacion_personal.php',
                        'data' => $evaluadoCreado
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al crear evaluado desde autorización.',
                        'action' => 'error',
                        'redirect' => null
                    ];
                }
            }
            
            // Paso 4: No encontrado en ninguna tabla
            return [
                'success' => false,
                'message' => 'No se encontró ninguna cédula asociada con carta de autorización.',
                'action' => 'no_encontrado',
                'redirect' => '../../carta_visita/index_carta.php'
            ];
            
        } catch (Exception $e) {
            error_log('ERROR DocumentoValidatorController: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'action' => 'error',
                'redirect' => null
            ];
        }
    }
    
    /**
     * Valida el formato del documento
     * 
     * @param string $cedula
     * @return array
     */
    private function validarFormatoDocumento($cedula) {
        // Limpiar el documento
        $cedula = trim($cedula);
        
        // Validar que sea numérico y mayor que 0
        if (!is_numeric($cedula) || $cedula <= 0) {
            return [
                'valido' => false,
                'mensaje' => 'Número de documento inválido. Ingrese una cédula válida (7-10 dígitos).'
            ];
        }
        
        // Validar longitud (7-10 dígitos)
        $longitud = strlen($cedula);
        if ($longitud < 7 || $longitud > 10) {
            return [
                'valido' => false,
                'mensaje' => 'Número de documento inválido. Ingrese una cédula válida (7-10 dígitos).'
            ];
        }
        
        return [
            'valido' => true,
            'mensaje' => 'Documento válido'
        ];
    }
    
    /**
     * Busca el documento en la tabla evaluados
     * 
     * @param string $cedula
     * @return array|false
     */
    private function buscarEnEvaluados($cedula) {
        try {
            $sql = "SELECT * FROM evaluados WHERE id_cedula = :cedula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado) {
                error_log("DEBUG DocumentoValidatorController: Evaluado encontrado: " . $cedula);
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR DocumentoValidatorController buscarEnEvaluados: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca el documento en la tabla autorizaciones
     * 
     * @param string $cedula
     * @return array|false
     */
    private function buscarEnAutorizaciones($cedula) {
        try {
            $sql = "SELECT * FROM autorizaciones WHERE cedula = :cedula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado) {
                error_log("DEBUG DocumentoValidatorController: Autorización encontrada: " . $cedula);
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR DocumentoValidatorController buscarEnAutorizaciones: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea un nuevo evaluado a partir de datos de autorización
     * 
     * @param array $autorizacion Datos de la autorización
     * @return array|false
     */
    private function crearEvaluadoDesdeAutorizacion($autorizacion) {
        try {
            // Verificar nuevamente que no exista en evaluados (doble verificación)
            $existe = $this->buscarEnEvaluados($autorizacion['cedula']);
            if ($existe) {
                error_log("DEBUG DocumentoValidatorController: Evaluado ya existe, no crear duplicado: " . $autorizacion['cedula']);
                return $existe;
            }
            
            // Preparar datos para insertar
            $datosEvaluado = [
                'id_cedula' => $autorizacion['cedula'],
                'nombres' => $autorizacion['nombres'] ?? '',
                'direccion' => $autorizacion['direccion'] ?? '',
                'localidad' => $autorizacion['localidad'] ?? '',
                'barrio' => $autorizacion['barrio'] ?? '',
                'telefono' => $autorizacion['telefono'] ?? '',
                'celular_1' => $autorizacion['celular'] ?? '',
                'correo' => $autorizacion['correo'] ?? '',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            
            // Insertar en evaluados
            $sql = "INSERT INTO evaluados (
                id_cedula, nombres, direccion, localidad, barrio, 
                telefono, celular_1, correo, fecha_creacion
            ) VALUES (
                :id_cedula, :nombres, :direccion, :localidad, :barrio,
                :telefono, :celular_1, :correo, :fecha_creacion
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $datosEvaluado['id_cedula']);
            $stmt->bindParam(':nombres', $datosEvaluado['nombres']);
            $stmt->bindParam(':direccion', $datosEvaluado['direccion']);
            $stmt->bindParam(':localidad', $datosEvaluado['localidad']);
            $stmt->bindParam(':barrio', $datosEvaluado['barrio']);
            $stmt->bindParam(':telefono', $datosEvaluado['telefono']);
            $stmt->bindParam(':celular_1', $datosEvaluado['celular_1']);
            $stmt->bindParam(':correo', $datosEvaluado['correo']);
            $stmt->bindParam(':fecha_creacion', $datosEvaluado['fecha_creacion']);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                error_log("DEBUG DocumentoValidatorController: Evaluado creado desde autorización: " . $autorizacion['cedula']);
                
                // Obtener el registro creado
                $evaluadoCreado = $this->buscarEnEvaluados($autorizacion['cedula']);
                return $evaluadoCreado;
            } else {
                error_log("ERROR DocumentoValidatorController: Error al crear evaluado desde autorización: " . $autorizacion['cedula']);
                return false;
            }
            
        } catch (PDOException $e) {
            error_log('ERROR DocumentoValidatorController crearEvaluadoDesdeAutorizacion: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de validación de documentos
     * 
     * @return array
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM evaluados) as total_evaluados,
                        (SELECT COUNT(*) FROM autorizaciones) as total_autorizaciones,
                        (SELECT COUNT(DISTINCT cedula) FROM autorizaciones WHERE cedula NOT IN (SELECT id_cedula FROM evaluados)) as autorizaciones_sin_evaluado";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR DocumentoValidatorController obtenerEstadisticas: ' . $e->getMessage());
            return [];
        }
    }
}
