<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea superadministrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('Location: ../../../index.php');
    exit();
}

require_once __DIR__ . '/../../../app/Controllers/SuperAdminController.php';
use App\Controllers\SuperAdminController;

$superAdmin = new SuperAdminController();
$mensaje = '';
$tipoMensaje = 'success';

try {
    // Procesar acciones GET (activar/desactivar/eliminar)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $accion = $_GET['accion'] ?? '';
        $usuario_id = $_GET['usuario_id'] ?? '';
        
        if (empty($usuario_id)) {
            throw new Exception('ID de usuario no proporcionado');
        }
        
        switch ($accion) {
            case 'activar':
                $resultado = $superAdmin->gestionarUsuarios('activar', ['id' => $usuario_id]);
                if (isset($resultado['success'])) {
                    $mensaje = 'Usuario activado exitosamente';
                } else {
                    throw new Exception($resultado['error'] ?? 'Error al activar usuario');
                }
                break;
                
            case 'desactivar':
                $resultado = $superAdmin->gestionarUsuarios('desactivar', ['id' => $usuario_id]);
                if (isset($resultado['success'])) {
                    $mensaje = 'Usuario desactivado exitosamente';
                } else {
                    throw new Exception($resultado['error'] ?? 'Error al desactivar usuario');
                }
                break;
                
            case 'eliminar':
                $resultado = $superAdmin->gestionarUsuarios('eliminar', ['id' => $usuario_id]);
                if (isset($resultado['success'])) {
                    $mensaje = 'Usuario eliminado exitosamente';
                } else {
                    throw new Exception($resultado['error'] ?? 'Error al eliminar usuario');
                }
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
    }
    
    // Procesar acciones POST (crear/actualizar)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        
        // Validar campos requeridos
        $camposRequeridos = ['usuario', 'nombre', 'cedula', 'correo', 'rol', 'activo'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
                throw new Exception("El campo '$campo' es requerido");
            }
        }
        
        // Validar contraseña para creación
        if ($accion === 'crear' && (empty($_POST['password']) || strlen($_POST['password']) < 6)) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }
        
        // Preparar datos del usuario
        $datosUsuario = [
            'usuario' => trim($_POST['usuario']),
            'nombre' => trim($_POST['nombre']),
            'cedula' => trim($_POST['cedula']),
            'correo' => trim($_POST['correo']),
            'rol' => (int)$_POST['rol'],
            'activo' => (int)$_POST['activo']
        ];
        
        // Validar formato de correo
        if (!filter_var($datosUsuario['correo'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El formato del correo electrónico no es válido');
        }
        
        // Validar rol
        if (!in_array($datosUsuario['rol'], [1, 2, 3])) {
            throw new Exception('El rol seleccionado no es válido');
        }
        
        // Validar longitud de campos
        if (strlen($datosUsuario['usuario']) > 50) {
            throw new Exception('El nombre de usuario no puede exceder 50 caracteres');
        }
        
        if (strlen($datosUsuario['nombre']) > 100) {
            throw new Exception('El nombre no puede exceder 100 caracteres');
        }
        
        if (strlen($datosUsuario['cedula']) > 20) {
            throw new Exception('La cédula no puede exceder 20 caracteres');
        }
        
        if (strlen($datosUsuario['correo']) > 100) {
            throw new Exception('El correo no puede exceder 100 caracteres');
        }
        
        switch ($accion) {
            case 'crear':
                // Agregar contraseña para creación
                $datosUsuario['password'] = $_POST['password'];
                
                // Verificar si se debe enviar credenciales por correo
                $enviarCredenciales = isset($_POST['enviar_credenciales']) && $_POST['enviar_credenciales'] == '1';
                $datosUsuario['enviar_credenciales'] = $enviarCredenciales;
                
                $resultado = $superAdmin->gestionarUsuarios('crear', $datosUsuario);
                if (isset($resultado['success'])) {
                    $mensaje = 'Usuario creado exitosamente';
                    if ($enviarCredenciales) {
                        $mensaje .= ' y credenciales enviadas por correo';
                    }
                } else {
                    throw new Exception($resultado['error'] ?? 'Error al crear usuario');
                }
                break;
                
            case 'actualizar':
                // Agregar ID para actualización
                $datosUsuario['id'] = $_POST['usuario_id'];
                
                // Agregar contraseña si se proporcionó una nueva
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                    }
                    $datosUsuario['password'] = $_POST['password'];
                }
                
                $resultado = $superAdmin->gestionarUsuarios('actualizar', $datosUsuario);
                if (isset($resultado['success'])) {
                    $mensaje = 'Usuario actualizado exitosamente';
                    if (isset($datosUsuario['password'])) {
                        $mensaje .= ' (contraseña actualizada)';
                    }
                } else {
                    throw new Exception($resultado['error'] ?? 'Error al actualizar usuario');
                }
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
    }
    
    else {
        throw new Exception('Método de solicitud no válido');
    }
    
} catch (Exception $e) {
    $mensaje = $e->getMessage();
    $tipoMensaje = 'danger';
}

// Redirigir de vuelta a la gestión de usuarios con mensaje
$urlRedireccion = 'gestion_usuarios.php?mensaje=' . urlencode($mensaje) . '&tipo=' . urlencode($tipoMensaje);
header('Location: ' . $urlRedireccion);
exit();
?>
