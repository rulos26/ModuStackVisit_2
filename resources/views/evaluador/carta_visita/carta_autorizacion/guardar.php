<?php
session_start();
include '../../../../../conn/conexion.php';
$fecha_actual = date('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $fecha = $fecha_actual;
    $ciudad = $_POST['ciudad'];
    $nombre = $_POST['nombre'];
    $tipo_documento = $_POST['opc_tipo_documentos'];
    $cedula = $_SESSION['id_cedula'];
    $lugar_expedicion = $_POST['cedula_expedida'];
    $autorizacion = $_POST['autorizacion'];
   
    $sql = "INSERT INTO `autorizaciones`( cedula, nombres,  fecha, autorizacion)
                               VALUES ( '$cedula', '$nombre', '$fecha','$autorizacion')";
    if ($mysqli->query($sql) === TRUE) {
        echo "Los datos se han guardado correctamente.";
        header("Location: ../datos_basicos/datos_basicos.php");
    } else {
        echo "Error al guardar los datos: " . $mysqli->error;
    }
} else {
    echo "Error: Método de solicitud no válido.";
}
