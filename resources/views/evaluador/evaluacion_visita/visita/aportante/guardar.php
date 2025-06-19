<?php
session_start();
include '../../../../../conn/conexion.php';


// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos del formulario
    if (isset($_POST['nombre']) && isset($_POST['valor'])) {
        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $nombre = $_POST['nombre'];
        $valor = $_POST['valor'];
        
        //$_SESSION['id_cedula']=$id_cedula;
        for ($i = 0; $i < count($nombre); $i++) {
            // Preparar la consulta SQL para insertar los datos del formulario en la base de datos
            $sql = "INSERT INTO aportante (id_cedula, nombre, valor) VALUES ('$id_cedula', '$nombre[$i]', '$valor[$i]')";

            // Ejecutar la consulta
            if ($mysqli->query($sql) === TRUE) {
                header("Location: ../data_credito/data_credito.php");
            } else {
                echo "Error al guardar el registro: " . $mysqli->error;
            }
        }
        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "Todos los campos del formulario son requeridos.";
    }
} else {
    echo "Acceso denegado.";
}
