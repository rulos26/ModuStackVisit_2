<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$invetario="SELECT 
ie.televisor_cant, ie.dvd_cant, ie.teatro_casa_cant, ie.equipo_sonido_cant, 
ie.computador_cant, ie.impresora_cant, ie.movil_cant,ie.estufa_cant,  ie.nevera_cant, 
ie.lavadora_cant, ie.microondas_cant, ie.moto_cant, ie.carro_cant, ie.observacion,
oe1.nombre AS televisor_nombre_cant,
oe2.nombre AS dvd_nombre_cant,
oe3.nombre AS teatro_casa_nombre_cant,
oe4.nombre AS equipo_sonido_nombre_cant,
oe5.nombre AS computador_nombre_cant,
oe6.nombre AS impresora_nombre_cant,
oe7.nombre AS movil_nombre_cant,
oe8.nombre AS estufa_nombre_cant,
oe9.nombre AS nevera_nombre_cant,
oe10.nombre AS lavadora_nombre_cant,
oe11.nombre AS microondas_nombre_cant,
oe12.nombre AS moto_nombre_cant,
oe13.nombre AS carro_nombre_cant
FROM 
inventario_enseres ie
LEFT JOIN 
opc_parametro oe1 ON ie.televisor_cant = oe1.id
LEFT JOIN 
opc_parametro oe2 ON ie.dvd_cant = oe2.id
LEFT JOIN 
opc_parametro oe3 ON ie.teatro_casa_cant = oe3.id
LEFT JOIN 
opc_parametro oe4 ON ie.equipo_sonido_cant = oe4.id
LEFT JOIN 
opc_parametro oe5 ON ie.computador_cant = oe5.id
LEFT JOIN 
opc_parametro oe6 ON ie.impresora_cant = oe6.id
LEFT JOIN 
opc_parametro oe7 ON ie.movil_cant = oe7.id
LEFT JOIN 
opc_parametro oe8 ON ie.estufa_cant = oe8.id
LEFT JOIN 
opc_parametro oe9 ON ie.nevera_cant = oe9.id
LEFT JOIN 
opc_parametro oe10 ON ie.lavadora_cant = oe10.id
LEFT JOIN 
opc_parametro oe11 ON ie.microondas_cant = oe11.id
LEFT JOIN 
opc_parametro oe12 ON ie.moto_cant = oe12.id
LEFT JOIN 
opc_parametro oe13 ON ie.carro_cant = oe13.id
WHERE 
ie.id_cedula = '$id_cedula'";

$data_invetario = $mysqli->query($invetario);

if ($data_invetario->num_rows > 0) {
    $filas_invetario = $data_invetario->fetch_assoc();
    //var_dump($filas_invetario);
} else {
    echo "No se encontraron registros.";
}
