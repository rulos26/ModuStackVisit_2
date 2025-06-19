<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$aportante="SELECT `id`, `id_cedula`, `nombre`, `valor` FROM `aportante` WHERE  `id_cedula`='$id_cedula';";
$data_apor = $mysqli->query($aportante);