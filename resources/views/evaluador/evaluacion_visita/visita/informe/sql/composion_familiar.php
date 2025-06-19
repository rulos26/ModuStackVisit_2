<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$familia = "SELECT cf.id, cf.id_cedula, cf.nombre, cf.id_parentesco, cf.edad, cf.id_ocupacion, cf.telefono, cf.id_conviven,cf.observacion,
op.nombre AS nombre_parentesco,
oo.nombre AS nombre_ocupacion,
opa.nombre AS nombre_parametro 
FROM composicion_familiar cf
LEFT JOIN opc_parentesco op ON cf.id_parentesco = op.id
LEFT JOIN opc_ocupacion oo ON cf.id_ocupacion = oo.id
LEFT JOIN opc_parametro opa ON cf.id_conviven = opa.id
WHERE cf.id_cedula = '$id_cedula';";
$familia_data = $mysqli->query($familia);
if ($familia_data->num_rows > 0) {
    
} else {
    
}
/* while ($familia_row = $familia_data->fetch_assoc()) {
    echo "<tr>";
    echo  $familia_row['id'] . "</td>";
    echo  $familia_row['id_cedula'] . "</td>";
    echo  $familia_row['nombre'] . "</td>";
    echo  $familia_row['nombre_parentesco'] . "</td>";
    echo  $familia_row['edad'] . "</td>";
    echo  $familia_row['nombre_ocupacion'] . "</td>";
    echo  $familia_row['telefono'] . "</td>";
    echo  $familia_row['nombre_parametro'] . "</td>";
    echo "</tr>";
} */
