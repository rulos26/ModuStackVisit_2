<?php
// Test básico de conexión
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Básico de Conexión</h2>";

// Test 1: Conexión mysqli básica
echo "<h3>1. Conexión mysqli</h3>";
$mysqli = new mysqli("localhost", "u130454517_root", "0382646740Ju*", "u130454517_modulo_vista");

if ($mysqli->connect_error) {
    echo "❌ Error de conexión: " . $mysqli->connect_error . "<br>";
} else {
    echo "✅ Conexión exitosa<br>";
    
    // Mostrar información de la base de datos
    $result = $mysqli->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Base de datos: " . $row['db_name'] . "<br>";
        echo "Usuario: " . $row['user_name'] . "<br>";
        echo "Versión MySQL: " . $row['version'] . "<br>";
    }
    
    // Mostrar todas las tablas
    echo "<br><h4>Tablas en la base de datos:</h4>";
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        $tablas = [];
        while ($row = $result->fetch_array()) {
            $tablas[] = $row[0];
        }
        
        echo "Total de tablas: " . count($tablas) . "<br><br>";
        
        foreach ($tablas as $tabla) {
            echo "• " . $tabla . "<br>";
        }
        
        // Buscar tablas que puedan contener usuarios
        echo "<br><h4>Tablas que podrían contener usuarios:</h4>";
        $tablasUsuarios = [];
        foreach ($tablas as $tabla) {
            if (strpos(strtolower($tabla), 'eval') !== false || 
                strpos(strtolower($tabla), 'user') !== false ||
                strpos(strtolower($tabla), 'usuario') !== false ||
                strpos(strtolower($tabla), 'persona') !== false ||
                strpos(strtolower($tabla), 'cliente') !== false) {
                $tablasUsuarios[] = $tabla;
            }
        }
        
        if (count($tablasUsuarios) > 0) {
            foreach ($tablasUsuarios as $tabla) {
                echo "• " . $tabla . "<br>";
            }
        } else {
            echo "No se encontraron tablas que parezcan contener usuarios.<br>";
        }
    }
}

// Test 2: Conexión PDO
echo "<br><h3>2. Conexión PDO</h3>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=u130454517_modulo_vista;charset=utf8mb4",
        'u130454517_root',
        '0382646740Ju*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión PDO exitosa<br>";
    
    // Probar consulta
    $stmt = $pdo->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
    $info = $stmt->fetch();
    echo "Base de datos: " . $info['db_name'] . "<br>";
    echo "Usuario: " . $info['user_name'] . "<br>";
    echo "Versión MySQL: " . $info['version'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error PDO: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
