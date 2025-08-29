<?php
// Script para verificar redirecciones según rol
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔍 Verificar Redirecciones por Rol</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }";
echo ".warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo ".btn-warning { background: #ffc107; color: #212529; }";
echo ".btn-warning:hover { background: #e0a800; }";
echo ".test-result { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }";
echo ".redirect-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #2196f3; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Verificar Redirecciones por Rol</h1>";
echo "<p>Este script verifica las redirecciones según el rol en el LoginController.</p>";

// 1. Cargar autoloader
echo "<div class='info'>";
echo "<h3>1. Cargando Autoloader</h3>";
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    echo "<p>✅ Autoloader cargado correctamente</p>";
} else {
    echo "<p>❌ Autoloader no encontrado</p>";
    exit;
}
echo "</div>";

// 2. Verificar configuración de redirecciones en LoginController
echo "<div class='info'>";
echo "<h3>2. Configuración de Redirecciones en LoginController</h3>";

$redireccionesConfiguradas = [
    [
        'rol' => 1,
        'descripcion' => 'Administrador',
        'url_esperada' => 'resources/views/admin/dashboardAdmin.php'
    ],
    [
        'rol' => 2,
        'descripcion' => 'Evaluador/Cliente',
        'url_esperada' => 'resources/views/evaluador/dashboardEavaluador.php'
    ],
    [
        'rol' => 3,
        'descripcion' => 'Superadministrador',
        'url_esperada' => 'resources/views/superadmin/dashboardSuperAdmin.php'
    ]
];

echo "<div class='redirect-info'>";
echo "<h4>📋 Redirecciones Configuradas:</h4>";
foreach ($redireccionesConfiguradas as $redireccion) {
    echo "<p><strong>Rol {$redireccion['rol']} ({$redireccion['descripcion']}):</strong> {$redireccion['url_esperada']}</p>";
}
echo "</div>";

echo "</div>";

// 3. Verificar existencia de archivos de destino
echo "<div class='info'>";
echo "<h3>3. Verificando Existencia de Archivos de Destino</h3>";

$archivosExistentes = 0;
$archivosFaltantes = 0;

foreach ($redireccionesConfiguradas as $redireccion) {
    $rutaCompleta = __DIR__ . '/../../' . $redireccion['url_esperada'];
    
    echo "<div class='test-result'>";
    echo "<h4>🔍 Verificando: {$redireccion['descripcion']} (Rol {$redireccion['rol']})</h4>";
    echo "<p><strong>URL configurada:</strong> {$redireccion['url_esperada']}</p>";
    echo "<p><strong>Ruta completa:</strong> {$rutaCompleta}</p>";
    
    if (file_exists($rutaCompleta)) {
        echo "<p style='color: #28a745; font-weight: bold;'>✅ Archivo existe</p>";
        $archivosExistentes++;
        
        // Verificar contenido básico
        $contenido = file_get_contents($rutaCompleta);
        $tamaño = strlen($contenido);
        echo "<p><strong>Tamaño del archivo:</strong> {$tamaño} bytes</p>";
        
        if ($tamaño > 100) {
            echo "<p style='color: #28a745;'>✅ Archivo tiene contenido válido</p>";
        } else {
            echo "<p style='color: #ffc107;'>⚠️ Archivo muy pequeño - posiblemente vacío</p>";
        }
    } else {
        echo "<p style='color: #dc3545; font-weight: bold;'>❌ Archivo NO existe</p>";
        $archivosFaltantes++;
    }
    echo "</div>";
}

echo "</div>";

// 4. Probar redirecciones con usuarios reales
echo "<div class='info'>";
echo "<h3>4. Probando Redirecciones con Usuarios Reales</h3>";

