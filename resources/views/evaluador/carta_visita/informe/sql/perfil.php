<?php
//session_start();
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto perfil
$sql = "SELECT * FROM firmas where id_cedula= $id_cedula";
$foto = $mysqli->query($sql);
if ($foto->num_rows > 0) {
    $foto = $foto->fetch_assoc();
    // Imprimir los datos del registro
    $ruta_firma = $foto['ruta'] . $foto['nombre'];
    //echo $ruta_firma;
    $nombre_imagen = $foto['nombre'];
}