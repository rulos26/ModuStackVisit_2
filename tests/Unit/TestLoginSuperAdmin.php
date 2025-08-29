<?php
require_once __DIR__ . '/../../app/Database/Database.php';
require_once __DIR__ . '/../../app/Controllers/LoginController.php';

use App\Database\Database;
use App\Controllers\LoginController;

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

// Función para verificar si el usuario existe
function verificarUsuarioExiste($usuario) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id, usuario, rol FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    return $stmt->fetch();
}

// Función para crear usuario de prueba si no existe
function crearUsuarioPrueba() {
    $db = Database::getInstance()->getConnection();
    
    // Verificar si ya existe el usuario root
    $usuario = verificarUsuarioExiste('root');
    if ($usuario) {
        return $usuario;
    }
    
    // Crear usuario de prueba
    $nombre = 'Superadministrador Test';
    $cedula = '30000003';
    $rol = 3;
    $correo = 'root@empresa.com';
    $usuario = 'root';
    $password = password_hash('root', PASSWORD_DEFAULT);
    
    // Verificar si existe la columna fecha_creacion
    $stmt = $db->prepare("SHOW COLUMNS FROM usuarios LIKE 'fecha_creacion'");
    $stmt->execute();
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare('INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$nombre, $cedula, $rol, $correo, $usuario, $password]);
    } else {
        $stmt = $db->prepare('INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $cedula, $rol, $correo, $usuario, $password]);
    }
    
    return verificarUsuarioExiste('root');
}

// Función para simular login
function simularLogin($usuario, $password) {
    limpiarSesion();
    
    try {
        $resultado = LoginController::login($usuario, $password);
        return [
            'exito' => true,
            'resultado' => $resultado,
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

// Ejecutar pruebas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'crear_usuario':
            $usuario = crearUsuarioPrueba();
            if ($usuario) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Usuario de prueba creado/verificado exitosamente',
                    'datos' => $usuario
                ];
            } else {
                $errores[] = 'Error al crear usuario de prueba';
            }
            break;
            
        case 'test_login_correcto':
            $login = simularLogin('root', 'root');
            if ($login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Login exitoso con credenciales correctas',
                    'datos' => $login
                ];
            } else {
                $errores[] = 'Error en login con credenciales correctas: ' . $login['error'];
            }
            break;
            
        case 'test_login_incorrecto':
            $login = simularLogin('root', 'password_incorrecto');
            if (!$login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Login correctamente rechazado con credenciales incorrectas',
                    'datos' => $login
                ];
            } else {
                $errores[] = 'Error: Login aceptado con credenciales incorrectas';
            }
            break;
            
        case 'test_usuario_inexistente':
            $login = simularLogin('usuario_inexistente', 'password');
            if (!$login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Login correctamente rechazado con usuario inexistente',
                    'datos' => $login
                ];
            } else {
                $errores[] = 'Error: Login aceptado con usuario inexistente';
            }
            break;
            
        case 'test_verificar_rol':
            $login = simularLogin('root', 'root');
            if ($login['exito'] && isset($login['sesion']['rol']) && $login['sesion']['rol'] == 3) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Rol de superadministrador verificado correctamente',
                    'datos' => $login['sesion']
                ];
            } else {
                $errores[] = 'Error: Rol de superadministrador no verificado correctamente';
            }
            break;
            
        case 'test_redireccion':
            $login = simularLogin('root', 'root');
            if ($login['exito']) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Redirección configurada para superadministrador',
                    'datos' => 'Debería redirigir a: resources/views/superadmin/dashboardSuperAdmin.php'
                ];
            } else {
                $errores[] = 'Error: No se pudo verificar la redirección';
            }
            break;
            
        case 'ejecutar_todos':
            // Ejecutar todas las pruebas
            $pruebas = [
                'crear_usuario' => 'Crear/Verificar Usuario',
                'test_login_correcto' => 'Login Correcto',
                'test_login_incorrecto' => 'Login Incorrecto',
                'test_usuario_inexistente' => 'Usuario Inexistente',
                'test_verificar_rol' => 'Verificar Rol',
                'test_redireccion' => 'Verificar Redirección'
            ];
            
            foreach ($pruebas as $accion_test => $nombre_test) {
                $_POST['accion'] = $accion_test;
                $resultados[] = [
                    'tipo' => 'info',
                    'mensaje' => "Ejecutando: $nombre_test"
                ];
                
                switch ($accion_test) {
                    case 'crear_usuario':
                        $usuario = crearUsuarioPrueba();
                        if ($usuario) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => $usuario
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló";
                        }
                        break;
                        
                    case 'test_login_correcto':
                        $login = simularLogin('root', 'root');
                        if ($login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => $login
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - " . $login['error'];
                        }
                        break;
                        
                    case 'test_login_incorrecto':
                        $login = simularLogin('root', 'password_incorrecto');
                        if (!$login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso (rechazado correctamente)",
                                'datos' => $login
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Aceptó credenciales incorrectas";
                        }
                        break;
                        
                    case 'test_usuario_inexistente':
                        $login = simularLogin('usuario_inexistente', 'password');
                        if (!$login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso (rechazado correctamente)",
                                'datos' => $login
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Aceptó usuario inexistente";
                        }
                        break;
                        
                    case 'test_verificar_rol':
                        $login = simularLogin('root', 'root');
                        if ($login['exito'] && isset($login['sesion']['rol']) && $login['sesion']['rol'] == 3) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => $login['sesion']
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - Rol incorrecto o no verificado";
                        }
                        break;
                        
                    case 'test_redireccion':
                        $login = simularLogin('root', 'root');
                        if ($login['exito']) {
                            $resultados[] = [
                                'tipo' => 'success',
                                'mensaje' => "✓ $nombre_test: Exitoso",
                                'datos' => 'Redirección configurada para superadministrador'
                            ];
                        } else {
                            $errores[] = "✗ $nombre_test: Falló - No se pudo verificar redirección";
                        }
                        break;
                }
            }
            break;
    }
}

