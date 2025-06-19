<?php
include '../../../../../conn/conexion.php';
$vivienda = "SELECT 
tv.id,tv.id_cedula,tv.id_tipo_vivienda,tv.id_sector,tv.id_propietario,tv.numero_de_familia, 
tv.personas_nucleo_familiar,tv.tiempo_sector,tv.numero_de_pisos,
otv.nombre AS nombre_tipo_vivienda,
os.nombre AS nombre_sector,
op.nombre AS nombre_propiedad
FROM 
tipo_vivienda AS tv
JOIN 
opc_tipo_vivienda AS otv ON tv.id_tipo_vivienda = otv.id
JOIN 
opc_sector AS os ON tv.id_sector = os.id
JOIN 
opc_propiedad AS op ON tv.id_propietario = op.id
WHERE 
tv.id_cedula = '$id_cedula';";

$data_vivienda = $mysqli->query($vivienda);

if ($data_vivienda->num_rows > 0) {
    $filas_vivienda = $data_vivienda->fetch_assoc();
} else {
    echo "No se encontraron registros.";
}

$estado_vivienda = "SELECT ev.id, ev.id_cedula, ev.id_estado, op.nombre
        FROM estado_vivienda AS ev
        JOIN opc_estados AS op ON ev.id_estado = op.id
        WHERE ev.id_cedula = '1110456003'";

$data_vivi = $mysqli->query($estado_vivienda);
if ($data_vivi->num_rows > 0) {
    $row_viv = $data_vivi->fetch_assoc();
} else {
    echo "No se encontraron registros.";
}
