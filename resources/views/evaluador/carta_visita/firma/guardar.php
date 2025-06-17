<?php
session_start();
include '../../../../../conn/conexion.php';

// Verifica si se ha recibido la imagen de la firma
if (isset($_POST['image'])) {
    // Obtiene la imagen de la firma en formato base64
    $data = $_POST['image'];

    // Decodifica la imagen de base64
    //$data = str_replace('data:image/png;base64,', '', $data);
    //$data = str_replace(' ', '+', $data);
    //$decodedImage = base64_decode($data);
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
    $id_cedula = $_SESSION['id_cedula'];
    $nombre_foto = $_SESSION['id_cedula'].'.jpg';
    // Directorio donde se guardarán las imágenes
    $directory = "../informe/img/firma/" . $id_cedula . "/";

    // Verificar si el directorio existe, si no, intentar crearlo
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true); // Cambia los permisos según tu configuración
    }

    // Nombre de archivo único basado en el tiempo
    $filePath = $directory . $id_cedula. '.jpg';

    if (file_put_contents($filePath, $imageData) !== false) {
        $directorio_destino = "../informe/img/firma/" . $id_cedula . "/";
            $sql = "INSERT INTO `firmas`(`id_cedula`, `ruta`, `nombre`) VALUES 
            ('$id_cedula', '$directorio_destino', '$nombre_foto')";
            header("Location: ../ubicacion/ubicacion.php");
            // Ejecutar la consulta
            if ($mysqli->query($sql) === TRUE) {
                header("Location: ../ubicacion/ubicacion.php");
            } else {
                echo "Error al registrar: " . $mysqli->error;
            }
        echo 'La imagen se guardó correctamente en: ' . $filePath;
    } else {
        echo 'Error al guardar la imagen.';
    }
} else {
    // Si no se recibe la imagen, muestra un mensaje de error
    echo 'Error: No se recibió la imagen de la firma.';
}
?>
