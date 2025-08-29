<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class LoginController {
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_DURATION = 900; // 15 minutos
    private const SESSION_TIMEOUT = 3600; // 1 hora
    
    private $db;
    private $logger;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new \App\Services\LoggerService();
        
        // DEBUG: Constructor inicializado
        $this->debugLog('LoginController constructor initialized');
        
        // Verificar y crear usuarios predeterminados
        $this->ensureDefaultUsers();
    }
    
    /**
     * Método principal de autenticación
     * @param string $usuario
     * @param string $password
     * @return array
     */
    public function authenticate($usuario, $password) {
        // DEBUG: Inicio de autenticación
        $this->debugLog("=== INICIO AUTENTICACIÓN ===");
        $this->debugLog("Usuario: $usuario");
        $this->debugLog("Password length: " . strlen($password));
        
        // DEBUG CONSOLE: Enviar a consola JavaScript
        $this->debugConsole("🚀 INICIO AUTENTICACIÓN", [
            'usuario' => $usuario,
            'password_length' => strlen($password),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        try {
            // Validación de entrada
            $this->debugLog("Validando entrada...");
            $this->debugConsole("🔍 VALIDANDO ENTRADA", ['usuario' => $usuario]);
            
            $validation = $this->validateInput($usuario, $password);
            $this->debugLog("Resultado validación: " . ($validation['valid'] ? 'VÁLIDA' : 'INVÁLIDA'));
            
            $this->debugConsole("✅ VALIDACIÓN COMPLETADA", [
                'valida' => $validation['valid'],
                'mensaje' => $validation['message']
            ]);
            
            if (!$validation['valid']) {
                $this->debugLog("Error de validación: " . $validation['message']);
                $this->debugConsole("❌ ERROR DE VALIDACIÓN", [
                    'error' => $validation['message'],
                    'codigo' => 'VALIDATION_ERROR'
                ]);
                return $this->createErrorResponse($validation['message'], 'VALIDATION_ERROR');
            }
            
            // Verificar rate limiting
            $this->debugLog("Verificando rate limiting para usuario: $usuario");
            $this->debugConsole("🔒 VERIFICANDO RATE LIMITING", ['usuario' => $usuario]);
            
            if ($this->isAccountLocked($usuario)) {
                $this->debugLog("CUENTA BLOQUEADA - Usuario: $usuario");
                $this->debugConsole("🚫 CUENTA BLOQUEADA", [
                    'usuario' => $usuario,
                    'razon' => 'ACCOUNT_LOCKED',
                    'mensaje' => 'Cuenta temporalmente bloqueada. Intente en 15 minutos.'
                ]);
                $this->logFailedAttempt($usuario, 'ACCOUNT_LOCKED');
                return $this->createErrorResponse('Cuenta temporalmente bloqueada. Intente en 15 minutos.', 'ACCOUNT_LOCKED');
            }
            $this->debugLog("Rate limiting OK - Usuario: $usuario");
            $this->debugConsole("✅ RATE LIMITING OK", ['usuario' => $usuario]);
            
            // Buscar usuario
            $this->debugLog("Buscando usuario en BD: $usuario");
            $this->debugConsole("🔍 BUSCANDO USUARIO EN BD", ['usuario' => $usuario]);
            
            $user = $this->findUser($usuario);
            if (!$user) {
                $this->debugLog("USUARIO NO ENCONTRADO - Usuario: $usuario");
                $this->debugConsole("❌ USUARIO NO ENCONTRADO", [
                    'usuario' => $usuario,
                    'razon' => 'USER_NOT_FOUND',
                    'accion' => 'incrementar intentos fallidos'
                ]);
                $this->logFailedAttempt($usuario, 'USER_NOT_FOUND');
                $this->incrementFailedAttempts($usuario);
                return $this->createErrorResponse('Credenciales inválidas.', 'AUTH_ERROR');
            }
            $this->debugLog("Usuario encontrado - ID: " . $user['id'] . ", Rol: " . $user['rol']);
            $this->debugConsole("✅ USUARIO ENCONTRADO", [
                'id' => $user['id'],
                'rol' => $user['rol'],
                'activo' => $user['activo'] ?? 'NULL'
            ]);
            
            // Verificar contraseña
            $this->debugLog("Verificando contraseña para usuario: $usuario");
            $this->debugConsole("🔐 VERIFICANDO CONTRASEÑA", [
                'usuario' => $usuario,
                'hash_preview' => substr($user['password'], 0, 20) . "...",
                'hash_length' => strlen($user['password'])
            ]);
            
            if (!$this->verifyPassword($password, $user['password'])) {
                $this->debugLog("CONTRASEÑA INVÁLIDA - Usuario: $usuario");
                $this->debugConsole("❌ CONTRASEÑA INVÁLIDA", [
                    'usuario' => $usuario,
                    'razon' => 'INVALID_PASSWORD',
                    'accion' => 'incrementar intentos fallidos'
                ]);
                $this->logFailedAttempt($usuario, 'INVALID_PASSWORD');
                $this->incrementFailedAttempts($usuario);
                return $this->createErrorResponse('Credenciales inválidas.', 'AUTH_ERROR');
            }
            $this->debugLog("Contraseña válida - Usuario: $usuario");
            $this->debugConsole("✅ CONTRASEÑA VÁLIDA", ['usuario' => $usuario]);
            
            // Verificar si el usuario está activo
            $this->debugLog("Verificando estado activo del usuario");
            $this->debugConsole("👤 VERIFICANDO ESTADO ACTIVO", ['usuario' => $usuario]);
            
            $isActive = $this->isUserActive($user);
            $this->debugLog("Usuario activo: " . ($isActive ? 'SÍ' : 'NO'));
            $this->debugConsole("📊 ESTADO DEL USUARIO", [
                'usuario' => $usuario,
                'activo' => $isActive ? 'SÍ' : 'NO'
            ]);
            
            if (!$isActive) {
                $this->debugLog("USUARIO INACTIVO - Usuario: $usuario");
                $this->debugConsole("🚫 USUARIO INACTIVO", [
                    'usuario' => $usuario,
                    'razon' => 'INACTIVE_USER',
                    'mensaje' => 'Usuario inactivo. Contacte al administrador.'
                ]);
                $this->logFailedAttempt($usuario, 'INACTIVE_USER');
                return $this->createErrorResponse('Usuario inactivo. Contacte al administrador.', 'INACTIVE_USER');
            }
            
            // Crear sesión
            $this->debugLog("Creando sesión para usuario: $usuario");
            $this->debugConsole("🔑 CREANDO SESIÓN", ['usuario' => $usuario]);
            
            $sessionData = $this->createSession($user);
            $this->debugLog("Sesión creada - Token: " . substr($sessionData['session_token'], 0, 10) . "...");
            $this->debugConsole("✅ SESIÓN CREADA", [
                'usuario' => $usuario,
                'token_preview' => substr($sessionData['session_token'], 0, 10) . "...",
                'rol' => $sessionData['rol'],
                'redirect_url' => $sessionData['redirect_url']
            ]);
            
            // Limpiar intentos fallidos
            $this->debugLog("Limpiando intentos fallidos");
            $this->debugConsole("🧹 LIMPIANDO INTENTOS FALLIDOS", ['usuario' => $usuario]);
            $this->clearFailedAttempts($usuario);
            
            // Log de acceso exitoso
            $this->debugLog("Registrando login exitoso");
            $this->debugConsole("📝 REGISTRANDO LOGIN EXITOSO", [
                'usuario' => $usuario,
                'user_id' => $user['id']
            ]);
            $this->logSuccessfulLogin($usuario, $user['id']);
            
            $this->debugLog("=== AUTENTICACIÓN EXITOSA ===");
            $this->debugConsole("🎉 AUTENTICACIÓN EXITOSA", [
                'usuario' => $usuario,
                'rol' => $sessionData['rol'],
                'redirect_url' => $sessionData['redirect_url']
            ]);
            return $this->createSuccessResponse($sessionData);
            
        } catch (PDOException $e) {
            $this->debugLog("ERROR DE BASE DE DATOS: " . $e->getMessage());
            $this->logger->error('Database error during login', [
                'usuario' => $usuario,
                'error' => $e->getMessage()
            ]);
            return $this->createErrorResponse('Error interno del sistema.', 'SYSTEM_ERROR');
        } catch (\Exception $e) {
            $this->debugLog("ERROR INESPERADO: " . $e->getMessage());
            $this->logger->error('Unexpected error during login', [
                'usuario' => $usuario,
                'error' => $e->getMessage()
            ]);
            return $this->createErrorResponse('Error interno del sistema.', 'SYSTEM_ERROR');
        }
    }
    
    /**
     * Validar entrada del usuario
     * @param string $usuario
     * @param string $password
     * @return array
     */
    private function validateInput($usuario, $password) {
        // Validar que no estén vacíos
        if (empty($usuario) || empty($password)) {
            return ['valid' => false, 'message' => 'Usuario y contraseña son requeridos.'];
        }
        
        // Validar longitud
        if (strlen($usuario) > 50 || strlen($password) > 255) {
            return ['valid' => false, 'message' => 'Datos de entrada demasiado largos.'];
        }
        
        // Validar caracteres permitidos (solo alfanuméricos y algunos símbolos)
        if (!preg_match('/^[a-zA-Z0-9@._-]+$/', $usuario)) {
            return ['valid' => false, 'message' => 'Usuario contiene caracteres no permitidos.'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Buscar usuario en la base de datos
     * @param string $usuario
     * @return array|false
     */
    private function findUser($usuario) {
        $this->debugLog("Buscando usuario en BD: $usuario");
        
        $stmt = $this->db->prepare('
            SELECT id, usuario, password, rol, cedula, nombre, 
                   activo, ultimo_acceso, intentos_fallidos, 
                   bloqueado_hasta
            FROM usuarios 
            WHERE usuario = :usuario 
            LIMIT 1
        ');
        $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user) {
            $this->debugLog("Usuario encontrado - ID: " . $user['id'] . ", Rol: " . $user['rol']);
            $this->debugLog("Hash password: " . substr($user['password'], 0, 20) . "...");
            $this->debugLog("Activo: " . ($user['activo'] ?? 'NULL'));
            $this->debugLog("Intentos fallidos: " . ($user['intentos_fallidos'] ?? 'NULL'));
        } else {
            $this->debugLog("Usuario NO encontrado en BD");
        }
        
        return $user;
    }
    
    /**
     * Verificar contraseña de forma segura
     * @param string $password
     * @param string $hash
     * @return bool
     */
    private function verifyPassword($password, $hash) {
        $this->debugLog("=== VERIFICACIÓN DE CONTRASEÑA ===");
        $this->debugConsole("🔐 INICIO VERIFICACIÓN CONTRASEÑA", [
            'hash_length' => strlen($hash),
            'hash_prefix' => substr($hash, 0, 7)
        ]);
        
        // Detectar tipo de hash de forma más robusta
        if (strpos($hash, '$2y$') === 0) {
            $this->debugLog("Detectado hash bcrypt");
            $this->debugConsole("🔍 DETECTADO HASH BCRYPT", ['hash_prefix' => substr($hash, 0, 7)]);
            
            $result = password_verify($password, $hash);
            $this->debugLog("Resultado bcrypt: " . ($result ? 'VÁLIDO' : 'INVÁLIDO'));
            $this->debugConsole("✅ RESULTADO BCRYPT", ['valido' => $result]);
            return $result;
        } elseif (strlen($hash) === 32) {
            $this->debugLog("Detectado hash MD5 (legacy)");
            $this->debugConsole("⚠️ DETECTADO HASH MD5 (LEGACY)", ['hash_length' => strlen($hash)]);
            
            $isValid = (md5($password) === $hash);
            $this->debugLog("Resultado MD5: " . ($isValid ? 'VÁLIDO' : 'INVÁLIDO'));
            $this->debugConsole("✅ RESULTADO MD5", ['valido' => $isValid]);
            
            if ($isValid) {
                $this->debugLog("ADVERTENCIA: Usuario usando hash MD5 - debe migrar a bcrypt");
                $this->debugConsole("⚠️ ADVERTENCIA: Usuario usando hash MD5", ['accion' => 'debe migrar a bcrypt']);
                $this->logger->warning('User using MD5 hash - should migrate to bcrypt');
            }
            return $isValid;
        } else {
            $this->debugLog("ERROR: Formato de hash desconocido");
            $this->debugConsole("❌ ERROR: Formato de hash desconocido", [
                'hash_length' => strlen($hash),
                'hash_preview' => substr($hash, 0, 20)
            ]);
            $this->logger->error('Unknown hash format detected');
            return false;
        }
    }
    
    /**
     * Verificar si el usuario está activo
     * @param array $user
     * @return bool
     */
    private function isUserActive($user) {
        return isset($user['activo']) ? (bool)$user['activo'] : true;
    }
    
    /**
     * Verificar si la cuenta está bloqueada
     * @param string $usuario
     * @return bool
     */
    private function isAccountLocked($usuario) {
        $this->debugLog("Verificando bloqueo de cuenta para: $usuario");
        $this->debugConsole("🔒 VERIFICANDO BLOQUEO DE CUENTA", ['usuario' => $usuario]);
        
        $stmt = $this->db->prepare('
            SELECT intentos_fallidos, bloqueado_hasta 
            FROM usuarios 
            WHERE usuario = :usuario
        ');
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            $this->debugLog("Usuario no encontrado para verificar bloqueo");
            $this->debugConsole("❌ USUARIO NO ENCONTRADO PARA BLOQUEO", ['usuario' => $usuario]);
            return false;
        }
        
        $this->debugLog("Intentos fallidos: " . ($user['intentos_fallidos'] ?? 'NULL'));
        $this->debugLog("Bloqueado hasta: " . ($user['bloqueado_hasta'] ?? 'NULL'));
        $this->debugLog("Máximo intentos permitidos: " . self::MAX_LOGIN_ATTEMPTS);
        
        $this->debugConsole("📊 ESTADO DE BLOQUEO", [
            'intentos_fallidos' => $user['intentos_fallidos'] ?? 'NULL',
            'bloqueado_hasta' => $user['bloqueado_hasta'] ?? 'NULL',
            'max_intentos' => self::MAX_LOGIN_ATTEMPTS
        ]);
        
        // Verificar si excedió intentos fallidos
        if ($user['intentos_fallidos'] >= self::MAX_LOGIN_ATTEMPTS) {
            $this->debugLog("Usuario excedió intentos fallidos");
            $this->debugConsole("⚠️ USUARIO EXCEDIÓ INTENTOS FALLIDOS", [
                'intentos_actuales' => $user['intentos_fallidos'],
                'max_permitidos' => self::MAX_LOGIN_ATTEMPTS
            ]);
            
            // Verificar si el bloqueo ya expiró
            if ($user['bloqueado_hasta'] && strtotime($user['bloqueado_hasta']) > time()) {
                $this->debugLog("CUENTA BLOQUEADA - Bloqueo activo");
                $this->debugConsole("🚫 CUENTA BLOQUEADA - BLOQUEO ACTIVO", [
                    'bloqueado_hasta' => $user['bloqueado_hasta'],
                    'tiempo_actual' => date('Y-m-d H:i:s')
                ]);
                return true;
            } else {
                $this->debugLog("Desbloqueando cuenta - Bloqueo expirado");
                $this->debugConsole("🔓 DESBLOQUEANDO CUENTA - BLOQUEO EXPIRADO", [
                    'bloqueado_hasta' => $user['bloqueado_hasta'],
                    'accion' => 'limpiar intentos fallidos'
                ]);
                // Desbloquear cuenta si expiró
                $this->clearFailedAttempts($usuario);
                return false;
            }
        }
        
        $this->debugLog("Cuenta no bloqueada");
        $this->debugConsole("✅ CUENTA NO BLOQUEADA", ['usuario' => $usuario]);
        return false;
    }
    
    /**
     * Incrementar contador de intentos fallidos
     * @param string $usuario
     */
    private function incrementFailedAttempts($usuario) {
        $stmt = $this->db->prepare('
            UPDATE usuarios 
            SET intentos_fallidos = COALESCE(intentos_fallidos, 0) + 1,
                bloqueado_hasta = CASE 
                    WHEN COALESCE(intentos_fallidos, 0) + 1 >= :max_attempts 
                    THEN DATE_ADD(NOW(), INTERVAL :lockout_duration SECOND)
                    ELSE bloqueado_hasta 
                END
            WHERE usuario = :usuario
        ');
        
        $maxAttempts = self::MAX_LOGIN_ATTEMPTS;
        $lockoutDuration = self::LOCKOUT_DURATION;
        
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':max_attempts', $maxAttempts, \PDO::PARAM_INT);
        $stmt->bindParam(':lockout_duration', $lockoutDuration, \PDO::PARAM_INT);
        $stmt->execute();
    }
    
    /**
     * Limpiar intentos fallidos
     * @param string $usuario
     */
    private function clearFailedAttempts($usuario) {
        $stmt = $this->db->prepare('
            UPDATE usuarios 
            SET intentos_fallidos = 0, 
                bloqueado_hasta = NULL 
            WHERE usuario = :usuario
        ');
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
    }
    
    /**
     * Crear sesión de usuario
     * @param array $user
     * @return array
     */
    private function createSession($user) {
        // Generar token de sesión único
        $sessionToken = bin2hex(random_bytes(32));
        
        // Configurar datos de sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['cedula'] = $user['cedula'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['session_token'] = $sessionToken;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // Actualizar último acceso en BD
        $this->updateLastAccess($user['id']);
        
        // Determinar redirección según rol
        $redirectUrl = $this->getRedirectUrl($user['rol']);
        
        return [
            'user_id' => $user['id'],
            'username' => $user['usuario'],
            'rol' => $user['rol'],
            'nombre' => $user['nombre'],
            'session_token' => $sessionToken,
            'redirect_url' => $redirectUrl
        ];
    }
    
    /**
     * Obtener URL de redirección según rol
     * @param int $rol
     * @return string
     */
    private function getRedirectUrl($rol) {
        switch ($rol) {
            case 1:
                return 'resources/views/admin/dashboardAdmin.php';
            case 2:
                return 'resources/views/evaluador/dashboardEavaluador.php';
            case 3:
                return 'resources/views/superadmin/dashboardSuperAdmin.php';
            default:
                throw new \InvalidArgumentException('Rol de usuario no válido: ' . $rol);
        }
    }
    
    /**
     * Actualizar último acceso del usuario
     * @param int $userId
     */
    private function updateLastAccess($userId) {
        $stmt = $this->db->prepare('
            UPDATE usuarios 
            SET ultimo_acceso = NOW() 
            WHERE id = :user_id
        ');
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }
    
    /**
     * Log de intento fallido
     * @param string $usuario
     * @param string $reason
     */
    private function logFailedAttempt($usuario, $reason) {
        $this->logger->warning('Failed login attempt', [
            'usuario' => $usuario,
            'reason' => $reason,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    /**
     * Log de login exitoso
     * @param string $usuario
     * @param int $userId
     */
    private function logSuccessfulLogin($usuario, $userId) {
        $this->logger->info('Successful login', [
            'usuario' => $usuario,
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    /**
     * Crear respuesta de éxito
     * @param array $data
     * @return array
     */
    private function createSuccessResponse($data) {
        return [
            'success' => true,
            'message' => 'Login exitoso',
            'data' => $data,
            'timestamp' => time()
        ];
    }
    
    /**
     * Crear respuesta de error
     * @param string $message
     * @param string $code
     * @return array
     */
    private function createErrorResponse($message, $code) {
        return [
            'success' => false,
            'message' => $message,
            'error_code' => $code,
            'timestamp' => time()
        ];
    }
    
    /**
     * Método estático para compatibilidad (deprecated)
     * @param string $usuario
     * @param string $password
     * @return string|array
     * @deprecated Use authenticate() method instead
     */
    public static function login($usuario, $password) {
        $controller = new self();
        $result = $controller->authenticate($usuario, $password);
        
        if ($result['success']) {
            // Redirigir según el rol
            header('Location: ' . $result['data']['redirect_url']);
            exit();
        } else {
            return $result['message'];
        }
    }
    
    /**
     * Verificar si la sesión es válida
     * @return bool
     */
    public static function isSessionValid() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            return false;
        }
        
        // Verificar timeout de sesión
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > self::SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
        
        // Actualizar última actividad
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        // Log de logout
        if (isset($_SESSION['username'])) {
            $logger = new \App\Services\LoggerService();
            $logger->info('User logout', [
                'usuario' => $_SESSION['username'],
                'user_id' => $_SESSION['user_id'] ?? null
            ]);
        }
        
        // Destruir sesión
        session_unset();
        session_destroy();
        
        // Redirigir al login
        header('Location: index.php');
        exit();
    }
    
    /**
     * Método de debug para seguimiento
     * @param string $message
     */
    private function debugLog($message) {
        // Escribir a un archivo de debug específico
        $debugFile = __DIR__ . '/../../logs/debug.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] DEBUG: $message" . PHP_EOL;
        
        // Crear directorio si no existe
        $logDir = dirname($debugFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Escribir al archivo de debug
        file_put_contents($debugFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // También escribir al logger principal si está disponible
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }
    
    /**
     * Método de debug para consola JavaScript
     * @param string $message
     * @param array $data
     */
    private function debugConsole($message, $data = []) {
        // Crear script JavaScript para consola
        $script = "<script>";
        $script .= "console.group('🔍 LOGINCONTROLLER DEBUG: " . addslashes($message) . "');";
        $script .= "console.log('📅 Timestamp:', '" . date('Y-m-d H:i:s') . "');";
        
        if (!empty($data)) {
            $script .= "console.log('📊 Data:', " . json_encode($data) . ");";
        }
        
        $script .= "console.trace('📍 Stack Trace');";
        $script .= "console.groupEnd();";
        $script .= "</script>";
        
        // Enviar al navegador
        echo $script;
        
        // También escribir al log de debug
        $this->debugLog("CONSOLE DEBUG: $message - " . json_encode($data));
    }
    
    /**
     * Verificar y crear usuarios predeterminados si no existen
     */
    private function ensureDefaultUsers() {
        $this->debugLog("Verificando usuarios predeterminados...");
        $this->debugConsole("🔍 VERIFICANDO USUARIOS PREDETERMINADOS", ['accion' => 'inicio']);
        
        $defaultUsers = [
            [
                'usuario' => 'root',
                'password' => 'root',
                'rol' => 3, // Superadministrador
                'nombre' => 'Super Administrador',
                'cedula' => '30000001',
                'correo' => 'root@empresa.com'
            ],
            [
                'usuario' => 'admin',
                'password' => 'admin',
                'rol' => 1, // Administrador
                'nombre' => 'Administrador',
                'cedula' => '30000002',
                'correo' => 'admin@empresa.com'
            ],
            [
                'usuario' => 'cliente',
                'password' => 'cliente',
                'rol' => 2, // Evaluador/Cliente
                'nombre' => 'Cliente',
                'cedula' => '30000003',
                'correo' => 'cliente@empresa.com'
            ]
        ];
        
        foreach ($defaultUsers as $userData) {
            $this->createUserIfNotExists($userData);
        }
        
        $this->debugLog("Verificación de usuarios predeterminados completada");
        $this->debugConsole("✅ USUARIOS PREDETERMINADOS VERIFICADOS", ['accion' => 'completado']);
    }
    
    /**
     * Crear usuario si no existe
     * @param array $userData
     */
    private function createUserIfNotExists($userData) {
        $usuario = $userData['usuario'];
        
        // Verificar si el usuario ya existe
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            $this->debugLog("Usuario '$usuario' ya existe - saltando creación");
            $this->debugConsole("⏭️ USUARIO YA EXISTE", [
                'usuario' => $usuario,
                'accion' => 'saltar creación'
            ]);
            return;
        }
        
        // Crear hash de contraseña correcto
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Preparar query de inserción
        $columns = ['usuario', 'password', 'rol', 'nombre', 'cedula', 'correo', 'activo'];
        $placeholders = [':usuario', ':password', ':rol', ':nombre', ':cedula', ':correo', ':activo'];
        
        // Verificar si existe la columna fecha_creacion
        $stmt = $this->db->prepare("SHOW COLUMNS FROM usuarios LIKE 'fecha_creacion'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $columns[] = 'fecha_creacion';
            $placeholders[] = 'NOW()';
        }
        
        $sql = "INSERT INTO usuarios (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario', $userData['usuario']);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':rol', $userData['rol'], \PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $userData['nombre']);
        $stmt->bindParam(':cedula', $userData['cedula']);
        $stmt->bindParam(':correo', $userData['correo']);
        $stmt->bindParam(':activo', 1, \PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->debugLog("Usuario '$usuario' creado exitosamente");
            $this->debugConsole("✅ USUARIO CREADO", [
                'usuario' => $usuario,
                'rol' => $userData['rol'],
                'hash_length' => strlen($passwordHash),
                'hash_preview' => substr($passwordHash, 0, 20) . "..."
            ]);
            
            // Log de creación
            $this->logger->info('Default user created', [
                'usuario' => $usuario,
                'rol' => $userData['rol'],
                'nombre' => $userData['nombre']
            ]);
        } else {
            $this->debugLog("Error al crear usuario '$usuario'");
            $this->debugConsole("❌ ERROR AL CREAR USUARIO", [
                'usuario' => $usuario,
                'error' => 'Error en inserción'
            ]);
        }
    }
} 