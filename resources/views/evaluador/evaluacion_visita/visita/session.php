<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la cédula del formulario
    $id_cedula = $_POST['id_cedula'];
    
    // Incluir el controlador de validación de documentos
    require_once __DIR__ . '/../../../../../../app/Controllers/DocumentoValidatorController.php';
    
    use App\Controllers\DocumentoValidatorController;
    
    try {
        // Crear instancia del validador
        $validador = new DocumentoValidatorController();
        
        // Validar el documento según el flujo optimizado
        $resultado = $validador->validarDocumento($id_cedula);
        
        if ($resultado['success']) {
            // Almacenar la cédula en la sesión
            $_SESSION['id_cedula'] = $id_cedula;
            
            // Almacenar mensaje de éxito
            $_SESSION['success'] = $resultado['message'];
            
            // Redirigir según la acción
            if ($resultado['action'] === 'evaluado_existente' || $resultado['action'] === 'evaluado_creado') {
                header("Location: " . $resultado['redirect']);
            } else {
                header("Location: informacion_personal/informacion_personal.php");
            }
        } else {
            // Almacenar mensaje de error
            $_SESSION['error'] = $resultado['message'];
            
            // Redirigir según la acción
            if ($resultado['action'] === 'no_encontrado' && $resultado['redirect']) {
                header("Location: " . $resultado['redirect']);
            } else {
                // Volver al formulario de ingreso con error
                header("Location: index.php");
            }
        }
        
        exit;
        
    } catch (Exception $e) {
        error_log("ERROR session.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor. Intente nuevamente.";
        header("Location: index.php");
        exit;
    }
}
?>
