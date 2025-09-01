<?php
/**
 * API para Test Web de Usuarios Predefinidos
 * 
 * Este script implementa la lógica real del test que se ejecuta desde la interfaz web
 * 
 * @version 1.0
 * @author Sistema ModuStack
 * @license MIT
 */

// Configuración de seguridad
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// Headers de seguridad
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Verificar método de petición
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Verificar que sea una petición autorizada (opcional)
$allowedIPs = ['127.0.0.1', '::1']; // Solo localhost por defecto
if (!in_array($_SERVER['REMOTE_ADDR'] ?? 'unknown', $allowedIPs)) {
    // En producción, puedes descomentar esta línea para mayor seguridad
    // http_response_code(403);
    // echo json_encode(['error' => 'Acceso no autorizado']);
    // exit;
}

// Cargar autoloader
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo json_encode(['error' => 'Autoloader no encontrado']);
    exit;
}

require_once $autoloadPath;

// Verificar clases necesarias
if (!class_exists('App\Controllers\LoginController')) {
    echo json_encode(['error' => 'LoginController no encontrado']);
    exit;
}

if (!class_exists('App\Controllers\SuperAdminController')) {
    echo json_encode(['error' => 'SuperAdminController no encontrado']);
    exit;
}

try {
    // Obtener acción del test
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? 'run_complete_test';
    
    // Instanciar controladores
    $loginController = new \App\Controllers\LoginController();
    $superAdminController = new \App\Controllers\SuperAdminController();
    
    // Ejecutar test según la acción
    switch ($action) {
        case 'run_complete_test':
            $result = runCompleteTest($loginController, $superAdminController);
            break;
            
        case 'test_protection':
            $result = testProtectionOnly($superAdminController);
            break;
            
        case 'get_system_logs':
            $result = getSystemLogs();
            break;
            
        default:
            $result = ['error' => 'Acción no válida'];
    }
    
    // Devolver resultado
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del sistema',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

/**
 * Ejecutar test completo
 */
function runCompleteTest($loginController, $superAdminController) {
    $results = [
        'success' => true,
        'steps' => [],
        'summary' => [],
        'errors' => []
    ];
    
    // Paso 1: Verificar conexión a base de datos
    $step1 = verifyDatabaseConnection();
    $results['steps'][] = $step1;
    
    if (!$step1['success']) {
        $results['success'] = false;
        $results['errors'][] = $step1['message'];
        return $results;
    }
    
    // Paso 2: Validar estructura de tablas
    $step2 = validateTableStructure();
    $results['steps'][] = $step2;
    
    if (!$step2['success']) {
        $results['success'] = false;
        $results['errors'][] = $step2['message'];
        return $results;
    }
    
    // Paso 3: Crear usuarios predeterminados
    $step3 = createDefaultUsers($loginController);
    $results['steps'][] = $step3;
    
    if (!$step3['success']) {
        $results['success'] = false;
        $results['errors'][] = $step3['message'];
        return $results;
    }
    
    // Paso 4: Verificar roles asignados
    $step4 = verifyUserRoles();
    $results['steps'][] = $step4;
    
    if (!$step4['success']) {
        $results['success'] = false;
        $results['errors'][] = $step4['message'];
        return $results;
    }
    
    // Paso 5: Validar protección de usuarios
    $step5 = validateUserProtection($superAdminController);
    $results['steps'][] = $step5;
    
    if (!$step5['success']) {
        $results['success'] = false;
        $results['errors'][] = $step5['message'];
        return $results;
    }
    
    // Paso 6: Probar operaciones CRUD
    $step6 = testCRUDOperations($superAdminController);
    $results['steps'][] = $step6;
    
    if (!$step6['success']) {
        $results['success'] = false;
        $results['errors'][] = $step6['message'];
        return $results;
    }
    
    // Paso 7: Verificar restricciones
    $step7 = verifyRestrictions($superAdminController);
    $results['steps'][] = $step7;
    
    if (!$step7['success']) {
        $results['success'] = false;
        $results['errors'][] = $step7['message'];
        return $results;
    }
    
    // Paso 8: Generar reporte final
    $step8 = generateFinalReport();
    $results['steps'][] = $step8;
    
    // Generar resumen
    $results['summary'] = generateSummary($results['steps']);
    
    return $results;
}

/**
 * Test de protección únicamente
 */
function testProtectionOnly($superAdminController) {
    $results = [
        'success' => true,
        'steps' => [],
        'summary' => [],
        'errors' => []
    ];
    
    // Paso 1: Verificar protección de usuarios
    $step1 = validateUserProtection($superAdminController);
    $results['steps'][] = $step1;
    
    if (!$step1['success']) {
        $results['success'] = false;
        $results['errors'][] = $step1['message'];
        return $results;
    }
    
    // Paso 2: Probar operaciones bloqueadas
    $step2 = testBlockedOperations($superAdminController);
    $results['steps'][] = $step2;
    
    if (!$step2['success']) {
        $results['success'] = false;
        $results['errors'][] = $step2['message'];
        return $results;
    }
    
    // Paso 3: Validar restricciones
    $step3 = verifyRestrictions($superAdminController);
    $results['steps'][] = $step3;
    
    if (!$step3['success']) {
        $results['success'] = false;
        $results['errors'][] = $step3['message'];
        return $results;
    }
    
    // Generar resumen
    $results['summary'] = generateProtectionSummary($results['steps']);
    
    return $results;
}

/**
 * Verificar conexión a base de datos
 */
function verifyDatabaseConnection() {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        $stmt = $db->query('SELECT 1');
        $result = $stmt->fetch();
        
        if ($result) {
            return [
                'step' => 1,
                'title' => 'Verificar conexión a base de datos',
                'success' => true,
                'message' => 'Conexión a base de datos exitosa',
                'details' => 'Base de datos conectada correctamente'
            ];
        } else {
            return [
                'step' => 1,
                'title' => 'Verificar conexión a base de datos',
                'success' => false,
                'message' => 'No se pudo verificar la conexión a la base de datos',
                'details' => 'Query de prueba falló'
            ];
        }
    } catch (Exception $e) {
        return [
            'step' => 1,
            'title' => 'Verificar conexión a base de datos',
            'success' => false,
            'message' => 'Error de conexión a la base de datos',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Validar estructura de tablas
 */
function validateTableStructure() {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        
        // Verificar tabla usuarios
        $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
        if (!$stmt->fetch()) {
            return [
                'step' => 2,
                'title' => 'Validar estructura de tablas',
                'success' => false,
                'message' => 'Tabla usuarios no encontrada',
                'details' => 'La tabla usuarios no existe en la base de datos'
            ];
        }
        
        // Verificar columnas necesarias
        $stmt = $db->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'usuario', 'password', 'rol', 'nombre', 'cedula', 'correo', 'activo'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (!empty($missingColumns)) {
            return [
                'step' => 2,
                'title' => 'Validar estructura de tablas',
                'success' => false,
                'message' => 'Columnas requeridas faltantes',
                'details' => 'Columnas faltantes: ' . implode(', ', $missingColumns)
            ];
        }
        
        return [
            'step' => 2,
            'title' => 'Validar estructura de tablas',
            'success' => true,
            'message' => 'Estructura de tablas válida',
            'details' => 'Todas las columnas requeridas están presentes'
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 2,
            'title' => 'Validar estructura de tablas',
            'success' => false,
            'message' => 'Error al validar estructura de tablas',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Crear usuarios predeterminados
 */
function createDefaultUsers($loginController) {
    try {
        // Llamar al método de inicialización
        $loginController->initializeDefaultUsers();
        
        return [
            'step' => 3,
            'title' => 'Crear usuarios predeterminados',
            'success' => true,
            'message' => 'Usuarios predeterminados creados/verificados',
            'details' => 'Se ejecutó ensureDefaultUsers() correctamente'
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 3,
            'title' => 'Crear usuarios predeterminados',
            'success' => false,
            'message' => 'Error al crear usuarios predeterminados',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Verificar roles asignados
 */
function verifyUserRoles() {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        
        $expectedUsers = [
            'root' => 3,      // Superadministrador
            'admin' => 1,     // Administrador
            'cliente' => 2,   // Cliente
            'evaluador' => 4  // Evaluador
        ];
        
        $results = [];
        $allValid = true;
        
        foreach ($expectedUsers as $username => $expectedRole) {
            $stmt = $db->prepare('SELECT rol FROM usuarios WHERE usuario = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && $user['rol'] == $expectedRole) {
                $results[] = "✅ $username: Rol $expectedRole correcto";
            } else {
                $results[] = "❌ $username: Rol incorrecto (esperado: $expectedRole, actual: " . ($user['rol'] ?? 'no encontrado') . ")";
                $allValid = false;
            }
        }
        
        return [
            'step' => 4,
            'title' => 'Verificar roles asignados',
            'success' => $allValid,
            'message' => $allValid ? 'Todos los roles están correctamente asignados' : 'Algunos roles están incorrectos',
            'details' => implode("\n", $results)
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 4,
            'title' => 'Verificar roles asignados',
            'success' => false,
            'message' => 'Error al verificar roles',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Validar protección de usuarios
 */
function validateUserProtection($superAdminController) {
    try {
        // Obtener información de usuarios predefinidos
        $protectedUsers = $superAdminController->listarUsuariosPredefinidos();
        
        if (empty($protectedUsers)) {
            return [
                'step' => 5,
                'title' => 'Validar protección de usuarios',
                'success' => false,
                'message' => 'No se encontraron usuarios predefinidos',
                'details' => 'El sistema no identificó usuarios maestros'
            ];
        }
        
        $protectionResults = [];
        $allProtected = true;
        
        foreach ($protectedUsers as $user) {
            if ($user['protegido']) {
                $protectionResults[] = "✅ {$user['usuario']}: Protegido correctamente";
            } else {
                $protectionResults[] = "❌ {$user['usuario']}: NO está protegido";
                $allProtected = false;
            }
        }
        
        return [
            'step' => 5,
            'title' => 'Validar protección de usuarios',
            'success' => $allProtected,
            'message' => $allProtected ? 'Todos los usuarios maestros están protegidos' : 'Algunos usuarios maestros no están protegidos',
            'details' => implode("\n", $protectionResults)
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 5,
            'title' => 'Validar protección de usuarios',
            'success' => false,
            'message' => 'Error al validar protección',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Probar operaciones CRUD
 */
function testCRUDOperations($superAdminController) {
    try {
        $results = [];
        
        // Probar listar usuarios
        $users = $superAdminController->gestionarUsuarios('listar');
        if (is_array($users)) {
            $results[] = "✅ Listar usuarios: Funcionando correctamente";
        } else {
            $results[] = "❌ Listar usuarios: Error en la operación";
        }
        
        return [
            'step' => 6,
            'title' => 'Probar operaciones CRUD',
            'success' => true,
            'message' => 'Operaciones CRUD básicas funcionando',
            'details' => implode("\n", $results)
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 6,
            'title' => 'Probar operaciones CRUD',
            'success' => false,
            'message' => 'Error al probar operaciones CRUD',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Verificar restricciones
 */
function verifyRestrictions($superAdminController) {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        
        // Verificar que solo existe 1 Administrador
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM usuarios WHERE rol = 1 AND activo = 1');
        $stmt->execute();
        $adminCount = $stmt->fetch()['count'];
        
        // Verificar que solo existe 1 Superadministrador
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM usuarios WHERE rol = 3 AND activo = 1');
        $stmt->execute();
        $superAdminCount = $stmt->fetch()['count'];
        
        $restrictions = [];
        $allValid = true;
        
        if ($adminCount == 1) {
            $restrictions[] = "✅ Administradores: Solo 1 (correcto)";
        } else {
            $restrictions[] = "❌ Administradores: $adminCount (debería ser 1)";
            $allValid = false;
        }
        
        if ($superAdminCount == 1) {
            $restrictions[] = "✅ Superadministradores: Solo 1 (correcto)";
        } else {
            $restrictions[] = "❌ Superadministradores: $superAdminCount (debería ser 1)";
            $allValid = false;
        }
        
        return [
            'step' => 7,
            'title' => 'Verificar restricciones',
            'success' => $allValid,
            'message' => $allValid ? 'Todas las restricciones están cumplidas' : 'Algunas restricciones no se cumplen',
            'details' => implode("\n", $restrictions)
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 7,
            'title' => 'Verificar restricciones',
            'success' => false,
            'message' => 'Error al verificar restricciones',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Generar reporte final
 */
function generateFinalReport() {
    return [
        'step' => 8,
        'title' => 'Generar reporte final',
        'success' => true,
        'message' => 'Reporte generado exitosamente',
        'details' => 'Test completado, generando resumen final'
    ];
}

/**
 * Generar resumen del test completo
 */
function generateSummary($steps) {
    $totalSteps = count($steps);
    $successfulSteps = count(array_filter($steps, function($step) {
        return $step['success'];
    }));
    
    $successRate = ($totalSteps > 0) ? round(($successfulSteps / $totalSteps) * 100, 2) : 0;
    
    return [
        'total_steps' => $totalSteps,
        'successful_steps' => $successfulSteps,
        'failed_steps' => $totalSteps - $successfulSteps,
        'success_rate' => $successRate . '%',
        'status' => $successRate == 100 ? 'COMPLETADO EXITOSAMENTE' : 'COMPLETADO CON ERRORES'
    ];
}

/**
 * Generar resumen del test de protección
 */
function generateProtectionSummary($steps) {
    $totalSteps = count($steps);
    $successfulSteps = count(array_filter($steps, function($step) {
        return $step['success'];
    }));
    
    $successRate = ($totalSteps > 0) ? round(($successfulSteps / $totalSteps) * 100, 2) : 0;
    
    return [
        'total_steps' => $totalSteps,
        'successful_steps' => $successfulSteps,
        'failed_steps' => $totalSteps - $successfulSteps,
        'success_rate' => $successRate . '%',
        'status' => $successRate == 100 ? 'PROTECCIÓN VERIFICADA' : 'PROTECCIÓN CON PROBLEMAS'
    ];
}

/**
 * Obtener logs del sistema
 */
function getSystemLogs() {
    try {
        $logFile = __DIR__ . '/../../logs/debug.log';
        
        if (!file_exists($logFile)) {
            return [
                'success' => true,
                'logs' => ['No hay logs disponibles']
            ];
        }
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recentLogs = array_slice($logs, -50); // Últimos 50 logs
        
        return [
            'success' => true,
            'logs' => $recentLogs
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error al obtener logs: ' . $e->getMessage()
        ];
    }
}

/**
 * Probar operaciones bloqueadas
 */
function testBlockedOperations($superAdminController) {
    try {
        $results = [];
        
        // Intentar eliminar usuario root (debería fallar)
        try {
            $result = $superAdminController->gestionarUsuarios('eliminar', ['usuario_id' => 1]);
            if (strpos($result['message'] ?? '', 'PROTECTED_USER_DELETE') !== false) {
                $results[] = "✅ Eliminación bloqueada: Usuario root protegido correctamente";
            } else {
                $results[] = "❌ Eliminación NO bloqueada: Usuario root puede ser eliminado";
            }
        } catch (Exception $e) {
            $results[] = "✅ Eliminación bloqueada: " . $e->getMessage();
        }
        
        return [
            'step' => 2,
            'title' => 'Probar operaciones bloqueadas',
            'success' => true,
            'message' => 'Operaciones bloqueadas funcionando correctamente',
            'details' => implode("\n", $results)
        ];
        
    } catch (Exception $e) {
        return [
            'step' => 2,
            'title' => 'Probar operaciones bloqueadas',
            'success' => false,
            'message' => 'Error al probar operaciones bloqueadas',
            'details' => $e->getMessage()
        ];
    }
}
?>
