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
                    <h5 class="card-title">VISITA DOMICILIARÍA - TIPO DE VIVIENDA</h5>
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
                        <div class="row">

                            <!-- Campo ID de Tipo de Vivienda -->
                            <div class="col-md-4">
                                <label for="id_tipo_vivienda" class="form-label">Tipo de Vivienda:</label>
                                <select class="form-select" id="id_tipo_vivienda" name="id_tipo_vivienda">
                                    <?php
                                    // Consulta SQL para obtener los tipos de vivienda
                                    $sql_tipo_vivienda = "SELECT id, nombre FROM opc_tipo_vivienda";
                                    $resultado_tipo_vivienda = $mysqli->query($sql_tipo_vivienda);
                                    if ($resultado_tipo_vivienda->num_rows > 0) {
                                        while ($fila_tipo_vivienda = $resultado_tipo_vivienda->fetch_assoc()) {
                                            echo "<option value='" . $fila_tipo_vivienda['id'] . "'>" . $fila_tipo_vivienda['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Campo ID de Sector -->
                            <div class="col-md-4">
                                <label for="id_sector" class="form-label">Sector:</label>
                                <select class="form-select" id="id_sector" name="id_sector">
                                    <?php
                                    // Consulta SQL para obtener los sectores
                                    $sql_sector = "SELECT id, nombre FROM opc_sector";
                                    $resultado_sector = $mysqli->query($sql_sector);
                                    if ($resultado_sector->num_rows > 0) {
                                        while ($fila_sector = $resultado_sector->fetch_assoc()) {
                                            echo "<option value='" . $fila_sector['id'] . "'>" . $fila_sector['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Campo ID de Propietario -->
                            <div class="col-md-4">
                                <label for="id_propietario" class="form-label">Propietario:</label>
                                <select class="form-select" id="id_propietario" name="id_propietario">
                                    <?php
                                    // Consulta SQL para obtener los propietarios
                                    $sql_propietario = "SELECT id, nombre FROM opc_propiedad";
                                    $resultado_propietario = $mysqli->query($sql_propietario);
                                    if ($resultado_propietario->num_rows > 0) {
                                        while ($fila_propietario = $resultado_propietario->fetch_assoc()) {
                                            echo "<option value='" . $fila_propietario['id'] . "'>" . $fila_propietario['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Campo Número de Familia -->
                            <div class="col-md-4">
                                <label for="numero_de_familia" class="form-label">Número de hogares habitan en la vivienda:</label>
                                <input type="number" class="form-control" id="numero_de_familia" name="numero_de_familia" required>
                            </div>
                            <!-- Campo Personas Núcleo Familiar -->
                            <div class="col-md-4">
                                <label for="personas_nucleo_familiar" class="form-label">Personas que conforman núcleo familiar:</label>
                                <input type="number" class="form-control" id="personas_nucleo_familiar" name="personas_nucleo_familiar" required>
                            </div>
                            <!-- Campo Tiempo en el Sector -->
                            <div class="col-md-4">
                                <label for="tiempo_sector" class="form-label">Tiempo en Residencia en el Sector:</label>
                                <input type="date" class="form-control" id="tiempo_sector" name="tiempo_sector" required>
                            </div>
                            <!-- Campo Número de Pisos -->
                            <div class="col-md-4">
                                <label for="numero_de_pisos" class="form-label">Número de Pisos de la Vivienda:</label>
                                <input type="number" class="form-control" id="numero_de_pisos" name="numero_de_pisos" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="cargo" class="form-label">observación:</label>
                            <textarea id="observacion" class="form-control" name="observacion" rows="6" required></textarea>
                        </div>
                        <div class="row mt-3">
                            <!-- Botón de Enviar -->
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mt-3">siguiente</button>
                            </div>
                        </div>
                        <!-- Agrega más filas de campos según sea necesario -->


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