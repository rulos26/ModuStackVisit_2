<?php
include '../../../../../conn/conexion.php';
$id_cedula = $_SESSION['id_cedula'];
$patrimonio="SELECT id,id_cedula,valor_vivienda,direccion,
id_vehiculo,id_marca,id_modelo,id_ahorro,otros,observacion 
FROM patrimonio WHERE id_cedula='$id_cedula ';";
$dat_patrimonio = $mysqli->query($patrimonio);

if ($dat_patrimonio->num_rows > 0) {
    $filas_patrimonio = $dat_patrimonio->fetch_assoc();
    //var_dump($filas_servicios );
} else {
    echo "No se encontraron registros.";
}