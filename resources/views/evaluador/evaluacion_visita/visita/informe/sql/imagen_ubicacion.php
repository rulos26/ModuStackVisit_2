<?php
//session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto ubicación
$sql = "SELECT * FROM ubicacion_autorizacion where id_cedula= $id_cedula";
$foto_ubi = $mysqli->query($sql);
if ($foto_ubi->num_rows > 0) {
    $foto_ubi_data = $foto_ubi->fetch_assoc();
    // Imprimir los datos del registro
    $ruta_imagen_ubi = $foto_ubi_data['ruta'] . $foto_ubi_data['nombre'];
    $nombre_imagen_ubi = $foto_ubi_data['nombre'];
}