<?php
session_start();
include '../../../../../conn/conexion.php';



?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="../../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../../css/header.css" rel="stylesheet">
    <!-- Se eliminó la referencia a Google Maps API ya que no se utilizará el mapa -->
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
                    <h5 class="card-title">Carta de Autorización-UBICACIÓN EN TIEMPO REAL</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col-8" style="text-align: justify;">
                            <p><b>GRUPO DE TAREAS EMPRESARIALES LTDA</b> está procesando su ubicación actual para la evaluación. Por favor, mantenga esta ventana abierta mientras se genera el mapa de ubicación. Este proceso es parte de nuestro sistema de validación y verificación de datos.</p>
                        </div>
                        <div class="col-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3 text-center">
                            <!-- Sección de coordenadas geográficas -->
                            <div class="mt-3">
                                <h4>Ubicación Actual del Evaluado</h4>
                                <p>Latitud: <span id="latitud"></span></p>
                                <p>Longitud: <span id="longitud"></span></p>
                                
                                <!-- Contador de progreso -->
                                <div class="progress mt-3" style="height: 50px; width: 80%; margin: 0 auto;">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: 0%; font-size: 16px; font-weight: bold;" 
                                         aria-valuenow="0" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <span id="countdown">0 %</span>%
                                    </div>
                                </div>
                                <p class="mt-3 text-muted" style="font-size: 16px;">Generando mapa de ubicación...</p>
                            </div>
                        </div>
                    </div>
                    <!-- Formulario para mostrar la longitud y latitud -->
                    <form id="ubicacionForm" action="guardar.php" method="POST">
                        <input type="hidden" id="latituds" name="latituds">
                        <input type="hidden" id="longituds" name="longituds">
                    </form>

                </div>
                <div class="card-footer text-body-secondary">
                    © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../footer/footer.php'; ?>
    </div>

    <script src="../../../../../js/toggleMenu.js"></script>
    <script src="../../../../../js/active_link.js"></script>
    <script src="../../../../../js/autorizacion,js"></script>
    <script src="../../../../../js/validar_password.js"></script>
    <script>
        // Función para obtener la ubicación automáticamente al cargar la página
        function obtenerUbicacion() {
            if (navigator.geolocation) {
                // Obtener la ubicación actual del dispositivo
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitud = position.coords.latitude;
                    var longitud = position.coords.longitude;

                    // Mostrar la latitud y longitud en la vista
                    document.getElementById('latitud').innerText = latitud;
                    document.getElementById('longitud').innerText = longitud;
                    // Asignar los valores de longitud y latitud a los inputs del formulario
                    document.getElementById('latituds').value = latitud;
                    document.getElementById('longituds').value = longitud;
                    
                    // Iniciar el contador
                    iniciarContador();
                }, function() {
                    alert('No se pudo obtener la ubicación.');
                });
            } else {
                alert('La geolocalización no está disponible en este navegador.');
            }
        }

        // Función para el contador regresivo
        function iniciarContador() {
            let tiempoRestante = 30;
            const progressBar = document.getElementById('progressBar');
            const countdown = document.getElementById('countdown');
            
            const intervalo = setInterval(() => {
                tiempoRestante--;
                const porcentaje = Math.round(((30 - tiempoRestante) / 30) * 100);
                
                progressBar.style.width = porcentaje + '%';
                progressBar.setAttribute('aria-valuenow', porcentaje);
                countdown.textContent = porcentaje;
                
                if (tiempoRestante <= 0) {
                    clearInterval(intervalo);
                    // Actualizar el mensaje y el estilo de la barra
                    document.querySelector('.text-muted').textContent = '¡Mapa generado! Enviando datos...';
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    
                    // Enviar el formulario automáticamente
                    setTimeout(() => {
                        document.getElementById('ubicacionForm').submit();
                    }, 1000); // Esperar 1 segundo después de completar la barra
                }
            }, 1000);
        }

        // Llamar a la función de ubicación cuando se carga la página
        window.onload = obtenerUbicacion;
    </script>
    <!-- Bootstrap Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Se eliminó la carga de Google Maps API -->


</body>

</html>