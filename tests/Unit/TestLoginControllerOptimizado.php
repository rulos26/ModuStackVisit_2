<?php
require_once __DIR__ . '/../../app/Database/Database.php';
require_once __DIR__ . '/../../app/Controllers/LoginController.php';
require_once __DIR__ . '/../../app/Services/LoggerService.php';

use App\Database\Database;
use App\Controllers\LoginController;
use App\Services\LoggerService;

// Iniciar sesión para las pruebas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$resultados = [];
$errores = [];

// Función para limpiar la sesión
function limpiarSesion() {
    session_unset();
    session_destroy();
    session_start();
}

// Función para verificar usuario en BD
function verificarUsuarioEnBD($usuario) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id, usuario, password, rol, activo, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    return $stmt->fetch();
}

// Función para simular login con el nuevo controlador
function simularLoginOptimizado($usuario, $password) {
    limpiarSesion();
    
    try {
        $controller = new LoginController();
        $resultado = $controller->authenticate($usuario, $password);
        
        return [
            'exito' => $resultado['success'],
            'mensaje' => $resultado['message'],
            'error_code' => $resultado['error_code'] ?? null,
            'datos' => $resultado['data'] ?? null,
            'sesion' => $_SESSION ?? []
        ];
    } catch (Exception $e) {
        return [
            'exito' => false,
            'error' => $e->getMessage(),
            'sesion' => $_SESSION ?? []
        ];
    }
}

// Función para verificar estado de bloqueo
function verificarEstadoBloqueo($usuario) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT intentos_fallidos, bloqueado_hasta FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    return $stmt->fetch();
}

