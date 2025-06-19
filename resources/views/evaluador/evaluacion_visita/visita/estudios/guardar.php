<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos requeridos
    if (
        isset($_POST['centro_estudios']) && isset($_POST['id_jornada']) &&
        isset($_POST['id_ciudad']) && isset($_POST['anno']) && isset($_POST['titulos']) && isset($_POST['id_resultado'])
    ) {

        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $centro_estudios = $_POST['centro_estudios'];
        $id_jornada = $_POST['id_jornada'];
        $id_ciudad = $_POST['id_ciudad'];
        $anno = $_POST['anno'];
        $titulos = $_POST['titulos'];
        $id_resultado = $_POST['id_resultado'];
        //$_SESSION['id_cedula']=$id_cedula;

        for ($i = 0; $i < count($centro_estudios); $i++) {
            // Preparar la consulta SQL para insertar el nuevo registro
            $sql = "INSERT INTO estudios (id_cedula, centro_estudios, id_jornada, id_ciudad, anno, titulos, id_resultado) VALUES
         ('$id_cedula', '$centro_estudios[$i]', '$id_jornada[$i]', '$id_ciudad[$i]', '$anno[$i]', '$titulos[$i]', '$id_resultado[$i]')";

            // Ejecutar la consulta
            if ($mysqli->query($sql) === TRUE) {
                // Redirigir a la página de éxito
                header("Location: ../informacion_judicial/informacion_judicial.php");
                exit();
            } else {
                echo "Error al guardar el registro: " . $mysqli->error;
            }
        }
        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "Todos los campos son requeridos.";
    }
} else {
    echo "Acceso denegado.";
}