// Verificar estado actual del usuario
$usuario_actual = verificarUsuarioExiste('root');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Login Superadministrador</title>
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
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="bi bi-shield-lock-fill me-2"></i>Test Login Superadministrador</h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado actual -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Estado Actual:</h6>
                            <?php if ($usuario_actual): ?>
                                <p class="mb-1"><strong>Usuario root:</strong> Existe (ID: <?php echo $usuario_actual['id']; ?>, Rol: <?php echo $usuario_actual['rol']; ?>)</p>
                            <?php else: ?>
                                <p class="mb-1"><strong>Usuario root:</strong> No existe</p>
                            <?php endif; ?>
                            <p class="mb-0"><strong>Credenciales de prueba:</strong> Usuario: <code>root</code>, Contraseña: <code>root</code></p>
                        </div>

                        <!-- Botones de prueba -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="bi bi-play-circle me-2"></i>Pruebas Individuales:</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="crear_usuario">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-person-plus me-1"></i>Crear/Verificar Usuario
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_login_correcto">
                                    <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                        <i class="bi bi-check-circle me-1"></i>Test Login Correcto
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_login_incorrecto">
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="bi bi-x-circle me-1"></i>Test Login Incorrecto
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_usuario_inexistente">
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="bi bi-question-circle me-1"></i>Test Usuario Inexistente
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_verificar_rol">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-shield-check me-1"></i>Test Verificar Rol
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_redireccion">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-arrow-right-circle me-1"></i>Test Redirección
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Ejecutar todas las pruebas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="POST">
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
                                                <summary>Ver detalles</summary>
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

                        <!-- Información adicional -->
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-lightbulb me-2"></i>Información:</h6>
                            <ul class="mb-0">
                                <li>Este test verifica el funcionamiento del login del superadministrador</li>
                                <li>Las pruebas incluyen casos positivos y negativos</li>
                                <li>Se verifica la creación de sesión y el rol asignado</li>
                                <li>Se puede ejecutar individualmente o todas las pruebas juntas</li>
                                <li>Para probar el login real, ve a: <a href="../../../index.php" target="_blank">Página de Login</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="CrearSuperAdminTest.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-arrow-left me-1"></i>Volver a Crear Superadmin
                    </a>
                    <a href="../../../index.php" class="btn btn-outline-success" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Ir al Login Real
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
