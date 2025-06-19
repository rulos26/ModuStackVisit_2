<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos requeridos del formulario
    if ( isset($_POST['actitud']) && isset($_POST['condiciones_vivienda']) && isset($_POST['dinamica_familiar']) && isset($_POST['condiciones_economicas']) && isset($_POST['condiciones_academicas']) && isset($_POST['evaluacion_experiencia_laboral']) && isset($_POST['observaciones']) && isset($_POST['id_concepto_final']) && isset($_POST['nombre_evaluador']) && isset($_POST['id_concepto_seguridad'])) {
        
        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $actitud = $_POST['actitud'];
        $condiciones_vivienda = $_POST['condiciones_vivienda'];
        $dinamica_familiar = $_POST['dinamica_familiar'];
        $condiciones_economicas = $_POST['condiciones_economicas'];
        $condiciones_academicas = $_POST['condiciones_academicas'];
        $evaluacion_experiencia_laboral = $_POST['evaluacion_experiencia_laboral'];
        $observaciones = $_POST['observaciones'];
        $id_concepto_final = $_POST['id_concepto_final'];
        $nombre_evaluador = $_POST['nombre_evaluador'];
        $id_concepto_seguridad = $_POST['id_concepto_seguridad'];
        //$_SESSION['id_cedula']=$id_cedula;
        // Realiza las validaciones necesarias antes de guardar los datos
        
        

        // Preparar la consulta SQL para insertar los datos del formulario en la base de datos
        $sql = "INSERT INTO concepto_final_evaluador (id_cedula, actitud, condiciones_vivienda, dinamica_familiar, condiciones_economicas, condiciones_academicas, evaluacion_experiencia_laboral, observaciones, id_concepto_final, nombre_evaluador, id_concepto_seguridad) 
                VALUES ('$id_cedula', '$actitud', '$condiciones_vivienda', '$dinamica_familiar', '$condiciones_economicas', '$condiciones_academicas', '$evaluacion_experiencia_laboral', '$observaciones', '$id_concepto_final', '$nombre_evaluador', '$id_concepto_seguridad')";

        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            header("Location: ../ubicacion/ubicacion.php");
        } else {
            echo "Error al guardar el registro: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "Faltan datos del formulario.";
    }
} else {
    echo "Acceso denegado.";
}
?>
