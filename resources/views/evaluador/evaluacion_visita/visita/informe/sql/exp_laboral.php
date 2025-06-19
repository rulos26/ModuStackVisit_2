<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$exp_laboral="SELECT `id`, `id_cedula`, `empresa`, `tiempo`, `cargo`, `salario`, `retiro`, `concepto`,
`nombre`, `numero` FROM `experiencia_laboral` WHERE `id_cedula`='1110456003';  ";
$exp_laboral_data = $mysqli->query($exp_laboral);
if ($exp_laboral_data->num_rows > 0) {
    $exp_laboral_row = $exp_laboral_data->fetch_assoc();
    //var_dump($judi_row);
} else {
    
}