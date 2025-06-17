<?php
session_start();
include '../../../../../conn/conexion.php';
echo 'hola <br>';
// Verifica si se ha recibido la imagen de la fotografía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se recibió la imagen correctamente
    if(isset($_POST['photo']) && !empty($_POST['photo'])) {
        $data = $_POST['photo'];
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
        $id_cedula = $_SESSION['id_cedula'];
        $nombre_foto = $_SESSION['id_cedula'].'.png';
        // Directorio donde se guardarán las imágenes
        $directory = "../informe/img/Registro_fotografico/" . $id_cedula . "/";

        // Verificar si el directorio existe, si no, intentar crearlo
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Cambia los permisos según tu configuración
        }

        // Nombre de archivo único basado en el tiempo
        $filePath = $directory . $id_cedula. '.png';

        // Guardar la imagen
        if (file_put_contents($filePath, $imageData) !== false) {
            $directorio_destino = "../informe/img/Registro_fotografico/" . $id_cedula . "/";
                $sql = "INSERT INTO `foto_perfil_autorizacion`(`id_cedula`, `ruta`, `nombre`) VALUES 
                ('$id_cedula', '$directorio_destino', '$nombre_foto')";
                header("Location: ../carta_autorizacion/carta_autorizacion.php");
                // Ejecutar la consulta
                if ($mysqli->query($sql) === TRUE) {
                    header("Location: ../carta_autorizacion/carta_autorizacion.php");
                } else {
                    echo "Error al registrar: " . $mysqli->error;
                }
            echo 'La imagen se guardó correctamente en: ' . $filePath;
        } else {
            echo 'Error al guardar la imagen.';
        }
    } else {
        echo 'No se recibió ninguna imagen.';
    }
} else {
    echo 'Error: método de solicitud no válido.';
}
?>
