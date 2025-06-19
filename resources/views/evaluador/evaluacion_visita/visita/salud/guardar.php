<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los campos del formulario
    if (
        isset($_POST['id_estado_salud']) && isset($_POST['tipo_enfermedad']) &&
        isset($_POST['tipo_enfermedad_cual']) && isset($_POST['limitacion_fisica']) &&
        isset($_POST['limitacion_fisica_cual']) && isset($_POST['tipo_medicamento']) &&
        isset($_POST['tipo_medicamento_cual']) && isset($_POST['ingiere_alcohol']) &&
        isset($_POST['ingiere_alcohol_cual']) && isset($_POST['fuma'])
    ) {

        // Recoger los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $id_estado_salud = $_POST['id_estado_salud'];
        $tipo_enfermedad = $_POST['tipo_enfermedad'];
        $tipo_enfermedad_cual = $_POST['tipo_enfermedad_cual'];
        $limitacion_fisica = $_POST['limitacion_fisica'];
        $limitacion_fisica_cual = $_POST['limitacion_fisica_cual'];
        $tipo_medicamento = $_POST['tipo_medicamento'];
        $tipo_medicamento_cual = $_POST['tipo_medicamento_cual'];
        $ingiere_alcohol = $_POST['ingiere_alcohol'];
        $ingiere_alcohol_cual = $_POST['ingiere_alcohol_cual'];
        $fuma = $_POST['fuma'];
        $observacion=$_POST['observacion'];
        // Asignar valores por defecto si los campos están vacíos
        $tipo_enfermedad_cual = (empty($_POST['tipo_enfermedad_cual'])) ? 'N/A' : $_POST['tipo_enfermedad_cual'];
        $limitacion_fisica_cual = (empty($_POST['limitacion_fisica_cual'])) ? 'N/A' : $_POST['limitacion_fisica_cual'];
        $tipo_medicamento_cual = (empty($_POST['tipo_medicamento_cual'])) ? 'N/A' : $_POST['tipo_medicamento_cual'];
        $ingiere_alcohol_cual = (empty($_POST['ingiere_alcohol_cual'])) ? 'N/A' : $_POST['ingiere_alcohol_cual'];
        $observacion = (empty($_POST['observacion'])) ? 'N/A' : $_POST['observacion'];
     
        // Luego puedes usar estas variables en tu lógica de aplicación


        // Preparar la consulta SQL para insertar los datos del formulario en la tabla de la base de datos
        $sql = "INSERT INTO estados_salud (id_cedula, id_estado_salud, tipo_enfermedad, tipo_enfermedad_cual, 
                limitacion_fisica, limitacion_fisica_cual, tipo_medicamento, tipo_medicamento_cual, 
                ingiere_alcohol, ingiere_alcohol_cual, fuma,observacion) VALUES ('$id_cedula', '$id_estado_salud', 
                '$tipo_enfermedad', '$tipo_enfermedad_cual', '$limitacion_fisica', '$limitacion_fisica_cual', 
                '$tipo_medicamento', '$tipo_medicamento_cual', '$ingiere_alcohol', '$ingiere_alcohol_cual', '$fuma','$observacion')";

        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            echo "Registro guardado con éxito.";
            header("Location: ../composición_familiar/composición_familiar.php");
        } else {
            echo "Error al guardar el registro: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "No se recibieron todos los campos del formulario.";
    }
} else {
    echo "Acceso denegado.";
}
