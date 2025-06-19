<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar arrays con valores por defecto
$filas_vivienda = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'id_tipo_vivienda' => '',
    'id_sector' => '',
    'id_propietario' => '',
    'numero_de_familia' => 'No disponible',
    'personas_nucleo_familiar' => 'No disponible',
    'tiempo_sector' => 'No disponible',
    'numero_de_pisos' => 'No disponible',
    'nombre_tipo_vivienda' => 'No disponible',
    'nombre_sector' => 'No disponible',
    'nombre_propiedad' => 'No disponible'
];

$row_viv = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'id_estado' => '',
    'nombre' => 'No disponible'
];

$vivienda = "SELECT 
tv.id,tv.id_cedula,tv.id_tipo_vivienda,tv.id_sector,tv.id_propietario,tv.numero_de_familia, 
tv.personas_nucleo_familiar,tv.tiempo_sector,tv.numero_de_pisos,
otv.nombre AS nombre_tipo_vivienda,
os.nombre AS nombre_sector,
op.nombre AS nombre_propiedad
FROM 
tipo_vivienda AS tv
JOIN 
opc_tipo_vivienda AS otv ON tv.id_tipo_vivienda = otv.id
JOIN 
opc_sector AS os ON tv.id_sector = os.id
JOIN 
opc_propiedad AS op ON tv.id_propietario = op.id
WHERE 
tv.id_cedula = '$id_cedula';";

$data_vivienda = $mysqli->query($vivienda);

if ($data_vivienda && $data_vivienda->num_rows > 0) {
    $temp_vivienda = $data_vivienda->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $filas_vivienda = array_merge($filas_vivienda, $temp_vivienda);
    
    // Asegurar que no haya valores nulos
    foreach ($filas_vivienda as $key => $value) {
        if ($value === null || $value === '') {
            $filas_vivienda[$key] = 'No disponible';
        }
    }
}

$estado_vivienda = "SELECT ev.id, ev.id_cedula, ev.id_estado, op.nombre
        FROM estado_vivienda AS ev
        JOIN opc_estados AS op ON ev.id_estado = op.id
        WHERE ev.id_cedula = '$id_cedula'";

$data_vivi = $mysqli->query($estado_vivienda);

if ($data_vivi && $data_vivi->num_rows > 0) {
    $temp_viv = $data_vivi->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $row_viv = array_merge($row_viv, $temp_viv);
    
    // Asegurar que no haya valores nulos
    foreach ($row_viv as $key => $value) {
        if ($value === null || $value === '') {
            $row_viv[$key] = 'No disponible';
        }
    }
}
