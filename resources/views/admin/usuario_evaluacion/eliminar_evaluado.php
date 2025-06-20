<?php
session_start();

// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Verificar que se recibió la cédula
if (!isset($_POST['cedula']) || empty($_POST['cedula'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Cédula no proporcionada']);
    exit();
}

$cedula = $_POST['cedula'];

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

try {
    // Iniciar transacción
    $mysqli->begin_transaction();

    // Lista de tablas a eliminar (en orden para evitar problemas de dependencias)
    $tablas_a_eliminar = [
        'concepto_final_evaluador',
        'experiencia_laboral',
        'informacion_judicial',
        'estudios',
        'gasto',
        'ingresos_mensuales',
        'data_credito',
        'aportante',
        'pasivos',
        'cuentas_bancarias',
        'patrimonio',
        'servicios_publicos',
        'inventario_enseres',
        'tipo_vivienda',
        'informacion_pareja',
        'composicion_familiar',
        'estados_salud',
        'camara_comercio',
        'evidencia_fotografica',
        'ubicacion_autorizacion',
        'evaluados'
    ];

    $eliminaciones_exitosas = 0;
    $errores = [];

    // Eliminar registros de cada tabla
    foreach ($tablas_a_eliminar as $tabla) {
        $sql = "DELETE FROM `$tabla` WHERE id_cedula = ?";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $cedula);
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $eliminaciones_exitosas++;
            } else {
                $errores[] = "Error al eliminar de $tabla: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $errores[] = "Error al preparar consulta para $tabla: " . $mysqli->error;
        }
    }

    // Verificar si hubo errores
    if (!empty($errores)) {
        // Revertir transacción
        $mysqli->rollback();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Errores durante la eliminación: ' . implode(', ', $errores)
        ]);
        exit();
    }

    // Confirmar transacción
    $mysqli->commit();

    // Eliminar archivos de imágenes si existen
    $directorios_imagenes = [
        __DIR__ . '/../../../../public/images/evidencia_fotografica/' . $cedula,
        __DIR__ . '/../../../../public/images/ubicacion_autorizacion/' . $cedula
    ];

    foreach ($directorios_imagenes as $directorio) {
        if (is_dir($directorio)) {
            // Eliminar archivos dentro del directorio
            $archivos = glob($directorio . '/*');
            foreach ($archivos as $archivo) {
                if (is_file($archivo)) {
                    unlink($archivo);
                }
            }
            // Eliminar directorio
            rmdir($directorio);
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => "Evaluado con cédula $cedula eliminado exitosamente. Se eliminaron registros de $eliminaciones_exitosas tablas."
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
?> 