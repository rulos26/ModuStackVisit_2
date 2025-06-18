<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class RegistroFotograficoController {
    public static function guardarFoto($fotoBase64, $id_cedula) {
        // Definir rutas física y relativa bajo public/images/registro_fotografico/{id_cedula}/
        $baseDir = realpath(__DIR__ . '/../../public/images/registro_fotografico');
        if ($baseDir === false) {
            return 'Directorio base de fotos no existe o no es accesible.';
        }
        $directory = $baseDir . DIRECTORY_SEPARATOR . $id_cedula . DIRECTORY_SEPARATOR;
        $rutaRelativa = "public/images/registro_fotografico/" . $id_cedula . "/";
        // Crear directorio si no existe
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                return 'No se pudo crear el directorio para la foto.';
            }
        }
        // Generar nombre de archivo único
        $nombreArchivo = 'foto_' . time() . '.png';
        $rutaCompleta = $directory . $nombreArchivo;
        $rutaRelativaCompleta = $rutaRelativa . $nombreArchivo;
        // Guardar la imagen
        $fotoBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
        $data = base64_decode($fotoBase64);
        if ($data === false) {
            return 'La imagen de la foto no es válida.';
        }
        if (file_put_contents($rutaCompleta, $data) === false) {
            return 'Error al guardar la imagen de la foto.';
        }
        // Guardar en la base de datos
        $db = Database::getInstance()->getConnection();
        try {
            $sql = "INSERT INTO foto_perfil_autorizacion (id_cedula, ruta, nombre) VALUES (:id_cedula, :ruta, :nombre)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':ruta', $rutaRelativaCompleta);
            $stmt->bindParam(':nombre', $nombreArchivo);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return 'Error al guardar la foto en la base de datos: ' . htmlspecialchars($e->getMessage());
        }
    }
} 