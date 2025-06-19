<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
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

if ($data_servicios->num_rows > 0) {
    $filas_servicios = $data_servicios->fetch_assoc();
    //var_dump($filas_servicios );
} else {
    echo "No se encontraron registros.";
}