<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array para almacenar datos de cuentas bancarias
$cuenta_data_array = [];

$cuenta="SELECT 
cb.id, 
cb.id_cedula, 
cb.id_entidad, 
cb.id_tipo_cuenta, 
cb.id_ciudad, 
m.municipio AS ciudad,
cb.observaciones
FROM 
cuentas_bancarias AS cb
JOIN 
municipios AS m ON cb.id_ciudad = m.id_municipio
WHERE  
cb.id_cedula = '$id_cedula';";

$cuenta_data = $mysqli->query($cuenta);

if ($cuenta_data && $cuenta_data->num_rows > 0) {
    while ($cuenta_row = $cuenta_data->fetch_assoc()) {
        // Validar y limpiar datos antes de agregarlos al array
        $clean_row = [];
        foreach ($cuenta_row as $key => $value) {
            $clean_row[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        $cuenta_data_array[] = $clean_row;
    }
} else {
    // Si no hay datos, crear un registro vacío para evitar warnings
    $cuenta_data_array[] = [
        'id' => '',
        'id_cedula' => $id_cedula,
        'id_entidad' => '',
        'id_tipo_cuenta' => '',
        'id_ciudad' => '',
        'ciudad' => 'No disponible',
        'observaciones' => ''
    ];
}