<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class InformacionJudicialController {
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
            'denuncias_opc' => 'Denuncias',
            'procesos_judiciales_opc' => 'Procesos Judiciales',
            'preso_opc' => 'Privación de Libertad',
            'familia_detenido_opc' => 'Familia Detenida',
            'centros_penitenciarios_opc' => 'Centros Penitenciarios',
            'revi_fiscal' => 'Revisión Fiscal'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                $errores[] = "El campo '$nombre' es requerido.";
            }
        }
        
        // Validar que las opciones sean números válidos
        $campos_opcion = [
            'denuncias_opc' => 'Denuncias',
            'procesos_judiciales_opc' => 'Procesos Judiciales',
            'preso_opc' => 'Privación de Libertad',
            'familia_detenido_opc' => 'Familia Detenida',
            'centros_penitenciarios_opc' => 'Centros Penitenciarios'
        ];
        
        foreach ($campos_opcion as $campo => $nombre) {
            if (isset($datos[$campo]) && (!is_numeric($datos[$campo]) || $datos[$campo] < 1)) {
                $errores[] = "Debe seleccionar una opción válida para '$nombre'.";
            }
        }
        
        // Validar descripciones cuando la opción es "Sí" (valor 2)
        $descripciones_requeridas = [
            'denuncias_opc' => ['denuncias_desc', 'Denuncias'],
            'procesos_judiciales_opc' => ['procesos_judiciales_desc', 'Procesos Judiciales'],
            'preso_opc' => ['preso_desc', 'Privación de Libertad'],
            'familia_detenido_opc' => ['familia_detenido_desc', 'Familia Detenida'],
            'centros_penitenciarios_opc' => ['centros_penitenciarios_desc', 'Centros Penitenciarios']
        ];
        
        foreach ($descripciones_requeridas as $opcion_campo => $info) {
            $desc_campo = $info[0];
            $nombre = $info[1];
            
            if (isset($datos[$opcion_campo]) && $datos[$opcion_campo] == '2') {
                if (!isset($datos[$desc_campo]) || empty(trim($datos[$desc_campo]))) {
                    $errores[] = "Debe proporcionar una descripción para '$nombre'.";
                } elseif (strlen(trim($datos[$desc_campo])) < 10) {
                    $errores[] = "La descripción de '$nombre' debe tener al menos 10 caracteres.";
                }
            }
        }
        
        // Validar revisión fiscal
        if (isset($datos['revi_fiscal']) && strlen(trim($datos['revi_fiscal'])) < 50) {
            $errores[] = "La revisión fiscal debe tener al menos 50 caracteres.";
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            
            // Primero eliminar registros existentes para esta cédula
            $sql_delete = "DELETE FROM informacion_judicial WHERE id_cedula = :id_cedula";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cedula', $id_cedula);
            $stmt_delete->execute();
            
            // Preparar valores por defecto para descripciones
            $denuncias_desc = (!empty($datos['denuncias_desc'])) ? $datos['denuncias_desc'] : 'N/A';
            $procesos_judiciales_desc = (!empty($datos['procesos_judiciales_desc'])) ? $datos['procesos_judiciales_desc'] : 'N/A';
            $preso_desc = (!empty($datos['preso_desc'])) ? $datos['preso_desc'] : 'N/A';
            $familia_detenido_desc = (!empty($datos['familia_detenido_desc'])) ? $datos['familia_detenido_desc'] : 'N/A';
            $centros_penitenciarios_desc = (!empty($datos['centros_penitenciarios_desc'])) ? $datos['centros_penitenciarios_desc'] : 'N/A';
            
            // Insertar el nuevo registro
            $sql = "INSERT INTO informacion_judicial (
                id_cedula, denuncias_opc, denuncias_desc, procesos_judiciales_opc, 
                procesos_judiciales_desc, preso_opc, preso_desc, familia_detenido_opc, 
                familia_detenido_desc, centros_penitenciarios_opc, centros_penitenciarios_desc, revi_fiscal
            ) VALUES (
                :id_cedula, :denuncias_opc, :denuncias_desc, :procesos_judiciales_opc,
                :procesos_judiciales_desc, :preso_opc, :preso_desc, :familia_detenido_opc,
                :familia_detenido_desc, :centros_penitenciarios_opc, :centros_penitenciarios_desc, :revi_fiscal
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':denuncias_opc', $datos['denuncias_opc']);
            $stmt->bindParam(':denuncias_desc', $denuncias_desc);
            $stmt->bindParam(':procesos_judiciales_opc', $datos['procesos_judiciales_opc']);
            $stmt->bindParam(':procesos_judiciales_desc', $procesos_judiciales_desc);
            $stmt->bindParam(':preso_opc', $datos['preso_opc']);
            $stmt->bindParam(':preso_desc', $preso_desc);
            $stmt->bindParam(':familia_detenido_opc', $datos['familia_detenido_opc']);
            $stmt->bindParam(':familia_detenido_desc', $familia_detenido_desc);
            $stmt->bindParam(':centros_penitenciarios_opc', $datos['centros_penitenciarios_opc']);
            $stmt->bindParam(':centros_penitenciarios_desc', $centros_penitenciarios_desc);
            $stmt->bindParam(':revi_fiscal', $datos['revi_fiscal']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Información judicial guardada exitosamente.'
                ];
            } else {
                return ['success' => false, 'message' => 'No se pudo guardar la información judicial.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM informacion_judicial WHERE id_cedula = :id_cedula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function obtenerOpciones() {
        try {
            $sql = "SELECT * FROM opc_parametro ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
} 