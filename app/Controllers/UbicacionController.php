<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use Exception;
use PDO;
use App\Database\Database;

class UbicacionController {
    private static $instance = null;
    private $db;

    private function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
            if (!$this->db instanceof PDO) {
                throw new Exception("Error al obtener la conexión a la base de datos");
            }
        } catch (Exception $e) {
            error_log("Error en UbicacionController::__construct: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function guardarUbicacion($id_cedula, $latitud, $longitud) {
        try {
            // Validar datos
            if (empty($id_cedula) || empty($latitud) || empty($longitud)) {
                throw new Exception("Todos los campos son requeridos");
            }

            // Validar formato de coordenadas
            if (!is_numeric($latitud) || !is_numeric($longitud)) {
                throw new Exception("Las coordenadas deben ser valores numéricos");
            }

            // Generar y guardar mapa
            $ruta_mapa = $this->generarMapa($id_cedula, $latitud, $longitud);

            // Verificar si ya existe un registro para esta cédula
            $stmt_check = $this->db->prepare("SELECT id FROM ubicacion_autorizacion WHERE id_cedula = ?");
            if (!$stmt_check) {
                throw new Exception("Error al preparar la consulta de verificación: " . $this->db->errorInfo()[2]);
            }
            
            $stmt_check->execute([$id_cedula]);
            $existe = $stmt_check->fetch();

            if ($existe) {
                // Actualizar registro existente
                $stmt = $this->db->prepare("UPDATE ubicacion_autorizacion SET ruta = ?, nombre = ? WHERE id_cedula = ?");
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta de actualización: " . $this->db->errorInfo()[2]);
                }
                
                $stmt->execute([dirname($ruta_mapa), basename($ruta_mapa), $id_cedula]);
            } else {
                // Insertar nuevo registro
                $stmt = $this->db->prepare("INSERT INTO ubicacion_autorizacion (id_cedula, ruta, nombre) VALUES (?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta de inserción: " . $this->db->errorInfo()[2]);
                }
                
                $stmt->execute([$id_cedula, dirname($ruta_mapa), basename($ruta_mapa)]);
            }

            return [
                'success' => true,
                'message' => 'Ubicación guardada exitosamente',
                'data' => [
                    'ruta_mapa' => $ruta_mapa
                ]
            ];

        } catch (Exception $e) {
            error_log("Error en UbicacionController::guardarUbicacion: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function generarMapa($id_cedula, $latitud, $longitud) {
        try {
            // Token de Mapbox (debería estar en archivo de configuración)
            $token = 'pk.eyJ1IjoianVhbmRpYXo4NzAxMjYiLCJhIjoiY21hbWxueHJ1MGtlMTJrb3N3bWVwamowNSJ9.5Gsp0Q69b1z3oQijt-Aw2Q';
            
            // URL para obtener el mapa estático
            $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({$longitud},{$latitud})/{$longitud},{$latitud},15,0/600x300?access_token={$token}";

            // Crear directorio si no existe - RUTA CORREGIDA
            $directorio_destino = __DIR__ . "/../../public/images/ubicacion_autorizacion/{$id_cedula}/";
            if (!file_exists($directorio_destino)) {
                if (!mkdir($directorio_destino, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para el mapa");
                }
            }

            // Descargar y guardar imagen
            $imagen = file_get_contents($url);
            if ($imagen === false) {
                throw new Exception("Error al obtener la imagen del mapa");
            }

            $nombre_archivo = 'mapa_ubicacion_' . time() . '.jpg';
            $ruta_completa = $directorio_destino . $nombre_archivo;

            if (file_put_contents($ruta_completa, $imagen) === false) {
                throw new Exception("Error al guardar la imagen del mapa");
            }

            return $ruta_completa;

        } catch (Exception $e) {
            error_log("Error en UbicacionController::generarMapa: " . $e->getMessage());
            throw new Exception("Error al generar el mapa: " . $e->getMessage());
        }
    }

    public function obtenerUbicacion($id_cedula) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ubicacion_autorizacion WHERE id_cedula = ? ORDER BY id DESC LIMIT 1");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->errorInfo()[2]);
            }
            
            $stmt->execute([$id_cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en UbicacionController::obtenerUbicacion: " . $e->getMessage());
            throw new Exception("Error al obtener la ubicación: " . $e->getMessage());
        }
    }

    public function verificarUbicacion($id_cedula) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM ubicacion_autorizacion WHERE id_cedula = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->errorInfo()[2]);
            }
            
            $stmt->execute([$id_cedula]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch (Exception $e) {
            error_log("Error en UbicacionController::verificarUbicacion: " . $e->getMessage());
            return false;
        }
    }
} 