<?php

include '../../../../../conn/conexion.php';

$id_cedula = $_SESSION['id_cedula'];
$sql1 = "SELECT * FROM foto_perfil_autorizacion where id_cedula= $id_cedula ";
$foto1 = $mysqli->query($sql1);


if ($foto1 ->num_rows > 0) {
    $foto1  = $foto1 ->fetch_assoc();
    // Imprimir los datos del registro
    $foto1  = $foto1 ['ruta'] . $foto1 ['nombre'];
    
}

