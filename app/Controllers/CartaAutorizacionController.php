<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class CartaAutorizacionController {
    public static function guardarAutorizacion($cedula, $nombres, $direccion, $localidad, $barrio, $telefono, $celular, $fecha, $autorizacion, $correo) {
        // Debug: log y echo de los datos recibidos
        $debugData = [
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
        ];
        error_log('DEBUG CartaAutorizacionController: Datos recibidos: ' . json_encode($debugData));
        echo '<pre style="background:#222;color:#0f0;padding:1em;">DEBUG CartaAutorizacionController: Datos recibidos: ' . print_r($debugData, true) . '</pre>';
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
            echo '<pre style="background:#222;color:#0ff;padding:1em;">DEBUG CartaAutorizacionController: Resultado execute: ' . var_export($result, true) . '</pre>';
            if ($result) {
                error_log('DEBUG CartaAutorizacionController: Insert exitoso.');
                echo '<pre style="background:#222;color:#0f0;padding:1em;">DEBUG CartaAutorizacionController: Insert exitoso.</pre>';
                return true;
            } else {
                error_log('DEBUG CartaAutorizacionController: Insert fallido.');
                echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG CartaAutorizacionController: Insert fallido.</pre>';
                return 'Error al guardar la autorización: No se pudo ejecutar el insert.';
            }
        } catch (PDOException $e) {
            error_log('DEBUG CartaAutorizacionController: Excepción PDO: ' . $e->getMessage());
            echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG CartaAutorizacionController: Excepción PDO: ' . htmlspecialchars($e->getMessage()) . '</pre>';
            return 'Error al guardar la autorización: ' . htmlspecialchars($e->getMessage());
        }
    }
} 