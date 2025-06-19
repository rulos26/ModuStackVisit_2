<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$ingresos="SELECT * FROM `ingresos_mensuales` WHERE   `id_cedula`='$id_cedula';";
$ingresos_data = $mysqli->query($ingresos);
if ($ingresos_data->num_rows > 0) {
    $ingresos_row = $ingresos_data->fetch_assoc();
    //var_dump($ingresos_row);
} else {
    
}