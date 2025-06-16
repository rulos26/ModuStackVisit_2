<?php
session_start();

require_once __DIR__ . '/app/Controllers/LoginController.php';
use App\Controllers\LoginController;

// Verificar si ya hay una sesión activa
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $error = LoginController::login($usuario, $password);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <div class="login-container">
            <div class="card shadow">
                <div class="card-body">
                    <div class="login-header text-center">
                        <img src="public/images/logo.jpg" alt="Logo" class="mb-3" style="max-width: 180px; width: 100%; height: auto;">
                        <h1 class="h3 mb-3 fw-normal">Iniciar Sesión</h1>
                    </div>
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Usuario" required 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            <label for="username">Usuario</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Contraseña" required>
                            <label for="password">Contraseña</label>
                            <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor:pointer;"></i>
                        </div>
                        <div class="form-check text-start my-3">
                            <input class="form-check-input" type="checkbox" value="remember-me" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">
                                Recordarme
                            </label>
                        </div>
                        <button class="btn btn-primary w-100 py-2 btn-login" type="submit">
                            Iniciar Sesión
                        </button>
                        <div class="forgot-password mt-3">
                            <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="public/js/theme.js"></script>
    <script src="public/js/show-password.js"></script>
</body>
</html> 