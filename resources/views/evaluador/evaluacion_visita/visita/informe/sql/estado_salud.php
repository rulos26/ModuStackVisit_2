<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$fila_salud = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'nombre_estado_salud' => 'No disponible',
    'nombre_tipo_enfermedad' => 'No disponible',
    'tipo_enfermedad_cual' => 'No disponible',
    'nombre_limitacion_fisica' => 'No disponible',
    'limitacion_fisica_cual' => 'No disponible',
    'nombre_tipo_medicamento' => 'No disponible',
    'tipo_medicamento_cual' => 'No disponible',
    'nombre_ingiere_alcohol' => 'No disponible',
    'ingiere_alcohol_cual' => 'No disponible',
    'nombre_fuma' => 'No disponible',
    'observacion' => ''
];

$salud =  "SELECT e.id, e.id_cedula, oe1.nombre AS nombre_estado_salud, oe2.nombre AS nombre_tipo_enfermedad,
e.tipo_enfermedad_cual, oe3.nombre AS nombre_limitacion_fisica, e.limitacion_fisica_cual,
oe4.nombre AS nombre_tipo_medicamento, e.tipo_medicamento_cual, oe5.nombre AS nombre_ingiere_alcohol,
e.ingiere_alcohol_cual, oe6.nombre AS nombre_fuma, e.observacion
FROM estados_salud e
LEFT JOIN opc_estados oe1 ON e.id_estado_salud = oe1.id
LEFT JOIN opc_parametro oe2 ON e.tipo_enfermedad = oe2.id
LEFT JOIN opc_parametro oe3 ON e.limitacion_fisica = oe3.id
LEFT JOIN opc_parametro oe4 ON e.tipo_medicamento = oe4.id
LEFT JOIN opc_parametro oe5 ON e.ingiere_alcohol = oe5.id
LEFT JOIN opc_parametro oe6 ON e.fuma = oe6.id
WHERE e.id_cedula = '$id_cedula'";

$salud_data = $mysqli->query($salud);

if ($salud_data && $salud_data->num_rows > 0) {
    $temp_salud = $salud_data->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $fila_salud = array_merge($fila_salud, $temp_salud);
    
    // Asegurar que no haya valores nulos
    foreach ($fila_salud as $key => $value) {
        if ($value === null || $value === '') {
            $fila_salud[$key] = 'No disponible';
        }
    }
} else {
    echo "No se encontraron registros.";
}