<?php
// Procesador simple sin dependencias complejas
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en la respuesta JSON

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.']);
    exit();
}

// Obtener la acción solicitada
$accion = $_POST['accion'] ?? '';

try {
    // Conexión directa a la base de datos
    $pdo = new PDO(
        "mysql:host=localhost;dbname=u130454517_modulo_vista;charset=utf8mb4",
        'u130454517_root',
        '0382646740Ju*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    switch ($accion) {
        case 'obtener_usuarios_evaluados':
            // Primero verificar si la tabla evaluados existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'evaluados'");
            if ($stmt->rowCount() == 0) {
                echo json_encode(['error' => 'La tabla "evaluados" no existe en la base de datos']);
                break;
            }
            
            // Verificar la estructura de la tabla
            $stmt = $pdo->query("DESCRIBE evaluados");
            $columnas = $stmt->fetchAll();
            $columnasExistentes = array_column($columnas, 'Field');
            
            // Verificar si las columnas necesarias existen
            $columnasRequeridas = ['id_cedula', 'nombres', 'apellidos'];
            $columnasFaltantes = [];
            
            foreach ($columnasRequeridas as $columna) {
                if (!in_array($columna, $columnasExistentes)) {
                    $columnasFaltantes[] = $columna;
                }
            }
            
            if (!empty($columnasFaltantes)) {
                echo json_encode([
                    'error' => "Las siguientes columnas no existen en la tabla evaluados: " . implode(', ', $columnasFaltantes) . 
                              ". Columnas disponibles: " . implode(', ', $columnasExistentes)
                ]);
                break;
            }
            
            // Obtener los usuarios
            $stmt = $pdo->query("SELECT id_cedula, nombres, apellidos FROM evaluados WHERE id_cedula IS NOT NULL ORDER BY nombres, apellidos");
            $usuarios = $stmt->fetchAll();
            
            echo json_encode($usuarios);
            break;
            
        case 'verificar_tablas_con_datos':
            $idCedula = $_POST['id_cedula'] ?? '';
            
            if (empty($idCedula) || !is_numeric($idCedula)) {
                echo json_encode(['error' => 'ID de cédula inválido']);
                break;
            }
            
            // Lista de tablas relacionadas
            $tablasRelacionadas = [
                'autorizaciones', 'camara_comercio', 'composicion_familiar', 'concepto_final_evaluador',
                'cuentas_bancarias', 'data_credito', 'estados_salud', 'estado_vivienda', 'estudios',
                'evidencia_fotografica', 'experiencia_laboral', 'firmas', 'foto_perfil_autorizacion',
                'gasto', 'informacion_judicial', 'informacion_pareja', 'ingresos_mensuales',
                'inventario_enseres', 'pasivos', 'patrimonio', 'servicios_publicos', 'tipo_vivienda',
                'ubicacion', 'ubicacion_autorizacion', 'ubicacion_foto', 'foto_perfil_visita'
            ];
            
            $tablasConDatos = [];
            
            // Verificar en evaluados
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM evaluados WHERE id_cedula = ?");
            $stmt->execute([$idCedula]);
            $result = $stmt->fetch();
            if ($result['count'] > 0) {
                $tablasConDatos[] = 'evaluados';
            }
            
            // Verificar en tablas relacionadas
            foreach ($tablasRelacionadas as $tabla) {
                try {
                    // Verificar si la tabla existe
                    $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                    if ($stmt->rowCount() > 0) {
                        // Verificar si tiene datos para este id_cedula
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$tabla` WHERE id_cedula = ?");
                        $stmt->execute([$idCedula]);
                        $result = $stmt->fetch();
                        if ($result['count'] > 0) {
                            $tablasConDatos[] = $tabla;
                        }
                    }
                } catch (Exception $e) {
                    // Si hay error con una tabla específica, continuar con las demás
                    error_log("Error verificando tabla $tabla: " . $e->getMessage());
                    continue;
                }
            }
            
            echo json_encode($tablasConDatos);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
