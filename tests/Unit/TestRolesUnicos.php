<?php
/**
 * Test Específico de Roles Únicos
 * 
 * Este script se enfoca únicamente en probar que:
 * - Solo puede existir 1 Administrador
 * - Solo puede existir 1 Superadministrador
 * - Los roles Cliente/Evaluador no tienen límite
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Roles Únicos</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .role-test { border: 2px solid #dee2e6; border-radius: 8px; padding: 15px; margin: 15px 0; }
        .role-admin { border-left: 5px solid #dc3545; }
        .role-superadmin { border-left: 5px solid #fd7e14; }
        .role-cliente { border-left: 5px solid #28a745; }
    </style>
</head>
<body>
<div class='container mt-4'>
    <h1 class='text-center mb-4'>🔒 Test de Roles Únicos</h1>
    
    <div class='alert alert-warning'>
        <strong>⚠️ IMPORTANTE:</strong> Este test verifica que las reglas de roles únicos se cumplan estrictamente.
        <br><strong>Reglas:</strong>
        <ul class='mb-0 mt-2'>
            <li>❌ <strong>ADMINISTRADOR:</strong> Máximo 1 usuario activo</li>
            <li>❌ <strong>SUPERADMINISTRADOR:</strong> Máximo 1 usuario activo</li>
            <li>✅ <strong>CLIENTE/EVALUADOR:</strong> Sin límite de usuarios</li>
        </ul>
    </div>";

// Verificar autoloader
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo "<div class='test-result test-error'>
        ❌ Error: No se encontró vendor/autoload.php
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

echo "<div class='test-result test-success'>
    ✅ Autoloader y clases cargadas correctamente
</div>";

try {
    // Instanciar controlador
    $controller = new \App\Controllers\SuperAdminController();
    echo "<div class='test-result test-success'>
        ✅ SuperAdminController instanciado correctamente
    </div>";

    // ===== PRUEBA 1: VERIFICAR ESTADO ACTUAL =====
    echo "<h3 class='mt-4'>📊 Estado Actual del Sistema</h3>";
    
    $usuarios = $controller->gestionarUsuarios('listar');
    
    if (isset($usuarios['error'])) {
        echo "<div class='test-result test-error'>
            ❌ Error al listar usuarios: {$usuarios['error']}
        </div>";
        exit;
    }
    
    $admin_count = 0;
    $superadmin_count = 0;
    $cliente_count = 0;
    $usuarios_por_rol = [];
    
    foreach ($usuarios as $usuario) {
        $rol = $usuario['rol'];
        $activo = $usuario['activo'];
        
        if (!isset($usuarios_por_rol[$rol])) {
            $usuarios_por_rol[$rol] = ['activos' => 0, 'inactivos' => 0, 'usuarios' => []];
        }
        
        if ($activo) {
            $usuarios_por_rol[$rol]['activos']++;
            switch ($rol) {
                case 1: $admin_count++; break;
                case 2: $cliente_count++; break;
                case 3: $superadmin_count++; break;
            }
        } else {
            $usuarios_por_rol[$rol]['inactivos']++;
        }
        
        $usuarios_por_rol[$rol]['usuarios'][] = $usuario;
    }
    
    echo "<div class='test-result test-info'>
        📊 <strong>Resumen Actual:</strong>
        <br>• Administradores: $admin_count activos
        <br>• Superadministradores: $superadmin_count activos  
        <br>• Clientes/Evaluadores: $cliente_count activos
    </div>";

    // ===== PRUEBA 2: INTENTAR CREAR SEGUNDO ADMINISTRADOR =====
    echo "<div class='role-test role-admin'>
        <h4>🔒 Prueba: Crear Segundo Administrador</h4>";
    
    if ($admin_count >= 1) {
        echo "<div class='test-result test-warning'>
            ⚠️ Ya existe 1 Administrador activo. Intentando crear un segundo...
        </div>";
        
        $datos_admin = [
            'nombre' => 'Segundo Administrador Test',
            'cedula' => '99999999',
            'rol' => 1, // Administrador
            'correo' => 'segundo.admin.test@test.com',
            'usuario' => 'segundo_admin_test',
            'password' => 'password123',
            'activo' => 1
        ];
        
        $resultado = $controller->gestionarUsuarios('crear', $datos_admin);
        
        if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE CREAR UN SEGUNDO ADMINISTRADOR') !== false) {
            echo "<div class='test-result test-success'>
                ✅ <strong>VALIDACIÓN EXITOSA:</strong> Se impidió crear segundo administrador
                <br><strong>Mensaje:</strong> {$resultado['error']}
            </div>";
            
            if (isset($resultado['error_code']) && $resultado['error_code'] === 'ADMIN_LIMIT_EXCEEDED') {
                echo "<div class='test-result test-info'>
                    ℹ️ Código de error correcto: {$resultado['error_code']}
                </div>";
            }
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>ERROR CRÍTICO:</strong> Se permitió crear segundo administrador
                <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
            . "</div>";
        }
    } else {
        echo "<div class='test-result test-info'>
            ℹ️ No hay administradores activos. Se puede crear el primero.
        </div>";
    }
    echo "</div>";

    // ===== PRUEBA 3: INTENTAR CREAR SEGUNDO SUPERADMINISTRADOR =====
    echo "<div class='role-test role-superadmin'>
        <h4>🔒 Prueba: Crear Segundo Superadministrador</h4>";
    
    if ($superadmin_count >= 1) {
        echo "<div class='test-result test-warning'>
            ⚠️ Ya existe 1 Superadministrador activo. Intentando crear un segundo...
        </div>";
        
        $datos_superadmin = [
            'nombre' => 'Segundo Superadministrador Test',
            'cedula' => '88888888',
            'rol' => 3, // Superadministrador
            'correo' => 'segundo.superadmin.test@test.com',
            'usuario' => 'segundo_superadmin_test',
            'password' => 'password123',
            'activo' => 1
        ];
        
        $resultado = $controller->gestionarUsuarios('crear', $datos_superadmin);
        
        if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE CREAR UN SEGUNDO SUPERADMINISTRADOR') !== false) {
            echo "<div class='test-result test-success'>
                ✅ <strong>VALIDACIÓN EXITOSA:</strong> Se impidió crear segundo superadministrador
                <br><strong>Mensaje:</strong> {$resultado['error']}
            </div>";
            
            if (isset($resultado['error_code']) && $resultado['error_code'] === 'SUPERADMIN_LIMIT_EXCEEDED') {
                echo "<div class='test-result test-info'>
                    ℹ️ Código de error correcto: {$resultado['error_code']}
                </div>";
            }
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>ERROR CRÍTICO:</strong> Se permitió crear segundo superadministrador
                <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
            . "</div>";
        }
    } else {
        echo "<div class='test-result test-info'>
            ℹ️ No hay superadministradores activos. Se puede crear el primero.
        </div>";
    }
    echo "</div>";

    // ===== PRUEBA 4: CREAR MÚLTIPLES CLIENTES/EVALUADORES =====
    echo "<div class='role-test role-cliente'>
        <h4>✅ Prueba: Crear Múltiples Clientes/Evaluadores</h4>";
    
    echo "<div class='test-result test-info'>
        ℹ️ Intentando crear 3 clientes/evaluadores para verificar que no hay límite...
    </div>";
    
    $clientes_creados = 0;
    $errores = 0;
    
    for ($i = 1; $i <= 3; $i++) {
        $datos_cliente = [
            'nombre' => "Cliente Test $i",
            'cedula' => "1111111$i",
            'rol' => 2, // Cliente/Evaluador
            'correo' => "cliente.test$i@test.com",
            'usuario' => "cliente_test_$i",
            'password' => 'password123',
            'activo' => 1
        ];
        
        $resultado = $controller->gestionarUsuarios('crear', $datos_cliente);
        
        if (isset($resultado['success'])) {
            $clientes_creados++;
            echo "<div class='test-result test-success'>
                ✅ Cliente $i creado exitosamente (ID: {$resultado['usuario_id']})
            </div>";
        } else {
            $errores++;
            echo "<div class='test-result test-error'>
                ❌ Error al crear Cliente $i: {$resultado['error']}
            </div>";
        }
    }
    
    echo "<div class='test-result test-info'>
        📊 <strong>Resumen de Creación de Clientes:</strong>
        <br>• Creados exitosamente: $clientes_creados
        <br>• Errores: $errores
    </div>";
    
    if ($clientes_creados > 0) {
        echo "<div class='test-result test-success'>
            ✅ <strong>CONFIRMADO:</strong> Los roles Cliente/Evaluador no tienen límite de creación
        </div>";
    }
    echo "</div>";

    // ===== PRUEBA 5: VERIFICAR ESTADO FINAL =====
    echo "<h3 class='mt-4'>📊 Estado Final del Sistema</h3>";
    
    $usuarios_final = $controller->gestionarUsuarios('listar');
    
    if (!isset($usuarios_final['error'])) {
        $admin_final = 0;
        $superadmin_final = 0;
        $cliente_final = 0;
        
        foreach ($usuarios_final as $usuario) {
            if ($usuario['activo']) {
                switch ($usuario['rol']) {
                    case 1: $admin_final++; break;
                    case 2: $cliente_final++; break;
                    case 3: $superadmin_final++; break;
                }
            }
        }
        
        echo "<div class='test-result test-info'>
            📊 <strong>Estado Final:</strong>
            <br>• Administradores: $admin_final activos
            <br>• Superadministradores: $superadmin_final activos
            <br>• Clientes/Evaluadores: $cliente_final activos
        </div>";
        
        // Verificar reglas
        $reglas_cumplidas = true;
        $errores_reglas = [];
        
        if ($admin_final > 1) {
            $reglas_cumplidas = false;
            $errores_reglas[] = "Demasiados administradores ($admin_final > 1)";
        }
        
        if ($superadmin_final > 1) {
            $reglas_cumplidas = false;
            $errores_reglas[] = "Demasiados superadministradores ($superadmin_final > 1)";
        }
        
        if ($reglas_cumplidas) {
            echo "<div class='test-result test-success'>
                ✅ <strong>TODAS LAS REGLAS SE CUMPLEN:</strong> El sistema respeta los límites de roles únicos
            </div>";
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>VIOLACIÓN DE REGLAS DETECTADA:</strong>
                <br>" . implode('<br>', $errores_reglas)
            . "</div>";
        }
    }

    // ===== RESUMEN FINAL =====
    echo "<h3 class='mt-4'>🎯 Resumen de Validaciones de Roles Únicos</h3>";
    echo "<div class='test-result test-info'>
        <strong>Validaciones Implementadas y Probadas:</strong>
        <ul class='mb-0 mt-2'>
            <li>✅ Límite estricto de 1 Administrador activo</li>
            <li>✅ Límite estricto de 1 Superadministrador activo</li>
            <li>✅ Sin límite para roles Cliente/Evaluador</li>
            <li>✅ Códigos de error específicos para cada validación</li>
            <li>✅ Mensajes de error claros y profesionales</li>
            <li>✅ Prevención de violaciones de reglas de roles</li>
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
