<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

require_once __DIR__ . '/../../../app/Controllers/OpcionesController.php';
use App\Controllers\OpcionesController;

$opcionesController = new OpcionesController();
$mensaje = '';
$tipoMensaje = '';

// Obtener la tabla desde la URL
$tabla = $_GET['tabla'] ?? '';
$accion = $_GET['accion'] ?? '';

// Validar que la tabla sea válida
$tablasValidas = [
    'opc_concepto_final', 'opc_concepto_seguridad', 'opc_conviven', 'opc_cuenta',
    'opc_entidad', 'opc_estados', 'opc_estado_civiles', 'opc_estado_vivienda',
    'opc_estaturas', 'opc_estratos', 'opc_genero', 'opc_informacion_judicial',
    'opc_inventario_enseres', 'opc_jornada', 'opc_marca', 'opc_modelo',
    'opc_nivel_academico', 'opc_num_hijos', 'opc_ocupacion', 'opc_parametro',
    'opc_parentesco', 'opc_peso', 'opc_propiedad', 'opc_resultado', 'opc_rh',
    'opc_sector', 'opc_servicios_publicos', 'opc_tipo_cuenta', 'opc_tipo_documentos',
    'opc_tipo_inversion', 'opc_tipo_vivienda', 'opc_vehiculo', 'opc_viven'
];

if (!in_array($tabla, $tablasValidas)) {
    $mensaje = 'Tabla no válida';
    $tipoMensaje = 'danger';
} else {
    // Procesar la acción
    switch ($accion) {
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? '')
                ];
                
                // Validar datos
                $validacion = $opcionesController->validarDatos($datos);
                if (!$validacion['valido']) {
                    $mensaje = 'Errores de validación: ' . implode(', ', $validacion['errores']);
                    $tipoMensaje = 'danger';
                } else {
                    $resultado = $opcionesController->crearOpcion($tabla, $datos);
                    $mensaje = $resultado['message'];
                    $tipoMensaje = $resultado['success'] ? 'success' : 'danger';
                }
            }
            break;
            
        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = (int)($_POST['id'] ?? 0);
                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? '')
                ];
                
                if ($id <= 0) {
                    $mensaje = 'ID de opción no válido';
                    $tipoMensaje = 'danger';
                } else {
                    // Validar datos
                    $validacion = $opcionesController->validarDatos($datos);
                    if (!$validacion['valido']) {
                        $mensaje = 'Errores de validación: ' . implode(', ', $validacion['errores']);
                        $tipoMensaje = 'danger';
                    } else {
                        $resultado = $opcionesController->actualizarOpcion($tabla, $id, $datos);
                        $mensaje = $resultado['message'];
                        $tipoMensaje = $resultado['success'] ? 'success' : 'danger';
                    }
                }
            }
            break;
            
        case 'eliminar':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                $mensaje = 'ID de opción no válido';
                $tipoMensaje = 'danger';
            } else {
                $resultado = $opcionesController->eliminarOpcion($tabla, $id);
                $mensaje = $resultado['message'];
                $tipoMensaje = $resultado['success'] ? 'success' : 'danger';
            }
            break;
            
        default:
            $mensaje = 'Acción no válida';
            $tipoMensaje = 'danger';
            break;
    }
}

// Redirigir de vuelta a la vista de gestión
$redirectUrl = "gestion_opciones.php?tabla=" . urlencode($tabla);
if ($mensaje) {
    $redirectUrl .= "&mensaje=" . urlencode($mensaje) . "&tipo=" . urlencode($tipoMensaje);
}

header("Location: $redirectUrl");
exit();
?>
