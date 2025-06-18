<?php
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la cédula del formulario
    $id_cedula =$_POST['id_cedula'];

    // Almacenar la cédula en una variable de sesión
    $_SESSION['id_cedula']=$id_cedula;

    // Redirigir a otra página o realizar otras acciones
    header("Location: informacion_personal/informacion_personal.php");
    exit;
}
?>
