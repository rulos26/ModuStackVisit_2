<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de familia
$familia_data_array = [];

$familia = "SELECT cf.id, cf.id_cedula, cf.nombre, cf.id_parentesco, cf.edad, cf.id_ocupacion, cf.telefono, cf.id_conviven,cf.observacion,
op.nombre AS nombre_parentesco,
oo.nombre AS nombre_ocupacion,
opa.nombre AS nombre_parametro 
FROM composicion_familiar cf
LEFT JOIN opc_parentesco op ON cf.id_parentesco = op.id
LEFT JOIN opc_ocupacion oo ON cf.id_ocupacion = oo.id
LEFT JOIN opc_parametro opa ON cf.id_conviven = opa.id
WHERE cf.id_cedula = '$id_cedula';";

$familia_data = $mysqli->query($familia);

if ($familia_data && $familia_data->num_rows > 0) {
    while ($familia_row = $familia_data->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($familia_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $familia_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $familia_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'nombre' => 'No disponible',
        'id_parentesco' => '',
        'edad' => 'No disponible',
        'id_ocupacion' => '',
        'telefono' => 'No disponible',
        'id_conviven' => '',
        'observacion' => '',
        'nombre_parentesco' => 'No disponible',
        'nombre_ocupacion' => 'No disponible',
        'nombre_parametro' => 'No disponible'
    ];
}

/* while ($familia_row = $familia_data->fetch_assoc()) {
    echo "<tr>";
    echo  $familia_row['id'] . "</td>";
    echo  $familia_row['id_cedula'] . "</td>";
    echo  $familia_row['nombre'] . "</td>";
    echo  $familia_row['nombre_parentesco'] . "</td>";
    echo  $familia_row['edad'] . "</td>";
    echo  $familia_row['nombre_ocupacion'] . "</td>";
    echo  $familia_row['telefono'] . "</td>";
    echo  $familia_row['nombre_parametro'] . "</td>";
    echo "</tr>";
} */
