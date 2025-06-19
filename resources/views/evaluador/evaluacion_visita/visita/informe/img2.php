<?php


include '../../../../../conn/conexion.php';
// Consulta SQL
$sql = "SELECT * FROM `ubicacion` WHERE `id_cedula` = '1110456003'";

$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $ingresos_row = $result->fetch_assoc();
    $longitud = $ingresos_row['longitud'];
    $latitud = $ingresos_row['latitud'];
    var_dump($ingresos_row);
} else {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capturar Mapa como Imagen</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css"/>
    <script src="leaflet-image.js"></script> <!-- Asegúrate de incluir el archivo JS de Leaflet.imageExport -->
</head>
<body>
    <div id="map" style="height: 400px;"></div>
    <button id="capturar">Capturar y Descargar Imagen</button>

    <script>
        var map = L.map('map').setView([<?php echo $latitud; ?>, <?php echo $longitud; ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        var marker = L.marker([<?php echo $latitud; ?>, <?php echo $longitud; ?>]).addTo(map);

        document.getElementById('capturar').addEventListener('click', function() {
            leafletImage(map, function(err, canvas) {
                // Descargar la imagen
                var link = document.createElement('a');
                link.href = canvas.toDataURL('image/jpeg');
                link.download = '../informe/img/fotos_perfil/mapa.jpg';
                link.click();
                
            });
        });
    </script>
</body>
</html>
