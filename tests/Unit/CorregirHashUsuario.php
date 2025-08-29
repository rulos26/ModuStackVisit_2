<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
$resultados = [];

// Función para verificar usuario en BD
function verificarUsuarioEnBD($usuario) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    return $stmt->fetch();
}

// Función para analizar el hash
function analizarHash($hash) {
    $longitud = strlen($hash);
    $tipo = '';
    $esBcrypt = false;
    $esMD5 = false;
    
    if ($longitud > 32) {
        $tipo = 'bcrypt';
        $esBcrypt = true;
    } else {
        $tipo = 'md5';
        $esMD5 = true;
    }
    
    return [
        'hash' => $hash,
        'longitud' => $longitud,
        'tipo' => $tipo,
        'esBcrypt' => $esBcrypt,
        'esMD5' => $esMD5,
        'prefijo' => substr($hash, 0, 7),
        'esValido' => ($longitud === 60 && $esBcrypt) || ($longitud === 32 && $esMD5)
    ];
}

// Función para corregir el hash
function corregirHashUsuario($usuario_id, $password_plain) {
    $db = Database::getInstance()->getConnection();
    
    // Generar nuevo hash bcrypt
    $nuevo_hash = password_hash($password_plain, PASSWORD_DEFAULT);
    
    // Actualizar en la base de datos
    $stmt = $db->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
    $resultado = $stmt->execute([$nuevo_hash, $usuario_id]);
    
    return [
        'exito' => $resultado,
        'nuevo_hash' => $nuevo_hash,
        'longitud_nuevo' => strlen($nuevo_hash)
    ];
}

// Procesar la corrección
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'corregir_hash':
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $analisis_antes = analizarHash($usuario['password']);
                
                // Corregir el hash
                $correccion = corregirHashUsuario($usuario['id'], 'root');
                
                if ($correccion['exito']) {
                    // Verificar después de la corrección
                    $usuario_despues = verificarUsuarioEnBD('root');
                    $analisis_despues = analizarHash($usuario_despues['password']);
                    
                    $resultados[] = [
                        'tipo' => 'success',
                        'mensaje' => 'Hash corregido exitosamente',
                        'datos' => [
                            'antes' => [
                                'hash' => $usuario['password'],
                                'longitud' => $analisis_antes['longitud'],
                                'esValido' => $analisis_antes['esValido'],
                                'verificacion' => password_verify('root', $usuario['password'])
                            ],
                            'despues' => [
                                'hash' => $usuario_despues['password'],
                                'longitud' => $analisis_despues['longitud'],
                                'esValido' => $analisis_despues['esValido'],
                                'verificacion' => password_verify('root', $usuario_despues['password'])
                            ]
                        ]
                    ];
                } else {
                    $resultados[] = [
                        'tipo' => 'error',
                        'mensaje' => 'Error al corregir el hash'
                    ];
                }
            } else {
                $resultados[] = [
                    'tipo' => 'error',
                    'mensaje' => 'Usuario root no encontrado'
                ];
            }
            break;
            
        case 'verificar_estado':
            $usuario = verificarUsuarioEnBD('root');
            if ($usuario) {
                $analisis = analizarHash($usuario['password']);
                $verificacion = password_verify('root', $usuario['password']);
                
                $resultados[] = [
                    'tipo' => 'info',
                    'mensaje' => 'Estado actual del hash',
                    'datos' => [
                        'usuario' => $usuario['usuario'],
                        'id' => $usuario['id'],
                        'rol' => $usuario['rol'],
                        'hash' => $usuario['password'],
                        'longitud' => $analisis['longitud'],
                        'tipo' => $analisis['tipo'],
                        'esValido' => $analisis['esValido'],
                        'verificacion' => $verificacion
                    ]
                ];
            } else {
                $resultados[] = [
                    'tipo' => 'error',
                    'mensaje' => 'Usuario root no encontrado'
                ];
            }
            break;
    }
}

// Verificar estado actual del usuario
$usuario_actual = verificarUsuarioEnBD('root');
$analisis_actual = $usuario_actual ? analizarHash($usuario_actual['password']) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corregir Hash Usuario</title>
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
                    <div class="card-header bg-warning text-dark text-center">
                        <h4><i class="bi bi-wrench me-2"></i>Corregir Hash del Usuario</h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado actual -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Estado Actual:</h6>
                            <?php if ($usuario_actual && $analisis_actual): ?>
                                <p class="mb-1"><strong>Usuario:</strong> <?php echo $usuario_actual['usuario']; ?></p>
                                <p class="mb-1"><strong>Hash:</strong> <?php echo substr($usuario_actual['password'], 0, 20) . '...'; ?></p>
                                <p class="mb-1"><strong>Longitud:</strong> <?php echo $analisis_actual['longitud']; ?> caracteres</p>
                                <p class="mb-1"><strong>Tipo:</strong> <?php echo $analisis_actual['tipo']; ?></p>
                                <p class="mb-1"><strong>Es válido:</strong> <?php echo $analisis_actual['esValido'] ? 'SÍ' : 'NO'; ?></p>
                                <p class="mb-0"><strong>Verificación:</strong> <?php echo password_verify('root', $usuario_actual['password']) ? 'TRUE' : 'FALSE'; ?></p>
                            <?php else: ?>
                                <p class="mb-0"><strong>Usuario root:</strong> No encontrado</p>
                            <?php endif; ?>
                        </div>

                        <!-- Diagnóstico del problema -->
                        <?php if ($usuario_actual && $analisis_actual): ?>
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle me-2"></i>Diagnóstico:</h6>
                                <?php if (!$analisis_actual['esValido']): ?>
                                    <p class="mb-1"><strong>Problema detectado:</strong> Hash incompleto o corrupto</p>
                                    <p class="mb-1"><strong>Longitud esperada:</strong> 60 caracteres (bcrypt)</p>
                                    <p class="mb-1"><strong>Longitud actual:</strong> <?php echo $analisis_actual['longitud']; ?> caracteres</p>
                                    <p class="mb-0"><strong>Solución:</strong> Regenerar el hash con password_hash()</p>
                                <?php else: ?>
                                    <p class="mb-0"><strong>Estado:</strong> Hash válido</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Botones de acción -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="bi bi-tools me-2"></i>Acciones:</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="verificar_estado">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-search me-1"></i>Verificar Estado Actual
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 mb-2">
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres corregir el hash?');">
                                    <input type="hidden" name="accion" value="corregir_hash">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-wrench me-2"></i>Corregir Hash del Usuario
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Resultados -->
                        <?php if (!empty($resultados)): ?>
                            <div class="mb-4">
                                <h6><i class="bi bi-list-check me-2"></i>Resultados:</h6>
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

                        <!-- Información adicional -->
                        <div class="alert alert-success">
                            <h6><i class="bi bi-lightbulb me-2"></i>Información:</h6>
                            <ul class="mb-0">
                                <li>Este script corrige hashes incompletos o corruptos</li>
                                <li>Regenera el hash usando <code>password_hash('root', PASSWORD_DEFAULT)</code></li>
                                <li>Mantiene la misma contraseña: <code>root</code></li>
                                <li>Después de la corrección, el login debería funcionar</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="VerificarHashUsuario.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Verificar Hash
                    </a>
                    <a href="TestLoginSuperAdmin.php" class="btn btn-outline-success me-2">
                        <i class="bi bi-shield-lock me-1"></i>Test Login
                    </a>
                    <a href="CrearSuperAdminTest.php" class="btn btn-outline-warning me-2">
                        <i class="bi bi-person-plus me-1"></i>Crear Superadmin
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