// Ejecutar pruebas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'test_login_exitoso':
            $login = simularLoginOptimizado('root', 'root');
            if ($login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Login exitoso con controlador optimizado',
                    'datos' => [
                        'usuario' => $login['datos']['username'],
                        'rol' => $login['datos']['rol'],
                        'session_token' => $login['datos']['session_token'],
                        'redirect_url' => $login['datos']['redirect_url']
                    ]
                ];
            } else {
                $errores[] = 'Error en login exitoso: ' . $login['mensaje'];
            }
            break;
            
        case 'test_login_fallido':
            $login = simularLoginOptimizado('root', 'password_incorrecto');
            if (!$login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Login correctamente rechazado con credenciales incorrectas',
                    'datos' => [
                        'error_code' => $login['error_code'],
                        'mensaje' => $login['mensaje']
                    ]
                ];
            } else {
                $errores[] = 'Error: Login aceptado con credenciales incorrectas';
            }
            break;
            
        case 'test_rate_limiting':
            // Simular múltiples intentos fallidos
            for ($i = 0; $i < 6; $i++) {
                simularLoginOptimizado('root', 'password_incorrecto');
            }
            
            // Verificar estado de bloqueo
            $estado = verificarEstadoBloqueo('root');
            if ($estado && $estado['intentos_fallidos'] >= 5) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Rate limiting funcionando correctamente',
                    'datos' => [
                        'intentos_fallidos' => $estado['intentos_fallidos'],
                        'bloqueado_hasta' => $estado['bloqueado_hasta']
                    ]
                ];
            } else {
                $errores[] = 'Error: Rate limiting no funcionó correctamente';
            }
            break;
            
        case 'test_validacion_entrada':
            // Test con entrada vacía
            $login1 = simularLoginOptimizado('', '');
            $login2 = simularLoginOptimizado('root', '');
            $login3 = simularLoginOptimizado('', 'root');
            
            $validaciones = [];
            if (!$login1['exito']) $validaciones[] = 'Entrada vacía rechazada';
            if (!$login2['exito']) $validaciones[] = 'Usuario vacío rechazado';
            if (!$login3['exito']) $validaciones[] = 'Contraseña vacía rechazada';
            
            if (count($validaciones) === 3) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Validación de entrada funcionando correctamente',
                    'datos' => $validaciones
                ];
            } else {
                $errores[] = 'Error: Validación de entrada no funcionó correctamente';
            }
            break;
            
        case 'test_verificacion_sesion':
            // Primero hacer login exitoso
            $login = simularLoginOptimizado('root', 'root');
            if ($login['exito']) {
                // Verificar sesión
                $sesionValida = LoginController::isSessionValid();
                if ($sesionValida) {
                    $resultados[] = [
                        'tipo' => 'success',
                        'mensaje' => 'Verificación de sesión funcionando correctamente',
                        'datos' => [
                            'sesion_valida' => $sesionValida,
                            'session_token' => $_SESSION['session_token'] ?? null,
                            'last_activity' => $_SESSION['last_activity'] ?? null
                        ]
                    ];
                } else {
                    $errores[] = 'Error: Verificación de sesión falló';
                }
            } else {
                $errores[] = 'Error: No se pudo hacer login para probar sesión';
            }
            break;
            
        case 'test_logout':
            // Primero hacer login
            $login = simularLoginOptimizado('root', 'root');
            if ($login['exito']) {
                // Hacer logout
                LoginController::logout();
                
                // Verificar que la sesión se destruyó
                if (empty($_SESSION)) {
                    $resultados[] = [
                        'tipo' => 'success',
                        'mensaje' => 'Logout funcionando correctamente',
                        'datos' => [
                            'sesion_destruida' => true,
                            'session_vacia' => empty($_SESSION)
                        ]
                    ];
                } else {
                    $errores[] = 'Error: Sesión no se destruyó correctamente';
                }
            } else {
                $errores[] = 'Error: No se pudo hacer login para probar logout';
            }
            break;
            
        case 'test_compatibilidad_legacy':
            // Test del método estático legacy
            $resultado = LoginController::login('root', 'root');
            if ($resultado === 'Login exitoso' || is_array($resultado)) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Compatibilidad con método legacy funcionando',
                    'datos' => [
                        'resultado' => $resultado
                    ]
                ];
            } else {
                $errores[] = 'Error: Método legacy no funcionó correctamente';
            }
            break;
            
        case 'ejecutar_todos':
            // Ejecutar todas las pruebas
            $pruebas = [
                'test_login_exitoso' => 'Login Exitoso',
                'test_login_fallido' => 'Login Fallido',
                'test_validacion_entrada' => 'Validación de Entrada',
                'test_rate_limiting' => 'Rate Limiting',
                'test_verificacion_sesion' => 'Verificación de Sesión',
                'test_logout' => 'Logout',
                'test_compatibilidad_legacy' => 'Compatibilidad Legacy'
            ];
            
            foreach ($pruebas as $accion_test => $nombre_test) {
                $resultados[] = [
                    'tipo' => 'info',
                    'mensaje' => "Ejecutando: $nombre_test"
                ];
                
                switch ($accion_test) {
                    case 'test_login_exitoso':
                        $login = simularLoginOptimizado('root', 'root');
                        if ($login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => [
                                    'usuario' => $login['datos']['username'],
                                    'rol' => $login['datos']['rol']
                                ]
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - " . $login['mensaje'];
                        }
                        break;
                        
                    case 'test_login_fallido':
                        $login = simularLoginOptimizado('root', 'password_incorrecto');
                        if (!$login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso (rechazado correctamente)",
                                'datos' => $login['error_code']
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Aceptó credenciales incorrectas";
                        }
                        break;
                        
                    case 'test_validacion_entrada':
                        $login1 = simularLoginOptimizado('', '');
                        $login2 = simularLoginOptimizado('root', '');
                        $login3 = simularLoginOptimizado('', 'root');
                        
                        if (!$login1['exito'] && !$login2['exito'] && !$login3['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => 'Todas las validaciones funcionaron'
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Validaciones no funcionaron";
                        }
                        break;
                        
                    case 'test_rate_limiting':
                        // Simular intentos fallidos
                        for ($i = 0; $i < 6; $i++) {
                            simularLoginOptimizado('root', 'password_incorrecto');
                        }
                        
                        $estado = verificarEstadoBloqueo('root');
                        if ($estado && $estado['intentos_fallidos'] >= 5) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => [
                                    'intentos_fallidos' => $estado['intentos_fallidos']
                                ]
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Rate limiting no funcionó";
                        }
                        break;
                        
                    case 'test_verificacion_sesion':
                        $login = simularLoginOptimizado('root', 'root');
                        if ($login['exito']) {
                            $sesionValida = LoginController::isSessionValid();
                            if ($sesionValida) {
                                $resultados[] = [
                                    'tipo' => 'success',
                                    'mensaje' => "✓ $nombre_test: Exitoso",
                                    'datos' => 'Sesión válida'
                                ];
                            } else {
                                $errores[] = "✗ $nombre_test: Falló - Sesión no válida";
                            }
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - No se pudo hacer login";
                        }
                        break;
                        
                    case 'test_logout':
                        $login = simularLoginOptimizado('root', 'root');
                        if ($login['exito']) {
                            LoginController::logout();
                            if (empty($_SESSION)) {
                                $resultados[] = [
                                    'tipo' => 'success',
                                    'mensaje' => "✓ $nombre_test: Exitoso",
                                    'datos' => 'Sesión destruida'
                                ];
                            } else {
                                $errores[] = "✗ $nombre_test: Falló - Sesión no destruida";
                            }
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - No se pudo hacer login";
                        }
                        break;
                        
                    case 'test_compatibilidad_legacy':
                        $resultado = LoginController::login('root', 'root');
                        if ($resultado === 'Login exitoso' || is_array($resultado)) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => 'Método legacy funciona'
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Método legacy no funciona";
                        }
                        break;
                }
            }
            break;
    }
}

