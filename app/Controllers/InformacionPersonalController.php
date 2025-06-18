<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use Exception;
use PDO;
use App\Database\Database;

class InformacionPersonalController {
    private static $instance = null;
    private $db;

    private function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
            if (!$this->db instanceof PDO) {
                throw new Exception("Error al obtener la conexión a la base de datos");
            }
        } catch (Exception $e) {
            error_log("Error en InformacionPersonalController::__construct: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener información personal por cédula
     */
    public function obtenerPorCedula($id_cedula) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM informacion_personal WHERE id_cedula = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->errorInfo()[2]);
            }
            
            $stmt->execute([$id_cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en InformacionPersonalController::obtenerPorCedula: " . $e->getMessage());
            throw new Exception("Error al obtener la información personal: " . $e->getMessage());
        }
    }

    /**
     * Guardar información personal
     */
    public function guardar($datos) {
        try {
            // Validar datos requeridos
            $campos_requeridos = [
                'id_cedula', 'id_tipo_documentos', 'cedula_expedida', 'nombres', 
                'apellidos', 'edad', 'fecha_expedicion', 'lugar_nacimiento', 
                'celular_1', 'id_rh', 'id_estatura', 'peso_kg', 'id_estado_civil', 
                'direccion', 'id_ciudad', 'localidad', 'barrio', 'id_estrato', 'correo'
            ];

            foreach ($campos_requeridos as $campo) {
                if (empty($datos[$campo])) {
                    throw new Exception("El campo {$campo} es requerido");
                }
            }

            // Validar formato de email
            if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El formato del correo electrónico no es válido");
            }

            // Validar edad
            if (!is_numeric($datos['edad']) || $datos['edad'] < 18 || $datos['edad'] > 120) {
                throw new Exception("La edad debe estar entre 18 y 120 años");
            }

            // Verificar si ya existe información para esta cédula
            $existente = $this->obtenerPorCedula($datos['id_cedula']);
            
            if ($existente) {
                // Actualizar registro existente
                $sql = "UPDATE informacion_personal SET 
                    id_tipo_documentos = ?, cedula_expedida = ?, nombres = ?, 
                    apellidos = ?, edad = ?, fecha_expedicion = ?, lugar_nacimiento = ?, 
                    celular_1 = ?, celular_2 = ?, telefono = ?, id_rh = ?, 
                    id_estatura = ?, peso_kg = ?, id_estado_civil = ?, hacer_cuanto = ?, 
                    numero_hijos = ?, direccion = ?, id_ciudad = ?, localidad = ?, 
                    barrio = ?, id_estrato = ?, correo = ?, cargo = ?, observacion = ?,
                    fecha_actualizacion = NOW()
                    WHERE id_cedula = ?";
                
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta de actualización: " . $this->db->errorInfo()[2]);
                }
                
                $stmt->execute([
                    $datos['id_tipo_documentos'], $datos['cedula_expedida'], $datos['nombres'],
                    $datos['apellidos'], $datos['edad'], $datos['fecha_expedicion'], $datos['lugar_nacimiento'],
                    $datos['celular_1'], $datos['celular_2'] ?? null, $datos['telefono'] ?? null, $datos['id_rh'],
                    $datos['id_estatura'], $datos['peso_kg'], $datos['id_estado_civil'], $datos['hacer_cuanto'] ?? null,
                    $datos['numero_hijos'] ?? null, $datos['direccion'], $datos['id_ciudad'], $datos['localidad'],
                    $datos['barrio'], $datos['id_estrato'], $datos['correo'], $datos['cargo'] ?? null, 
                    $datos['observacion'] ?? null, $datos['id_cedula']
                ]);

                return [
                    'success' => true,
                    'message' => 'Información personal actualizada exitosamente',
                    'action' => 'updated'
                ];
            } else {
                // Insertar nuevo registro
                $sql = "INSERT INTO informacion_personal (
                    id_cedula, id_tipo_documentos, cedula_expedida, nombres, apellidos, 
                    edad, fecha_expedicion, lugar_nacimiento, celular_1, celular_2, 
                    telefono, id_rh, id_estatura, peso_kg, id_estado_civil, hacer_cuanto, 
                    numero_hijos, direccion, id_ciudad, localidad, barrio, id_estrato, 
                    correo, cargo, observacion, fecha_creacion, fecha_actualizacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta de inserción: " . $this->db->errorInfo()[2]);
                }
                
                $stmt->execute([
                    $datos['id_cedula'], $datos['id_tipo_documentos'], $datos['cedula_expedida'], 
                    $datos['nombres'], $datos['apellidos'], $datos['edad'], $datos['fecha_expedicion'], 
                    $datos['lugar_nacimiento'], $datos['celular_1'], $datos['celular_2'] ?? null, 
                    $datos['telefono'] ?? null, $datos['id_rh'], $datos['id_estatura'], $datos['peso_kg'], 
                    $datos['id_estado_civil'], $datos['hacer_cuanto'] ?? null, $datos['numero_hijos'] ?? null, 
                    $datos['direccion'], $datos['id_ciudad'], $datos['localidad'], $datos['barrio'], 
                    $datos['id_estrato'], $datos['correo'], $datos['cargo'] ?? null, $datos['observacion'] ?? null
                ]);

                return [
                    'success' => true,
                    'message' => 'Información personal guardada exitosamente',
                    'action' => 'created',
                    'id' => $this->db->lastInsertId()
                ];
            }

        } catch (Exception $e) {
            error_log("Error en InformacionPersonalController::guardar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener opciones para los select boxes
     */
    public function obtenerOpciones($tipo) {
        try {
            $queries = [
                'tipo_documentos' => "SELECT id, nombre FROM opc_tipo_documentos ORDER BY nombre",
                'municipios' => "SELECT id_municipio as id, municipio as nombre FROM municipios ORDER BY municipio",
                'rh' => "SELECT id, nombre FROM opc_rh ORDER BY nombre",
                'estaturas' => "SELECT id, nombre FROM opc_estaturas ORDER BY nombre",
                'pesos' => "SELECT id, nombre FROM opc_peso ORDER BY nombre",
                'estado_civil' => "SELECT id, nombre FROM opc_estado_civiles ORDER BY nombre",
                'estratos' => "SELECT id, nombre FROM opc_estratos ORDER BY nombre"
            ];

            if (!isset($queries[$tipo])) {
                throw new Exception("Tipo de opciones no válido: {$tipo}");
            }

            $stmt = $this->db->prepare($queries[$tipo]);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->errorInfo()[2]);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en InformacionPersonalController::obtenerOpciones: " . $e->getMessage());
            throw new Exception("Error al obtener las opciones: " . $e->getMessage());
        }
    }

    /**
     * Validar datos antes de guardar
     */
    public function validarDatos($datos) {
        $errores = [];

        // Validar campos requeridos
        $campos_requeridos = [
            'id_cedula' => 'Número de documento',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'edad' => 'Edad',
            'celular_1' => 'Celular 1',
            'direccion' => 'Dirección',
            'correo' => 'Correo electrónico'
        ];

        foreach ($campos_requeridos as $campo => $nombre) {
            if (empty($datos[$campo])) {
                $errores[] = "El campo {$nombre} es requerido";
            }
        }

        // Validar formato de email
        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        // Validar edad
        if (!empty($datos['edad'])) {
            if (!is_numeric($datos['edad']) || $datos['edad'] < 18 || $datos['edad'] > 120) {
                $errores[] = "La edad debe estar entre 18 y 120 años";
            }
        }

        // Validar formato de teléfonos
        if (!empty($datos['celular_1']) && !preg_match('/^[0-9]{10}$/', $datos['celular_1'])) {
            $errores[] = "El celular 1 debe tener 10 dígitos";
        }

        if (!empty($datos['celular_2']) && !preg_match('/^[0-9]{10}$/', $datos['celular_2'])) {
            $errores[] = "El celular 2 debe tener 10 dígitos";
        }

        if (!empty($datos['telefono']) && !preg_match('/^[0-9]{7}$/', $datos['telefono'])) {
            $errores[] = "El teléfono debe tener 7 dígitos";
        }

        return $errores;
    }

    /**
     * Limpiar y sanitizar datos de entrada
     */
    public function sanitizarDatos($datos) {
        $datos_limpios = [];
        
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $datos_limpios[$clave] = trim(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'));
            } else {
                $datos_limpios[$clave] = $valor;
            }
        }
        
        return $datos_limpios;
    }
} 