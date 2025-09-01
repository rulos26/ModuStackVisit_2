<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Services/LoggerService.php';

use App\Database\Database;
use App\Services\LoggerService;
use PDOException;

class SuperAdminController {
    private $db;
    private $logger;
    
    // Usuarios predefinidos del sistema que NO pueden ser modificados
    private const USUARIOS_PREDEFINIDOS = [
        'root' => [
            'rol' => 3,
            'descripcion' => 'Superadministrador del Sistema',
            'proteccion' => 'PROTEGIDO - Cuenta maestra del sistema'
        ],
        'admin' => [
            'rol' => 1,
            'descripcion' => 'Administrador del Sistema',
            'proteccion' => 'PROTEGIDO - Cuenta administrativa maestra'
        ],
        'cliente' => [
            'rol' => 2,
            'descripcion' => 'Cliente/Evaluador del Sistema',
            'proteccion' => 'PROTEGIDO - Cuenta de cliente maestra'
        ],
        'evaluador' => [
            'rol' => 2,
            'descripcion' => 'Evaluador del Sistema',
            'proteccion' => 'PROTEGIDO - Cuenta de evaluador maestra'
        ]
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new LoggerService();
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
                case 'activar':
                    return $this->activarUsuario($datos['id']);
                case 'desactivar':
                    return $this->desactivarUsuario($datos['id']);
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
     * Crear nuevo usuario con validaciones estrictas de roles
     */
    private function crearUsuario($datos) {
        try {
            // 1. VALIDACIÓN DE DATOS REQUERIDOS
            $campos_requeridos = ['nombre', 'cedula', 'rol', 'correo', 'usuario', 'password'];
            foreach ($campos_requeridos as $campo) {
                if (empty(trim($datos[$campo]))) {
                    return ['error' => "El campo '$campo' es obligatorio"];
                }
            }

            // 2. VALIDACIÓN DE FORMATO DE EMAIL
            if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'El formato del correo electrónico no es válido'];
            }

            // 3. VALIDACIÓN DE LONGITUD DE CONTRASEÑA
            if (strlen($datos['password']) < 6) {
                return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
            }

            // 4. VALIDACIÓN DE ROLES ÚNICOS (CRÍTICA)
            $rol = (int)$datos['rol'];
            
            // Verificar si se está intentando crear un Administrador (rol 1)
            if ($rol == 1) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 1 AND activo = 1");
                $stmt->execute();
                $resultado = $stmt->fetch();
                
                if ($resultado['total'] >= 1) {
                    return [
                        'error' => 'NO SE PUEDE CREAR UN SEGUNDO ADMINISTRADOR. El sistema solo permite un (1) Administrador activo.',
                        'error_code' => 'ADMIN_LIMIT_EXCEEDED',
                        'current_count' => $resultado['total'],
                        'max_allowed' => 1
                    ];
                }
            }
            
            // Verificar si se está intentando crear un Superadministrador (rol 3)
            if ($rol == 3) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 3 AND activo = 1");
                $stmt->execute();
                $resultado = $stmt->fetch();
                
