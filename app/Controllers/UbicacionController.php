<?php
namespace App\Controllers;

use Exception;
use PDO;

class UbicacionController {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = require __DIR__ . '/../Database/Database.php';
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

            // Insertar ubicación
            $stmt = $this->db->prepare("INSERT INTO ubicacion (id_cedula, latitud, longitud) VALUES (?, ?, ?)");
            $stmt->execute([$id_cedula, $latitud, $longitud]);
            $id_ubicacion = $this->db->lastInsertId();

            // Generar y guardar mapa
            $ruta_mapa = $this->generarMapa($id_cedula, $latitud, $longitud);

            // Guardar registro de mapa
            $stmt = $this->db->prepare("INSERT INTO ubicacion_autorizacion (id_cedula, ruta, nombre) VALUES (?, ?, ?)");
            $stmt->execute([$id_cedula, dirname($ruta_mapa), basename($ruta_mapa)]);

            return [
                'success' => true,
                'message' => 'Ubicación guardada exitosamente',
                'data' => [
                    'id_ubicacion' => $id_ubicacion,
                    'ruta_mapa' => $ruta_mapa
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function generarMapa($id_cedula, $latitud, $longitud) {
        try {
            // Token de Mapbox
            $token = 'pk.eyJ1IjoianVhbmRpYXo4NzAxMjYiLCJhIjoiY21hbWxueHJ1MGtlMTJrb3N3bWVwamowNSJ9.5Gsp0Q69b1z3oQijt-Aw2Q';
            
            // URL para obtener el mapa estático
            $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({$longitud},{$latitud})/{$longitud},{$latitud},15,0/600x300?access_token={$token}";

            // Crear directorio si no existe
            $directorio_destino = __DIR__ . "/../../public/images/ubicacion_autorizacion/{$id_cedula}/";
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
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
            throw new Exception("Error al generar el mapa: " . $e->getMessage());
        }
    }

    public function obtenerUbicacion($id_cedula) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ubicacion WHERE id_cedula = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$id_cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener la ubicación: " . $e->getMessage());
        }
    }
} 