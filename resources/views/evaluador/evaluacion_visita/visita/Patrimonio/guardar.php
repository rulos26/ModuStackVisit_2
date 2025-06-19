<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos necesarios
    echo $_POST['id_cedula'],$_POST['valor_vivienda'], $_POST['direccion'],$_POST['id_vehiculo'],$_POST['id_marca'],$_POST['id_modelo'] ,$_POST['id_ahorro'];
    if (isset($_POST['valor_vivienda']) && isset($_POST['direccion']) &&
        isset($_POST['id_vehiculo']) && isset($_POST['id_marca']) && isset($_POST['id_modelo']) &&
        isset($_POST['id_ahorro'])) {

        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $valor_vivienda = $_POST['valor_vivienda'];
        $direccion = $_POST['direccion'];
        $id_vehiculo = $_POST['id_vehiculo'];
        $id_marca = $_POST['id_marca'];
        $id_modelo = $_POST['id_modelo'];
        $id_ahorro = $_POST['id_ahorro'];
        $otros = isset($_POST['otros']) ? $_POST['otros'] : '';
        $observacion=$_POST['observacion'];
        //$_SESSION['id_cedula']=$id_cedula;
        // Aquí puedes realizar la validación y sanitización de los datos recibidos, según tus necesidades

       
        // Preparar la consulta SQL para insertar los datos en la base de datos
        $sql = "INSERT INTO  patrimonio (id_cedula, valor_vivienda, direccion, id_vehiculo, id_marca, id_modelo, id_ahorro, otros,observacion) 
                VALUES ('$id_cedula', '$valor_vivienda', '$direccion', '$id_vehiculo', '$id_marca', '$id_modelo', '$id_ahorro', '$otros','$observacion')";

        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            // Redirigir al usuario a una página de éxito o a donde lo desees
            header("Location: ../cuentas_bancarias/cuentas_bancarias.php");
            exit();
        } else {
            echo "Error al procesar el registro: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "Todos los campos son obligatorios.";
    }
} else {
    echo "Acceso denegado.";
}
?>
