<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
?>
<style>
.container-visita {
    max-width: 600px;
    margin: 40px auto;
    font-family: Arial, sans-serif;
}
.card-visita {
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 30px 30px 10px 30px;
    background: #fff;
}
.card-visita h5 {
    font-size: 1.5em;
    margin-bottom: 18px;
    color: #4361ee;
}
.card-visita .logo {
    display: block;
    margin: 0 auto 18px auto;
    max-width: 180px;
    max-height: 120px;
}
.card-visita .alert {
    background: #e7f3fe;
    border: 1px solid #b3e5fc;
    color: #31708f;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 18px;
}
.card-visita .form-label { font-weight: bold; }
.card-visita .btn { font-size: 1.1em; border-radius: 6px; }
.card-visita .card-footer { text-align: right; color: #888; font-size: 0.95em; margin-top: 18px; }
</style>
<div class="container-visita">
    <div class="card-visita">
        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="logo">
        <h5 class="card-title">VISITA DOMICILIARÍA</h5>
        <div class="alert alert-info">
            <strong>PROTECCIÓN DE DATOS PERSONALES:</strong>
            Al suministrar sus datos personales en este formulario AUTORIZA su tratamiento de forma expresa y voluntaria a la empresa Grupo de Tareas Empresariales. 
            Le informamos que estos serán tratados conforme a lo previsto en la ley 1581 del 2012 y 
            serán incluidos en una base de datos cuyo responsable es Grupo de Tareas Empresariales. 
            La finalidad de la recolección es para proceso que llevaen curso con la <b>ENTIDAD</b>. Usted podrá revocar su autorización en cualquier momento, consultar su información personal y ejercer sus derechos de conocer, actualizar, rectificar, corregir, suprimir o revocar su autorización enviando un email a: grpte@hotmail.com
        </div>
        <!-- Mensajes de sesión -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="session.php" method="POST" id="formDocumento" autocomplete="off">
            <div class="mb-3">
                <label for="id_cedula" class="form-label">
                    <i class="bi bi-card-text me-1"></i>Número de Documento:
                </label>
                <input type="number" class="form-control" id="id_cedula" name="id_cedula" 
                       required min="1" max="9999999999" autocomplete="off" 
                       placeholder="Ingrese su número de cédula">
                <div class="invalid-feedback">
                    Por favor ingrese un número de documento válido (7-10 dígitos).
                </div>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    Ingrese su número de cédula de ciudadanía (7-10 dígitos)
                </div>
            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="Validar Documento" id="btnEnviar" disabled>
            </div>
        </form>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script>
// Validación mejorada del documento
const inputCedula = document.getElementById('id_cedula');
const btnEnviar = document.getElementById('btnEnviar');

function validarDocumento() {
    const valor = inputCedula.value.trim();
    const longitud = valor.length;
    
    // Validar que sea numérico y mayor que 0
    if (!valor || !/^\d+$/.test(valor) || parseInt(valor) <= 0) {
        inputCedula.classList.add('is-invalid');
        inputCedula.classList.remove('is-valid');
        btnEnviar.disabled = true;
        return false;
    }
    
    // Validar longitud (7-10 dígitos)
    if (longitud < 7 || longitud > 10) {
        inputCedula.classList.add('is-invalid');
        inputCedula.classList.remove('is-valid');
        btnEnviar.disabled = true;
        return false;
    }
    
    // Documento válido
    inputCedula.classList.remove('is-invalid');
    inputCedula.classList.add('is-valid');
    btnEnviar.disabled = false;
    return true;
}

// Event listeners
inputCedula.addEventListener('input', validarDocumento);
inputCedula.addEventListener('blur', validarDocumento);

// Validar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    validarDocumento();
});

// Prevenir envío si no es válido
document.getElementById('formDocumento').addEventListener('submit', function(e) {
    if (!validarDocumento()) {
        e.preventDefault();
        inputCedula.focus();
    }
});
</script>
<?php
$contenido = ob_get_clean();

// Verificar si la sesión ya está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../../../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../../../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación Visita Domiciliaria - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .container-visita {
            max-width: 600px;
            margin: 40px auto;
            font-family: Arial, sans-serif;
        }
        .card-visita {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 30px 30px 10px 30px;
            background: #fff;
        }
        .card-visita h5 {
            font-size: 1.5em;
            margin-bottom: 18px;
            color: #4361ee;
        }
        .card-visita .logo {
            display: block;
            margin: 0 auto 18px auto;
            max-width: 180px;
            max-height: 120px;
        }
        .card-visita .alert {
            background: #e7f3fe;
            border: 1px solid #b3e5fc;
            color: #31708f;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 18px;
        }
        .card-visita .form-label { 
            font-weight: bold; 
        }
        .card-visita .btn { 
            font-size: 1.1em; 
            border-radius: 6px; 
        }
        .card-visita .card-footer { 
            text-align: right; 
            color: #888; 
            font-size: 0.95em; 
            margin-top: 18px; 
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Evaluación Visita Domiciliaria</h1>
                            <p class="text-muted mb-0">Inicio del proceso de evaluación</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>