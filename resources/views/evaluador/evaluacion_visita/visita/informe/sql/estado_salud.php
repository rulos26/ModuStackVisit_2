<?php
include '../../../../../conn/conexion.php';
$salud =  "SELECT e.id, e.id_cedula, oe1.nombre AS nombre_estado_salud, oe2.nombre AS nombre_tipo_enfermedad,
e.tipo_enfermedad_cual, oe3.nombre AS nombre_limitacion_fisica, e.limitacion_fisica_cual,
oe4.nombre AS nombre_tipo_medicamento, e.tipo_medicamento_cual, oe5.nombre AS nombre_ingiere_alcohol,
e.ingiere_alcohol_cual, oe6.nombre AS nombre_fuma, e.observacion
FROM estados_salud e
LEFT JOIN opc_estados oe1 ON e.id_estado_salud = oe1.id
LEFT JOIN opc_parametro oe2 ON e.tipo_enfermedad = oe2.id
LEFT JOIN opc_parametro oe3 ON e.limitacion_fisica = oe3.id
LEFT JOIN opc_parametro oe4 ON e.tipo_medicamento = oe4.id
LEFT JOIN opc_parametro oe5 ON e.ingiere_alcohol = oe5.id
LEFT JOIN opc_parametro oe6 ON e.fuma = oe6.id
WHERE e.id_cedula = '$id_cedula'";
$salud_data = $mysqli->query($salud);

if ($salud_data->num_rows > 0) {
    $fila_salud = $salud_data->fetch_assoc();
    //var_dump($fila_salud);
} else {
    echo "No se encontraron registros.";
}