<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class ConceptoFinalEvaluadorController {
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
        
        // Validar campos requeridos
        $campos_requeridos = [
            'actitud' => 'Actitud del evaluado y su grupo familiar',
            'condiciones_vivienda' => 'Condiciones de Vivienda',
            'dinamica_familiar' => 'Dinámica Familiar',
            'condiciones_economicas' => 'Condiciones Socio Económicas',
            'condiciones_academicas' => 'Condiciones Académicas',
            'evaluacion_experiencia_laboral' => 'Evaluación Experiencia Laboral',
            'observaciones' => 'Observaciones',
            'id_concepto_final' => 'Concepto Final de la Visita',
            'nombre_evaluador' => 'Nombre del Evaluador',
            'id_concepto_seguridad' => 'Concepto de Seguridad'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                $errores[] = "El campo '$nombre' es requerido.";
            }
        }
        
        // Validar longitud mínima de campos de texto
        $campos_texto = [
            'actitud' => ['Actitud del evaluado y su grupo familiar', 10],
            'condiciones_vivienda' => ['Condiciones de Vivienda', 10],
            'dinamica_familiar' => ['Dinámica Familiar', 10],
            'condiciones_economicas' => ['Condiciones Socio Económicas', 10],
            'condiciones_academicas' => ['Condiciones Académicas', 10],
            'evaluacion_experiencia_laboral' => ['Evaluación Experiencia Laboral', 10],
            'observaciones' => ['Observaciones', 15],
            'nombre_evaluador' => ['Nombre del Evaluador', 5]
        ];
        
        foreach ($campos_texto as $campo => $info) {
            $nombre = $info[0];
            $min_length = $info[1];
            
            if (isset($datos[$campo]) && strlen(trim($datos[$campo])) < $min_length) {
                $errores[] = "El campo '$nombre' debe tener al menos $min_length caracteres.";
            }
        }
        
        // Validar que los conceptos sean números válidos
        $campos_concepto = [
            'id_concepto_final' => 'Concepto Final de la Visita',
            'id_concepto_seguridad' => 'Concepto de Seguridad'
        ];
        
        foreach ($campos_concepto as $campo => $nombre) {
            if (isset($datos[$campo]) && (!is_numeric($datos[$campo]) || $datos[$campo] < 1)) {
                $errores[] = "Debe seleccionar una opción válida para '$nombre'.";
            }
        }
        
        // Validar valores específicos para concepto de seguridad
        if (isset($datos['id_concepto_seguridad']) && !empty($datos['id_concepto_seguridad'])) {
            $valores_validos = [1, 2, 3]; // Aptos, No Apto, Apto con reserva
            if (!in_array((int)$datos['id_concepto_seguridad'], $valores_validos)) {
                $errores[] = "El concepto de seguridad debe ser una opción válida (Aptos, No Apto, o Apto con reserva).";
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM concepto_final_evaluador WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO concepto_final_evaluador (
                id_cedula, actitud, condiciones_vivienda, dinamica_familiar, 
                condiciones_economicas, condiciones_academicas, evaluacion_experiencia_laboral, 
                observaciones, id_concepto_final, nombre_evaluador, id_concepto_seguridad
            ) VALUES (
                :id_cedula, :actitud, :condiciones_vivienda, :dinamica_familiar,
                :condiciones_economicas, :condiciones_academicas, :evaluacion_experiencia_laboral,
                :observaciones, :id_concepto_final, :nombre_evaluador, :id_concepto_seguridad
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':actitud', $datos['actitud']);
            $stmt->bindParam(':condiciones_vivienda', $datos['condiciones_vivienda']);
            $stmt->bindParam(':dinamica_familiar', $datos['dinamica_familiar']);
            $stmt->bindParam(':condiciones_economicas', $datos['condiciones_economicas']);
            $stmt->bindParam(':condiciones_academicas', $datos['condiciones_academicas']);
            $stmt->bindParam(':evaluacion_experiencia_laboral', $datos['evaluacion_experiencia_laboral']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            $stmt->bindParam(':id_concepto_final', $datos['id_concepto_final']);
            $stmt->bindParam(':nombre_evaluador', $datos['nombre_evaluador']);
            $stmt->bindParam(':id_concepto_seguridad', $datos['id_concepto_seguridad']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Concepto final del evaluador guardado exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar el concepto final del evaluador.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM concepto_final_evaluador WHERE id_cedula = :id_cedula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function obtenerConceptosFinales() {
        try {
            $sql = "SELECT id, nombre FROM opc_estados ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerConceptosSeguridad() {
        try {
            $sql = "SELECT id, nombre FROM opc_concepto_seguridad ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
} 