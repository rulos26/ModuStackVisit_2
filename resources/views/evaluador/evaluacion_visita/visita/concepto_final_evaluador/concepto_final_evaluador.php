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
                    <h5 class="card-title">VISITA DOMICILIARÍA-CONCEPTO FINAL DEL PROFESIONAL O EVALUADOR</h5>
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

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="actitud" class="form-label">Actitud del evaluado y su grupo familiar</label>
                                    <input type="text" class="form-control" id="actitud" name="actitud" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="condiciones_vivienda" class="form-label">Condiciones Vivienda</label>
                                    <input type="text" class="form-control" id="condiciones_vivienda" name="condiciones_vivienda" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dinamica_familiar" class="form-label">Dinámica Familiar</label>
                                    <input type="text" class="form-control" id="dinamica_familiar" name="dinamica_familiar" required>
                                </div>
                            </div>
                        
                        </div>
                        <!-- Continuación del formulario de registro -->
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="condiciones_economicas" class="form-label">Condiciones socio económicas</label>
                                    <input type="text" class="form-control" id="condiciones_economicas" name="condiciones_economicas" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="condiciones_academicas" class="form-label">Condiciones Académicas</label>
                                    <input type="text" class="form-control" id="condiciones_academicas" name="condiciones_academicas" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="evaluacion_experiencia_laboral" class="form-label">Evaluación Experiencia Laboral</label>
                                    <input type="text" class="form-control" id="evaluacion_experiencia_laboral" name="evaluacion_experiencia_laboral" required>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                           
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <input type="text" class="form-control" id="observaciones" name="observaciones" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_concepto_final" class="form-label">CONCEPTO FINAL DE LA VISITA</label>
                                    <select class="form-select" id="id_concepto_final" name="id_concepto_final">
                                        <?php
                                        // Consulta SQL para id_estado_salud
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nombre_evaluador" class="form-label">Nombre del Evaluador</label>
                                    <input type="text" class="form-control" id="nombre_evaluador" name="nombre_evaluador" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                           
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_concepto_seguridad" class="form-label">CONCEPTO DE SEGURIDAD</label>
                                    <select class="form-select" id="id_concepto_seguridad" name="id_concepto_seguridad">
                                        <?php
                                        // Consulta SQL para id_estado_salud
                                        $sql_estado_salud = "SELECT * FROM `opc_concepto_seguridad`";
                                        // Ejecutar consulta y recorrer resultados
                                        $resultado_estado_salud = $mysqli->query($sql_estado_salud);
                                        if ($resultado_estado_salud->num_rows > 0) {
                                            while ($fila_estado_salud = $resultado_estado_salud->fetch_assoc()) {
                                                echo "<option value='" . $fila_estado_salud['id'] . "'>" . $fila_estado_salud['nombre'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select> </div>
                            </div>
                        </div>

                        <!-- Agrega más filas con campos según sea necesario -->

                        <!-- Agrega más filas con campos según sea necesario -->
                        <button type="submit" class="btn btn-primary">Guardar</button>


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