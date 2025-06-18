<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Automatización: Si ya hay sesión, redirigir a la página principal del sistema
if (isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../css/header.css" rel="stylesheet">
</head>
<body>
    <!-- Menú Vertical -->
    <?php include '../menu/menu.php'; ?>
    <!-- Navbar -->
    <?php include '../header/header.php'; ?>

    <!-- Contenido de la página -->
    <div style="margin-left: 250px; padding: 20px;">
        <div class="container">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">VISITA DOMICILIARÍA</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="65%" height="55%">
                        </div>
                        <div class="col-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col-8" style="text-align: justify;">
                            <p>
                                <strong>PROTECCIÓN DE DATOS PERSONALES:</strong>
                                Al suministrar sus datos personales en este formulario AUTORIZA su tratamiento de forma expresa y voluntaria a la empresa Grupo de Tareas Empresariales. Le informamos que estos serán tratados conforme a lo previsto en la ley 1581 del 2012 y serán incluidos en una base de datos cuyo responsable es Grupo de Tareas Empresariales. La finalidad de la recolección es la gestión de siniestros y el trámite de reclamos ante las compañías de seguros. Usted podrá revocar su autorización en cualquier momento, consultar su información personal y ejercer sus derechos de conocer, actualizar, rectificar, corregir, suprimir o revocar su autorización enviando un email a: grpte@hotmail.com
                            </p>
                        </div>
                        <div class="col-2"></div>
                    </div>
                    <!-- Formulario optimizado -->
                    <form action="session.php" method="POST" id="formDocumento" autocomplete="off">
                        <div class="row mb-3">
                            <div class="col"></div>
                            <div class="col">
                                <label for="id_cedula" class="form-label">Número de Documento:</label>
                                <input type="number" class="form-control" id="id_cedula" name="id_cedula" required min="1" autocomplete="off">
                                <div class="invalid-feedback">
                                    Por favor ingrese un número de documento válido.
                                </div>
                            </div>
                            <div class="col"></div>
                        </div>
                        <center>
                            <br><br>
                            <input type="submit" class="btn btn-primary" value="Empezar" id="btnEnviar" disabled>
                        </center>
                    </form>
                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../footer/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../../js/toggleMenu.js"></script>
    <script src="../../../../js/active_link.js"></script>
    <script src="../../../../js/autorizacion.js"></script>
    <script src="../../../../js/validar_password.js"></script>
    <script>
    // Automatización: Habilitar el botón solo si el campo tiene valor válido
    const inputCedula = document.getElementById('id_cedula');
    const btnEnviar = document.getElementById('btnEnviar');
    inputCedula.addEventListener('input', function() {
        if (inputCedula.value.length > 0 && parseInt(inputCedula.value) > 0) {
            btnEnviar.disabled = false;
            inputCedula.classList.remove('is-invalid');
        } else {
            btnEnviar.disabled = true;
            inputCedula.classList.add('is-invalid');
        }
    });
    </script>
</body>
</html>