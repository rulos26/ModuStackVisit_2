<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$pasivo="SELECT 
p.item, 
p.id_entidad, 
p.id_tipo_inversion, 
p.id_ciudad, 
p.deuda, 
p.cuota_mes,
m.id_municipio, 
m.municipio
FROM 
pasivos p
JOIN 
municipios m ON p.id_ciudad = m.id_municipio
WHERE 
p.id_cedula = '$id_cedula';";
$data_pasivo= $mysqli->query($pasivo);
