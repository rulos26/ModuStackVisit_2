<?php
session_start();
include '../../../../../conn/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tiene_pareja'])) {
        $pareja = $_POST['tiene_pareja'];
        if ($pareja === '1') {
            echo 'no tiene pareja';
            // Obtener los datos del formulario
            $id_cedula = $_SESSION['id_cedula'];
            $valor_vivienda = 'N/A';
            $direccion = 'N/A';
            $id_vehiculo = 'N/A';
            $id_marca = 'N/A';
            $id_modelo = 'N/A';
            $id_ahorro = 'N/A';
            $otros = 'N/A';
            $observacion = 'N/A';
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
            header("Location: ../Patrimonio/patrimonio.php");
            echo 'tiene pareja';
        }
    }
} else {
    echo "Acceso denegado.";
}
