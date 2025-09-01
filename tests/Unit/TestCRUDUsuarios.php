<?php
// Script para probar el CRUD completo de usuarios
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test CRUD Usuarios</title>";
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
echo ".btn-danger { background: #dc3545; }";
echo ".btn-danger:hover { background: #c82333; }";
echo ".table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
echo ".table th, .table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }";
echo ".table th { background-color: #f8f9fa; font-weight: bold; }";
echo ".table tr:nth-child(even) { background-color: #f8f9fa; }";
echo ".table tr:hover { background-color: #e9ecef; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🧪 Test CRUD Usuarios - Superadministrador</h1>";
echo "<p>Este script prueba todas las funcionalidades del CRUD de usuarios.</p>";

// 1. Verificar autoloader
echo "<div class='info'>";
echo "<h3>1. Verificando Autoloader</h3>";

$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<p>✅ Autoloader encontrado</p>";
    
    try {
        require_once $autoloadPath;
        echo "<p>✅ Autoloader cargado correctamente</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error al cargar autoloader: " . $e->getMessage() . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
    }
} else {
    echo "<p>❌ Autoloader NO encontrado</p>";
    echo "</div>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
    exit;
}
echo "</div>";

// 2. Verificar SuperAdminController
echo "<div class='info'>";
echo "<h3>2. Verificando SuperAdminController</h3>";

