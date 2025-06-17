<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class CartaAutorizacionController {
    public static function guardarAutorizacion($cedula, $nombres, $direccion, $localidad, $barrio, $telefono, $celular, $fecha, $autorizacion, $correo) {
        // Debug: log de los datos recibidos
        error_log('DEBUG CartaAutorizacionController: Datos recibidos: ' . json_encode([
            'cedula' => $cedula,
            'nombres' => $nombres,
            'direccion' => $direccion,
            'localidad' => $localidad,
            'barrio' => $barrio,
            'telefono' => $telefono,
            'celular' => $celular,
            'fecha' => $fecha,
            'autorizacion' => $autorizacion,
            'correo' => $correo
        ]));
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
            $result = $stmt->execute();
            error_log('DEBUG CartaAutorizacionController: Resultado execute: ' . var_export($result, true));
            if ($result) {
                error_log('DEBUG CartaAutorizacionController: Insert exitoso.');
                return true;
            } else {
                error_log('DEBUG CartaAutorizacionController: Insert fallido.');
                return 'Error al guardar la autorización: No se pudo ejecutar el insert.';
            }
        } catch (PDOException $e) {
            error_log('DEBUG CartaAutorizacionController: Excepción PDO: ' . $e->getMessage());
            return 'Error al guardar la autorización: ' . htmlspecialchars($e->getMessage());
        }
    }
} 