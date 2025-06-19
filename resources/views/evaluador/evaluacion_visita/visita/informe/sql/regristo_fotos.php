<?php

include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

$id_cedula = $_SESSION['id_cedula'];
$sql1 = "SELECT * FROM `