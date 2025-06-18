<?php
namespace App\Controllers;

// Actualizar la ruta de la base de datos para la nueva ubicación
require_once __DIR__ . '/../../../../../../app/Database/Database.php';

use App\Database\Database;
use PDOException;
use Exception;

class InformacionPersonalController {
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
     * Sanitiza los datos de entrada
     */
    public function sanitizarDatos($datos) {
        $sanitizados = [];
        
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $sanitizados[$clave] = trim(strip_tags($valor));
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        
        // Debug: log de datos sanitizados
        error_log('DEBUG InformacionPersonalController: Datos sanitizados: ' . json_encode($sanitizados));
        
        return $sanitizados;
    }
    
    /**
     * Valida los datos de entrada
     */
    public function validarDatos($datos) {
        $errores = [];
        
        // Validar cédula
        if (empty($datos['id_cedula']) || !is_numeric($datos['id_cedula'])) {
            $errores[] = 'La cédula es obligatoria y debe ser numérica.';
        }
        
        // Validar nombres
        if (empty($datos['nombres']) || !preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/', $datos['nombres'])) {
            $errores[] = 'Los nombres son obligatorios y solo pueden contener letras.';
        }
        
        // Validar apellidos
        if (empty($datos['apellidos']) || !preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/', $datos['apellidos'])) {
            $errores[] = 'Los apellidos son obligatorios y solo pueden contener letras.';
        }
        
        // Validar edad
        if (empty($datos['edad']) || !is_numeric($datos['edad']) || $datos['edad'] < 18 || $datos['edad'] > 120) {
            $errores[] = 'La edad debe estar entre 18 y 120 años.';
        }
        
        // Validar celular 1
        if (empty($datos['celular_1']) || !preg_match('/^[0-9]{10}$/', $datos['celular_1'])) {
            $errores[] = 'El celular 1 es obligatorio y debe tener 10 dígitos.';
        }
        
        // Validar celular 2 (opcional)
        if (!empty($datos['celular_2']) && !preg_match('/^[0-9]{10}$/', $datos['celular_2'])) {
            $errores[] = 'El celular 2 debe tener 10 dígitos.';
        }
        
        // Validar teléfono (opcional)
        if (!empty($datos['telefono']) && !preg_match('/^[0-9]{7}$/', $datos['telefono'])) {
            $errores[] = 'El teléfono debe tener 7 dígitos.';
        }
        
        // Validar correo
        if (empty($datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico es obligatorio y debe ser válido.';
        }
        
        // Validar dirección
        if (empty($datos['direccion'])) {
            $errores[] = 'La dirección es obligatoria.';
        }
        
        // Validar campos de selección
        $camposSelect = ['id_tipo_documentos', 'cedula_expedida', 'id_rh', 'id_estatura', 'peso_kg', 'id_estado_civil', 'id_ciudad', 'lugar_nacimiento', 'id_estrato'];
        foreach ($camposSelect as $campo) {
            if (empty($datos[$campo]) || $datos[$campo] == '0') {
                $errores[] = 'El campo ' . str_replace('_', ' ', $campo) . ' es obligatorio.';
            }
        }
        
        // Validar número de hijos
        if (isset($datos['numero_hijos']) && (!is_numeric($datos['numero_hijos']) || $datos['numero_hijos'] < 0 || $datos['numero_hijos'] > 20)) {
            $errores[] = 'El número de hijos debe estar entre 0 y 20.';
        }
        
        // Validar hacer_cuanto
        if (isset($datos['hacer_cuanto']) && (!is_numeric($datos['hacer_cuanto']) || $datos['hacer_cuanto'] < 0 || $datos['hacer_cuanto'] > 50)) {
            $errores[] = 'El tiempo en estado civil debe estar entre 0 y 50 años.';
        }
        
        return $errores;
    }
    
    /**
     * Guarda o actualiza la información personal
     */
    public function guardar($datos) {
        try {
            // Debug: log de datos recibidos
            error_log('DEBUG InformacionPersonalController: Datos recibidos para guardar: ' . json_encode($datos));
            
            // Verificar si ya existe un registro para esta cédula
            $existe = $this->obtenerPorCedula($datos['id_cedula']);
            
            if ($existe) {
                // Actualizar registro existente
                $resultado = $this->actualizar($datos);
                if ($resultado) {
                    return [
                        'success' => true,
                        'message' => 'Información personal actualizada exitosamente.',
                        'action' => 'updated'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al actualizar la información personal.'
                    ];
                }
            } else {
                // Crear nuevo registro
                $resultado = $this->crear($datos);
                if ($resultado) {
                    return [
                        'success' => true,
                        'message' => 'Información personal guardada exitosamente.',
                        'action' => 'created'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al guardar la información personal.'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log('ERROR InformacionPersonalController: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Crea un nuevo registro
     */
    private function crear($datos) {
        try {
            $sql = "INSERT INTO informacion_personal (
                id_cedula, id_tipo_documentos, cedula_expedida, nombres, apellidos, 
                edad, fecha_expedicion, lugar_nacimiento, celular_1, celular_2, 
                telefono, id_rh, id_estatura, peso_kg, id_estado_civil, hacer_cuanto, 
                numero_hijos, direccion, id_ciudad, localidad, barrio, id_estrato, 
                correo, cargo, observacion, fecha_creacion
            ) VALUES (
                :id_cedula, :id_tipo_documentos, :cedula_expedida, :nombres, :apellidos,
                :edad, :fecha_expedicion, :lugar_nacimiento, :celular_1, :celular_2,
                :telefono, :id_rh, :id_estatura, :peso_kg, :id_estado_civil, :hacer_cuanto,
                :numero_hijos, :direccion, :id_ciudad, :localidad, :barrio, :id_estrato,
                :correo, :cargo, :observacion, NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            $stmt->bindParam(':id_cedula', $datos['id_cedula']);
            $stmt->bindParam(':id_tipo_documentos', $datos['id_tipo_documentos']);
            $stmt->bindParam(':cedula_expedida', $datos['cedula_expedida']);
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':edad', $datos['edad']);
            $stmt->bindParam(':fecha_expedicion', $datos['fecha_expedicion']);
            $stmt->bindParam(':lugar_nacimiento', $datos['lugar_nacimiento']);
            $stmt->bindParam(':celular_1', $datos['celular_1']);
            $stmt->bindParam(':celular_2', $datos['celular_2']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':id_rh', $datos['id_rh']);
            $stmt->bindParam(':id_estatura', $datos['id_estatura']);
            $stmt->bindParam(':peso_kg', $datos['peso_kg']);
            $stmt->bindParam(':id_estado_civil', $datos['id_estado_civil']);
            $stmt->bindParam(':hacer_cuanto', $datos['hacer_cuanto']);
            $stmt->bindParam(':numero_hijos', $datos['numero_hijos']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':id_ciudad', $datos['id_ciudad']);
            $stmt->bindParam(':localidad', $datos['localidad']);
            $stmt->bindParam(':barrio', $datos['barrio']);
            $stmt->bindParam(':id_estrato', $datos['id_estrato']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':cargo', $datos['cargo']);
            $stmt->bindParam(':observacion', $datos['observacion']);
            
            $resultado = $stmt->execute();
            
            error_log('DEBUG InformacionPersonalController: Resultado crear: ' . var_export($resultado, true));
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController crear: ' . $e->getMessage());
            throw new Exception('Error al crear registro: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un registro existente
     */
    private function actualizar($datos) {
        try {
            $sql = "UPDATE informacion_personal SET 
                id_tipo_documentos = :id_tipo_documentos,
                cedula_expedida = :cedula_expedida,
                nombres = :nombres,
                apellidos = :apellidos,
                edad = :edad,
                fecha_expedicion = :fecha_expedicion,
                lugar_nacimiento = :lugar_nacimiento,
                celular_1 = :celular_1,
                celular_2 = :celular_2,
                telefono = :telefono,
                id_rh = :id_rh,
                id_estatura = :id_estatura,
                peso_kg = :peso_kg,
                id_estado_civil = :id_estado_civil,
                hacer_cuanto = :hacer_cuanto,
                numero_hijos = :numero_hijos,
                direccion = :direccion,
                id_ciudad = :id_ciudad,
                localidad = :localidad,
                barrio = :barrio,
                id_estrato = :id_estrato,
                correo = :correo,
                cargo = :cargo,
                observacion = :observacion,
                fecha_actualizacion = NOW()
                WHERE id_cedula = :id_cedula";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            $stmt->bindParam(':id_cedula', $datos['id_cedula']);
            $stmt->bindParam(':id_tipo_documentos', $datos['id_tipo_documentos']);
            $stmt->bindParam(':cedula_expedida', $datos['cedula_expedida']);
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':edad', $datos['edad']);
            $stmt->bindParam(':fecha_expedicion', $datos['fecha_expedicion']);
            $stmt->bindParam(':lugar_nacimiento', $datos['lugar_nacimiento']);
            $stmt->bindParam(':celular_1', $datos['celular_1']);
            $stmt->bindParam(':celular_2', $datos['celular_2']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':id_rh', $datos['id_rh']);
            $stmt->bindParam(':id_estatura', $datos['id_estatura']);
            $stmt->bindParam(':peso_kg', $datos['peso_kg']);
            $stmt->bindParam(':id_estado_civil', $datos['id_estado_civil']);
            $stmt->bindParam(':hacer_cuanto', $datos['hacer_cuanto']);
            $stmt->bindParam(':numero_hijos', $datos['numero_hijos']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':id_ciudad', $datos['id_ciudad']);
            $stmt->bindParam(':localidad', $datos['localidad']);
            $stmt->bindParam(':barrio', $datos['barrio']);
            $stmt->bindParam(':id_estrato', $datos['id_estrato']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':cargo', $datos['cargo']);
            $stmt->bindParam(':observacion', $datos['observacion']);
            
            $resultado = $stmt->execute();
            
            error_log('DEBUG InformacionPersonalController: Resultado actualizar: ' . var_export($resultado, true));
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController actualizar: ' . $e->getMessage());
            throw new Exception('Error al actualizar registro: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene información personal por cédula
     */
    public function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT * FROM informacion_personal WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController obtenerPorCedula: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene opciones para los select boxes
     */
    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'tipo_documentos' => 'opc_tipo_documentos',
                'municipios' => 'municipios',
                'rh' => 'opc_rh',
                'estaturas' => 'opc_estaturas',
                'pesos' => 'opc_pesos',
                'estado_civil' => 'opc_estado_civil',
                'estratos' => 'opc_estratos'
            ];
            
            if (!isset($tablas[$tipo])) {
                throw new Exception("Tipo de opción no válido: $tipo");
            }
            
            $tabla = $tablas[$tipo];
            $sql = "SELECT * FROM $tabla ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController obtenerOpciones: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verifica si existe información para una cédula
     */
    public function existeInformacion($cedula) {
        try {
            $sql = "SELECT COUNT(*) FROM informacion_personal WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController existeInformacion: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina información personal por cédula
     */
    public function eliminar($cedula) {
        try {
            $sql = "DELETE FROM informacion_personal WHERE id_cedula = :cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController eliminar: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de información personal
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_registros,
                        COUNT(DISTINCT id_ciudad) as ciudades_diferentes,
                        AVG(edad) as edad_promedio,
                        COUNT(CASE WHEN id_estado_civil = 1 THEN 1 END) as solteros,
                        COUNT(CASE WHEN id_estado_civil = 2 THEN 1 END) as casados
                    FROM informacion_personal";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('ERROR InformacionPersonalController obtenerEstadisticas: ' . $e->getMessage());
            return [];
        }
    }
} 