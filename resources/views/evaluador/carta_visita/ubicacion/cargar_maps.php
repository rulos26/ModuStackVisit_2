<?php
session_start();
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$sql = "SELECT * FROM `ubicacion` WHERE `id_cedula` = '$id_cedula'";


$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $ingresos_row = $result->fetch_assoc();
    $longitud = $ingresos_row['longitud'];
    $latitud = $ingresos_row['latitud'];
    
} else {
}


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
    <style>
        .map-container {
            width: 100%;
            height: 400px;
            position: relative;
        }
        .map-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
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
                    <!--  <form action="guardar.php" method="POST"> -->
                    <div class="row mt-3">
                        <div class="map-container">
                            <img id="mapImage" class="map-image" src="" alt="Mapa de ubicación">
                        </div>
                        <button id="descargar" class="btn btn-primary mt-3">Descargar Imagen</button>
                        <!-- Botón de Enviar -->
                        <div class="col-md-12">
                            <a href="foto_ubicacion.php" class="btn btn-primary mt-3">siguiente</a>
                        </div>
                    </div>
                    <!-- Agrega más filas de campos según sea necesario -->


                    <!--      </form> -->


                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../footer/footer.php'; ?>
    </div>

    <script src="../../../../../js/toggleMenu.js"></script>
    <script src="../../../../../js/active_link.js"></script>

    <script>
        // Configuración de Mapbox
        const MAPBOX_ACCESS_TOKEN = 'TU_ACCESS_TOKEN'; // Reemplazar con tu token de Mapbox
        const latitud = <?php echo $latitud; ?>;
        const longitud = <?php echo $longitud; ?>;
        
        // Generar URL de la imagen estática
        function generarMapaEstatico() {
            const zoom = 15;
            const width = 800;
            const height = 400;
            const marker = `pin-s+ff0000(${longitud},${latitud})`;
            
            const url = `https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/${marker}/${longitud},${latitud},${zoom}/${width}x${height}?access_token=${MAPBOX_ACCESS_TOKEN}`;
            
            document.getElementById('mapImage').src = url;
        }

        // Cargar el mapa al iniciar
        window.onload = generarMapaEstatico;

        // Función para descargar la imagen
        document.getElementById('descargar').addEventListener('click', function() {
            const link = document.createElement('a');
            link.href = document.getElementById('mapImage').src;
            link.download = 'mapa_ubicacion.jpg';
            link.click();
        });
    </script>

</body>

</html>