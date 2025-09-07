<?php
// Test simple de conexión
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Simple de Conexión</h2>";

// Test 1: Conexión directa usando el archivo de conexión existente
echo "<h3>1. Conexión Directa</h3>";
try {
    require_once __DIR__ . '/../../conn/conexion.php';
    
    if ($mysqli->connect_error) {
        echo "❌ Error de conexión: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ Conexión exitosa con mysqli<br>";
        
        // Probar consulta simple
        $result = $mysqli->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Base de datos: " . $row['db_name'] . "<br>";
            echo "Usuario: " . $row['user_name'] . "<br>";
            echo "Versión MySQL: " . $row['version'] . "<br>";
        }
        
        // Mostrar tablas
        $result = $mysqli->query("SHOW TABLES");
        if ($result) {
            echo "<br>Tablas encontradas:<br>";
            while ($row = $result->fetch_array()) {
                echo "• " . $row[0] . "<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Probar PDO
echo "<h3>2. Conexión PDO</h3>";
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

// Test 3: Verificar si existe la tabla evaluados
echo "<h3>3. Verificar Tabla Evaluados</h3>";
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SHOW TABLES LIKE 'evaluados'");
        if ($stmt->rowCount() > 0) {
            echo "✅ La tabla 'evaluados' existe<br>";
            
            // Describir la tabla
            $stmt = $pdo->query("DESCRIBE evaluados");
            $columnas = $stmt->fetchAll();
            
            echo "<br>Estructura de la tabla evaluados:<br>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
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
            echo "</table>";
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluados");
            $count = $stmt->fetch();
            echo "<br>Total de registros: " . $count['total'] . "<br>";
            
        } else {
            echo "❌ La tabla 'evaluados' NO existe<br>";
            
            // Buscar tablas similares
            $stmt = $pdo->query("SHOW TABLES");
            $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<br>Tablas similares encontradas:<br>";
            foreach ($tablas as $tabla) {
                if (strpos(strtolower($tabla), 'eval') !== false || 
                    strpos(strtolower($tabla), 'user') !== false ||
                    strpos(strtolower($tabla), 'usuario') !== false) {
                    echo "• " . $tabla . "<br>";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error verificando tabla: " . $e->getMessage() . "<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
