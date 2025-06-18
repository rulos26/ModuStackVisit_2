<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si hay una sesión activa
if (!isset($_SESSION['id_cedula'])) {
    echo '<script>alert("No hay sesión activa"); window.location.href = "../../../login/login.php";</script>';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron todos los campos del formulario
    if (isset($_POST['latituds']) && isset($_POST['longituds'])) {
        // Recibir y sanitizar los datos del formulario
        $id_cedula = $_SESSION['id_cedula'];
        $latituds = $_POST['latituds'];
        $longituds = $_POST['longituds'];
        
        // Preparar la consulta SQL para insertar los datos del formulario
        $sql = "INSERT INTO `ubicacion`(`id_cedula`, `longitud`, `latitud`) VALUES 
         ('$id_cedula', '$longituds', '$latituds')";

        // Ejecutar la consulta
        if ($mysqli->query($sql) === TRUE) {
            // Obtener el ID de la inserción
            $id_ubicacion = $mysqli->insert_id;
            
            // Generar y guardar la imagen del mapa
            $token = 'pk.eyJ1IjoianVhbmRpYXo4NzAxMjYiLCJhIjoiY21hbWxueHJ1MGtlMTJrb3N3bWVwamowNSJ9.5Gsp0Q69b1z3oQijt-Aw2Q';
            $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({$longituds},{$latituds})/{$longituds},{$latituds},15,0/600x300?access_token={$token}";

            // Crear el directorio si no existe
            $directorio_destino = "../../../public/images/ubicacion_autorizacion/" . $id_cedula . "/";
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            // Descargar y guardar la imagen
            $imagen = file_get_contents($url);
            if ($imagen !== false) {
                $nombre_archivo = 'mapa_ubicacion_' . time() . '.jpg';
                $ruta_completa = $directorio_destino . $nombre_archivo;
                
                if (file_put_contents($ruta_completa, $imagen)) {
                    // Guardar la ruta de la imagen en la tabla ubicacion_autorizacion
                    $sql_imagen = "INSERT INTO `ubicacion_autorizacion` (`id_cedula`, `ruta`, `nombre`) 
                                 VALUES ('$id_cedula', '$directorio_destino', '$nombre_archivo')";
                    
                    if ($mysqli->query($sql_imagen)) {
                        // Redirigir a la página de informe en una nueva pestaña y mostrar mensaje
                        echo '<script>
                            // Abrir el informe en nueva pestaña
                            window.open("../informe/index.php", "_blank");
                            
                            // Mostrar mensaje de éxito
                            alert("¡Ubicación guardada exitosamente! Redirigiendo al inicio...");
                            
                            // Redirigir al index de carta de autorización después de 2 segundos
                            setTimeout(function() {
                                window.location.href = "../index.php";
                            }, 2000);
                        </script>';
                        exit();
                    } else {
                        echo "Error al guardar la ruta de la imagen: " . $mysqli->error;
                    }
                } else {
                    echo "Error al guardar la imagen del mapa.";
                }
            } else {
                echo "Error al obtener la imagen del mapa.";
            }
        } else {
            echo "Error al registrar: " . $mysqli->error;
        }

        // Cerrar la conexión
        $mysqli->close();
    } else {
        echo "No se recibieron todos los campos del formulario";
    }
} else {
    echo "Acceso denegado";
}
?>