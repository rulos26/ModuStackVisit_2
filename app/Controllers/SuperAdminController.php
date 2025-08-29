<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class SuperAdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener estadísticas generales del sistema
     */
    public function getEstadisticasGenerales() {
        try {
            $stats = [];
            
            // Total de usuarios por rol
            $stmt = $this->db->prepare("
                SELECT rol, COUNT(*) as total 
                FROM usuarios 
                GROUP BY rol
            ");
            $stmt->execute();
            $stats['usuarios_por_rol'] = $stmt->fetchAll();

            // Total de evaluaciones
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM evaluados
            ");
            $stmt->execute();
            $stats['total_evaluaciones'] = $stmt->fetch()->total;

            // Total de cartas de autorización
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM carta_autorizacion
            ");
            $stmt->execute();
            $stats['total_cartas'] = $stmt->fetch()->total;

            // Evaluaciones por mes (últimos 6 meses)
            $stmt = $this->db->prepare("
                SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(*) as total
                FROM evaluados 
                WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                ORDER BY mes DESC
            ");
            $stmt->execute();
            $stats['evaluaciones_por_mes'] = $stmt->fetchAll();

            return $stats;
        } catch (PDOException $e) {
            error_log("Error en SuperAdminController::getEstadisticasGenerales: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gestionar usuarios del sistema
     */
    public function gestionarUsuarios($accion, $datos = []) {
        try {
            switch ($accion) {
                case 'crear':
                    return $this->crearUsuario($datos);
                case 'actualizar':
                    return $this->actualizarUsuario($datos);
                case 'eliminar':
                    return $this->eliminarUsuario($datos['id']);
                case 'listar':
                    return $this->listarUsuarios();
                default:
                    return ['error' => 'Acción no válida'];
            }
        } catch (PDOException $e) {
            error_log("Error en SuperAdminController::gestionarUsuarios: " . $e->getMessage());
            return ['error' => 'Error en la base de datos'];
        }
    }

    /**
     * Crear nuevo usuario
     */
    private function crearUsuario($datos) {
        // Verificar si existe la columna fecha_creacion
        $stmt = $this->db->prepare("SHOW COLUMNS FROM usuarios LIKE 'fecha_creacion'");
        $stmt->execute();
        
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        if ($stmt->fetch()) {
            // Si existe la columna, incluirla en el INSERT
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
        } else {
            // Si no existe la columna, hacer INSERT sin ella
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
        }
        
        $stmt->execute([
            $datos['nombre'],
            $datos['cedula'],
            $datos['rol'],
            $datos['correo'],
            $datos['usuario'],
            $password_hash
        ]);

        return ['success' => 'Usuario creado exitosamente'];
    }

    /**
     * Actualizar usuario existente
     */
    private function actualizarUsuario($datos) {
        $sql = "UPDATE usuarios SET nombre = ?, cedula = ?, rol = ?, correo = ?, usuario = ?";
        $params = [$datos['nombre'], $datos['cedula'], $datos['rol'], $datos['correo'], $datos['usuario']];

        // Si se proporciona nueva contraseña, actualizarla
        if (!empty($datos['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $datos['id'];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return ['success' => 'Usuario actualizado exitosamente'];
    }

    /**
     * Eliminar usuario
     */
    private function eliminarUsuario($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => 'Usuario eliminado exitosamente'];
    }

    /**
     * Listar todos los usuarios
     */
    private function listarUsuarios() {
        // Verificar si existe la columna fecha_creacion
        $stmt = $this->db->prepare("SHOW COLUMNS FROM usuarios LIKE 'fecha_creacion'");
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // Si existe la columna, incluirla en el SELECT
            $stmt = $this->db->prepare("
                SELECT id, nombre, cedula, rol, correo, usuario, fecha_creacion,
                       CASE 
                           WHEN rol = 1 THEN 'Administrador'
                           WHEN rol = 2 THEN 'Evaluador'
                           WHEN rol = 3 THEN 'Superadministrador'
                           ELSE 'Desconocido'
                       END as rol_nombre
                FROM usuarios 
                ORDER BY fecha_creacion DESC
            ");
        } else {
            // Si no existe la columna, hacer SELECT sin ella
            $stmt = $this->db->prepare("
                SELECT id, nombre, cedula, rol, correo, usuario, 'N/A' as fecha_creacion,
                       CASE 
                           WHEN rol = 1 THEN 'Administrador'
                           WHEN rol = 2 THEN 'Evaluador'
                           WHEN rol = 3 THEN 'Superadministrador'
                           ELSE 'Desconocido'
                       END as rol_nombre
                FROM usuarios 
                ORDER BY id DESC
            ");
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener logs del sistema
     */
    public function getLogsSistema($limite = 100) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM logs_sistema 
                ORDER BY fecha DESC 
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en SuperAdminController::getLogsSistema: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Gestionar configuración del sistema
     */
    public function gestionarConfiguracion($accion, $datos = []) {
        try {
            switch ($accion) {
                case 'obtener':
                    return $this->obtenerConfiguracion();
                case 'actualizar':
                    return $this->actualizarConfiguracion($datos);
                default:
                    return ['error' => 'Acción no válida'];
            }
        } catch (PDOException $e) {
            error_log("Error en SuperAdminController::gestionarConfiguracion: " . $e->getMessage());
            return ['error' => 'Error en la base de datos'];
        }
    }

    /**
     * Obtener configuración del sistema
     */
    private function obtenerConfiguracion() {
        $stmt = $this->db->prepare("SELECT * FROM configuracion_sistema");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Actualizar configuración del sistema
     */
    private function actualizarConfiguracion($datos) {
        $stmt = $this->db->prepare("
            UPDATE configuracion_sistema 
            SET valor = ?, fecha_actualizacion = NOW() 
            WHERE clave = ?
        ");
        $stmt->execute([$datos['valor'], $datos['clave']]);

        return ['success' => 'Configuración actualizada exitosamente'];
    }

    /**
     * Generar reporte de auditoría
     */
    public function generarReporteAuditoria($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.nombre as usuario,
                    u.rol,
                    a.accion,
                    a.tabla_afectada,
                    a.datos_anteriores,
                    a.datos_nuevos,
                    a.fecha
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.fecha BETWEEN ? AND ?
                ORDER BY a.fecha DESC
            ");
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en SuperAdminController::generarReporteAuditoria: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Respaldar base de datos
     */
    public function respaldarBaseDatos() {
        try {
            $config = require __DIR__ . '/../Config/config.php';
            $dbConfig = $config['database'];
            
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backup_path = __DIR__ . '/../../backups/' . $filename;
            
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                $dbConfig['host'],
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['dbname'],
                $backup_path
            );
            
            exec($command, $output, $return_var);
            
            if ($return_var === 0) {
                return ['success' => 'Respaldo creado exitosamente: ' . $filename];
            } else {
                return ['error' => 'Error al crear el respaldo'];
            }
        } catch (\Exception $e) {
            error_log("Error en SuperAdminController::respaldarBaseDatos: " . $e->getMessage());
            return ['error' => 'Error al crear el respaldo'];
        }
    }
}
