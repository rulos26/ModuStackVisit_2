<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: ../../../error/error.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<a href="../error/error.php"></a>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace al archivo CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace al archivo de Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="../../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../../css/header.css" rel="stylesheet">
    <style>

    </style>

</head>

<body>

    <!-- Menú Vertical -->
    <?php include '../menu/menu.php'; ?>

    <!-- Navbar -->
    <?php include '../header/header.php'; ?>




    <!-- Contenido de la página -->
    <div style="margin-left: 250px; padding: 20px;">

        <div class="container">

            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">Carta de Autorización </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="65%" height="55%">

                        </div>
                        <div class="col-6">

                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-6 offset-md-3 text-center">
                                    <!-- <h2>Capturar Fotografía</h2>
                                    <video id="video" width="100%" height="auto" autoplay></video>
                                    <button class="btn btn-primary mt-3" onclick="tomarFoto()">Tomar Foto</button>
                                 -->
                                    <button id="captureButton">Tomar Foto</button>
                                    <canvas id="canvas" style="display: none;"></canvas>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../footer/footer.php'; ?>
    </div>


</body>

<!-- Bootstrap Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


<!-- <script src="registro_fotografico.js"></script>
 -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const captureButton = document.getElementById('captureButton');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        captureButton.addEventListener('click', function() {
            // Acceder a la cámara y tomar la foto
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    const video = document.createElement('video');
                    document.body.appendChild(video);
                    video.srcObject = stream;
                    video.play();

                    video.addEventListener('loadeddata', function() {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Convertir la imagen del canvas a base64
                        const imageData = canvas.toDataURL('image/png');

                        // Enviar la foto al servidor (puedes utilizar AJAX para esto)
                        fetch('guardar.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'photo=' + encodeURIComponent(imageData)
                            })
                           /*  .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error al enviar la imagen al servidor.');
                                    window.location.href = '../carta_autorizacion/carta_autorizacion.php'; // Cambia 'nueva_pagina.html' por la URL que desees

                                }
                                return response.text();
                            }) */
                          /*   .then(message => {
                                console.log(message); // Puedes manejar la respuesta del servidor aquí
                                window.location.href = '../carta_autorizacion/carta_autorizacion.php'; // Cambia 'nueva_pagina.html' por la URL que desees

                            }) */
                           /*  .catch(error => {
                                console.error('Error:', error);
                            }); */

                        // Detener la transmisión de la cámara
                        stream.getTracks().forEach(track => track.stop());

                        // Eliminar el elemento de video
                        document.body.removeChild(video);
                        window.location.href = '../carta_autorizacion/carta_autorizacion.php'; // Cambia 'nueva_pagina.html' por la URL que desees

                        
                    });
                })
                .catch(function(error) {
                    console.error('Error al acceder a la cámara:', error);
                });
        });
    });
</script>

</html>