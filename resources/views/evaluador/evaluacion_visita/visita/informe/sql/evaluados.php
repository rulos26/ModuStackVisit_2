<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$evaluado = "SELECT 
e.id,e.id_cedula,e.id_tipo_documentos, e.cedula_expedida, e.nombres, e.apellidos, 
e.edad, e.fecha_expedicion, e.lugar_nacimiento, e.celular_1, e.celular_2, e.telefono, 
e.id_rh, e.id_estatura, e.peso_kg, e.id_estado_civil,e.hacer_cuanto, e.numero_hijos, e.direccion, 
e.id_ciudad, e.localidad, e.barrio, e.id_estrato, e.correo, e.cargo,e.observacion,
td.nombre AS tipo_documento_nombre,
m1.municipio AS lugar_nacimiento_municipio,
m2.municipio AS ciudad_nombre,
rh.nombre AS rh_nombre,
est.nombre AS estatura_nombre,
ec.nombre AS estado_civil_nombre,
es.nombre AS estrato_nombre
FROM evaluados e
LEFT JOIN 
opc_tipo_documentos td ON e.id_tipo_documentos = td.id
LEFT JOIN 
municipios m1 ON e.lugar_nacimiento = m1.id_municipio
LEFT JOIN 
municipios m2 ON e.id_ciudad = m2.id_municipio
LEFT JOIN 
opc_rh rh ON e.id_rh = rh.id
LEFT JOIN 
opc_estaturas est ON e.id_estatura = est.id
LEFT JOIN 
opc_estado_civiles ec ON e.id_estado_civil = ec.id
LEFT JOIN 
opc_estratos es ON e.id_estrato = es.id
WHERE 
e.id_cedula = '$id_cedula';";
$data_evaluados = $mysqli->query($evaluado);
if ($data_evaluados->num_rows > 0) {
    $row = $data_evaluados->fetch_assoc();
}