<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto perfil
$sql = "SELECT * FROM foto_perfil_autorizacion where id_cedula= $id_cedula";
$foto = $mysqli->query($sql);
if ($foto->num_rows > 0) {
    $foto_data = $foto->fetch_assoc();
    // Imprimir los datos del registro
    $ruta_imagen = $foto_data['ruta'] . $foto_data['nombre'];
    $nombre_imagen = $foto_data['nombre'];
}