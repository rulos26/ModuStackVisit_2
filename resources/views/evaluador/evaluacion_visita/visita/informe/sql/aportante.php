<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de aportantes
$aportante_data_array = [];

$aportante="SELECT `id`, `id_cedula`, `nombre`, `valor` FROM `aportante` WHERE  `id_cedula`='$id_cedula';";
$data_apor = $mysqli->query($aportante);

if ($data_apor && $data_apor->num_rows > 0) {
    while ($aportante_row = $data_apor->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($aportante_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $aportante_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $aportante_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'nombre' => 'No disponible',
        'valor' => 'No disponible'
    ];
}