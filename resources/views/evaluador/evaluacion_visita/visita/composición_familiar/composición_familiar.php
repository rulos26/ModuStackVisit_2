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
        <div class="container mt-5">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">VISITA DOMICILIARÍA - COMPOSICIÓN FAMILIAR </h5>
                </div>
                <div class="card-body">

                    <!-- //formulario nuevo -->
                    <form action="guardar.php" method="POST" id="formulario">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre[]" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_parentesco" class="form-label">Parentesco:</label>
                                <select class="form-select" id="id_parentesco" name="id_parentesco[]" required>
                                    <?php
                                    // Consulta SQL para obtener los datos de parentesco
                                    $sql_parentesco = "SELECT `id`, `nombre` FROM `opc_parentesco`";
                                    // Ejecutar la consulta
                                    $resultado_parentesco = $mysqli->query($sql_parentesco);
                                    // Mostrar los resultados en el selectbox
                                    while ($fila_parentesco = $resultado_parentesco->fetch_assoc()) {
                                        echo "<option value='" . $fila_parentesco['id'] . "'>" . $fila_parentesco['nombre'] . "</option>";
                                    }
                                    // Cerrar la $mysqli

                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="edad" class="form-label">Edad:</label>
                                <input type="number" class="form-control" id="edad" name="edad[]" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_ocupacion" class="form-label">Ocupación:</label>
                                <select class="form-select" id="id_ocupacion" name="id_ocupacion[]">

                                    <?php
                                    $sql = "SELECT id, nombre FROM opc_ocupacion";
                                    $resultado = $mysqli->query($sql);

                                    // Verificar si la consulta fue exitosa
                                    if ($resultado->num_rows > 0) {
                                        // Procesar los resultados
                                        while ($fila = $resultado->fetch_assoc()) {
                                            echo "<option value='" . $fila['id'] . "'>" . $fila['nombre'] . "</option>";
                                        }
                                    } else {
                                        echo "No se encontraron resultados.";
                                    }

                                    // Cerrar la conexión

                                    ?>

                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono" name="telefono[]" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="id_conviven" class="form-label">Conviven:</label>
                                <select class="form-select" id="id_conviven" name="id_conviven[]">
                                    <?php
                                    // Consulta SQL para obtener los datos de conviven
                                    $sql_conviven = "SELECT `id`, `nombre` FROM `opc_parametro`";

                                    // Ejecutar la consulta
                                    $resultado_conviven = $mysqli->query($sql_conviven);

                                    // Mostrar los resultados en el selectbox
                                    while ($fila_conviven = $resultado_conviven->fetch_assoc()) {
                                        echo "<option value='" . $fila_conviven['id'] . "'>" . $fila_conviven['nombre'] . "</option>";
                                    }

                                    // Cerrar la conexión

                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="cargo" class="form-label">observación:</label>
                                <textarea id="observacion" class="form-control" name="observacion[]" rows="6" required></textarea>
                            </div>
                        </div>
                        <div class="row" id="campos_clonados">
                            <!-- Aquí se agregarán los campos clonados -->
                        </div>
                        <button type="submit" class="btn btn-primary">Siguiente</button>
                        <button type="button" class="btn btn-secondary" id="clonar">Agregar Campo</button>
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
    <script>
        document.getElementById('clonar').addEventListener('click', function() {
            const camposOriginales = document.querySelector('.row').cloneNode(true);
            limpiarCampos(camposOriginales);
            document.getElementById('campos_clonados').appendChild(camposOriginales);
        });

        function limpiarCampos(elemento) {
            // Recorrer todos los elementos dentro del elemento clonado
            elemento.querySelectorAll('input, textarea').forEach(function(input) {
                // Establecer el valor del campo como vacío
                input.value = '';
            });

            // También puedes limpiar campos select si los tienes
            elemento.querySelectorAll('select').forEach(function(select) {
                // Establecer el valor del campo select en su valor predeterminado (si lo hay)
                select.selectedIndex = 0; // Esto establecerá la primera opción como seleccionada
            });
        }
    </script>
</body>

</html>