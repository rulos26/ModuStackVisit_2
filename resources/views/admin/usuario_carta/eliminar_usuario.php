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
    
    $errores = [];
    $eliminaciones = [];
    
    // 1. Eliminar de ubicacion_autorizacion
    $sql_ubicacion = "DELETE FROM ubicacion_autorizacion WHERE id_cedula = ?";
    $stmt_ubicacion = $mysqli->prepare($sql_ubicacion);
    if ($stmt_ubicacion) {
        $stmt_ubicacion->bind_param('s', $cedula);
        if ($stmt_ubicacion->execute()) {
            $eliminaciones[] = "ubicacion_autorizacion: " . $stmt_ubicacion->affected_rows . " registros";
        } else {
            $errores[] = "Error al eliminar de ubicacion_autorizacion: " . $stmt_ubicacion->error;
        }
        $stmt_ubicacion->close();
    } else {
        $errores[] = "Error al preparar consulta de ubicacion_autorizacion";
    }
    
    // 2. Eliminar de firmas
    $sql_firmas = "DELETE FROM firmas WHERE id_cedula = ?";
    $stmt_firmas = $mysqli->prepare($sql_firmas);
    if ($stmt_firmas) {
        $stmt_firmas->bind_param('s', $cedula);
        if ($stmt_firmas->execute()) {
            $eliminaciones[] = "firmas: " . $stmt_firmas->affected_rows . " registros";
        } else {
            $errores[] = "Error al eliminar de firmas: " . $stmt_firmas->error;
        }
        $stmt_firmas->close();
    } else {
        $errores[] = "Error al preparar consulta de firmas";
    }
    
    // 3. Eliminar de foto_perfil_autorizacion
    $sql_foto = "DELETE FROM foto_perfil_autorizacion WHERE id_cedula = ?";
    $stmt_foto = $mysqli->prepare($sql_foto);
    if ($stmt_foto) {
        $stmt_foto->bind_param('s', $cedula);
        if ($stmt_foto->execute()) {
            $eliminaciones[] = "foto_perfil_autorizacion: " . $stmt_foto->affected_rows . " registros";
        } else {
            $errores[] = "Error al eliminar de foto_perfil_autorizacion: " . $stmt_foto->error;
        }
        $stmt_foto->close();
    } else {
        $errores[] = "Error al preparar consulta de foto_perfil_autorizacion";
    }
    
    // 4. Eliminar de autorizaciones (tabla principal)
    $sql_autorizaciones = "DELETE FROM autorizaciones WHERE cedula = ?";
    $stmt_autorizaciones = $mysqli->prepare($sql_autorizaciones);
    if ($stmt_autorizaciones) {
        $stmt_autorizaciones->bind_param('s', $cedula);
        if ($stmt_autorizaciones->execute()) {
            $eliminaciones[] = "autorizaciones: " . $stmt_autorizaciones->affected_rows . " registros";
        } else {
            $errores[] = "Error al eliminar de autorizaciones: " . $stmt_autorizaciones->error;
        }
        $stmt_autorizaciones->close();
    } else {
        $errores[] = "Error al preparar consulta de autorizaciones";
    }
    
    // Verificar si hubo errores
    if (!empty($errores)) {
        // Si hay errores, hacer rollback
        $mysqli->rollback();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Errores durante la eliminación: ' . implode(', ', $errores)
        ]);
        exit();
    }
    
    // Si todo salió bien, hacer commit
    $mysqli->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Usuario eliminado exitosamente',
        'eliminaciones' => $eliminaciones
    ]);
    
} catch (Exception $e) {
    // Si hay una excepción, hacer rollback
    $mysqli->rollback();
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