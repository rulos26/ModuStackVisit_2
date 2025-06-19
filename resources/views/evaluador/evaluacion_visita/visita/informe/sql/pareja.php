<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$pareja_data_array = [];

$pareja = "SELECT IP.id, IP.id_cedula,IP.cedula, IP.id_tipo_documentos, 
IP.cedula_expedida, IP.nombres, IP.edad, IP.id_genero,
IP.id_nivel_academico, IP.actividad, IP.empresa, IP.antiguedad, 
IP.direccion_empresa, IP.telefono_1, IP.telefono_2, 
IP.vive_candidato, 
TD.nombre AS tipo_documento_nombre,
G.id AS id_genero_pareja, 
G.nombre AS nombre_genero, 
NA.id AS id_nivel_academico_pareja, 
NA.nombre AS nombre_nivel_academico 
FROM informacion_pareja AS IP
LEFT JOIN opc_tipo_documentos AS TD ON IP.id_tipo_documentos = TD.id 
LEFT JOIN opc_genero AS G ON IP.id_genero = G.id 
LEFT JOIN opc_nivel_academico AS NA ON IP.id_nivel_academico = NA.id
WHERE IP.id_cedula = '$id_cedula';";

$pareja_data = $mysqli->query($pareja);

if ($pareja_data && $pareja_data->num_rows > 0) {
    while ($pareja_row = $pareja_data->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($pareja_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $pareja_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $pareja_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'cedula' => 'No disponible',
        'id_tipo_documentos' => '',
        'cedula_expedida' => '',
        'nombres' => 'No disponible',
        'edad' => 'No disponible',
        'id_genero' => '',
        'id_nivel_academico' => '',
        'actividad' => 'No disponible',
        'empresa' => 'No disponible',
        'antiguedad' => 'No disponible',
        'direccion_empresa' => 'No disponible',
        'telefono_1' => 'No disponible',
        'telefono_2' => 'No disponible',
        'vive_candidato' => 'No disponible',
        'tipo_documento_nombre' => 'No disponible',
        'id_genero_pareja' => '',
        'nombre_genero' => 'No disponible',
        'id_nivel_academico_pareja' => '',
        'nombre_nivel_academico' => 'No disponible'
    ];
}
