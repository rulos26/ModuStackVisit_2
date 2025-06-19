<?php
// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$data_row = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'tiene_camara' => 'No disponible',
    'nombre' => 'No disponible',
    'razon' => 'No disponible',
    'activdad' => 'No disponible',
    'observacion' => ''
];

//consulta cámara de comercio
$sql = "SELECT * FROM camara_comercio where id_cedula= $id_cedula";
$data_comercio = $mysqli->query($sql);

if ($data_comercio && $data_comercio->num_rows > 0) {
    $temp_data = $data_comercio->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $data_row = array_merge($data_row, $temp_data);
    
    // Asegurar que no haya valores nulos
    foreach ($data_row as $key => $value) {
        if ($value === null || $value === '') {
            $data_row[$key] = 'No disponible';
        }
    }
}