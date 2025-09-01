<?php
// Dashboard Router - Redirige usuarios según su rol
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    // No hay sesión, redirigir al login
    header('Location: index.php');
    exit();
}

// Determinar redirección según el rol
$redirectUrl = '';
switch ($_SESSION['rol']) {
    case 1:
        $redirectUrl = 'resources/views/admin/dashboardAdmin.php';
        break;
    case 2:
        $redirectUrl = 'resources/views/cliente/dashboardCliente.php';
        break;
    case 3:
        $redirectUrl = 'resources/views/superadmin/dashboardSuperAdmin.php';
        break;
    case 4:
        $redirectUrl = 'resources/views/evaluador/dashboardEvaluador.php';
        break;
    default:
        // Rol inválido, destruir sesión y redirigir al login
        session_destroy();
        header('Location: index.php');
        exit();
}

// Verificar que el archivo de destino existe
if (file_exists($redirectUrl)) {
    // Redirigir al dashboard correspondiente
    header('Location: ' . $redirectUrl);
    exit();
} else {
    // El archivo no existe, mostrar error
    http_response_code(500);
    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Error - Dashboard No Encontrado</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 50px; }';
    echo '.error-container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }';
    echo '.error-code { font-size: 72px; color: #dc3545; margin: 0; }';
    echo '.error-title { color: #333; margin: 20px 0; }';
    echo '.error-message { color: #666; margin-bottom: 30px; }';
    echo '.btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px; }';
    echo '.btn:hover { background: #0056b3; }';
    echo '.btn-danger { background: #dc3545; }';
    echo '.btn-danger:hover { background: #c82333; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<div class="error-container">';
    echo '<h1 class="error-code">500</h1>';
    echo '<h2 class="error-title">Error del Sistema</h2>';
    echo '<p class="error-message">El dashboard para tu rol no está disponible en este momento.</p>';
    echo '<p><strong>Rol:</strong> ' . $_SESSION['rol'] . '</p>';
    echo '<p><strong>Dashboard esperado:</strong> ' . $redirectUrl . '</p>';
    echo '<div>';
    echo '<a href="index.php" class="btn">🏠 Volver al Login</a>';
    echo '<a href="logout.php" class="btn btn-danger">🚪 Cerrar Sesión</a>';
    echo '</div>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
    exit();
}
?>
