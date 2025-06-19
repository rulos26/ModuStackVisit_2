<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de experiencia laboral
$exp_laboral_data_array = [];

$exp_laboral="SELECT `id`, `id_cedula`, `empresa`, `tiempo`, `cargo`, `salario`, `retiro`, `concepto`,
`nombre`, `numero` FROM `experiencia_laboral` WHERE `id_cedula`='$id_cedula';";

$exp_laboral_data = $mysqli->query($exp_laboral);

if ($exp_laboral_data && $exp_laboral_data->num_rows > 0) {
    while ($exp_laboral_row = $exp_laboral_data->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($exp_laboral_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $exp_laboral_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $exp_laboral_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'empresa' => 'No disponible',
        'tiempo' => 'No disponible',
        'cargo' => 'No disponible',
        'salario' => 'No disponible',
        'retiro' => 'No disponible',
        'concepto' => 'No disponible',
        'nombre' => 'No disponible',
        'numero' => 'No disponible'
    ];
}