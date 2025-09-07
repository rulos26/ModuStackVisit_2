<?php
session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    header('Location: ../../index.php');
    exit();
}

require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Controllers\TablasPrincipalesController;
use App\Database\Database;

$controller = new TablasPrincipalesController();
$db = Database::getInstance()->getConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Tablas - Superadministrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="bi bi-database"></i> Diagnóstico de Base de Datos</h3>
                    </div>
                    <div class="card-body">
                        
                        <h4>1. Verificación de Conexión</h4>
                        <?php
                        try {
                            $stmt = $db->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
                            $info = $stmt->fetch(\PDO::FETCH_ASSOC);
                            echo "<div class='alert alert-success'>";
                            echo "<strong>✅ Conexión exitosa</strong><br>";
                            echo "Base de datos: " . $info['db_name'] . "<br>";
                            echo "Usuario: " . $info['user_name'] . "<br>";
                            echo "Versión MySQL: " . $info['version'];
                            echo "</div>";
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>";
                            echo "<strong>❌ Error de conexión:</strong> " . $e->getMessage();
                            echo "</div>";
                        }
                        ?>
                        
                        <h4>2. Tablas Existentes en la Base de Datos</h4>
                        <?php
                        try {
                            $stmt = $db->query("SHOW TABLES");
                            $tablas = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                            
                            echo "<div class='alert alert-info'>";
                            echo "<strong>Total de tablas encontradas:</strong> " . count($tablas) . "<br><br>";
                            echo "<strong>Lista de tablas:</strong><br>";
                            foreach ($tablas as $tabla) {
                                echo "• " . $tabla . "<br>";
                            }
                            echo "</div>";
                            
                            // Verificar si existe la tabla evaluados
                            if (in_array('evaluados', $tablas)) {
                                echo "<div class='alert alert-success'>";
                                echo "<strong>✅ La tabla 'evaluados' existe</strong>";
                                echo "</div>";
                            } else {
                                echo "<div class='alert alert-warning'>";
                                echo "<strong>⚠️ La tabla 'evaluados' NO existe</strong><br>";
                                echo "Tablas similares encontradas:<br>";
                                foreach ($tablas as $tabla) {
                                    if (strpos(strtolower($tabla), 'eval') !== false || 
                                        strpos(strtolower($tabla), 'user') !== false ||
                                        strpos(strtolower($tabla), 'usuario') !== false) {
                                        echo "• " . $tabla . "<br>";
                                    }
                                }
                                echo "</div>";
                            }
                            
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>";
                            echo "<strong>❌ Error al obtener tablas:</strong> " . $e->getMessage();
                            echo "</div>";
                        }
                        ?>
                        
                        <h4>3. Estructura de Tablas Relevantes</h4>
                        <?php
                        $tablasRelevantes = ['evaluados', 'usuarios', 'user', 'evaluacion'];
                        
                        foreach ($tablasRelevantes as $tabla) {
                            if (in_array($tabla, $tablas)) {
                                echo "<h5>Tabla: $tabla</h5>";
                                try {
                                    $stmt = $db->prepare("DESCRIBE `$tabla`");
                                    $stmt->execute();
                                    $columnas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                    
                                    echo "<table class='table table-sm table-bordered'>";
                                    echo "<thead><tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr></thead>";
                                    echo "<tbody>";
                                    foreach ($columnas as $columna) {
                                        echo "<tr>";
                                        echo "<td>" . $columna['Field'] . "</td>";
                                        echo "<td>" . $columna['Type'] . "</td>";
                                        echo "<td>" . $columna['Null'] . "</td>";
                                        echo "<td>" . $columna['Key'] . "</td>";
                                        echo "<td>" . $columna['Default'] . "</td>";
                                        echo "<td>" . $columna['Extra'] . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody></table>";
                                    
                                    // Mostrar algunos registros de ejemplo
                                    $stmt = $db->prepare("SELECT * FROM `$tabla` LIMIT 3");
                                    $stmt->execute();
                                    $registros = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                    
                                    if (count($registros) > 0) {
                                        echo "<h6>Registros de ejemplo:</h6>";
                                        echo "<table class='table table-sm table-bordered'>";
                                        echo "<thead><tr>";
                                        foreach (array_keys($registros[0]) as $columna) {
                                            echo "<th>$columna</th>";
                                        }
                                        echo "</tr></thead>";
                                        echo "<tbody>";
                                        foreach ($registros as $registro) {
                                            echo "<tr>";
                                            foreach ($registro as $valor) {
                                                echo "<td>" . htmlspecialchars($valor ?? 'NULL') . "</td>";
                                            }
                                            echo "</tr>";
                                        }
                                        echo "</tbody></table>";
                                    } else {
                                        echo "<p class='text-muted'>No hay registros en esta tabla.</p>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>Error al describir tabla $tabla: " . $e->getMessage() . "</div>";
                                }
                                echo "<hr>";
                            }
                        }
                        ?>
                        
                        <h4>4. Prueba del Controlador</h4>
                        <?php
                        try {
                            $resultado = $controller->obtenerUsuariosEvaluados();
                            
                            if (isset($resultado['error'])) {
                                echo "<div class='alert alert-danger'>";
                                echo "<strong>❌ Error en el controlador:</strong><br>";
                                echo $resultado['error'];
                                echo "</div>";
                            } else {
                                echo "<div class='alert alert-success'>";
                                echo "<strong>✅ Controlador funcionando correctamente</strong><br>";
                                echo "Usuarios encontrados: " . count($resultado);
                                echo "</div>";
                                
                                if (count($resultado) > 0) {
                                    echo "<h6>Usuarios encontrados:</h6>";
                                    echo "<table class='table table-sm table-bordered'>";
                                    echo "<thead><tr><th>ID Cédula</th><th>Nombres</th><th>Apellidos</th></tr></thead>";
                                    echo "<tbody>";
                                    foreach ($resultado as $usuario) {
                                        echo "<tr>";
                                        echo "<td>" . $usuario['id_cedula'] . "</td>";
                                        echo "<td>" . $usuario['nombres'] . "</td>";
                                        echo "<td>" . $usuario['apellidos'] . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody></table>";
                                }
                            }
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>";
                            echo "<strong>❌ Error al probar controlador:</strong> " . $e->getMessage();
                            echo "</div>";
                        }
                        ?>
                        
                        <div class="mt-4">
                            <a href="gestion_tablas_principales.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Volver a Tablas Principales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
