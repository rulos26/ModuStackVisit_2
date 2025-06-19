<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de estudios
$estudios_data_array = [];

$estudios="SELECT e.id, e.id_cedula, e.centro_estudios, e.id_jornada, e.id_ciudad, e.anno, e.titulos, e.id_resultado,
m.id_municipio, m.municipio
FROM estudios e
JOIN municipios m ON e.id_ciudad = m.id_municipio
WHERE e.id_cedula = '$id_cedula';";

$estudios_data = $mysqli->query($estudios);

if ($estudios_data && $estudios_data->num_rows > 0) {
    while ($estudios_row = $estudios_data->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($estudios_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $estudios_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $estudios_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'centro_estudios' => 'No disponible',
        'id_jornada' => '',
        'id_ciudad' => '',
        'anno' => 'No disponible',
        'titulos' => 'No disponible',
        'id_resultado' => '',
        'id_municipio' => '',
        'municipio' => 'No disponible'
    ];
}