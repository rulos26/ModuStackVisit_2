<?php
// Verificar si se recibió la imagen
if(isset($_POST['imgData'])) {
    // Obtener la imagen en base64
    $imgData = $_POST['imgData'];
    
    // Decodificar la imagen base64 y guardarla en el servidor
    $imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
    $imgData = str_replace(' ', '+', $imgData);
    $imgData = base64_decode($imgData);
    
    // Guardar la imagen en una ubicación específica en el servidor
    $file = '../informe/img/fotos_perfil/imagen.jpg';
    file_put_contents($file, $imgData);
    
    echo 'Imagen guardada correctamente en el servidor';
} else {
    echo 'Error al recibir la imagen';
}


if (isset($_POST['image'])) {
    $data = $_POST['image'];
    $img = str_replace('data:image/jpeg;base64,', '', $data);
    $img = str_replace(' ', '+', $img);
    $file = 'mapa.jpg';
    file_put_contents($file, base64_decode($img));
    echo 'Imagen guardada con éxito.';
} else {
    echo 'No se recibió ninguna imagen.';
}


?>