// Verificar estado actual del usuario
$usuario_actual = verificarUsuarioEnBD('root');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test LoginController Optimizado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .test-result {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4><i class="bi bi-shield-check me-2"></i>Test LoginController Optimizado</h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado actual -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Estado Actual:</h6>
                            <?php if ($usuario_actual): ?>
                                <p class="mb-1"><strong>Usuario root:</strong> Existe (ID: <?php echo $usuario_actual['id']; ?>)</p>
                                <p class="mb-1"><strong>Estado:</strong> <?php echo $usuario_actual['activo'] ? 'Activo' : 'Inactivo'; ?></p>
                                <p class="mb-1"><strong>Intentos fallidos:</strong> <?php echo $usuario_actual['intentos_fallidos'] ?? 0; ?></p>
                                <p class="mb-0"><strong>Bloqueado hasta:</strong> <?php echo $usuario_actual['bloqueado_hasta'] ?? 'No bloqueado'; ?></p>
                            <?php else: ?>
                                <p class="mb-0"><strong>Usuario root:</strong> No existe</p>
                            <?php endif; ?>
                        </div>

                        <!-- Nuevas funcionalidades -->
                        <div class="alert alert-success">
                            <h6><i class="bi bi-star me-2"></i>Nuevas Funcionalidades del LoginController:</h6>
                            <ul class="mb-0">
                                <li><strong>Validación de entrada:</strong> Verificación de datos de entrada</li>
                                <li><strong>Rate limiting:</strong> Bloqueo después de 5 intentos fallidos</li>
                                <li><strong>Logging:</strong> Registro de intentos de login</li>
                                <li><strong>Tokens de sesión:</strong> Tokens únicos para cada sesión</li>
                                <li><strong>Timeout de sesión:</strong> Sesiones expiran en 1 hora</li>
                                <li><strong>Manejo de errores:</strong> Respuestas estructuradas</li>
                                <li><strong>Compatibilidad:</strong> Método legacy mantenido</li>
                            </ul>
                        </div>

                        <!-- Botones de prueba -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="bi bi-play-circle me-2"></i>Pruebas del Sistema Optimizado:</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_login_exitoso">
                                    <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                        <i class="bi bi-check-circle me-1"></i>Test Login Exitoso
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_login_fallido">
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="bi bi-x-circle me-1"></i>Test Login Fallido
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_validacion_entrada">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-input-cursor me-1"></i>Test Validación Entrada
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_rate_limiting">
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-shield-lock me-1"></i>Test Rate Limiting
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_verificacion_sesion">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-person-check me-1"></i>Test Verificación Sesión
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_logout">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-box-arrow-right me-1"></i>Test Logout
                                    </button>
                                </form>
                            </div>
                            <div class="col-12 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="ejecutar_todos">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-play-fill me-2"></i>Ejecutar Todas las Pruebas
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Resultados -->
                        <?php if (!empty($resultados)): ?>
                            <div class="mb-4">
                                <h6><i class="bi bi-list-check me-2"></i>Resultados de las Pruebas:</h6>
                                <?php foreach ($resultados as $resultado): ?>
                                    <div class="test-result test-<?php echo $resultado['tipo']; ?>">
                                        <strong><?php echo htmlspecialchars($resultado['mensaje']); ?></strong>
                                        <?php if (isset($resultado['datos'])): ?>
                                            <details class="mt-2">
                                                <summary>Ver detalles técnicos</summary>
                                                <pre class="mt-2 mb-0"><code><?php echo htmlspecialchars(json_encode($resultado['datos'], JSON_PRETTY_PRINT)); ?></code></pre>
                                            </details>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Errores -->
                        <?php if (!empty($errores)): ?>
                            <div class="mb-4">
                                <h6><i class="bi bi-exclamation-triangle me-2"></i>Errores Encontrados:</h6>
                                <?php foreach ($errores as $error): ?>
                                    <div class="test-result test-error">
                                        <strong><?php echo htmlspecialchars($error); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="ActualizarTablaUsuarios.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-database-gear me-1"></i>Actualizar Tabla
                    </a>
                    <a href="CorregirHashUsuario.php" class="btn btn-outline-warning me-2">
                        <i class="bi bi-wrench me-1"></i>Corregir Hash
                    </a>
                    <a href="../../../index.php" class="btn btn-outline-success" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Login Real
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
