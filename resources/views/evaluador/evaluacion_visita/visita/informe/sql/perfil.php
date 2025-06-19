<?php
//session_start();
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto perfil
$sql = "SELECT * FROM foto_perfil_visita where id_cedula= $id_cedula";
$foto = $mysqli->query($sql);
if ($foto->num_rows > 0) {
    $foto = $foto->fetch_assoc();
    // Imprimir los datos del registro
    $ruta_imagen = $foto['ruta'] . $foto['nombre'];
    $nombre_imagen = $foto['nombre'];
}