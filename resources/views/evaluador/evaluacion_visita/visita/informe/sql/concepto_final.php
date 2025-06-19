<?php

include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$concepto_final="SELECT c.id, c.id_cedula, c.actitud, c.condiciones_vivienda, c.dinamica_familiar, c.condiciones_economicas, 
c.condiciones_academicas, c.evaluacion_experiencia_laboral, c.observaciones, c.id_concepto_final, 
c.nombre_evaluador, c.id_concepto_seguridad, e.nombre AS estado_nombre
FROM concepto_final_evaluador AS c
LEFT JOIN opc_estados AS e ON c.id_concepto_final = e.id
WHERE c.id_cedula = '$id_cedula';";

$concepto_final = $mysqli->query($concepto_final);
if ($concepto_final ->num_rows > 0) {
    $row_concepto_final = $concepto_final->fetch_assoc();
    //var_dump($row_concepto_final);
}