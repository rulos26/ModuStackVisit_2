<?php

include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar variables con valores por defecto
$foto1 = '';
$foto2 = '';
$foto3 = '';
$foto4 = '';
$foto5 = '';
$foto6 = '';
$foto7 = '';
$foto8 = '';

$sql1 = "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 1";
$foto1_data = $mysqli->query($sql1);

$sql2= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 2";
$foto2_data = $mysqli->query($sql2);

$sql3= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 3";
$foto3_data = $mysqli->query($sql3);

$sql4= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 4";
$foto4_data = $mysqli->query($sql4);

$sql5= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 5";
$foto5_data = $mysqli->query($sql5);

$sql6= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 6";
$foto6_data = $mysqli->query($sql6);

$sql7= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 7";
$foto7_data = $mysqli->query($sql7);

$sql8= "SELECT * FROM `evidencia_fotografica` where id_cedula= $id_cedula and tipo = 8";
$foto8_data = $mysqli->query($sql8);

if ($foto1_data && $foto1_data->num_rows > 0) {
    $foto1_row = $foto1_data->fetch_assoc();
    if (isset($foto1_row['ruta']) && isset($foto1_row['nombre']) && 
        !empty($foto1_row['ruta']) && !empty($foto1_row['nombre'])) {
        $foto1 = $foto1_row['ruta'] . $foto1_row['nombre'];
    }
}

if ($foto2_data && $foto2_data->num_rows > 0) {
    $foto2_row = $foto2_data->fetch_assoc();
    if (isset($foto2_row['ruta']) && isset($foto2_row['nombre']) && 
        !empty($foto2_row['ruta']) && !empty($foto2_row['nombre'])) {
        $foto2 = $foto2_row['ruta'] . $foto2_row['nombre'];
    }
}

if ($foto3_data && $foto3_data->num_rows > 0) {
    $foto3_row = $foto3_data->fetch_assoc();
    if (isset($foto3_row['ruta']) && isset($foto3_row['nombre']) && 
        !empty($foto3_row['ruta']) && !empty($foto3_row['nombre'])) {
        $foto3 = $foto3_row['ruta'] . $foto3_row['nombre'];
    }
}

if ($foto4_data && $foto4_data->num_rows > 0) {
    $foto4_row = $foto4_data->fetch_assoc();
    if (isset($foto4_row['ruta']) && isset($foto4_row['nombre']) && 
        !empty($foto4_row['ruta']) && !empty($foto4_row['nombre'])) {
        $foto4 = $foto4_row['ruta'] . $foto4_row['nombre'];
    }
}

if ($foto5_data && $foto5_data->num_rows > 0) {
    $foto5_row = $foto5_data->fetch_assoc();
    if (isset($foto5_row['ruta']) && isset($foto5_row['nombre']) && 
        !empty($foto5_row['ruta']) && !empty($foto5_row['nombre'])) {
        $foto5 = $foto5_row['ruta'] . $foto5_row['nombre'];
    }
}

if ($foto6_data && $foto6_data->num_rows > 0) {
    $foto6_row = $foto6_data->fetch_assoc();
    if (isset($foto6_row['ruta']) && isset($foto6_row['nombre']) && 
        !empty($foto6_row['ruta']) && !empty($foto6_row['nombre'])) {
        $foto6 = $foto6_row['ruta'] . $foto6_row['nombre'];
    }
}

if ($foto7_data && $foto7_data->num_rows > 0) {
    $foto7_row = $foto7_data->fetch_assoc();
    if (isset($foto7_row['ruta']) && isset($foto7_row['nombre']) && 
        !empty($foto7_row['ruta']) && !empty($foto7_row['nombre'])) {
        $foto7 = $foto7_row['ruta'] . $foto7_row['nombre'];
    }
}

if ($foto8_data && $foto8_data->num_rows > 0) {
    $foto8_row = $foto8_data->fetch_assoc();
    if (isset($foto8_row['ruta']) && isset($foto8_row['nombre']) && 
        !empty($foto8_row['ruta']) && !empty($foto8_row['nombre'])) {
        $foto8 = $foto8_row['ruta'] . $foto8_row['nombre'];
    }
}
?>