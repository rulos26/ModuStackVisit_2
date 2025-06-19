<?php

include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$row_concepto_final = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'actitud' => 'No disponible',
    'condiciones_vivienda' => 'No disponible',
    'dinamica_familiar' => 'No disponible',
    'condiciones_economicas' => 'No disponible',
    'condiciones_academicas' => 'No disponible',
    'evaluacion_experiencia_laboral' => 'No disponible',
    'observaciones' => '',
    'id_concepto_final' => '',
    'nombre_evaluador' => 'No disponible',
    'id_concepto_seguridad' => '',
    'estado_nombre' => 'No disponible'
];

$concepto_final="SELECT c.id, c.id_cedula, c.actitud, c.condiciones_vivienda, c.dinamica_familiar, c.condiciones_economicas, 
c.condiciones_academicas, c.evaluacion_experiencia_laboral, c.observaciones, c.id_concepto_final, 
c.nombre_evaluador, c.id_concepto_seguridad, e.nombre AS estado_nombre
FROM concepto_final_evaluador AS c
LEFT JOIN opc_estados AS e ON c.id_concepto_final = e.id
WHERE c.id_cedula = '$id_cedula';";

$concepto_final_data = $mysqli->query($concepto_final);

if ($concepto_final_data && $concepto_final_data->num_rows > 0) {
    $temp_concepto = $concepto_final_data->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $row_concepto_final = array_merge($row_concepto_final, $temp_concepto);
    
    // Asegurar que no haya valores nulos
    foreach ($row_concepto_final as $key => $value) {
        if ($value === null || $value === '') {
            $row_concepto_final[$key] = 'No disponible';
        }
    }
}