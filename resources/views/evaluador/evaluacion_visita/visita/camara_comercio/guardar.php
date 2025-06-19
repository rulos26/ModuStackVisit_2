<?php
session_start();
include '../../../../../conn/conexion.php';


// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos del formulario
    if (isset($_POST['nombre']) &&
        isset($_POST['razon'])  &&
        isset($_POST['actividad']))        {
       // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $tiene_camara= 'Si';
        $nombre=$_POST['nombre'];	
		$razon	=$_POST['razon'];
		$actividad=$_POST['actividad'];	
		$observacion=$_POST['observacion'];
        
        //$_SESSION['id_cedula']=$id_cedula;
            // Preparar la consulta SQL para insertar los datos del formulario en la base de datos
            $sql = "INSERT INTO `camara_comercio`(`id_cedula`,`tiene_camara`, `nombre`, `razon`, `activdad`,observacion) VALUES 
            ('$id_cedula','$tiene_camara', '$nombre', '$razon','$actividad','$observacion')";

            // Ejecutar la consulta
            if ($mysqli->query($sql) === TRUE) {
                //header("Location: ../salud/salud.php");
                header("Location: ../salud/salud.php");
            } else {
                echo "Error al guardar el registro: " . $mysqli->error;
            }
       
        // Cerrar la conexión
        $mysqli->close();
    }  else {
        echo "Todos los campos del formulario son requeridos.";
    } 
} else {
    echo "Acceso denegado.";
}
