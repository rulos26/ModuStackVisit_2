<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de pasivos
$pasivos_data_array = [];

$pasivo="SELECT 
p.item, 
p.id_entidad, 
p.id_tipo_inversion, 
p.id_ciudad, 
p.deuda, 
p.cuota_mes,
m.id_municipio, 
m.municipio
FROM 
pasivos p
JOIN 
municipios m ON p.id_ciudad = m.id_municipio
WHERE 
p.id_cedula = '$id_cedula';";

$data_pasivo= $mysqli->query($pasivo);

if ($data_pasivo && $data_pasivo->num_rows > 0) {
    while ($pasivo_row = $data_pasivo->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($pasivo_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $pasivos_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $pasivos_data_array[] = [
        'item' => 'No disponible',
        'id_entidad' => '',
        'id_tipo_inversion' => '',
        'id_ciudad' => '',
        'deuda' => 'No disponible',
        'cuota_mes' => 'No disponible',
        'id_municipio' => '',
        'municipio' => 'No disponible'
    ];
}
