<?php
namespace App\Controllers;

class CamaraComercioController {
    private static $instance = null;
    private $db;

    private function __construct() {
        require_once __DIR__ . '/../../../../../conn/conexion.php';
        global $mysqli;
        $this->db = $mysqli;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sanitizarDatos($datos) {
        foreach ($datos as $k => $v) {
            $datos[$k] = htmlspecialchars(trim($v));
        }
        return $datos;
    }

    public function validarDatos($datos) {
        $errores = [];
        if (empty($datos['tiene_camara'])) {
            $errores[] = 'Debe seleccionar si tiene cámara de comercio.';
        }
        // Si tiene_camara es Sí, validar los campos adicionales
        if ($datos['tiene_camara'] === 'Si') {
            if (empty($datos['nombre'])) $errores[] = 'El nombre de la empresa es obligatorio.';
            if (empty($datos['razon'])) $errores[] = 'La razón social es obligatoria.';
            if (empty($datos['actividad'])) $errores[] = 'La actividad es obligatoria.';
        }
        return $errores;
    }

    public function guardar($datos) {
        $id_cedula = $_SESSION['id_cedula'];
        $tiene_camara = $datos['tiene_camara'];
        $nombre = $datos['nombre'] ?? '';
        $razon = $datos['razon'] ?? '';
        $actividad = $datos['actividad'] ?? '';
        $observacion = $datos['observacion'] ?? '';
        // Verificar si ya existe
        $sql_check = "SELECT COUNT(*) FROM camara_comercio WHERE id_cedula = ?";
        $stmt = $this->db->prepare($sql_check);
        $stmt->bind_param('s', $id_cedula);
        $count = 0;
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            // Update
            $sql = "UPDATE camara_comercio SET tiene_camara=?, nombre=?, razon=?, actividad=?, observacion=? WHERE id_cedula=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssssss', $tiene_camara, $nombre, $razon, $actividad, $observacion, $id_cedula);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                return ['success'=>true, 'message'=>'Información actualizada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al actualizar la información.'];
            }
        } else {
            // Insert
            $sql = "INSERT INTO camara_comercio (id_cedula, tiene_camara, nombre, razon, actividad, observacion) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssssss', $id_cedula, $tiene_camara, $nombre, $razon, $actividad, $observacion);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                return ['success'=>true, 'message'=>'Información guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información.'];
            }
        }
    }

    public function obtenerPorCedula($id_cedula) {
        $sql = "SELECT * FROM camara_comercio WHERE id_cedula = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $id_cedula);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = $res->fetch_assoc();
        $stmt->close();
        return $data;
    }
} 