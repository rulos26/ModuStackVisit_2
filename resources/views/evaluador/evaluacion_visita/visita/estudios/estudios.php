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
                    <h5 class="card-title">VISITA DOMICILIARÍA-VERIFICACIÓN ACADEMICA</h5>
                </div>
                <div class="card-body">

                    <!-- //formulario nuevo -->
                    <form action="guardar.php" method="POST" id="formulario">
                        <div class="row">
                        <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="centro_estudios" class="form-label">Centro de Estudios:</label>
                                    <input type="text" class="form-control" id="centro_estudios" name="centro_estudios[]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="id_jornada" class="form-label">Jornada:</label>
                                    <input type="text" class="form-control" id="id_jornada" name="id_jornada[]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="id_ciudad" class="form-label">Ciudad:</label>
                                    <select class="form-select" id="id_ciudad" name="id_ciudad[]">
                                        <?php
                                        // Consulta a la tabla de municipios para obtener las ciudades
                                        $consulta_ciudad = "SELECT id_municipio, municipio FROM municipios";
                                        $resultado_ciudad = $mysqli->query($consulta_ciudad);

                                        // Mostrar opciones en el selectbox
                                        while ($fila_ciudad = $resultado_ciudad->fetch_assoc()) {
                                            echo "<option value='" . $fila_ciudad['id_municipio'] . "'>" . $fila_ciudad['municipio'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="anno" class="form-label">Año:</label>
                                    <input type="number" class="form-control" id="anno" name="anno[]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="titulos" class="form-label">Títulos:</label>
                                    <input type="text" class="form-control" id="titulos" name="titulos[]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="id_resultado" class="form-label">Resultado:</label>
                                    <input type="text" class="form-control" id="id_resultado" name="id_resultado[]" required>
                                </div>
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

    </div>


    <!-- Scripts de Bootstrap y JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('clonar').addEventListener('click', function() {
            const camposOriginales = document.querySelector('.row').cloneNode(true);
            document.getElementById('campos_clonados').appendChild(camposOriginales);
        });
    </script>
    <script>
        const valor = document.getElementById('valor');
        new AutoNumeric(
            valor, {
                currencySymbol: '$', // Símbolo de moneda
                decimalCharacter: '.', // Carácter decimal
                digitGroupSeparator: ',', // Separador de miles
            });
    </script>
</body>

</html>