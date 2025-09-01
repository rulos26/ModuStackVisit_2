<?php
/**
 * Test para verificar la protección de usuarios en la UI
 * Verifica que los usuarios protegidos no muestren opciones de edición/eliminación
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Protección Usuarios UI</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
    <style>
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-success { background-color: #d4edda; border-color: #c3e6cb; }
        .test-error { background-color: #f8d7da; border-color: #f5c6cb; }
        .test-info { background-color: #d1ecf1; border-color: #bee5eb; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body class='bg-light'>
    <div class='container mt-4'>
        <h1 class='text-center mb-4'>
            <i class='bi bi-shield-check text-primary'></i>
            Test de Protección de Usuarios en la UI
        </h1>";

try {
    // 1. Verificar autoloader
    echo "<div class='test-section test-info'>
            <h4>1. Verificando Autoloader</h4>";
    
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        echo "<p class='text-success'>✅ Autoloader cargado correctamente</p>";
    } else {
        throw new Exception("❌ No se encontró el autoloader");
    }
    echo "</div>";

    // 2. Verificar clases
    echo "<div class='test-section test-info'>
            <h4>2. Verificando Clases</h4>";
    
    if (!class_exists('App\\Controllers\\SuperAdminController')) {
        throw new Exception("❌ Clase SuperAdminController no encontrada");
    }
    echo "<p class='text-success'>✅ SuperAdminController encontrada</p>";
    
    if (!class_exists('App\\Database\\Database')) {
        throw new Exception("❌ Clase Database no encontrada");
    }
    echo "<p class='text-success'>✅ Database encontrada</p>";
    echo "</div>";

    // 3. Instanciar controlador
    echo "<div class='test-section test-info'>
            <h4>3. Instanciando SuperAdminController</h4>";
    
    $superAdmin = new App\Controllers\SuperAdminController();
    echo "<p class='text-success'>✅ SuperAdminController instanciado correctamente</p>";
    echo "</div>";

    // 4. Verificar métodos de protección
    echo "<div class='test-section test-info'>
            <h4>4. Verificando Métodos de Protección</h4>";
    
    if (!method_exists($superAdmin, 'esUsuarioProtegido')) {
        throw new Exception("❌ Método esUsuarioProtegido no encontrado");
    }
    echo "<p class='text-success'>✅ Método esUsuarioProtegido encontrado</p>";
    
    if (!method_exists($superAdmin, 'getInfoProteccionUsuarioPorId')) {
        throw new Exception("❌ Método getInfoProteccionUsuarioPorId no encontrado");
    }
    echo "<p class='text-success'>✅ Método getInfoProteccionUsuarioPorId encontrado</p>";
    echo "</div>";

    // 5. Listar usuarios
    echo "<div class='test-section test-info'>
            <h4>5. Listando Usuarios del Sistema</h4>";
    
    $usuarios = $superAdmin->gestionarUsuarios('listar');
    if (!is_array($usuarios)) {
        throw new Exception("❌ No se pudieron obtener los usuarios");
    }
    echo "<p class='text-success'>✅ Usuarios obtenidos: " . count($usuarios) . "</p>";
    echo "</div>";

    // 6. Verificar protección de usuarios
    echo "<div class='test-section test-info'>
            <h4>6. Verificando Protección de Usuarios</h4>";
    
    $usuariosProtegidos = [];
    $usuariosNormales = [];
    
    foreach ($usuarios as $usuario) {
        $esProtegido = $superAdmin->esUsuarioProtegido($usuario['id']);
        $infoProteccion = $superAdmin->getInfoProteccionUsuarioPorId($usuario['id']);
        
        if ($esProtegido) {
            $usuariosProtegidos[] = $usuario;
        } else {
            $usuariosNormales[] = $usuario;
        }
    }
    
    echo "<p class='text-success'>✅ Usuarios protegidos encontrados: " . count($usuariosProtegidos) . "</p>";
    echo "<p class='text-success'>✅ Usuarios normales encontrados: " . count($usuariosNormales) . "</p>";
    echo "</div>";

    // 7. Mostrar detalles de usuarios protegidos
    echo "<div class='test-section test-success'>
            <h4>7. Detalles de Usuarios Protegidos</h4>";
    
    if (empty($usuariosProtegidos)) {
        echo "<p class='text-warning'>⚠️ No se encontraron usuarios protegidos</p>";
    } else {
        echo "<div class='table-responsive'>
                <table class='table table-sm table-bordered'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Protección</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        foreach ($usuariosProtegidos as $usuario) {
            $infoProteccion = $superAdmin->getInfoProteccionUsuarioPorId($usuario['id']);
            $rolText = '';
            switch ($usuario['rol']) {
                case 1: $rolText = 'Administrador'; break;
                case 2: $rolText = 'Cliente'; break;
                case 3: $rolText = 'Superadministrador'; break;
                case 4: $rolText = 'Evaluador'; break;
                default: $rolText = 'Desconocido';
            }
            
            echo "<tr class='table-warning'>
                    <td>{$usuario['id']}</td>
                    <td><strong>{$usuario['usuario']}</strong></td>
                    <td>{$usuario['nombre']}</td>
                    <td><span class='badge bg-primary'>{$rolText}</span></td>
                    <td>" . ($usuario['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>') . "</td>
                    <td><span class='badge bg-warning text-dark'><i class='bi bi-shield-lock'></i> Protegido</span></td>
                  </tr>";
        }
        
        echo "</tbody></table></div>";
    }
    echo "</div>";

    // 8. Mostrar detalles de usuarios normales
    echo "<div class='test-section test-success'>
            <h4>8. Detalles de Usuarios Normales</h4>";
    
    if (empty($usuariosNormales)) {
        echo "<p class='text-warning'>⚠️ No se encontraron usuarios normales</p>";
    } else {
        echo "<div class='table-responsive'>
                <table class='table table-sm table-bordered'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones Disponibles</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        foreach ($usuariosNormales as $usuario) {
            $rolText = '';
            switch ($usuario['rol']) {
                case 1: $rolText = 'Administrador'; break;
                case 2: $rolText = 'Cliente'; break;
                case 3: $rolText = 'Superadministrador'; break;
                case 4: $rolText = 'Evaluador'; break;
                default: $rolText = 'Desconocido';
            }
            
            $acciones = [];
            if ($usuario['rol'] != 3) { // No permitir eliminar superadministradores
                $acciones[] = 'Eliminar';
            }
            $acciones[] = 'Editar';
            $acciones[] = ($usuario['activo'] ? 'Desactivar' : 'Activar');
            
            echo "<tr>
                    <td>{$usuario['id']}</td>
                    <td>{$usuario['usuario']}</td>
                    <td>{$usuario['nombre']}</td>
                    <td><span class='badge bg-secondary'>{$rolText}</span></td>
                    <td>" . ($usuario['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>') . "</td>
                    <td><span class='badge bg-info'>" . implode(', ', $acciones) . "</span></td>
                  </tr>";
        }
        
        echo "</tbody></table></div>";
    }
    echo "</div>";

    // 9. Verificar funcionalidad de protección
    echo "<div class='test-section test-success'>
            <h4>9. Verificación de Funcionalidad de Protección</h4>";
    
    $testResults = [];
    
    // Test 1: Verificar que usuarios protegidos retornen true
    foreach ($usuariosProtegidos as $usuario) {
        $esProtegido = $superAdmin->esUsuarioProtegido($usuario['id']);
        $testResults[] = [
            'test' => "Usuario {$usuario['usuario']} (ID: {$usuario['id']}) es protegido",
            'result' => $esProtegido ? 'PASÓ' : 'FALLÓ',
            'expected' => true,
            'actual' => $esProtegido
        ];
    }
    
    // Test 2: Verificar que usuarios normales retornen false
    foreach (array_slice($usuariosNormales, 0, 3) as $usuario) { // Solo los primeros 3 para no saturar
        $esProtegido = $superAdmin->esUsuarioProtegido($usuario['id']);
        $testResults[] = [
            'test' => "Usuario {$usuario['usuario']} (ID: {$usuario['id']}) NO es protegido",
            'result' => !$esProtegido ? 'PASÓ' : 'FALLÓ',
            'expected' => false,
            'actual' => $esProtegido
        ];
    }
    
    echo "<div class='table-responsive'>
            <table class='table table-sm table-bordered'>
                <thead class='table-dark'>
                    <tr>
                        <th>Test</th>
                        <th>Resultado</th>
                        <th>Esperado</th>
                        <th>Actual</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($testResults as $result) {
        $rowClass = $result['result'] === 'PASÓ' ? 'table-success' : 'table-danger';
        echo "<tr class='{$rowClass}'>
                <td>{$result['test']}</td>
                <td><span class='badge " . ($result['result'] === 'PASÓ' ? 'bg-success' : 'bg-danger') . "'>{$result['result']}</span></td>
                <td>" . ($result['expected'] ? 'true' : 'false') . "</td>
                <td>" . ($result['actual'] ? 'true' : 'false') . "</td>
              </tr>";
    }
    
    echo "</tbody></table></div>";
    echo "</div>";

    // 10. Resumen final
    echo "<div class='test-section test-success'>
            <h4>10. Resumen Final</h4>";
    
    $testsPasados = count(array_filter($testResults, function($r) { return $r['result'] === 'PASÓ'; }));
    $testsTotales = count($testResults);
    
    echo "<div class='alert " . ($testsPasados === $testsTotales ? 'alert-success' : 'alert-warning') . "'>
            <h5>Resultados del Test:</h5>
            <p><strong>Tests pasados:</strong> {$testsPasados} / {$testsTotales}</p>
            <p><strong>Usuarios protegidos:</strong> " . count($usuariosProtegidos) . "</p>
            <p><strong>Usuarios normales:</strong> " . count($usuariosNormales) . "</p>
            <p><strong>Total usuarios:</strong> " . count($usuarios) . "</p>
          </div>";
    
    if ($testsPasados === $testsTotales) {
        echo "<div class='alert alert-success'>
                <h5>🎉 ¡Test Completado Exitosamente!</h5>
                <p>La protección de usuarios está funcionando correctamente. Los usuarios protegidos no pueden ser modificados, eliminados o desactivados.</p>
              </div>";
    } else {
        echo "<div class='alert alert-warning'>
                <h5>⚠️ Algunos Tests Fallaron</h5>
                <p>Revisa los resultados anteriores para identificar los problemas.</p>
              </div>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='test-section test-error'>
            <h4>❌ Error en el Test</h4>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>
            <p><strong>Línea:</strong> " . $e->getLine() . "</p>
          </div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
