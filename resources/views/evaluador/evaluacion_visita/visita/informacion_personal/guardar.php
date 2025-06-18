<?php
session_start();
include '../../../../../conn/conexion.php';
// Verificar si el usuario ha iniciado sesión

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos necesarios del formulario
    if ( isset($_POST['observacion'])&&isset($_POST['id_tipo_documentos']) && isset($_POST['cedula_expedida']) && isset($_POST['nombres']) && isset($_POST['apellidos']) && isset($_POST['edad']) && isset($_POST['fecha_expedicion']) && isset($_POST['lugar_nacimiento']) && isset($_POST['celular_1']) && isset($_POST['celular_2']) && isset($_POST['telefono']) && isset($_POST['id_rh']) && isset($_POST['id_estatura']) && isset($_POST['peso_kg']) && isset($_POST['id_estado_civil']) && isset($_POST['numero_hijos']) && isset($_POST['direccion']) && isset($_POST['id_ciudad']) && isset($_POST['localidad']) && isset($_POST['barrio']) && isset($_POST['id_estrato']) && isset($_POST['correo']) && isset($_POST['cargo'])) {
        
        // Obtener los datos del formulario
        $id_cedula =$_SESSION['id_cedula'];
        $id_tipo_documentos = $_POST['id_tipo_documentos'];
        $cedula_expedida = $_POST['cedula_expedida'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $edad = $_POST['edad'];
        $fecha_expedicion = $_POST['fecha_expedicion'];
        $lugar_nacimiento = $_POST['lugar_nacimiento'];
        $celular_1 = $_POST['celular_1'];
        $celular_2 = $_POST['celular_2'];
        $telefono = $_POST['telefono'];
        $id_rh = $_POST['id_rh'];
        $id_estatura = $_POST['id_estatura'];
        $peso_kg = $_POST['peso_kg'];
        $id_estado_civil = $_POST['id_estado_civil'];
        $numero_hijos = $_POST['numero_hijos'];
        $direccion = $_POST['direccion'];
        $id_ciudad = $_POST['id_ciudad'];
        $localidad = $_POST['localidad'];
        $barrio = $_POST['barrio'];
        $id_estrato = $_POST['id_estrato'];
        $correo = $_POST['correo'];
        $cargo = $_POST['cargo'];
        $observacion=$_POST['observacion'];
        $hacer_cuanto=$_POST['hacer_cuanto'];
        $_SESSION['id_cedula']=$id_cedula;
        // Conexión a la base de datos (ajusta los valores según tu configuración)
        //$mysqli = new mysqli('localhost', 'usuario', 'contraseña', 'nombre_base_de_datos');

        
        // Preparar la consulta SQL para insertar los datos en la base de datos
        $sql = "INSERT INTO evaluados (id_cedula, id_tipo_documentos, cedula_expedida, nombres, apellidos, edad, fecha_expedicion, lugar_nacimiento, celular_1, celular_2, telefono, id_rh, id_estatura, peso_kg, id_estado_civil,hacer_cuanto, numero_hijos, direccion, id_ciudad, localidad, barrio, id_estrato, correo, cargo,observacion) VALUES 
        ('$id_cedula', '$id_tipo_documentos', '$cedula_expedida', '$nombres', '$apellidos', '$edad', '$fecha_expedicion', '$lugar_nacimiento', '$celular_1', '$celular_2', '$telefono', '$id_rh', '$id_estatura', '$peso_kg', '$id_estado_civil','$hacer_cuanto', '$numero_hijos', '$direccion', '$id_ciudad', '$localidad', '$barrio', '$id_estrato', '$correo', '$cargo','$observacion')";
      
        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
             header("Location: ../camara_comercio/camara_comercio.php"); 
        } else {
            echo "Error al registrar el usuario: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "No se recibieron todos los campos del formulario.";
    }
} else {
    echo "Acceso denegado.";
}
?>
