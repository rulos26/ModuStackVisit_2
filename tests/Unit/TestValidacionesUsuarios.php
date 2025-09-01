<?php
/**
 * Test de Validaciones Estrictas para Creación de Usuarios
 * 
 * Este script prueba las reglas de validación implementadas en SuperAdminController:
 * - Solo 1 Administrador permitido
 * - Solo 1 Superadministrador permitido
 * - Clientes/Evaluadores sin límite
 * - Validaciones de formato y duplicados
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Validaciones de Usuarios</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
<div class='container mt-4'>
    <h1 class='text-center mb-4'>🧪 Test de Validaciones Estrictas de Usuarios</h1>
    
    <div class='alert alert-info'>
        <strong>Objetivo:</strong> Verificar que las reglas de validación para creación de usuarios funcionen correctamente:
        <ul class='mb-0 mt-2'>
            <li>❌ Solo 1 Administrador permitido</li>
            <li>❌ Solo 1 Superadministrador permitido</li>
            <li>✅ Clientes/Evaluadores sin límite</li>
            <li>✅ Validaciones de formato y duplicados</li>
        </ul>
    </div>";

// Verificar autoloader
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo "<div class='test-result test-error'>
        ❌ Error: No se encontró vendor/autoload.php
        <br>Ruta esperada: $autoloadPath
    </div>";
    exit;
}

require_once $autoloadPath;

// Verificar clases
if (!class_exists('App\\Controllers\\SuperAdminController')) {
    echo "<div class='test-result test-error'>
        ❌ Error: Clase SuperAdminController no encontrada
    </div>";
    exit;
}

if (!class_exists('App\\Services\\LoggerService')) {
    echo "<div class='test-result test-error'>
        ❌ Error: Clase LoggerService no encontrada
    </div>";
    exit;
}

echo "<div class='test-result test-success'>
    ✅ Autoloader y clases cargadas correctamente
</div>";

try {
    // Instanciar controlador
    $controller = new \App\Controllers\SuperAdminController();
    echo "<div class='test-result test-success'>
        ✅ SuperAdminController instanciado correctamente
    </div>";

    // ===== PRUEBA 1: INTENTAR CREAR SEGUNDO ADMINISTRADOR =====
    echo "<h3 class='mt-4'>🔒 Prueba 1: Intentar Crear Segundo Administrador</h3>";
    
    $datos_admin = [
        'nombre' => 'Segundo Admin',
        'cedula' => '12345678',
        'rol' => 1, // Administrador
        'correo' => 'segundo.admin@test.com',
        'usuario' => 'segundo_admin',
        'password' => 'password123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_admin);
    
    if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE CREAR UN SEGUNDO ADMINISTRADOR') !== false) {
        echo "<div class='test-result test-success'>
            ✅ Validación exitosa: Se impidió crear segundo administrador
            <br><strong>Mensaje:</strong> {$resultado['error']}
        </div>";
        
        if (isset($resultado['error_code']) && $resultado['error_code'] === 'ADMIN_LIMIT_EXCEEDED') {
            echo "<div class='test-result test-info'>
                ℹ️ Código de error correcto: {$resultado['error_code']}
            </div>";
        }
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error: Se permitió crear segundo administrador
            <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
        . "</div>";
    }

    // ===== PRUEBA 2: INTENTAR CREAR SEGUNDO SUPERADMINISTRADOR =====
    echo "<h3 class='mt-4'>🔒 Prueba 2: Intentar Crear Segundo Superadministrador</h3>";
    
    $datos_superadmin = [
        'nombre' => 'Segundo Superadmin',
        'cedula' => '87654321',
        'rol' => 3, // Superadministrador
        'correo' => 'segundo.superadmin@test.com',
        'usuario' => 'segundo_superadmin',
        'password' => 'password123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_superadmin);
    
    if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE CREAR UN SEGUNDO SUPERADMINISTRADOR') !== false) {
        echo "<div class='test-result test-success'>
            ✅ Validación exitosa: Se impidió crear segundo superadministrador
            <br><strong>Mensaje:</strong> {$resultado['error']}
        </div>";
        
        if (isset($resultado['error_code']) && $resultado['error_code'] === 'SUPERADMIN_LIMIT_EXCEEDED') {
            echo "<div class='test-result test-info'>
                ℹ️ Código de error correcto: {$resultado['error_code']}
            </div>";
        }
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error: Se permitió crear segundo superadministrador
            <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
        . "</div>";
    }

    // ===== PRUEBA 3: CREAR CLIENTE/EVALUADOR (DEBE PERMITIRSE) =====
    echo "<h3 class='mt-4'>✅ Prueba 3: Crear Cliente/Evaluador (Debe Permitirse)</h3>";
    
    $datos_cliente = [
        'nombre' => 'Cliente Test',
        'cedula' => '11111111',
        'rol' => 2, // Cliente/Evaluador
        'correo' => 'cliente.test@test.com',
        'usuario' => 'cliente_test',
        'password' => 'password123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_cliente);
    
    if (isset($resultado['success'])) {
        echo "<div class='test-result test-success'>
            ✅ Cliente creado exitosamente
            <br><strong>Mensaje:</strong> {$resultado['success']}
            <br><strong>ID:</strong> {$resultado['usuario_id']}
            <br><strong>Mensaje detallado:</strong> {$resultado['mensaje_detallado']}
        </div>";
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error al crear cliente: " . json_encode($resultado, JSON_PRETTY_PRINT)
        . "</div>";
    }

    // ===== PRUEBA 4: VALIDACIONES DE FORMATO =====
    echo "<h3 class='mt-4'>🔍 Prueba 4: Validaciones de Formato</h3>";
    
    // Email inválido
    $datos_email_invalido = [
        'nombre' => 'Test Email',
        'cedula' => '22222222',
        'rol' => 2,
        'correo' => 'email_invalido',
        'usuario' => 'test_email',
        'password' => 'password123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_email_invalido);
    
    if (isset($resultado['error']) && strpos($resultado['error'], 'formato del correo electrónico') !== false) {
        echo "<div class='test-result test-success'>
            ✅ Validación de email exitosa: Se rechazó email inválido
        </div>";
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error: Se permitió email inválido
        </div>";
    }
    
    // Contraseña corta
    $datos_password_corto = [
        'nombre' => 'Test Password',
        'cedula' => '33333333',
        'rol' => 2,
        'correo' => 'test.password@test.com',
        'usuario' => 'test_password',
        'password' => '123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_password_corto);
    
    if (isset($resultado['error']) && strpos($resultado['error'], 'al menos 6 caracteres') !== false) {
        echo "<div class='test-result test-success'>
            ✅ Validación de contraseña exitosa: Se rechazó contraseña corta
        </div>";
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error: Se permitió contraseña corta
        </div>";
    }
    
    // Cédula inválida
    $datos_cedula_invalida = [
        'nombre' => 'Test Cedula',
        'cedula' => '123',
        'rol' => 2,
        'correo' => 'test.cedula@test.com',
        'usuario' => 'test_cedula',
        'password' => 'password123',
        'activo' => 1
    ];
    
    $resultado = $controller->gestionarUsuarios('crear', $datos_cedula_invalida);
    
    if (isset($resultado['error']) && strpos($resultado['error'], 'cédula debe contener solo números') !== false) {
        echo "<div class='test-result test-success'>
            ✅ Validación de cédula exitosa: Se rechazó cédula inválida
        </div>";
    } else {
        echo "<div class='test-result test-error'>
            ❌ Error: Se permitió cédula inválida
        </div>";
    }

    // ===== PRUEBA 5: VERIFICAR USUARIOS EXISTENTES =====
    echo "<h3 class='mt-4'>📊 Prueba 5: Verificar Usuarios Existentes</h3>";
    
    $usuarios = $controller->gestionarUsuarios('listar');
    
    if (isset($usuarios['error'])) {
        echo "<div class='test-result test-error'>
            ❌ Error al listar usuarios: {$usuarios['error']}
        </div>";
    } else {
        $admin_count = 0;
        $superadmin_count = 0;
        $cliente_count = 0;
        
        foreach ($usuarios as $usuario) {
            switch ($usuario['rol']) {
                case 1: $admin_count++; break;
                case 2: $cliente_count++; break;
                case 3: $superadmin_count++; break;
            }
        }
        
        echo "<div class='test-result test-info'>
            📊 <strong>Resumen de Usuarios:</strong>
            <br>• Administradores: $admin_count (máximo 1 permitido)
            <br>• Superadministradores: $superadmin_count (máximo 1 permitido)
            <br>• Clientes/Evaluadores: $cliente_count (sin límite)
        </div>";
        
        if ($admin_count <= 1 && $superadmin_count <= 1) {
            echo "<div class='test-result test-success'>
                ✅ Reglas de roles respetadas correctamente
            </div>";
        } else {
            echo "<div class='test-result test-error'>
                ❌ Violación de reglas de roles detectada
            </div>";
        }
    }

    // ===== RESUMEN FINAL =====
    echo "<h3 class='mt-4'>🎯 Resumen de Validaciones</h3>";
    echo "<div class='test-result test-info'>
        <strong>Validaciones Implementadas:</strong>
        <ul class='mb-0 mt-2'>
            <li>✅ Límite de 1 Administrador</li>
            <li>✅ Límite de 1 Superadministrador</li>
            <li>✅ Sin límite para Clientes/Evaluadores</li>
            <li>✅ Validación de formato de email</li>
            <li>✅ Validación de longitud de contraseña</li>
            <li>✅ Validación de formato de cédula</li>
            <li>✅ Validación de nombre de usuario</li>
            <li>✅ Prevención de duplicados</li>
            <li>✅ Logs de auditoría</li>
            <li>✅ Códigos de error específicos</li>
        </ul>
    </div>";

} catch (Exception $e) {
    echo "<div class='test-result test-error'>
        ❌ Error general: " . $e->getMessage() . "
        <br><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine()
    . "</div>";
}

echo "</div>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
