<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$gastos_row = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'gasto_alimentacion' => 'No disponible',
    'gasto_transporte' => 'No disponible',
    'gasto_servicios' => 'No disponible',
    'gasto_educacion' => 'No disponible',
    'gasto_salud' => 'No disponible',
    'gasto_recreacion' => 'No disponible',
    'otros_gastos' => 'No disponible',
    'total_gastos' => 'No disponible',
    'observacion' => ''
];

$gastos="SELECT * FROM `gasto` WHERE   `id_cedula`='$id_cedula';";
$gastos_data = $mysqli->query($gastos);

if ($gastos_data && $gastos_data->num_rows > 0) {
    $temp_gastos = $gastos_data->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $gastos_row = array_merge($gastos_row, $temp_gastos);
    
    // Asegurar que no haya valores nulos
    foreach ($gastos_row as $key => $value) {
        if ($value === null || $value === '') {
            $gastos_row[$key] = 'No disponible';
        }
    }
}