<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$resultados = [];
$errores = [];

// Función para verificar usuario en BD
function verificarUsuarioEnBD($usuario) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    return $stmt->fetch();
}

// Función para simular la lógica del LoginController
function simularVerificacionPassword($password_ingresada, $hash_almacenado) {
    $isPasswordHash = (strlen($hash_almacenado) > 32);
    $passwordOk = false;
    
    if ($isPasswordHash) {
        $passwordOk = password_verify($password_ingresada, $hash_almacenado);
    } else {
        $passwordOk = (md5($password_ingresada) === $hash_almacenado);
    }
    
    return [
        'isPasswordHash' => $isPasswordHash,
        'passwordOk' => $passwordOk,
        'hash_length' => strlen($hash_almacenado),
        'hash_type' => $isPasswordHash ? 'bcrypt' : 'md5'
    ];
}

// Ejecutar pruebas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'verificar_usuario_root':
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Usuario root encontrado en BD',
                    'datos' => [
                        'id' => $usuario['id'],
                        'usuario' => $usuario['usuario'],
                        'rol' => $usuario['rol'],
                        'password_hash' => $usuario['password'],
                        'hash_length' => strlen($usuario['password'])
                    ]
                ];
            } else {
                $errores[] = 'Usuario root no encontrado en BD';
            }
            break;
            
        case 'test_password_verification':
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $verificacion = simularVerificacionPassword('root', $usuario['password']);
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Verificación de contraseña completada',
                    'datos' => [
                        'password_ingresada' => 'root',
                        'hash_almacenado' => $usuario['password'],
                        'hash_length' => $verificacion['hash_length'],
                        'hash_type' => $verificacion['hash_type'],
                        'isPasswordHash' => $verificacion['isPasswordHash'],
                        'passwordOk' => $verificacion['passwordOk']
                    ]
                ];
            } else {
                $errores[] = 'No se pudo verificar - usuario no encontrado';
            }
            break;
            
        case 'test_password_incorrecta':
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $verificacion = simularVerificacionPassword('password_incorrecta', $usuario['password']);
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Verificación de contraseña incorrecta',
                    'datos' => [
                        'password_ingresada' => 'password_incorrecta',
                        'hash_almacenado' => $usuario['password'],
                        'hash_length' => $verificacion['hash_length'],
                        'hash_type' => $verificacion['hash_type'],
                        'isPasswordHash' => $verificacion['isPasswordHash'],
                        'passwordOk' => $verificacion['passwordOk']
                    ]
                ];
            } else {
                $errores[] = 'No se pudo verificar - usuario no encontrado';
            }
            break;
            
        case 'test_creacion_hash':
            $password_original = 'root';
            $hash_creado = password_hash($password_original, PASSWORD_DEFAULT);
            $verificacion = password_verify($password_original, $hash_creado);
            
            $resultados[] = [
                'tipo' => 'success',
                'mensaje' => 'Test de creación y verificación de hash',
                'datos' => [
                    'password_original' => $password_original,
                    'hash_creado' => $hash_creado,
                    'hash_length' => strlen($hash_creado),
                    'verificacion_exitosa' => $verificacion,
                    'hash_type' => 'bcrypt (PASSWORD_DEFAULT)'
                ]
            ];
            break;
            
        case 'comparar_procesos':
            // Proceso 1: Creación (como en CrearSuperAdminTest.php)
            $password_original = 'root';
            $hash_creado = password_hash($password_original, PASSWORD_DEFAULT);
            
            // Proceso 2: Verificación (como en LoginController.php)
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $verificacion = simularVerificacionPassword($password_original, $usuario['password']);
                
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Comparación de procesos de creación y verificación',
                    'datos' => [
                        'proceso_creacion' => [
                            'password_original' => $password_original,
                            'funcion_usada' => 'password_hash($password, PASSWORD_DEFAULT)',
                            'hash_ejemplo' => $hash_creado,
                            'hash_length_ejemplo' => strlen($hash_creado)
                        ],
                        'proceso_verificacion' => [
                            'password_ingresada' => $password_original,
                            'hash_almacenado' => $usuario['password'],
                            'hash_length_almacenado' => $verificacion['hash_length'],
                            'hash_type' => $verificacion['hash_type'],
                            'verificacion_exitosa' => $verificacion['passwordOk']
                        ],
                        'comparacion' => [
                            'hash_creado_vs_almacenado' => (strlen($hash_creado) === $verificacion['hash_length']) ? 'Longitudes similares' : 'Longitudes diferentes',
                            'ambos_bcrypt' => ($verificacion['hash_type'] === 'bcrypt') ? 'Sí' : 'No'
                        ]
                    ]
                ];
            } else {
                $errores[] = 'No se pudo comparar - usuario no encontrado';
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
    <title>Test Verificación de Contraseñas</title>
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
                    <div class="card-header bg-info text-white text-center">
                        <h4><i class="bi bi-key-fill me-2"></i>Test Verificación de Contraseñas</h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado actual -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Estado Actual:</h6>
                            <?php if ($usuario_actual): ?>
                                <p class="mb-1"><strong>Usuario root:</strong> Existe en BD</p>
                                <p class="mb-1"><strong>Hash almacenado:</strong> <?php echo substr($usuario_actual['password'], 0, 20) . '...'; ?></p>
                                <p class="mb-0"><strong>Longitud del hash:</strong> <?php echo strlen($usuario_actual['password']); ?> caracteres</p>
                            <?php else: ?>
                                <p class="mb-0"><strong>Usuario root:</strong> No existe en BD</p>
                            <?php endif; ?>
                        </div>

                        <!-- Explicación del proceso -->
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-lightbulb me-2"></i>Proceso de Verificación:</h6>
                            <ol class="mb-0">
                                <li><strong>Creación (CrearSuperAdminTest.php):</strong> <code>password_hash('root', PASSWORD_DEFAULT)</code></li>
                                <li><strong>Almacenamiento:</strong> Hash se guarda en BD en campo <code>password</code></li>
                                <li><strong>Verificación (LoginController.php):</strong> <code>password_verify('root', $hash_almacenado)</code></li>
                                <li><strong>Resultado:</strong> true/false según coincidencia</li>
                            </ol>
                        </div>

                        <!-- Botones de prueba -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="bi bi-play-circle me-2"></i>Pruebas de Verificación:</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="verificar_usuario_root">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-search me-1"></i>Verificar Usuario en BD
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_password_verification">
                                    <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                        <i class="bi bi-check-circle me-1"></i>Test Password Correcta
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_password_incorrecta">
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="bi bi-x-circle me-1"></i>Test Password Incorrecta
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="test_creacion_hash">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-plus-circle me-1"></i>Test Creación Hash
                                    </button>
                                </form>
                            </div>
                            <div class="col-12 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="comparar_procesos">
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="bi bi-arrow-left-right me-2"></i>Comparar Procesos de Creación y Verificación
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
                    <a href="TestLoginSuperAdmin.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-arrow-left me-1"></i>Volver a Test Login
                    </a>
                    <a href="CrearSuperAdminTest.php" class="btn btn-outline-success me-2">
                        <i class="bi bi-person-plus me-1"></i>Crear Superadmin
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
