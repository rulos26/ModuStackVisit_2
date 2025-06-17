<?php
session_start();
include '../../../../../conn/conexion.php';
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: ../../../error/error.php");
    exit();
}


?>


<!DOCTYPE html>
<html lang="es">
<a href="../error/error.php"></a>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace al archivo CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace al archivo de Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="../../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../../css/header.css" rel="stylesheet">
    <style>

    </style>

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
                    <h5 class="card-title">Carta de Autorización </h5>
                </div>
                <div class="card-body">
                <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="65%" height="55%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <form action="guardar.php" method="POST">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="direccion" class="form-label">Dirección:</label>
                                <input type="text" class="form-control" id="direccion" name="direccion">
                            </div>
                            <div class="col-md-4">
                                <label for="barrio" class="form-label">Barrio:</label>
                                <input type="text" class="form-control" id="barrio" name="barrio">
                            </div>
                          
                            <div class="col-md-4">
                                <label for="localidad" class="form-label">Localidad:</label>
                                <input type="text" class="form-control" id="localidad" name="localidad">
                            </div>
                            <div class="col-md-4">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                            <div class="col-md-4">
                                <label for="celular_1" class="form-label">Celular :</label>
                                <input type="tel" class="form-control" id="celular_1" name="celular_1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="correo" name="correo">
                            </div>
                            
                            <!-- fin de row -->
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Siguiente</button>
                    </form>
                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../footer/footer.php'; ?>
    </div>


</body>

<!-- Bootstrap Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>



</html>