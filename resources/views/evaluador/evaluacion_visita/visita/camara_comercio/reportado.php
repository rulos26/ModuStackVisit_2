<?php
session_start();
include '../../../../../conn/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0/dist/autoNumeric.min.js"></script>

    <!-- Estilos personalizados -->
    <link href="../../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../../css/header.css" rel="stylesheet">



</head>

<body>
    <!-- Menú Vertical -->
 <?php include '../menu/menu.php'; ?>

<!-- Navbar -->
<?php include '../header/header.php'; ?>
    <!-- Contenido de la página -->
    <div style="margin-left: 250px; padding: 20px;">
        <div class="container mt-5">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">VISITA DOMICILIARÍA - CAMARA DE COMERCIO</h5>
                </div>
                <div class="card-body">
                <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <!-- //formulario nuevo -->
                    <form action="guardar.php" method="POST" id="formulario">
                        <div class="row">
                            <div class="col">
                                <label for="nombre" class="form-label">Nombre de Empresa:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col">
                                <label for="valor" class="form-label">Razon Social:</label>
                                <input type="text" class="form-control" id="valor" name="razon" required>
                            </div>
                            <div class="col">
                                <label for="valor" class="form-label">Actividad:</label>
                                <input type="text" class="form-control" id="valor" name="actividad" required>
                            </div>
                            <div class="col-md-12">
                                <label for="cargo" class="form-label">observación:</label>
                                <textarea id="observacion" class="form-control" name="observacion" rows="6" required></textarea>
                            </div>
                        </div> <br><br>
                        <button type="submit" class="btn btn-primary">Siguiente</button>
                     </form>
                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>


        </div>
        <?php include '../../footer/footer.php'; ?>
    </div>


    <!-- Scripts de Bootstrap y JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>