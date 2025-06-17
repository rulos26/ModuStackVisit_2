<?php
session_start();
include '../../../../../conn/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se ha enviado la imagen correctamente
    
    $direccion = $_POST['direccion'];
    $barrio = $_POST['barrio'];
    $localidad = $_POST['localidad'];
    $telefono = $_POST['telefono'];
    $celular_1 = $_POST['celular_1'];
    $correo = $_POST['correo'];
    $cedula = $_SESSION['id_cedula'];
    
    $sql = "UPDATE autorizaciones SET 
    direccion='$direccion', localidad='$localidad', barrio='$barrio',
    telefono='$telefono ', celular='$celular_1,', correo='$correo' WHERE cedula= $cedula";

    if ($mysqli->query($sql) === TRUE) {
        echo "Los datos se han guardado correctamente.";
        header("Location: ../firma/firma.php");
    } else {
        echo "Error al guardar los datos: " . $mysqli->error;
    }
   
} else {
    // Manejar el caso en el que la solicitud no sea POST
    echo 'Error: Método de solicitud no válido.';
}
?>
