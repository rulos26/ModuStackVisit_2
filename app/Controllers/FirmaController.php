<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class FirmaController {
    public static function guardarFirma($firmaBase64, $id_cedula) {
        // Crear directorio si no existe
        $directory = __DIR__ . "/../../informe/img/firma/" . $id_cedula . "/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        // Generar nombre de archivo único
        $nombreArchivo = 'firma_' . time() . '.png';
        $rutaCompleta = $directory . $nombreArchivo;
        $rutaRelativa = "informe/img/firma/" . $id_cedula . "/" . $nombreArchivo;
        // Guardar la imagen
        $firmaBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $firmaBase64);
        $data = base64_decode($firmaBase64);
        if (file_put_contents($rutaCompleta, $data) === false) {
            return 'Error al guardar la imagen de la firma.';
        }
        // Guardar en la base de datos
        $db = Database::getInstance()->getConnection();
        try {
            $sql = "INSERT INTO firmas (id_cedula, ruta, nombre) VALUES (:id_cedula, :ruta, :nombre)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':ruta', $rutaRelativa);
            $stmt->bindParam(':nombre', $nombreArchivo);
            $stmt->execute();
            // Redirigir a registro fotográfico
            header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/registro_fotografico');
            exit();
        } catch (PDOException $e) {
            return 'Error al guardar la firma en la base de datos: ' . htmlspecialchars($e->getMessage());
        }
    }
} 