<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

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
        'prefijo' => substr($hash, 0, 7)
    ];
}

// Verificar usuario root
$usuario = verificarUsuarioEnBD('root');

if ($usuario) {
    $analisis = analizarHash($usuario['password']);
    
    echo "<h2>Análisis del Hash del Usuario 'root'</h2>";
    echo "<pre>";
    echo "Usuario: " . $usuario['usuario'] . "\n";
    echo "ID: " . $usuario['id'] . "\n";
    echo "Rol: " . $usuario['rol'] . "\n";
    echo "Hash completo: " . $usuario['password'] . "\n";
    echo "Longitud: " . $analisis['longitud'] . " caracteres\n";
    echo "Tipo detectado: " . $analisis['tipo'] . "\n";
    echo "Es bcrypt: " . ($analisis['esBcrypt'] ? 'SÍ' : 'NO') . "\n";
    echo "Es MD5: " . ($analisis['esMD5'] ? 'SÍ' : 'NO') . "\n";
    echo "Prefijo: " . $analisis['prefijo'] . "\n";
    
    // Test de verificación
    echo "\n=== TEST DE VERIFICACIÓN ===\n";
    
    // Test con password_verify (bcrypt)
    $test_bcrypt = password_verify('root', $usuario['password']);
    echo "password_verify('root', hash): " . ($test_bcrypt ? 'TRUE' : 'FALSE') . "\n";
    
    // Test con MD5
    $test_md5 = (md5('root') === $usuario['password']);
    echo "md5('root') === hash: " . ($test_md5 ? 'TRUE' : 'FALSE') . "\n";
    
    // Test con lógica del LoginController
    $isPasswordHash = (strlen($usuario['password']) > 32);
    $passwordOk = false;
    if ($isPasswordHash) {
        $passwordOk = password_verify('root', $usuario['password']);
    } else {
        $passwordOk = (md5('root') === $usuario['password']);
    }
    echo "LoginController logic: " . ($passwordOk ? 'TRUE' : 'FALSE') . "\n";
    
    echo "</pre>";
    
    // Recomendación
    echo "<h3>Recomendación:</h3>";
    if ($analisis['esBcrypt'] && $test_bcrypt) {
        echo "<p style='color: green;'>✅ El hash es bcrypt y la verificación funciona correctamente.</p>";
    } elseif ($analisis['esMD5'] && $test_md5) {
        echo "<p style='color: orange;'>⚠️ El hash es MD5 y la verificación funciona, pero deberías migrar a bcrypt.</p>";
    } else {
        echo "<p style='color: red;'>❌ Hay un problema con el hash o la verificación.</p>";
    }
    
} else {
    echo "<h2>Usuario 'root' no encontrado</h2>";
    echo "<p>Primero debes crear el usuario usando CrearSuperAdminTest.php</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Hash Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4><i class="bi bi-search me-2"></i>Verificar Hash del Usuario</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Este test verifica:</h6>
                            <ul class="mb-0">
                                <li>Qué tipo de hash tiene el usuario 'root'</li>
                                <li>Si la verificación funciona correctamente</li>
                                <li>Si hay inconsistencias entre creación y verificación</li>
                            </ul>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="CrearSuperAdminTest.php" class="btn btn-outline-primary me-2">
                                <i class="bi bi-person-plus me-1"></i>Crear Superadmin
                            </a>
                            <a href="TestLoginSuperAdmin.php" class="btn btn-outline-success me-2">
                                <i class="bi bi-shield-lock me-1"></i>Test Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
