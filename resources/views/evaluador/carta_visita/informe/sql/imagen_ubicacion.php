<?php
//session_start();
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto perfil
$sql = "SELECT * FROM ubicacion_autorizacion where id_cedula= $id_cedula";
$foto_ubi = $mysqli->query($sql);

if ($foto_ubi->num_rows > 0) {
    $foto_ubi = $foto_ubi->fetch_assoc();
    // Imprimir los datos del registro
    $ruta_imagen_ubi = $foto_ubi['ruta'] . $foto_ubi['nombre'];
    $nombre_imagen = $foto_ubi['nombre'];
    //var_dump($ruta_imagen_ubi);
}