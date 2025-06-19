<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;
use PDO;

class UbicacionController {
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

    public function guardar($id_cedula, $latitud, $longitud) {
        try {
            // Validaciones básicas
            if (empty($id_cedula) || empty($latitud) || empty($longitud)) {
                throw new Exception("Todos los campos son requeridos");
            }
            if (!is_numeric($latitud) || !is_numeric($longitud)) {
                throw new Exception("Las coordenadas deben ser valores numéricos");
            }

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE ubicacion SET latitud = :latitud, longitud = :longitud WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':latitud', $latitud);
                $stmt->bindParam(':longitud', $longitud);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO ubicacion (id_cedula, latitud, longitud) VALUES (:id_cedula, :latitud, :longitud)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':latitud', $latitud);
                $stmt->bindParam(':longitud', $longitud);
                $ok = $stmt->execute();
            }

            // Generar y guardar mapa
            $ruta_mapa = $this->generarMapa($id_cedula, $latitud, $longitud);

            // Guardar registro de mapa
            $sql_foto = "INSERT INTO ubicacion_foto (id_cedula, ruta, nombre) VALUES (:id_cedula, :ruta, :nombre)";
            $stmt_foto = $this->db->prepare($sql_foto);
            $ruta = dirname($ruta_mapa);
            $nombre = basename($ruta_mapa);
            $stmt_foto->bindParam(':id_cedula', $id_cedula);
            $stmt_foto->bindParam(':ruta', $ruta);
            $stmt_foto->bindParam(':nombre', $nombre);
            $stmt_foto->execute();

            if ($ok) {
                return [
                    'success' => true,
                    'message' => 'Ubicación guardada exitosamente',
                    'data' => [
                        'ruta_mapa' => $ruta_mapa
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al guardar la ubicación.'
                ];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM ubicacion WHERE id_cedula = :id_cedula ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    private function generarMapa($id_cedula, $latitud, $longitud) {
        try {
            $token = 'pk.eyJ1IjoianVhbmRpYXo4NzAxMjYiLCJhIjoiY21hbWxueHJ1MGtlMTJrb3N3bWVwamowNSJ9.5Gsp0Q69b1z3oQijt-Aw2Q';
            $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({$longitud},{$latitud})/{$longitud},{$latitud},15,0/600x300?access_token={$token}";
            $directorio_destino = __DIR__ . "/../informe/img/ubicacion_foto/{$id_cedula}/";
            if (!file_exists($directorio_destino)) {
                if (!mkdir($directorio_destino, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para el mapa");
                }
            }
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
} 