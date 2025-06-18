<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos necesarios
    if ( isset($_FILES['foto'])) {
        // Recibir los datos del formulario
        //$nombre = $_POST['nombre'];
        //$correo = $_POST['correo'];
        $id_cedula = $_SESSION['id_cedula'];
        // Configurar la ubicación de carga para la foto de perfil
        $directorio_destino = "../informe/img/ubicacion_autorizacion/" . $id_cedula . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
            echo "Se ha creado la carpeta: $directorio_destino";
        } else {
            echo "La carpeta $directorio_destino ya existe";
        }
        // Obtener información del archivo de la foto de perfil
        $nombre_archivo = $_FILES['foto']['name'];
        $tipo_archivo = $_FILES['foto']['type'];
        $tamaño_archivo = $_FILES['foto']['size'];
        $temp_archivo = $_FILES['foto']['tmp_name'];
        $error_archivo = $_FILES['foto']['error'];

        // Generar un nombre único para la foto de perfil
        $nombre_foto = uniqid() . '_' . $nombre_archivo;

        // Comprobar si la foto se cargó correctamente
        if ($error_archivo === UPLOAD_ERR_OK) {
            // Mover el archivo cargado al directorio de destino
            if (move_uploaded_file($temp_archivo, $directorio_destino . $nombre_foto)) {
                // Procesar los datos y realizar la inserción en la base de datos
                // Aquí debes incluir la lógica para insertar los datos en tu base de datos
                // Preparar la consulta SQL para insertar los datos del formulario
                $directorio_destino = "../informe/img/ubicacion_autorizacion/" . $id_cedula . "/";
                $sql = "INSERT INTO `ubicacion_autorizacion`(`id_cedula`, `ruta`, `nombre`) VALUES 
                ('$id_cedula', '$directorio_destino', '$nombre_foto')";
                echo $sql . '<br>';
                // Ejecutar la consulta
                if ($mysqli->query($sql) === TRUE) {
                    
                    echo '<script>window.open("../informe/index.php", "_blank");</script>';
                    header("Location: ../index.php");
                } else {
                    echo "Error al registrar: " . $mysqli->error;
                }
                // Redirigir a una página de éxito
                //header("Location: registro_exitoso.php");
                exit();
            } else {
                echo "Error al mover el archivo.";
            }
        } else {
            echo "Error al cargar la foto.";
        }
    } else {
        echo "Faltan datos del formulario.";
    }
} else {
    echo "Acceso denegado.";
}
