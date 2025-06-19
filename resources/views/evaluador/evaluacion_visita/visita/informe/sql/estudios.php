<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$estudios="SELECT e.id, e.id_cedula, e.centro_estudios, e.id_jornada, e.id_ciudad, e.anno, e.titulos, e.id_resultado,
m.id_municipio, m.municipio
FROM estudios e
JOIN municipios m ON e.id_ciudad = m.id_municipio
WHERE e.id_cedula = '$id_cedula';";
$estudios_data = $mysqli->query($estudios);
if ($estudios_data->num_rows > 0) {
    $estudios_row = $gastos_data->fetch_assoc();
    //var_dump($gastos_row);
} else {
    
}