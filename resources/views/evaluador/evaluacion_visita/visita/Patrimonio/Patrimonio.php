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
        <div class="container">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">VISITA DOMICILIARÍA - PATRIMONIO</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <form action="guardar.php" method="POST">

                        <div class="row mb-3">
                            <div class="col">
                                <label for="valor_vivienda" class="form-label">Valor Vivienda</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)" id="valor_vivienda" name="valor_vivienda" required>
                                </div>
                            </div>

                            <div class="col">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                            <div class="col">
                                <label for="id_vehiculo" class="form-label">Vehículo</label>
                                <input type="texto" class="form-control" id="id_vehiculo" name="id_vehiculo" required>
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="id_marca" class="form-label">Marca</label>
                                <input type="texto" class="form-control" id="id_marca" name="id_marca" required>
                            </div>
                            <div class="col">
                                <label for="id_modelo" class="form-label">Modelo</label>
                                <input type="texto" class="form-control" id="id_modelo" name="id_modelo" required>
                            </div>
                            <div class="col">
                                <label for="id_ahorro-label">Ahorro (CDT, Inversiones)</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)" id="id_ahorro" name="id_ahorro" required>
                                </div>
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="otros" class="form-label">Otros</label>
                                <input type="text" class="form-control" id="otros" name="otros">
                            </div>
                        </div>
                        <div class="col-md-12">
                                <label for="cargo" class="form-label">observación:</label>
                                <textarea id="observacion" class="form-control" name="observacion" rows="6" required></textarea>
                            </div> 

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

    <script src="../../../../../js/toggleMenu.js"></script>
    <script src="../../../../../js/active_link.js"></script>


</body>

</html>