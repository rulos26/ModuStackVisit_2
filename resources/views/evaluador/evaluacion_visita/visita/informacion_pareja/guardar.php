<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos necesarios
    
    if (isset($_POST['id_tipo_documentos']) && isset($_POST['cedula_expedida']) && isset($_POST['nombres']) && isset($_POST['edad']) && isset($_POST['id_genero']) && isset($_POST['id_nivel_academico']) && isset($_POST['actividad']) && isset($_POST['empresa']) && isset($_POST['antiguedad']) && isset($_POST['direccion_empresa']) && isset($_POST['telefono_1']) && isset($_POST['vive_candidato'])) {
        
        // Obtener los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $cedula = $_POST['ced'];
       
        $id_tipo_documentos = $_POST['id_tipo_documentos'];
        $cedula_expedida = $_POST['cedula_expedida'];
        $nombres = $_POST['nombres'];
        $edad = $_POST['edad'];
        $id_genero = $_POST['id_genero'];
        $id_nivel_academico = $_POST['id_nivel_academico'];
        $actividad = $_POST['actividad'];
        $empresa = $_POST['empresa'];
        $antiguedad = $_POST['antiguedad'];
        $direccion_empresa = $_POST['direccion_empresa'];
        $telefono_1 = $_POST['telefono_1'];
        $telefono_2 = isset($_POST['telefono_2']) ? $_POST['telefono_2'] : null;
        $vive_candidato = $_POST['vive_candidato'];
        $observacion=$_POST['observacion'];
        //$_SESSION['id_cedula']=$id_cedula;
       

        // Preparar la consulta SQL para insertar los datos del registro
        $sql = "INSERT INTO informacion_pareja (id_cedula,cedula, id_tipo_documentos, cedula_expedida, nombres, edad, id_genero, id_nivel_academico, actividad, empresa, antiguedad, direccion_empresa, telefono_1, telefono_2, vive_candidato,observacion) VALUES 
        ('$id_cedula','$cedula', '$id_tipo_documentos', '$cedula_expedida', '$nombres', '$edad', '$id_genero', '$id_nivel_academico', '$actividad', '$empresa', '$antiguedad', '$direccion_empresa', '$telefono_1', '$telefono_2', '$vive_candidato','$observacion')";
echo $sql.'<br>';
        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            // Redirigir a la página de éxito
            header("Location: ../tipo_vivienda/tipo_vivienda.php");
            exit();
        } else {
            echo "Error al guardar el registro: " . $mysqli->error;
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
