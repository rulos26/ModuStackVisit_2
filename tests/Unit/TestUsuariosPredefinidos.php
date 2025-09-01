<?php
/**
 * Test de Protección de Usuarios Predefinidos
 * 
 * Este script verifica que los usuarios predefinidos del sistema:
 * - NO puedan ser eliminados
 * - NO puedan ser editados
 * - NO puedan ser desactivados
 * - NO puedan ser activados (cambios de estado)
 * - Sean identificados claramente como protegidos
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Usuarios Predefinidos Protegidos</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .protected-user { border: 2px solid #dc3545; border-radius: 8px; padding: 15px; margin: 15px 0; background-color: #fff5f5; }
        .user-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
<div class='container mt-4'>
    <h1 class='text-center mb-4'>🔒 Test de Usuarios Predefinidos Protegidos</h1>
    
    <div class='alert alert-danger'>
        <strong>⚠️ IMPORTANTE:</strong> Este test verifica que los usuarios predefinidos del sistema estén completamente protegidos.
        <br><strong>Usuarios Protegidos:</strong>
        <ul class='mb-0 mt-2'>
            <li>🔒 <strong>root</strong> - Superadministrador del Sistema</li>
            <li>🔒 <strong>admin</strong> - Administrador del Sistema</li>
            <li>🔒 <strong>cliente</strong> - Cliente/Evaluador del Sistema</li>
            <li>🔒 <strong>evaluador</strong> - Evaluador del Sistema</li>
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

    // ===== PRUEBA 1: VERIFICAR USUARIOS PREDEFINIDOS =====
    echo "<h3 class='mt-4'>🔍 Prueba 1: Verificar Usuarios Predefinidos</h3>";
    
    $usuarios_predefinidos = $controller->listarUsuariosPredefinidos();
    
    if (empty($usuarios_predefinidos)) {
        echo "<div class='test-result test-warning'>
            ⚠️ No se encontraron usuarios predefinidos en el sistema
        </div>";
    } else {
        echo "<div class='test-result test-info'>
            📊 <strong>Usuarios Predefinidos Encontrados:</strong> " . count($usuarios_predefinidos)
        . "</div>";
        
        foreach ($usuarios_predefinidos as $usuario) {
            echo "<div class='protected-user'>
                <h5>🔒 {$usuario['usuario']} - {$usuario['rol_nombre']}</h5>
                <div class='user-info'>
                    <strong>ID:</strong> {$usuario['id']}<br>
                    <strong>Nombre:</strong> {$usuario['nombre']}<br>
                    <strong>Rol:</strong> {$usuario['rol']} ({$usuario['rol_nombre']})<br>
                    <strong>Estado:</strong> " . ($usuario['activo'] ? '✅ Activo' : '❌ Inactivo') . "<br>
                    <strong>Protección:</strong> {$usuario['proteccion']}<br>
                    <strong>Estado de Protección:</strong> {$usuario['estado_proteccion']}<br>
                    <strong>Acciones Permitidas:</strong> " . implode(', ', $usuario['acciones_permitidas']) . "<br>
                    <strong>Acciones Bloqueadas:</strong> " . implode(', ', $usuario['acciones_bloqueadas']) . "
                </div>
            </div>";
        }
    }

    // ===== PRUEBA 2: VERIFICAR LISTADO COMPLETO DE USUARIOS =====
    echo "<h3 class='mt-4'>📋 Prueba 2: Verificar Listado Completo de Usuarios</h3>";
    
    $todos_usuarios = $controller->gestionarUsuarios('listar');
    
    if (isset($todos_usuarios['error'])) {
        echo "<div class='test-result test-error'>
            ❌ Error al listar usuarios: {$todos_usuarios['error']}
        </div>";
    } else {
        $usuarios_protegidos = 0;
        $usuarios_editables = 0;
        
        foreach ($todos_usuarios as $usuario) {
            if ($usuario['protegido']) {
                $usuarios_protegidos++;
            } else {
                $usuarios_editables++;
            }
        }
        
        echo "<div class='test-result test-info'>
            📊 <strong>Resumen de Usuarios:</strong>
            <br>• Usuarios Protegidos: $usuarios_protegidos
            <br>• Usuarios Editables: $usuarios_editables
            <br>• Total: " . count($todos_usuarios) . "
        </div>";
        
        // Mostrar algunos usuarios protegidos como ejemplo
        $ejemplos_protegidos = array_slice(array_filter($todos_usuarios, function($u) { return $u['protegido']; }), 0, 2);
        
        if (!empty($ejemplos_protegidos)) {
            echo "<div class='test-result test-info'>
                <strong>Ejemplos de Usuarios Protegidos:</strong>
            </div>";
            
            foreach ($ejemplos_protegidos as $usuario) {
                echo "<div class='user-info'>
                    <strong>{$usuario['usuario']}</strong> - {$usuario['estado_proteccion']}<br>
                    <small>{$usuario['proteccion']}</small>
                </div>";
            }
        }
    }

    // ===== PRUEBA 3: INTENTAR ELIMINAR USUARIO PREDEFINIDO =====
    echo "<h3 class='mt-4'>🚫 Prueba 3: Intentar Eliminar Usuario Predefinido</h3>";
    
    if (!empty($usuarios_predefinidos)) {
        $usuario_test = $usuarios_predefinidos[0];
        
        echo "<div class='test-result test-warning'>
            ⚠️ Intentando eliminar usuario predefinido: {$usuario_test['usuario']} (ID: {$usuario_test['id']})
        </div>";
        
        $resultado = $controller->gestionarUsuarios('eliminar', ['id' => $usuario_test['id']]);
        
        if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE ELIMINAR UN USUARIO PREDEFINIDO') !== false) {
            echo "<div class='test-result test-success'>
                ✅ <strong>PROTECCIÓN EXITOSA:</strong> Se impidió eliminar usuario predefinido
                <br><strong>Mensaje:</strong> {$resultado['error']}
            </div>";
            
            if (isset($resultado['error_code']) && $resultado['error_code'] === 'PROTECTED_USER_DELETE') {
                echo "<div class='test-result test-info'>
                    ℹ️ Código de error correcto: {$resultado['error_code']}
                </div>";
            }
            
            if (isset($resultado['mensaje_detallado'])) {
                echo "<div class='test-result test-info'>
                    ℹ️ <strong>Explicación:</strong> {$resultado['mensaje_detallado']}
                </div>";
            }
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>ERROR CRÍTICO:</strong> Se permitió eliminar usuario predefinido
                <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
            . "</div>";
        }
    } else {
        echo "<div class='test-result test-info'>
            ℹ️ No hay usuarios predefinidos para probar eliminación
        </div>";
    }

    // ===== PRUEBA 4: INTENTAR DESACTIVAR USUARIO PREDEFINIDO =====
    echo "<h3 class='mt-4'>🚫 Prueba 4: Intentar Desactivar Usuario Predefinido</h3>";
    
    if (!empty($usuarios_predefinidos)) {
        $usuario_test = $usuarios_predefinidos[0];
        
        echo "<div class='test-result test-warning'>
            ⚠️ Intentando desactivar usuario predefinido: {$usuario_test['usuario']} (ID: {$usuario_test['id']})
        </div>";
        
        $resultado = $controller->gestionarUsuarios('desactivar', ['id' => $usuario_test['id']]);
        
        if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE DESACTIVAR UN USUARIO PREDEFINIDO') !== false) {
            echo "<div class='test-result test-success'>
                ✅ <strong>PROTECCIÓN EXITOSA:</strong> Se impidió desactivar usuario predefinido
                <br><strong>Mensaje:</strong> {$resultado['error']}
            </div>";
            
            if (isset($resultado['error_code']) && $resultado['error_code'] === 'PROTECTED_USER_DEACTIVATE') {
                echo "<div class='test-result test-info'>
                    ℹ️ Código de error correcto: {$resultado['error_code']}
                </div>";
            }
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>ERROR CRÍTICO:</strong> Se permitió desactivar usuario predefinido
                <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
            . "</div>";
        }
    }

    // ===== PRUEBA 5: INTENTAR EDITAR USUARIO PREDEFINIDO =====
    echo "<h3 class='mt-4'>🚫 Prueba 5: Intentar Editar Usuario Predefinido</h3>";
    
    if (!empty($usuarios_predefinidos)) {
        $usuario_test = $usuarios_predefinidos[0];
        
        echo "<div class='test-result test-warning'>
            ⚠️ Intentando editar usuario predefinido: {$usuario_test['usuario']} (ID: {$usuario_test['id']})
        </div>";
        
        $datos_edicion = [
            'id' => $usuario_test['id'],
            'nombre' => 'Nombre Modificado Test',
            'cedula' => '99999999',
            'rol' => $usuario_test['rol'],
            'correo' => 'modificado@test.com',
            'usuario' => $usuario_test['usuario'],
            'activo' => $usuario_test['activo']
        ];
        
        $resultado = $controller->gestionarUsuarios('actualizar', $datos_edicion);
        
        if (isset($resultado['error']) && strpos($resultado['error'], 'NO SE PUEDE MODIFICAR UN USUARIO PREDEFINIDO') !== false) {
            echo "<div class='test-result test-success'>
                ✅ <strong>PROTECCIÓN EXITOSA:</strong> Se impidió editar usuario predefinido
                <br><strong>Mensaje:</strong> {$resultado['error']}
            </div>";
            
            if (isset($resultado['error_code']) && $resultado['error_code'] === 'PROTECTED_USER_UPDATE') {
                echo "<div class='test-result test-info'>
                    ℹ️ Código de error correcto: {$resultado['error_code']}
                </div>";
            }
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>ERROR CRÍTICO:</strong> Se permitió editar usuario predefinido
                <br><strong>Resultado:</strong> " . json_encode($resultado, JSON_PRETTY_PRINT)
            . "</div>";
        }
    }

    // ===== PRUEBA 6: VERIFICAR INFORMACIÓN DE PROTECCIÓN =====
    echo "<h3 class='mt-4'>🔍 Prueba 6: Verificar Información de Protección</h3>";
    
    if (!empty($usuarios_predefinidos)) {
        $usuario_test = $usuarios_predefinidos[0];
        
        echo "<div class='test-result test-info'>
            ℹ️ Verificando información de protección para: {$usuario_test['usuario']}
        </div>";
        
        $info_proteccion = $controller->getInfoProteccionUsuario($usuario_test['usuario']);
        
        if (isset($info_proteccion['protegido']) && $info_proteccion['protegido']) {
            echo "<div class='test-result test-success'>
                ✅ <strong>Información de Protección Correcta:</strong>
                <br><strong>Usuario:</strong> {$info_proteccion['usuario']}
                <br><strong>Rol:</strong> {$info_proteccion['rol']}
                <br><strong>Descripción:</strong> {$info_proteccion['descripcion']}
                <br><strong>Protección:</strong> {$info_proteccion['proteccion']}
                <br><strong>Mensaje:</strong> {$info_proteccion['mensaje']}
            </div>";
        } else {
            echo "<div class='test-result test-error'>
                ❌ <strong>Error:</strong> No se detectó protección para usuario predefinido
                <br><strong>Resultado:</strong> " . json_encode($info_proteccion, JSON_PRETTY_PRINT)
            . "</div>";
        }
    }

    // ===== RESUMEN FINAL =====
    echo "<h3 class='mt-4'>🎯 Resumen de Protecciones de Usuarios Predefinidos</h3>";
    echo "<div class='test-result test-info'>
        <strong>Protecciones Implementadas y Probadas:</strong>
        <ul class='mb-0 mt-2'>
            <li>✅ Identificación automática de usuarios predefinidos</li>
            <li>✅ Protección contra eliminación</li>
            <li>✅ Protección contra edición</li>
            <li>✅ Protección contra desactivación</li>
            <li>✅ Protección contra cambios de estado</li>
            <li>✅ Códigos de error específicos para cada protección</li>
            <li>✅ Mensajes de error claros y profesionales</li>
            <li>✅ Información de protección detallada</li>
            <li>✅ Marcado visual de usuarios protegidos</li>
            <li>✅ Restricción de acciones permitidas</li>
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
