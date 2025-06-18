<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;
use Exception;

class RegistroFotograficoController {
    public static function guardarFoto($fotoBase64, $id_cedula) {
        try {
            // Validar datos de entrada
            if (empty($fotoBase64) || empty($id_cedula)) {
                throw new Exception('Datos de entrada inválidos');
            }

            // Definir rutas física y relativa bajo public/images/registro_fotografico/{id_cedula}/
            $baseDir = realpath(__DIR__ . '/../../public/images/registro_fotografico');
            if ($baseDir === false) {
                throw new Exception('Directorio base de fotos no existe o no es accesible');
            }

            $directory = $baseDir . DIRECTORY_SEPARATOR . $id_cedula . DIRECTORY_SEPARATOR;
            $rutaRelativa = "public/images/registro_fotografico/" . $id_cedula . "/";

            // Crear directorio si no existe
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0777, true)) {
                    throw new Exception('No se pudo crear el directorio para la foto');
                }
            }

            // Generar nombre de archivo único
            $nombreArchivo = 'foto_' . time() . '.png';
            $rutaCompleta = $directory . $nombreArchivo;
            $rutaRelativaCompleta = $rutaRelativa . $nombreArchivo;

            // Procesar y guardar la imagen
            $fotoBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
            $data = base64_decode($fotoBase64);
            
            if ($data === false) {
                throw new Exception('La imagen de la foto no es válida');
            }

            if (file_put_contents($rutaCompleta, $data) === false) {
                throw new Exception('Error al guardar la imagen de la foto');
            }

            // Guardar en la base de datos
            $db = Database::getInstance()->getConnection();
            
            // Verificar si ya existe una foto para esta cédula
            $stmt = $db->prepare("SELECT id FROM foto_perfil_autorizacion WHERE id_cedula = ?");
            $stmt->execute([$id_cedula]);
            
            if ($stmt->rowCount() > 0) {
                // Actualizar registro existente
                $stmt = $db->prepare("UPDATE foto_perfil_autorizacion SET ruta = ?, nombre = ? WHERE id_cedula = ?");
                $stmt->execute([$rutaRelativaCompleta, $nombreArchivo, $id_cedula]);
            } else {
                // Insertar nuevo registro
                $stmt = $db->prepare("INSERT INTO foto_perfil_autorizacion (id_cedula, ruta, nombre) VALUES (?, ?, ?)");
                $stmt->execute([$id_cedula, $rutaRelativaCompleta, $nombreArchivo]);
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en RegistroFotograficoController::guardarFoto: " . $e->getMessage());
            return $e->getMessage();
        }
    }
} 