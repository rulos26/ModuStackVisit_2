<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;
use PDO;

class RegistroFotosController {
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

    public function sanitizarDatos($datos) {
        $sanitizados = [];
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $sanitizados[$clave] = trim(strip_tags($valor));
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        return $sanitizados;
    }

    public function validarDatos($datos) {
        $errores = [];
        
        // Validar tipo de foto
        if (empty($datos['tipo']) || !is_numeric($datos['tipo']) || $datos['tipo'] < 1 || $datos['tipo'] > 8) {
            $errores[] = 'Debe seleccionar un tipo de foto válido.';
        }
        
        // Validar archivo
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $errores[] = 'Debe seleccionar una imagen válida.';
        } else {
            $archivo = $_FILES['foto'];
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $tamano_maximo = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($archivo['type'], $tipos_permitidos)) {
                $errores[] = 'El archivo debe ser una imagen (JPG, PNG, GIF).';
            }
            
            if ($archivo['size'] > $tamano_maximo) {
                $errores[] = 'El archivo no puede superar los 5MB.';
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $tipo = $datos['tipo'];
            
            // Verificar si ya existe una foto de este tipo
            $foto_existente = $this->obtenerPorTipo($id_cedula, $tipo);
            
            // Procesar y guardar la imagen
            $archivo = $_FILES['foto'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = 'foto_' . $tipo . '_' . $id_cedula . '_' . time() . '.' . $extension;
            
            // Crear directorio si no existe
            $directorio_destino = __DIR__ . "/../../../../../public/images/evidencia_fotografica/{$id_cedula}/";
            if (!file_exists($directorio_destino)) {
                if (!mkdir($directorio_destino, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para las fotos");
                }
            }
            
            $ruta_completa = $directorio_destino . $nombre_archivo;
            
            // Si existe una foto anterior, eliminarla del servidor
            if ($foto_existente) {
                $ruta_foto_anterior = __DIR__ . "/../../../../../public/images/evidencia_fotografica/{$id_cedula}/" . $foto_existente['nombre'];
                if (file_exists($ruta_foto_anterior)) {
                    unlink($ruta_foto_anterior);
                }
            }
            
            // Mover archivo
            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                throw new Exception("Error al guardar la imagen");
            }
            
            // Guardar en base de datos
            $ruta_relativa = "public/images/evidencia_fotografica/{$id_cedula}/";
            
            if ($foto_existente) {
                // Actualizar registro existente
                $sql = "UPDATE evidencia_fotografica SET ruta = :ruta, nombre = :nombre WHERE id_cedula = :id_cedula AND tipo = :tipo";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':ruta', $ruta_relativa);
                $stmt->bindParam(':nombre', $nombre_archivo);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':tipo', $tipo);
                $ok = $stmt->execute();
                $mensaje = 'Foto actualizada exitosamente.';
            } else {
                // Insertar nuevo registro
                $sql = "INSERT INTO evidencia_fotografica (id_cedula, tipo, ruta, nombre) VALUES (:id_cedula, :tipo, :ruta, :nombre)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':ruta', $ruta_relativa);
                $stmt->bindParam(':nombre', $nombre_archivo);
                $ok = $stmt->execute();
                $mensaje = 'Foto guardada exitosamente.';
            }
            
            if ($ok) {
                return ['success' => true, 'message' => $mensaje];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la foto en la base de datos.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM evidencia_fotografica WHERE id_cedula = :id_cedula ORDER BY tipo";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerPorTipo($id_cedula, $tipo) {
        try {
            $sql = "SELECT * FROM evidencia_fotografica WHERE id_cedula = :id_cedula AND tipo = :tipo LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerTiposFotos() {
        return [
            1 => 'Foto de Cuerpo Entero',
            2 => 'Foto del medio cuerpo',
            3 => 'Foto la Fachada',
            4 => 'Foto de Sala Plano General',
            5 => 'Foto de Habitación',
            6 => 'Foto de la Nomenclatura',
            7 => 'Foto de la Cédula por Lado y Lado',
            8 => 'Foto de la Familia'
        ];
    }

    public function todasLasFotosCompletas($id_cedula) {
        try {
            $sql = "SELECT COUNT(*) as total FROM evidencia_fotografica WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] == 8; // Deben estar las 8 fotos
        } catch (PDOException $e) {
            return false;
        }
    }
} 