try {
    if (class_exists('\App\Controllers\SuperAdminController')) {
        echo "<p>✅ SuperAdminController disponible</p>";
        
        // Instanciar el controlador
        $superAdmin = new \App\Controllers\SuperAdminController();
        echo "<p>✅ SuperAdminController instanciado correctamente</p>";
        
    } else {
        echo "<p>❌ SuperAdminController no está disponible</p>";
        echo "</div>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar SuperAdminController: " . $e->getMessage() . "</p>";
    echo "</div>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
    exit;
}
echo "</div>";

// 3. Probar método listar usuarios
echo "<div class='info'>";
echo "<h3>3. Probando Método Listar Usuarios</h3>";

try {
    echo "<p>📊 Obteniendo lista de usuarios...</p>";
    
    $usuarios = $superAdmin->gestionarUsuarios('listar');
    
    if (is_array($usuarios)) {
        echo "<p>✅ Lista de usuarios obtenida correctamente</p>";
        echo "<p><strong>Total de usuarios:</strong> " . count($usuarios) . "</p>";
        
        // Mostrar tabla de usuarios
        if (!empty($usuarios)) {
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Usuario</th>";
            echo "<th>Nombre</th>";
            echo "<th>Cédula</th>";
            echo "<th>Correo</th>";
            echo "<th>Rol</th>";
            echo "<th>Estado</th>";
            echo "<th>Último Acceso</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            foreach ($usuarios as $user) {
                $rolText = '';
                switch ($user['rol']) {
                    case 1: $rolText = 'Administrador'; break;
                    case 2: $rolText = 'Evaluador'; break;
                    case 3: $rolText = 'Superadministrador'; break;
                    default: $rolText = 'Desconocido';
                }
                
                $estadoText = $user['activo'] ? 'Activo' : 'Inactivo';
                $estadoClass = $user['activo'] ? 'success' : 'danger';
                
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td><strong>" . htmlspecialchars($user['usuario']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($user['cedula']) . "</td>";
                echo "<td>" . htmlspecialchars($user['correo']) . "</td>";
                echo "<td>" . $rolText . "</td>";
                echo "<td><span class='badge bg-" . $estadoClass . "'>" . $estadoText . "</span></td>";
                echo "<td>" . ($user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca') . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ No hay usuarios registrados en el sistema</p>";
        }
        
    } else {
        echo "<p>❌ Error al obtener lista de usuarios</p>";
        if (is_array($usuarios) && isset($usuarios['error'])) {
            echo "<p><strong>Error:</strong> " . $usuarios['error'] . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en método listar usuarios: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// 4. Probar creación de usuario de prueba
echo "<div class='info'>";
echo "<h3>4. Probando Creación de Usuario</h3>";

try {
    echo "<p>👤 Creando usuario de prueba...</p>";
    
    $datosUsuario = [
        'usuario' => 'test_user_' . time(),
        'nombre' => 'Usuario de Prueba',
        'cedula' => '12345678',
        'correo' => 'test' . time() . '@test.com',
        'rol' => 2, // Evaluador
        'activo' => 1,
        'password' => 'test123'
    ];
    
    $resultado = $superAdmin->gestionarUsuarios('crear', $datosUsuario);
    
    if (isset($resultado['success'])) {
        echo "<p class='success'>✅ Usuario creado exitosamente</p>";
        echo "<p><strong>Mensaje:</strong> " . $resultado['success'] . "</p>";
        
        // Guardar ID del usuario creado para pruebas posteriores
        $usuarioTestId = null;
        
        // Obtener la lista actualizada para encontrar el ID
        $usuariosActualizados = $superAdmin->gestionarUsuarios('listar');
        foreach ($usuariosActualizados as $user) {
            if ($user['usuario'] === $datosUsuario['usuario']) {
                $usuarioTestId = $user['id'];
                break;
            }
        }
        
        if ($usuarioTestId) {
            echo "<p><strong>ID del usuario creado:</strong> $usuarioTestId</p>";
            
            // 5. Probar actualización del usuario
            echo "<div class='info'>";
            echo "<h3>5. Probando Actualización de Usuario</h3>";
            
            $datosActualizacion = [
                'id' => $usuarioTestId,
                'nombre' => 'Usuario de Prueba Actualizado',
                'cedula' => '87654321',
                'correo' => 'updated' . time() . '@test.com',
                'rol' => 1, // Cambiar a Administrador
                'activo' => 1
            ];
            
            $resultadoActualizacion = $superAdmin->gestionarUsuarios('actualizar', $datosActualizacion);
            
            if (isset($resultadoActualizacion['success'])) {
                echo "<p class='success'>✅ Usuario actualizado exitosamente</p>";
                echo "<p><strong>Mensaje:</strong> " . $resultadoActualizacion['success'] . "</p>";
            } else {
                echo "<p class='error'>❌ Error al actualizar usuario</p>";
                if (isset($resultadoActualizacion['error'])) {
                    echo "<p><strong>Error:</strong> " . $resultadoActualizacion['error'] . "</p>";
                }
            }
            echo "</div>";
            
            // 6. Probar desactivación del usuario
            echo "<div class='info'>";
            echo "<h3>6. Probando Desactivación de Usuario</h3>";
            
            $resultadoDesactivacion = $superAdmin->gestionarUsuarios('desactivar', ['id' => $usuarioTestId]);
            
            if (isset($resultadoDesactivacion['success'])) {
                echo "<p class='success'>✅ Usuario desactivado exitosamente</p>";
                echo "<p><strong>Mensaje:</strong> " . $resultadoDesactivacion['success'] . "</p>";
            } else {
                echo "<p class='error'>❌ Error al desactivar usuario</p>";
                if (isset($resultadoDesactivacion['error'])) {
                    echo "<p><strong>Error:</strong> " . $resultadoDesactivacion['error'] . "</p>";
                }
            }
            echo "</div>";
            
            // 7. Probar activación del usuario
            echo "<div class='info'>";
            echo "<h3>7. Probando Activación de Usuario</h3>";
            
            $resultadoActivacion = $superAdmin->gestionarUsuarios('activar', ['id' => $usuarioTestId]);
            
            if (isset($resultadoActivacion['success'])) {
                echo "<p class='success'>✅ Usuario activado exitosamente</p>";
                echo "<p><strong>Mensaje:</strong> " . $resultadoActivacion['success'] . "</p>";
            } else {
                echo "<p class='error'>❌ Error al activar usuario</p>";
                if (isset($resultadoActivacion['error'])) {
                    echo "<p><strong>Error:</strong> " . $resultadoActivacion['error'] . "</p>";
                }
            }
            echo "</div>";
            
            // 8. Probar eliminación del usuario
            echo "<div class='info'>";
            echo "<h3>8. Probando Eliminación de Usuario</h3>";
            
            $resultadoEliminacion = $superAdmin->gestionarUsuarios('eliminar', ['id' => $usuarioTestId]);
            
            if (isset($resultadoEliminacion['success'])) {
                echo "<p class='success'>✅ Usuario eliminado exitosamente</p>";
                echo "<p><strong>Mensaje:</strong> " . $resultadoEliminacion['success'] . "</p>";
            } else {
                echo "<p class='error'>❌ Error al eliminar usuario</p>";
                if (isset($resultadoEliminacion['error'])) {
                    echo "<p><strong>Error:</strong> " . $resultadoEliminacion['error'] . "</p>";
                }
            }
            echo "</div>";
            
        } else {
            echo "<p class='warning'>⚠️ No se pudo obtener el ID del usuario creado</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Error al crear usuario</p>";
        if (isset($resultado['error'])) {
            echo "<p><strong>Error:</strong> " . $resultado['error'] . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en creación de usuario: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// 9. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen del Test CRUD Usuarios</h3>";

$tests = [
    'Autoloader funcional' => file_exists($autoloadPath),
    'SuperAdminController disponible' => class_exists('\App\Controllers\SuperAdminController'),
    'Método listar usuarios funciona' => isset($usuarios) && is_array($usuarios),
    'Método crear usuario funciona' => isset($resultado) && isset($resultado['success']),
    'Método actualizar usuario funciona' => isset($resultadoActualizacion) && isset($resultadoActualizacion['success']),
    'Método desactivar usuario funciona' => isset($resultadoDesactivacion) && isset($resultadoDesactivacion['success']),
    'Método activar usuario funciona' => isset($resultadoActivacion) && isset($resultadoActivacion['success']),
    'Método eliminar usuario funciona' => isset($resultadoEliminacion) && isset($resultadoEliminacion['success'])
];

$testsExitosos = 0;
$testsTotales = count($tests);

foreach ($tests as $test => $resultado) {
    $status = $resultado ? '✅' : '❌';
    echo "<p>$status <strong>$test:</strong> " . ($resultado ? 'SÍ' : 'NO') . "</p>";
    if ($resultado) $testsExitosos++;
}

echo "<p><strong>Resultado:</strong> $testsExitosos de $testsTotales verificaciones exitosas</p>";

if ($testsExitosos === $testsTotales) {
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡CRUD DE USUARIOS FUNCIONA PERFECTAMENTE!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>⚠️ Algunas verificaciones fallaron. Revisa los errores arriba.</p>";
}
echo "</div>";

// 10. Enlaces útiles
echo "<div class='info'>";
echo "<h3>9. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='../index.php' class='btn btn-success'>🏠 Ir al Login</a>";
echo "<a href='../dashboard.php' class='btn'>🎯 Test Dashboard</a>";
echo "<a href='../resources/views/superadmin/gestion_usuarios.php' class='btn'>👥 Gestión de Usuarios</a>";
echo "<a href='TestHeadersCompletamenteCorregidos.php' class='btn'>🔧 Test Headers</a>";
echo "</div>";
echo "</div>";

// 11. Próximos pasos
echo "<div class='warning'>";
echo "<h3>10. Próximos Pasos</h3>";
echo "<ol>";
echo "<li><strong>Probar interfaz web:</strong> Ir a <code>../resources/views/superadmin/gestion_usuarios.php</code></li>";
echo "<li><strong>Verificar funcionalidades:</strong> Crear, editar, activar/desactivar y eliminar usuarios</li>";
echo "<li><strong>Monitorear logs:</strong> Revisar archivo <code>logs/debug.log</code> para debugging</li>";
echo "<li><strong>Probar envío de correos:</strong> Verificar que se envíen credenciales por correo</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🧪 Test CRUD Usuarios finalizado');";
echo "console.log('✅ Verificaciones exitosas: $testsExitosos');";
echo "console.log('❌ Verificaciones fallidas: " . ($testsTotales - $testsExitosos) . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
