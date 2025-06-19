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
                    <h5 class="card-title">VISITA DOMICILIARÍA - ESTADO DE LA VIVIENDA</h5>
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

                            <div class="col-md-4">
                                <label for="id_estado" class="form-label">Estado de la vivienda</label>
                                <select class="form-select" id="id_estado" name="id_estado">
                                    <?php
                                    // Consulta SQL para id_estado
                                    $sql_estado_salud = "SELECT id, nombre FROM opc_estados";
                                    // Ejecutar consulta y recorrer resultados
                                    $resultado_estado_salud = $mysqli->query($sql_estado_salud);
                                    if ($resultado_estado_salud->num_rows > 0) {
                                        while ($fila_estado_salud = $resultado_estado_salud->fetch_assoc()) {
                                            echo "<option value='" . $fila_estado_salud['id'] . "'>" . $fila_estado_salud['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="cargo" class="form-label">observación:</label>
                                <textarea id="observacion" class="form-control" name="observacion" rows="6" required></textarea>
                            </div>
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
    <script src="../../../../../js/autorizacion,js"></script>
    <script src="../../../../../js/validar_password.js"></script>
    <script>

    </script>

</body>

</html>