<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class CamaraComercioController {
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
        
        // El único campo obligatorio es tiene_camara
        if (empty($datos['tiene_camara'])) {
            $errores[] = 'Debe seleccionar si tiene cámara de comercio.';
        }
        
        // Si tiene_camara es Sí, los campos adicionales son opcionales pero si se llenan deben ser válidos
        if ($datos['tiene_camara'] === 'Si') {
            // Validar nombre solo si se proporciona
            if (!empty($datos['nombre']) && strlen(trim($datos['nombre'])) < 2) {
                $errores[] = 'El nombre de la empresa debe tener al menos 2 caracteres.';
            }
            
            // Validar razón social solo si se proporciona
            if (!empty($datos['razon']) && strlen(trim($datos['razon'])) < 2) {
                $errores[] = 'La razón social debe tener al menos 2 caracteres.';
            }
            
            // Validar actividad solo si se proporciona
            if (!empty($datos['actividad']) && strlen(trim($datos['actividad'])) < 2) {
                $errores[] = 'La actividad debe tener al menos 2 caracteres.';
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $tiene_camara = $datos['tiene_camara'];
            $nombre = $datos['nombre'] ?? '';
            $razon = $datos['razon'] ?? '';
            $actividad = $datos['actividad'] ?? '';
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE camara_comercio SET tiene_camara = :tiene_camara, nombre = :nombre, razon = :razon, actividad = :actividad, observacion = :observacion WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':tiene_camara', $tiene_camara);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':razon', $razon);
                $stmt->bindParam(':actividad', $actividad);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
                if ($ok) {
                    return ['success'=>true, 'message'=>'Información actualizada exitosamente.'];
                } else {
                    return ['success'=>false, 'message'=>'Error al actualizar la información.'];
                }
            } else {
                $sql = "INSERT INTO camara_comercio (id_cedula, tiene_camara, nombre, razon, actividad, observacion) VALUES (:id_cedula, :tiene_camara, :nombre, :razon, :actividad, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':tiene_camara', $tiene_camara);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':razon', $razon);
                $stmt->bindParam(':actividad', $actividad);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
                if ($ok) {
                    return ['success'=>true, 'message'=>'Información guardada exitosamente.'];
                } else {
                    return ['success'=>false, 'message'=>'Error al guardar la información.'];
                }
            }
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM camara_comercio WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
} 