                if ($resultado['total'] >= 1) {
                    return [
                        'error' => 'NO SE PUEDE CREAR UN SEGUNDO SUPERADMINISTRADOR. El sistema solo permite un (1) Superadministrador activo.',
                        'error_code' => 'SUPERADMIN_LIMIT_EXCEEDED',
                        'current_count' => $resultado['total'],
                        'max_allowed' => 1
                    ];
                }
            }

            // 5. VALIDACIÓN DE ROLES PERMITIDOS
            $roles_permitidos = [1, 2, 3, 4]; // 1=Admin, 2=Cliente, 3=Superadmin, 4=Evaluador
            if (!in_array($rol, $roles_permitidos)) {
                return ['error' => 'El rol especificado no es válido. Roles permitidos: Administrador (1), Cliente (2), Superadministrador (3), Evaluador (4)'];
            }

            // 6. VERIFICACIÓN DE USUARIO DUPLICADO
            $stmt = $this->db->prepare("SELECT id, usuario, cedula, correo FROM usuarios WHERE usuario = ? OR cedula = ? OR correo = ?");
            $stmt->execute([$datos['usuario'], $datos['cedula'], $datos['correo']]);
            $usuario_existente = $stmt->fetch();
            
            if ($usuario_existente) {
                $campos_duplicados = [];
                if ($usuario_existente['usuario'] === $datos['usuario']) $campos_duplicados[] = 'nombre de usuario';
                if ($usuario_existente['cedula'] === $datos['cedula']) $campos_duplicados[] = 'cédula';
                if ($usuario_existente['correo'] === $datos['correo']) $campos_duplicados[] = 'correo electrónico';
                
                return [
                    'error' => 'Ya existe un usuario con: ' . implode(', ', $campos_duplicados),
                    'error_code' => 'DUPLICATE_USER_DATA',
                    'duplicate_fields' => $campos_duplicados
                ];
            }

            // 7. VALIDACIÓN DE CÉDULA (solo números, mínimo 8 dígitos)
            if (!preg_match('/^\d{8,}$/', $datos['cedula'])) {
                return ['error' => 'La cédula debe contener solo números y tener al menos 8 dígitos'];
            }

            // 8. VALIDACIÓN DE NOMBRE DE USUARIO (solo alfanumérico y guiones bajos)
            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $datos['usuario'])) {
                return ['error' => 'El nombre de usuario debe contener solo letras, números y guiones bajos, entre 3 y 20 caracteres'];
            }

            // 9. CREACIÓN DEL USUARIO (si pasa todas las validaciones)
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password, activo, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $resultado = $stmt->execute([
                trim($datos['nombre']),
                $datos['cedula'],
                $rol,
                strtolower(trim($datos['correo'])),
                strtolower(trim($datos['usuario'])),
                $password_hash,
                isset($datos['activo']) ? (int)$datos['activo'] : 1
            ]);

            if (!$resultado) {
                return ['error' => 'Error al crear el usuario en la base de datos'];
            }

            $usuario_id = $this->db->lastInsertId();

            // 10. ENVÍO DE CREDENCIALES (si se solicitó)
            if (isset($datos['enviar_credenciales']) && $datos['enviar_credenciales']) {
                $this->enviarCredencialesPorCorreo($datos['correo'], $datos['usuario'], $datos['password'], $datos['nombre']);
            }

            // 11. LOG DE AUDITORÍA
            $this->logger->info("Usuario creado exitosamente", [
                'usuario_id' => $usuario_id,
                'nombre' => $datos['nombre'],
                'rol' => $rol,
                'creado_por' => $_SESSION['user_id'] ?? 'sistema'
            ]);

            return [
                'success' => 'Usuario creado exitosamente',
                'usuario_id' => $usuario_id,
                'rol_creado' => $rol,
                'mensaje_detallado' => $this->getMensajeRol($rol)
            ];

        } catch (PDOException $e) {
            $this->logger->error("Error al crear usuario", [
                'error' => $e->getMessage(),
                'datos' => array_diff_key($datos, ['password' => '***'])
            ]);
            return ['error' => 'Error interno del sistema al crear el usuario'];
        }
    }

    /**
     * Actualizar usuario existente
     */
    private function actualizarUsuario($datos) {
        // Verificar que no sea un usuario predefinido del sistema
        $usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($datos['id']);
        if ($usuario_predefinido) {
            return [
                'error' => 'NO SE PUEDE MODIFICAR UN USUARIO PREDEFINIDO DEL SISTEMA',
                'error_code' => 'PROTECTED_USER_UPDATE',
                'usuario' => $usuario_predefinido['descripcion'],
                'proteccion' => $usuario_predefinido['proteccion'],
                'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser modificada bajo ninguna circunstancia.'
            ];
        }
        
        $sql = "UPDATE usuarios SET nombre = ?, cedula = ?, rol = ?, correo = ?, usuario = ?, activo = ?";
        $params = [$datos['nombre'], $datos['cedula'], $datos['rol'], $datos['correo'], $datos['usuario'], $datos['activo']];

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
        // Verificar que no sea un usuario predefinido del sistema
        $usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
        if ($usuario_predefinido) {
            return [
                'error' => 'NO SE PUEDE ELIMINAR UN USUARIO PREDEFINIDO DEL SISTEMA',
                'error_code' => 'PROTECTED_USER_DELETE',
                'usuario' => $usuario_predefinido['descripcion'],
                'proteccion' => $usuario_predefinido['proteccion'],
                'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser eliminada bajo ninguna circunstancia.'
            ];
        }
        
        // Verificar que no sea un superadministrador (protección adicional)
        $stmt = $this->db->prepare("SELECT rol FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $usuario['rol'] == 3) {
            return ['error' => 'No se puede eliminar un superadministrador'];
        }
        
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => 'Usuario eliminado exitosamente'];
    }

    /**
     * Activar usuario
     */
    private function activarUsuario($id) {
        // Verificar que no sea un usuario predefinido del sistema
        $usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
        if ($usuario_predefinido) {
            return [
                'error' => 'NO SE PUEDE MODIFICAR EL ESTADO DE UN USUARIO PREDEFINIDO DEL SISTEMA',
                'error_code' => 'PROTECTED_USER_ACTIVATE',
                'usuario' => $usuario_predefinido['descripcion'],
                'proteccion' => $usuario_predefinido['proteccion'],
                'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y su estado NO puede ser modificado bajo ninguna circunstancia.'
            ];
        }
        
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 1 WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => 'Usuario activado exitosamente'];
    }

    /**
     * Desactivar usuario
     */
    private function desactivarUsuario($id) {
        // Verificar que no sea un usuario predefinido del sistema
        $usuario_predefinido = $this->obtenerUsuarioPredefinidoPorId($id);
        if ($usuario_predefinido) {
            return [
                'error' => 'NO SE PUEDE DESACTIVAR UN USUARIO PREDEFINIDO DEL SISTEMA',
                'error_code' => 'PROTECTED_USER_DEACTIVATE',
                'usuario' => $usuario_predefinido['descripcion'],
                'proteccion' => $usuario_predefinido['proteccion'],
                'mensaje_detallado' => 'Este usuario es una cuenta maestra del sistema y NO puede ser desactivada bajo ninguna circunstancia.'
            ];
        }
        
        // Verificar que no sea un superadministrador (protección adicional)
        $stmt = $this->db->prepare("SELECT rol FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $usuario['rol'] == 3) {
            return ['error' => 'No se puede desactivar un superadministrador'];
        }
        
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => 'Usuario desactivado exitosamente'];
    }

    /**
     * Listar todos los usuarios
     */
    private function listarUsuarios() {
        $stmt = $this->db->prepare("
            SELECT id, nombre, cedula, rol, correo, usuario, activo, ultimo_acceso,
                   CASE 
                       WHEN rol = 1 THEN 'Administrador'
                       WHEN rol = 2 THEN 'Cliente'
                       WHEN rol = 3 THEN 'Superadministrador'
                       WHEN rol = 4 THEN 'Evaluador'
                       ELSE 'Desconocido'
                   END as rol_nombre
            FROM usuarios 
            ORDER BY id DESC
        ");
        
        $stmt->execute();
        $usuarios = $stmt->fetchAll();
        
        // Agregar información de protección para usuarios predefinidos
        foreach ($usuarios as &$usuario) {
            if ($this->esUsuarioPredefinido($usuario['usuario'])) {
                $info_proteccion = self::USUARIOS_PREDEFINIDOS[strtolower($usuario['usuario'])];
                $usuario['protegido'] = true;
                $usuario['descripcion'] = $info_proteccion['descripcion'];
                $usuario['proteccion'] = $info_proteccion['proteccion'];
                $usuario['estado_proteccion'] = '🔒 PROTEGIDO - NO MODIFICABLE';
                $usuario['acciones_permitidas'] = ['ver'];
                $usuario['acciones_bloqueadas'] = ['editar', 'eliminar', 'activar', 'desactivar'];
            } else {
                $usuario['protegido'] = false;
                $usuario['estado_proteccion'] = '📝 EDITABLE';
                $usuario['acciones_permitidas'] = ['ver', 'editar', 'eliminar', 'activar', 'desactivar'];
                $usuario['acciones_bloqueadas'] = [];
            }
        }
        
        return $usuarios;
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

    /**
     * Enviar credenciales por correo electrónico
     */
    private function enviarCredencialesPorCorreo($correo, $usuario, $password, $nombre) {
        try {
            // Configurar headers para envío de correo
            $headers = "From: sistema@empresa.com\r\n";
            $headers .= "Reply-To: sistema@empresa.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $asunto = "Credenciales de Acceso - Sistema de Visitas";
            
            $mensaje = "
            <html>
            <head>
                <title>Credenciales de Acceso</title>
            </head>
            <body>
                <h2>Bienvenido al Sistema de Visitas</h2>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Se han creado tus credenciales de acceso al sistema:</p>
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Usuario:</strong> $usuario</p>
                    <p><strong>Contraseña:</strong> $password</p>
                </div>
                <p><strong>Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña después del primer acceso.</p>
                <p>Saludos,<br>Equipo de Sistemas</p>
            </body>
            </html>
            ";
            
            // Intentar enviar el correo
            if (mail($correo, $asunto, $mensaje, $headers)) {
                error_log("Credenciales enviadas por correo a: $correo");
                return true;
            } else {
                error_log("Error al enviar credenciales por correo a: $correo");
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Error en SuperAdminController::enviarCredencialesPorCorreo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener mensaje descriptivo del rol creado
     */
    private function getMensajeRol($rol) {
        switch ($rol) {
            case 1:
                return "Usuario Administrador creado exitosamente. Este es el único Administrador permitido en el sistema.";
            case 2:
                return "Usuario Cliente creado exitosamente. Pueden crearse múltiples usuarios con este rol.";
            case 4:
                return "Usuario Evaluador creado exitosamente. Pueden crearse múltiples usuarios con este rol.";
            case 3:
                return "Usuario Superadministrador creado exitosamente. Este es el único Superadministrador permitido en el sistema.";
            default:
                return "Usuario creado exitosamente.";
        }
    }
    
    /**
     * Verificar si un usuario es predefinido del sistema
     * @param string $usuario
     * @return bool
     */
    private function esUsuarioPredefinido($usuario) {
        return array_key_exists(strtolower($usuario), self::USUARIOS_PREDEFINIDOS);
    }
    
    /**
     * Verificar si un usuario por ID es predefinido
     * @param int $id
     * @return array|false
     */
    private function obtenerUsuarioPredefinidoPorId($id) {
        $stmt = $this->db->prepare("SELECT usuario FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $resultado = $stmt->fetch();
        
        if ($resultado && $this->esUsuarioPredefinido($resultado['usuario'])) {
            return self::USUARIOS_PREDEFINIDOS[strtolower($resultado['usuario'])];
        }
        
        return false;
    }
    
    /**
     * Verificar si un usuario por ID está protegido (es predefinido)
     * @param int $id
     * @return bool
     */
    public function esUsuarioProtegido($id) {
        return $this->obtenerUsuarioPredefinidoPorId($id) !== false;
    }
    
    /**
     * Obtener información de protección de usuario por ID
     * @param int $id
     * @return array
     */
    public function getInfoProteccionUsuarioPorId($id) {
        $usuarioPredefinido = $this->obtenerUsuarioPredefinidoPorId($id);
        
        if ($usuarioPredefinido) {
            return [
                'protegido' => true,
                'usuario' => $usuarioPredefinido['usuario'],
                'rol' => $usuarioPredefinido['rol'],
                'descripcion' => $usuarioPredefinido['descripcion'],
                'proteccion' => $usuarioPredefinido['proteccion'],
                'mensaje' => "Este usuario es una cuenta maestra del sistema y NO puede ser modificada, eliminada o desactivada."
            ];
        }
        
        return ['protegido' => false];
    }
    
    /**
     * Obtener información de protección de usuario predefinido
     * @param string $usuario
     * @return array|false
     */
    public function getInfoProteccionUsuario($usuario) {
        if ($this->esUsuarioPredefinido($usuario)) {
            $info = self::USUARIOS_PREDEFINIDOS[strtolower($usuario)];
            return [
                'protegido' => true,
                'usuario' => $usuario,
                'rol' => $info['rol'],
                'descripcion' => $info['descripcion'],
                'proteccion' => $info['proteccion'],
                'mensaje' => "Este usuario es una cuenta maestra del sistema y NO puede ser modificada, eliminada o desactivada."
            ];
        }
        
        return ['protegido' => false];
    }
    
    /**
     * Listar usuarios predefinidos del sistema
     * @return array
     */
    public function listarUsuariosPredefinidos() {
        $usuarios_predefinidos = [];
        
        foreach (self::USUARIOS_PREDEFINIDOS as $usuario => $info) {
            $stmt = $this->db->prepare("
                SELECT id, nombre, cedula, rol, correo, usuario, activo, ultimo_acceso
                FROM usuarios 
                WHERE usuario = ?
            ");
            $stmt->execute([$usuario]);
            $usuario_bd = $stmt->fetch();
            
            if ($usuario_bd) {
                $usuarios_predefinidos[] = [
                    'id' => $usuario_bd['id'],
                    'usuario' => $usuario_bd['usuario'],
                    'nombre' => $usuario_bd['nombre'],
                    'rol' => $usuario_bd['rol'],
                    'rol_nombre' => $this->getNombreRol($usuario_bd['rol']),
                    'activo' => $usuario_bd['activo'],
                    'ultimo_acceso' => $usuario_bd['ultimo_acceso'],
                    'protegido' => true,
                    'descripcion' => $info['descripcion'],
                    'proteccion' => $info['proteccion'],
                    'estado_proteccion' => '🔒 PROTEGIDO - NO MODIFICABLE'
                ];
            }
        }
        
        return $usuarios_predefinidos;
    }
    
    /**
     * Obtener nombre del rol
     * @param int $rol
     * @return string
     */
    private function getNombreRol($rol) {
        switch ($rol) {
            case 1: return 'Administrador';
            case 2: return 'Evaluador';
            case 3: return 'Superadministrador';
            default: return 'Desconocido';
        }
    }
}
