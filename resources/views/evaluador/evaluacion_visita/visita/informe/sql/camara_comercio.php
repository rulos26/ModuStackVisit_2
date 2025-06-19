<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
//consulta foto perfil
$sql = "SELECT * FROM camara_comercio where id_cedula= $id_cedula";
$data_comercio = $mysqli->query($sql);
if ($data_comercio->num_rows > 0) {
    $data_row = $data_comercio->fetch_assoc();
    // Imprimir los datos del registro
    //var_dump($data_row);
    
}