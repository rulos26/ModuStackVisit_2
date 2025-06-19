<?php
session_start();
include '../../../../../conn/conexion.php';
// Verificar si se recibieron datos del formulario y si el usuario tiene una sesión activa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_cedula'])) {
    // Recibir los datos del formulario
    $id_cedula = $_SESSION['id_cedula'];
    $nombres = $_POST['nombre'];
    $id_parentescos = $_POST['id_parentesco'];
    $edades = $_POST['edad'];
    $id_ocupacion = $_POST['id_ocupacion'];
    $telefono = $_POST['telefono'];
    $id_conviven = $_POST['id_conviven'];
    $observacion=$_POST['observacion'];

    // Preparar la consulta SQL para insertar los datos en la tabla
    $sql = "INSERT INTO composicion_familiar (id_cedula, nombre, id_parentesco, edad, id_ocupacion, telefono, id_conviven) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $mysqli->error);
    }

    // Asociar los parámetros y ejecutar la consulta para cada conjunto de datos recibidos
    // ...

    // Asociar los parámetros y ejecutar la consulta para cada conjunto de datos recibidos
    for ($i = 0; $i < count($nombres); $i++) {
        $sql = "INSERT INTO  composicion_familiar (id_cedula, nombre, id_parentesco, edad, id_ocupacion, telefono, id_conviven, observacion) 
   VALUES ('$id_cedula', '$nombres[$i]', '$id_parentescos[$i]', '$edades[$i]', '$id_ocupacion[$i]', '$telefono[$i]', '$id_conviven[$i]', '$observacion[$i]')";
        
        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            echo "Registro guardado con éxito.";
            header("Location: ../informacion_pareja/tiene_pareja.php");
        } else {
            echo "Error al guardar el registro: " . $mysqli->error;
        }
    }

    // ...


    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $mysqli->close();

    // Redirigir a una página de éxito
    //header("Location: exito.php");
    exit();
} else {
    echo "Acceso denegado o faltan datos del formulario.";
}
