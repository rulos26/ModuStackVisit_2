<?php
session_start();
include '../../../../../conn/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tiene_pareja'])){
        $pareja=$_POST['tiene_pareja'];
         if ($pareja === '1') {
            $id_cedula = $_SESSION['id_cedula'];;
            $tiene_camara = 'NO';
            $nombre = 'N/A';
            $razon    = 'N/A';
            $actividad = 'N/A';
            $observacion = 'N/A';
              // Preparar la consulta SQL para insertar los datos del formulario en la base de datos
              $sql = "INSERT INTO `camara_comercio`(`id_cedula`,`tiene_camara`, `nombre`, `razon`, `activdad`,observacion) VALUES 
              ('$id_cedula','$tiene_camara', '$nombre', '$razon','$actividad','$observacion')";
  
              // Ejecutar la consulta
              if ($mysqli->query($sql) === TRUE) {
                  
                header("Location: ../salud/salud.php");
              } else {
                  echo "Error al guardar el registro: " . $mysqli->error;
              }
         
          // Cerrar la conexión
          $mysqli->close();
            
           
        } else {
            
            header("Location: ../data_credito/data_credito.php");
            echo'tiene pareja';
        }
      

    }


} else {
    echo "Acceso denegado.";
}
?>
