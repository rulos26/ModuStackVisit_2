<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$judi_row = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'denuncias_opc' => '',
    'denuncias_desc' => 'No disponible',
    'procesos_judiciales_opc' => '',
    'procesos_judiciales_desc' => 'No disponible',
    'preso_opc' => '',
    'preso_desc' => 'No disponible',
    'familia_detenido_opc' => '',
    'familia_detenido_desc' => 'No disponible',
    'centros_penitenciarios_opc' => '',
    'centros_penitenciarios_desc' => 'No disponible',
    'revi_fiscal' => 'No disponible',
    'nombre_opcion1' => 'No disponible',
    'nombre_opcion2' => 'No disponible',
    'nombre_opcion3' => 'No disponible',
    'nombre_opcion4' => 'No disponible',
    'nombre_opcion5' => 'No disponible'
];

$info_judi="SELECT
ij.id,
ij.id_cedula,
ij.denuncias_opc,
ij.denuncias_desc,
ij.procesos_judiciales_opc,
ij.procesos_judiciales_desc,
ij.preso_opc,
ij.preso_desc,
ij.familia_detenido_opc,
ij.familia_detenido_desc,
ij.centros_penitenciarios_opc,
ij.centros_penitenciarios_desc,
ij.revi_fiscal,
op1.nombre AS nombre_opcion1,
op2.nombre AS nombre_opcion2,
op3.nombre AS nombre_opcion3,
op4.nombre AS nombre_opcion4,
op5.nombre AS nombre_opcion5
FROM
informacion_judicial AS ij
LEFT JOIN opc_parametro AS op1 ON ij.denuncias_opc = op1.id
LEFT JOIN opc_parametro AS op2 ON ij.procesos_judiciales_opc = op2.id
LEFT JOIN opc_parametro AS op3 ON ij.preso_opc = op3.id
LEFT JOIN opc_parametro AS op4 ON ij.familia_detenido_opc = op4.id
LEFT JOIN opc_parametro AS op5 ON ij.centros_penitenciarios_opc = op5.id
WHERE ij.id_cedula = '$id_cedula';";

$info_judi_data = $mysqli->query($info_judi);

if ($info_judi_data && $info_judi_data->num_rows > 0) {
    $temp_judi = $info_judi_data->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $judi_row = array_merge($judi_row, $temp_judi);
    
    // Asegurar que no haya valores nulos
    foreach ($judi_row as $key => $value) {
        if ($value === null || $value === '') {
            $judi_row[$key] = 'No disponible';
        }
    }
}