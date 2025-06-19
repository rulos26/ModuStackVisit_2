<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos necesarios del formulario
    if (isset($_POST['id_estado'])) {
        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $id_estado = $_POST['id_estado'];
        $observacion=$_POST['observacion'];
        ////$_SESSION['id_cedula']=$id_cedula;
        // Conexión a la base de datos (ajusta los valores según tu configuración)
        
        // Preparar la consulta SQL para insertar los datos del usuario
        $sql = "INSERT INTO estado_vivienda (id_cedula, id_estado,observacion) VALUES ('$id_cedula', '$id_estado', '$observacion')";

        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            header("Location: ../inventario_enseres/inventario_enseres.php");
        } else {
            echo "Error al registrar el usuario: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "Todos los campos son requeridos.";
    }
} else {
    echo "Acceso denegado.";
}
?>
