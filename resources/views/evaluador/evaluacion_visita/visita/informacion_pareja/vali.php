<?php
session_start();
include '../../../../../conn/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tiene_pareja'])){
        $pareja=$_POST['tiene_pareja'];
         if ($pareja === '1') {
            echo'no tiene pareja';
            header("Location: ../tipo_vivienda/tipo_vivienda.php");
           
        } else {
            header("Location: ../informacion_pareja/informacion_pareja.php");
            echo'tiene pareja';
        }
      

    }


} else {
    echo "Acceso denegado.";
}
?>
