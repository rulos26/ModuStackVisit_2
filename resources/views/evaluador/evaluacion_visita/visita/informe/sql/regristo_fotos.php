<?php

include '../../../../../conn/conexion.php';

$id_cedula = $_SESSION['id_cedula'];
$sql1 = "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 1";
$foto1 = $mysqli->query($sql1);
$sql2= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 2";
$foto2 = $mysqli->query($sql2);
$sql3= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 3";
$foto3 = $mysqli->query($sql3);
$sql4= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 4";
$foto4 = $mysqli->query($sql4);
$sql5= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 5";
$foto5 = $mysqli->query($sql5);
$sql6= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 6";
$foto6 = $mysqli->query($sql6);
$sql7= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 7";
$foto7 = $mysqli->query($sql7);
$sql8= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 8";
$foto8 = $mysqli->query($sql8);

if ($foto1 ->num_rows > 0) {
    $foto1  = $foto1 ->fetch_assoc();
    // Imprimir los datos del registro
    $foto1  = $foto1 ['ruta'] . $foto1 ['nombre'];
    
}

if ($foto2->num_rows > 0) {
    $foto2 = $foto2->fetch_assoc();
    // Imprimir los datos del registro
    $foto2 = $foto2['ruta'] . $foto2['nombre'];
   
}

if ($foto3->num_rows > 0) {
    $foto3 = $foto3->fetch_assoc();
    // Imprimir los datos del registro
    $foto3 = $foto3['ruta'] . $foto3['nombre'];
    
}

if ($foto4->num_rows > 0) {
    $foto4 = $foto4->fetch_assoc();
    // Imprimir los datos del registro
    $foto4 = $foto4['ruta'] . $foto4['nombre'];
   
}

if ($foto5->num_rows > 0) {
    $foto5 = $foto5->fetch_assoc();
    // Imprimir los datos del registro
    $foto5 = $foto5['ruta'] . $foto5['nombre'];
    
}

if ($foto6->num_rows > 0) {
    $foto6 = $foto6->fetch_assoc();
    // Imprimir los datos del registro
    $foto6 = $foto6['ruta'] . $foto6['nombre'];
    
}
if ($foto7->num_rows > 0) {
    $foto7 = $foto7->fetch_assoc();
    // Imprimir los datos del registro
    $foto7 = $foto7['ruta'] . $foto7['nombre'];
   
}
if ($foto8->num_rows > 0) {
    $foto8 = $foto8->fetch_assoc();
    // Imprimir los datos del registro
    $foto8 = $foto8['ruta'] . $foto8['nombre'];
    
}
