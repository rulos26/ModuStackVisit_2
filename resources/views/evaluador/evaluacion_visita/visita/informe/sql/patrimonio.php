<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$filas_patrimonio = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'valor_vivienda' => 'No disponible',
    'direccion' => 'No disponible',
    'id_vehiculo' => '',
    'id_marca' => '',
    'id_modelo' => '',
    'id_ahorro' => '',
    'otros' => 'No disponible',
    'observacion' => ''
];

$patrimonio="SELECT id,id_cedula,valor_vivienda,direccion,
id_vehiculo,id_marca,id_modelo,id_ahorro,otros,observacion 
FROM patrimonio WHERE id_cedula='$id_cedula ';";
$dat_patrimonio = $mysqli->query($patrimonio);

if ($dat_patrimonio && $dat_patrimonio->num_rows > 0) {
    $temp_patrimonio = $dat_patrimonio->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $filas_patrimonio = array_merge($filas_patrimonio, $temp_patrimonio);
    
    // Asegurar que no haya valores nulos
    foreach ($filas_patrimonio as $key => $value) {
        if ($value === null || $value === '') {
            $filas_patrimonio[$key] = 'No disponible';
        }
    }
} else {
    echo "No se encontraron registros.";
}