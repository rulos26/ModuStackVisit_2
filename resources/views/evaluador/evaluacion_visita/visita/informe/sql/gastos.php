<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$gastos="SELECT * FROM `gasto` WHERE   `id_cedula`='$id_cedula';";
$gastos_data = $mysqli->query($gastos);
if ($gastos_data->num_rows > 0) {
    $gastos_row = $gastos_data->fetch_assoc();
    //var_dump($gastos_row);
} else {
    
}