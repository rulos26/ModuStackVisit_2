<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_cedula =  $_SESSION['id_cedula'];
    $id_tipo_vivienda = $_POST['id_tipo_vivienda'];
    $id_sector = $_POST['id_sector'];
    $id_propietario = $_POST['id_propietario'];
    $numero_de_familia = $_POST['numero_de_familia'];
    $personas_nucleo_familiar = $_POST['personas_nucleo_familiar'];
    $tiempo_sector = $_POST['tiempo_sector'];
    $numero_de_pisos = $_POST['numero_de_pisos'];
    $observacion=$_POST['observacion'];
    ////$_SESSION['id_cedula']=$id_cedula;
    
    // Preparar la consulta SQL para insertar los datos en la base de datos
    $sql = "INSERT INTO tipo_vivienda (id_cedula, id_tipo_vivienda, id_sector, id_propietario, numero_de_familia, personas_nucleo_familiar, tiempo_sector, numero_de_pisos,observacion) 
            VALUES ('$id_cedula', '$id_tipo_vivienda', '$id_sector', '$id_propietario', '$numero_de_familia', '$personas_nucleo_familiar', '$tiempo_sector', '$numero_de_pisos','$observacion')";

    // Ejecutar la consulta
    if ($mysqli->query($sql) === TRUE) {
        // Redirigir al usuario a una página de éxito
        header("Location: ../estado_vivienda/estado_vivienda.php");
        exit();
    } else {
        echo "Error al guardar los datos: " . $mysqli->error;
    }

    // Cerrar la conexión
    $mysqli->close();
} else {
    echo "Acceso denegado.";
}
?>
