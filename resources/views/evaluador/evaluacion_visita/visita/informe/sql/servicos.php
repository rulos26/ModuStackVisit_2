<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar array con valores por defecto
$filas_servicios = [
    'id' => '',
    'id_cedula' => $id_cedula,
    'agua' => '',
    'luz' => '',
    'gas' => '',
    'telefono' => '',
    'alcantarillado' => '',
    'internet' => '',
    'administracion' => '',
    'parqueadero' => '',
    'observacion' => '',
    'nombre_agua' => 'No disponible',
    'nombre_luz' => 'No disponible',
    'nombre_gas' => 'No disponible',
    'nombre_telefono' => 'No disponible',
    'nombre_alcantarillado' => 'No disponible',
    'nombre_internet' => 'No disponible',
    'nombre_administracion' => 'No disponible',
    'nombre_parqueadero' => 'No disponible'
];

$servicios="SELECT sp.agua, sp.luz, sp.gas, sp.telefono, sp.alcantarillado, sp.internet, sp.administracion, sp.parqueadero,sp.observacion,
op1.nombre AS nombre_agua, op2.nombre AS nombre_luz, op3.nombre AS nombre_gas, op4.nombre AS nombre_telefono,
op5.nombre AS nombre_alcantarillado, op6.nombre AS nombre_internet, op7.nombre AS nombre_administracion,
op8.nombre AS nombre_parqueadero
FROM servicios_publicos sp
JOIN opc_parametro op1 ON sp.agua = op1.id
JOIN opc_parametro op2 ON sp.luz = op2.id
JOIN opc_parametro op3 ON sp.gas = op3.id
JOIN opc_parametro op4 ON sp.telefono = op4.id
JOIN opc_parametro op5 ON sp.alcantarillado = op5.id
JOIN opc_parametro op6 ON sp.internet = op6.id
JOIN opc_parametro op7 ON sp.administracion = op7.id
JOIN opc_parametro op8 ON sp.parqueadero = op8.id
WHERE sp.id_cedula = '$id_cedula';
";

$data_servicios = $mysqli->query($servicios);

if ($data_servicios && $data_servicios->num_rows > 0) {
    $temp_servicios = $data_servicios->fetch_assoc();
    // Combinar datos de la base de datos con valores por defecto
    $filas_servicios = array_merge($filas_servicios, $temp_servicios);
    
    // Asegurar que no haya valores nulos
    foreach ($filas_servicios as $key => $value) {
        if ($value === null || $value === '') {
            $filas_servicios[$key] = 'No disponible';
        }
    }
} else {
    echo "No se encontraron registros.";
}