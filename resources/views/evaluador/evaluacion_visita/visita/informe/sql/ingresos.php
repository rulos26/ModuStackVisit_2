<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$ingresos_row = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'ingreso_principal' => 'No disponible',
    'ingreso_secundario' => 'No disponible',
    'otros_ingresos' => 'No disponible',
    'total_ingresos' => 'No disponible',
    'observacion' => ''
];

$ingresos="SELECT * FROM `ingresos_mensuales` WHERE   `id_cedula`='$id_cedula';";
$ingresos_data = $mysqli->query($ingresos);

if ($ingresos_data && $ingresos_data->num_rows > 0) {
    $temp_ingresos = $ingresos_data->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $ingresos_row = array_merge($ingresos_row, $temp_ingresos);
    
    // Asegurar que no haya valores nulos
    foreach ($ingresos_row as $key => $value) {
        if ($value === null || $value === '') {
            $ingresos_row[$key] = 'No disponible';
        }
    }
}