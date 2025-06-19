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
                    <h5 class="card-title">VISITA DOMICILIARÍA - PERSONAS QUE APORTAN ECONOMICAMENTE AL HOGAR</h5>
                </div>
                <div class="card-body">

                    <!-- //formulario nuevo -->
                    <form action="guardar.php" method="POST" id="formulario">
                        <div class="row">
                            <div class="col">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre[]" required>
                            </div>
                            <div class="col">
                                <label for="valor" class="form-label">Valor:</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)" id="valor[]" name="valor[]" required>
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