try {
    $loginController = new App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
    
    $usuariosParaProbar = [
        [
            'usuario' => 'root',
            'password' => 'root',
            'rol' => 3,
            'descripcion' => 'Superadministrador'
        ],
        [
            'usuario' => 'admin',
            'password' => 'admin',
            'rol' => 1,
            'descripcion' => 'Administrador'
        ],
        [
            'usuario' => 'cliente',
            'password' => 'cliente',
            'rol' => 2,
            'descripcion' => 'Cliente/Evaluador'
        ]
    ];
    
    $redireccionesExitosas = 0;
    $redireccionesFallidas = 0;
    
    foreach ($usuariosParaProbar as $userInfo) {
        echo "<div class='test-result'>";
        echo "<h4>🔐 Probando Login y Redirección: {$userInfo['descripcion']}</h4>";
        
        try {
            $result = $loginController->authenticate($userInfo['usuario'], $userInfo['password']);
            
            if ($result['success']) {
                echo "<p style='color: #28a745; font-weight: bold;'>✅ LOGIN EXITOSO</p>";
                echo "<p><strong>Usuario:</strong> {$userInfo['usuario']}</p>";
                echo "<p><strong>Rol:</strong> {$result['data']['rol']} - {$userInfo['descripcion']}</p>";
                echo "<p><strong>Redirect URL:</strong> {$result['data']['redirect_url']}</p>";
                
                // Verificar si la URL de redirección coincide con la esperada
                $urlEsperada = $redireccionesConfiguradas[$userInfo['rol'] - 1]['url_esperada'];
                if ($result['data']['redirect_url'] === $urlEsperada) {
                    echo "<p style='color: #28a745; font-weight: bold;'>✅ REDIRECCIÓN CORRECTA</p>";
                    $redireccionesExitosas++;
                } else {
                    echo "<p style='color: #dc3545; font-weight: bold;'>❌ REDIRECCIÓN INCORRECTA</p>";
                    echo "<p><strong>Esperada:</strong> {$urlEsperada}</p>";
                    echo "<p><strong>Obtenida:</strong> {$result['data']['redirect_url']}</p>";
                    $redireccionesFallidas++;
                }
                
                // Verificar si el archivo de destino existe
                $rutaDestino = __DIR__ . '/../../' . $result['data']['redirect_url'];
                if (file_exists($rutaDestino)) {
                    echo "<p style='color: #28a745;'>✅ Archivo de destino existe</p>";
                } else {
                    echo "<p style='color: #dc3545;'>❌ Archivo de destino NO existe</p>";
                }
                
            } else {
                echo "<p style='color: #dc3545; font-weight: bold;'>❌ LOGIN FALLIDO</p>";
                echo "<p><strong>Error:</strong> {$result['message']}</p>";
                echo "<p><strong>Código:</strong> {$result['error_code']}</p>";
                $redireccionesFallidas++;
            }
            
        } catch (Exception $e) {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR EN LOGIN</p>";
            echo "<p><strong>Error:</strong> {$e->getMessage()}</p>";
            $redireccionesFallidas++;
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: {$e->getMessage()}</p>";
}

echo "</div>";

// 5. Probar método getRedirectUrl directamente (usando reflexión)
echo "<div class='info'>";
echo "<h3>5. Probando Método getRedirectUrl Directamente</h3>";

try {
    $loginController = new App\Controllers\LoginController();
    $reflection = new ReflectionClass($loginController);
    $method = $reflection->getMethod('getRedirectUrl');
    $method->setAccessible(true);
    
    foreach ($redireccionesConfiguradas as $redireccion) {
        echo "<div class='test-result'>";
        echo "<h4>🔍 Probando getRedirectUrl para Rol {$redireccion['rol']}</h4>";
        
        try {
            $url = $method->invoke($loginController, $redireccion['rol']);
            echo "<p><strong>URL obtenida:</strong> {$url}</p>";
            echo "<p><strong>URL esperada:</strong> {$redireccion['url_esperada']}</p>";
            
            if ($url === $redireccion['url_esperada']) {
                echo "<p style='color: #28a745; font-weight: bold;'>✅ REDIRECCIÓN CORRECTA</p>";
            } else {
                echo "<p style='color: #dc3545; font-weight: bold;'>❌ REDIRECCIÓN INCORRECTA</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR: {$e->getMessage()}</p>";
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error al probar getRedirectUrl: {$e->getMessage()}</p>";
}

echo "</div>";

// 6. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen de Verificación de Redirecciones</h3>";
echo "<p><strong>Archivos de destino existentes:</strong> {$archivosExistentes} de " . count($redireccionesConfiguradas) . "</p>";
echo "<p><strong>Archivos de destino faltantes:</strong> {$archivosFaltantes}</p>";
echo "<p><strong>Redirecciones exitosas:</strong> {$redireccionesExitosas}</p>";
echo "<p><strong>Redirecciones fallidas:</strong> {$redireccionesFallidas}</p>";

if ($archivosExistentes === count($redireccionesConfiguradas) && $redireccionesExitosas === count($usuariosParaProbar)) {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ ¡TODAS LAS REDIRECCIONES FUNCIONAN CORRECTAMENTE!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Algunas redirecciones necesitan corrección</p>";
}
echo "</div>";

// 7. Enlaces útiles
echo "<div class='info'>";
echo "<h3>6. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='TestLoginDespuesCorreccion.php' class='btn btn-success'>🧪 Test Login</a>";
echo "<a href='CorregirTodosLosHashes.php' class='btn btn-warning'>🔧 Corregir Hashes</a>";
echo "<a href='TestCorreccionBindParam.php' class='btn'>🧪 Test BindParam</a>";
echo "<a href='TestLoginControllerDebugConsole.php' class='btn'>🔍 Debug Console</a>";
echo "</div>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>📋 Información de Redirecciones</h3>";
echo "<ol>";
echo "<li><strong>Rol 1 (Administrador):</strong> → resources/views/admin/dashboardAdmin.php</li>";
echo "<li><strong>Rol 2 (Evaluador/Cliente):</strong> → resources/views/evaluador/dashboardEavaluador.php</li>";
echo "<li><strong>Rol 3 (Superadministrador):</strong> → resources/views/superadmin/dashboardSuperAdmin.php</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🔍 Verificación de redirecciones completada');";
echo "console.log('✅ Archivos existentes: {$archivosExistentes}');";
echo "console.log('❌ Archivos faltantes: {$archivosFaltantes}');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
