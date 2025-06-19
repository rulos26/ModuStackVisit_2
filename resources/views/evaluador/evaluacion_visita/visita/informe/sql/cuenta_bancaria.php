<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
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
if ($cuenta_data ->num_rows > 0) {
    
} else {
    
}