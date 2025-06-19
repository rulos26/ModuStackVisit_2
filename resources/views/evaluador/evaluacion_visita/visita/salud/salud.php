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
                    <h5 class="card-title">VISITA DOMICILIARÍA - SALUD</h5>
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
                                <label for="id_estado_salud" class="form-label">Estado de Salud:</label>
                                <select class="form-select" id="id_estado_salud" name="id_estado_salud">
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
                            <div class="col-md-4">
                                <label for="tipo_enfermedad" class="form-label">¿Padece algún tipo de enfermedad?:</label>
                                <select class="form-select" id="tipo_enfermedad" name="tipo_enfermedad">
                                    <option value="1" selected>No</option>
                                    <?php
                                    // Consulta SQL para tipo_enfermedad
                                    $sql_tipo_enfermedad = "SELECT id, nombre FROM opc_parametro ";
                                    // Ejecutar consulta y recorrer resultados
                                    $resultado_tipo_enfermedad = $mysqli->query($sql_tipo_enfermedad);
                                    if ($resultado_tipo_enfermedad->num_rows > 0) {
                                        while ($fila_tipo_enfermedad = $resultado_tipo_enfermedad->fetch_assoc()) {
                                            echo "<option value='" . $fila_tipo_enfermedad['id'] . "'>" . $fila_tipo_enfermedad['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4" id="tipo_enfermedad_cual">
                                <label for="tipo_enfermedad_cual" class="form-label">¿cual(es)?</label>
                                <input type="text" class="form-control" id="tipo_enfermedad_cual" name="tipo_enfermedad_cual" >
                            </div>
                            <div class="col-md-4">
                                <label for="limitacion_fisica" class="form-label">¿Tiene alguna limitación física?</label>
                                <select class="form-select" id="limitacion_fisica" name="limitacion_fisica">
                                    <option value="1" selected>No</option>
                                    <?php
                                    // Consulta SQL para limitacion_fisica
                                    $sql_limitacion_fisica = "SELECT id, nombre FROM opc_parametro ";
                                    // Ejecutar consulta y recorrer resultados
                                    $resultado_limitacion_fisica = $mysqli->query($sql_limitacion_fisica);
                                    if ($resultado_limitacion_fisica->num_rows > 0) {
                                        while ($fila_limitacion_fisica = $resultado_limitacion_fisica->fetch_assoc()) {
                                            echo "<option value='" . $fila_limitacion_fisica['id'] . "'>" . $fila_limitacion_fisica['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4" id="limitacion_fisica_cual">
                                <label for="limitacion_fisica_cual" class="form-label">¿cual(es)?</label>
                                <input type="text" class="form-control" id="limitacion_fisica_cual" name="limitacion_fisica_cual">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_medicamento" class="form-label">Tipo de Medicamento:</label>
                                <select class="form-select" id="tipo_medicamento" name="tipo_medicamento">
                                    <option value="1" selected>No</option>
                                    <?php
                                    // Consulta SQL para tipo_medicamento
                                    $sql_tipo_medicamento = "SELECT id, nombre FROM opc_parametro ";
                                    // Ejecutar consulta y recorrer resultados
                                    $resultado_tipo_medicamento = $mysqli->query($sql_tipo_medicamento);
                                    if ($resultado_tipo_medicamento->num_rows > 0) {
                                        while ($fila_tipo_medicamento = $resultado_tipo_medicamento->fetch_assoc()) {
                                            echo "<option value='" . $fila_tipo_medicamento['id'] . "'>" . $fila_tipo_medicamento['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4" id="tipo_medicamento_cual">
                                <label for="tipo_medicamento_cual" class="form-label">¿cual(es)?</label>
                                <input type="text" class="form-control" id="tipo_medicamento_cual" name="tipo_medicamento_cual">
                            </div>
                            <div class="col-md-4" >
                                <label for="ingiere_alcohol" class="form-label">Ingiere Alcohol:</label>
                                <select class="form-select" id="ingiere_alcohol" name="ingiere_alcohol">
                                    <option value="1" selected>No</option>
                                    <?php
                                    // Consulta SQL para ingiere_alcohol
                                    $sql_ingiere_alcohol = "SELECT id, nombre FROM opc_parametro ";
                                    // Ejecutar consulta y recorrer resultados
                                    $resultado_ingiere_alcohol = $mysqli->query($sql_ingiere_alcohol);
                                    if ($resultado_ingiere_alcohol->num_rows > 0) {
                                        while ($fila_ingiere_alcohol = $resultado_ingiere_alcohol->fetch_assoc()) {
                                            echo "<option value='" . $fila_ingiere_alcohol['id'] . "'>" . $fila_ingiere_alcohol['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4" id="ingiere_alcohol_cual">
                                <label for="ingiere_alcohol_cual" class="form-label">¿cual(es)?</label>
                                <input type="text" class="form-control" id="ingiere_alcohol_cual" name="ingiere_alcohol_cual">
                            </div>
                            <div class="col-md-4 " id="">
                                <label for="fuma" class="form-label">Fuma:</label>
                                <select class="form-select" id="fuma" name="fuma">
                                    <option value="1" selected>No</option>
                                    <?php
                                    // Consulta SQL para fuma
                                    $sql_fuma = "SELECT id, nombre FROM opc_parametro";
                                    // Ejecutar consulta y recorrer resultados

                                    $resultado_fuma = $mysqli->query($sql_fuma);
                                    if ($resultado_fuma->num_rows > 0) {
                                        while ($fila_fuma = $resultado_fuma->fetch_assoc()) {
                                            echo "<option value='" . $fila_fuma['id'] . "'>" . $fila_fuma['nombre'] . "</option>";
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

                        <button type="submit" class="btn btn-primary mt-3">Siguiente</button>
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

    <script>
        // Obtener los elementos de referencia
        const tipoEnfermedad = document.getElementById('tipo_enfermedad');
        const limitacionFisica = document.getElementById('limitacion_fisica');
        const tipoMedicamento = document.getElementById('tipo_medicamento');
        const ingiereAlcohol = document.getElementById('ingiere_alcohol');
        const fuma = document.getElementById('fuma');

        // Obtener los elementos a ocultar
        const tipoEnfermedadCual = document.getElementById('tipo_enfermedad_cual');
        const limitacionFisicaCual = document.getElementById('limitacion_fisica_cual');
        const tipoMedicamentoCual = document.getElementById('tipo_medicamento_cual');
        const ingiereAlcoholCual = document.getElementById('ingiere_alcohol_cual');
        // Función para mostrar u ocultar campos
        function toggleCampos() {
            // Mostrar u ocultar campos según el valor seleccionado
            tipoEnfermedadCual.style.display = tipoEnfermedad.value === '2' ? 'block' : 'none';
            limitacionFisicaCual.style.display = limitacionFisica.value === '2' ? 'block' : 'none';
            tipoMedicamentoCual.style.display = tipoMedicamento.value === '2' ? 'block' : 'none';
            ingiereAlcoholCual.style.display = ingiereAlcohol.value === '2' ? 'block' : 'none';
        }

        // Escuchar el evento de cambio en los campos de referencia
        tipoEnfermedad.addEventListener('change', toggleCampos);
        limitacionFisica.addEventListener('change', toggleCampos);
        tipoMedicamento.addEventListener('change', toggleCampos);
        ingiereAlcohol.addEventListener('change', toggleCampos);
        fuma.addEventListener('change', toggleCampos);

        // Ejecutar la función al cargar la página
        toggleCampos();
    </script>

</body>

</html>