<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
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
if ($pareja_data ->num_rows > 0) {
    
} else {
    
}
