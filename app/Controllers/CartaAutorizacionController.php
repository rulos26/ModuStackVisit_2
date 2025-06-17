<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class CartaAutorizacionController {
    public static function guardarAutorizacion($cedula, $nombres, $direccion, $localidad, $barrio, $telefono, $celular, $fecha, $autorizacion, $correo) {
        $db = Database::getInstance()->getConnection();
        try {
            $sql = "INSERT INTO `autorizaciones`(`id`, `cedula`, `nombres`, `direccion`, `localidad`, `barrio`, `telefono`, `celular`, `fecha`, `autorizacion`, `correo`) VALUES (NULL, :cedula, :nombres, :direccion, :localidad, :barrio, :telefono, :celular, :fecha, :autorizacion, :correo)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombres', $nombres);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':localidad', $localidad);
            $stmt->bindParam(':barrio', $barrio);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':celular', $celular);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':autorizacion', $autorizacion);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            return true;
            header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/firma/firma.php');
        } catch (PDOException $e) {
            return 'Error al guardar la autorización: ' . htmlspecialchars($e->getMessage());
        }
    }
} 