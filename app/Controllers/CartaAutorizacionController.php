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
                
                // Guardar cédula y datos en tabla evaluados
                self::guardarEnEvaluados($cedula, $nombres, $direccion, $localidad, $barrio, $telefono, $celular, $correo);
                
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
    
    /**
     * Guardar cédula y datos en tabla evaluados
     * @param string $cedula
     * @param string $nombres
     * @param string $direccion
     * @param string $localidad
     * @param string $barrio
     * @param string $telefono
     * @param string $celular
     * @param string $correo
     */
    private static function guardarEnEvaluados($cedula, $nombres, $direccion, $localidad, $barrio, $telefono, $celular, $correo) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar si ya existe la cédula en evaluados
            $stmt_check = $db->prepare('SELECT id FROM evaluados WHERE id_cedula = :cedula LIMIT 1');
            $stmt_check->bindParam(':cedula', $cedula);
            $stmt_check->execute();
            
            if ($stmt_check->fetch()) {
                error_log('DEBUG CartaAutorizacionController: Cédula ya existe en evaluados: ' . $cedula);
                echo '<pre style="background:#222;color:#ff0;padding:1em;">DEBUG CartaAutorizacionController: Cédula ya existe en evaluados: ' . $cedula . '</pre>';
                return; // Ya existe, no hacer nada
            }
            
            // Insertar nueva cédula y datos en evaluados
            $stmt = $db->prepare('INSERT INTO evaluados (id_cedula, nombres, direccion, localidad, barrio, telefono, celular_1, correo) VALUES (:cedula, :nombres, :direccion, :localidad, :barrio, :telefono, :celular, :correo)');
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombres', $nombres);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':localidad', $localidad);
            $stmt->bindParam(':barrio', $barrio);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':celular', $celular);
            $stmt->bindParam(':correo', $correo);
            $result = $stmt->execute();
            
            if ($result) {
                error_log('DEBUG CartaAutorizacionController: Cédula y datos guardados en evaluados: ' . $cedula);
                echo '<pre style="background:#222;color:#0f0;padding:1em;">DEBUG CartaAutorizacionController: Cédula y datos guardados en evaluados: ' . $cedula . '</pre>';
            } else {
                error_log('DEBUG CartaAutorizacionController: Error al guardar cédula y datos en evaluados: ' . $cedula);
                echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG CartaAutorizacionController: Error al guardar cédula y datos en evaluados: ' . $cedula . '</pre>';
            }
            
        } catch (PDOException $e) {
            error_log('DEBUG CartaAutorizacionController: Error PDO al guardar en evaluados: ' . $e->getMessage());
            echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG CartaAutorizacionController: Error PDO al guardar en evaluados: ' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
    }
} 