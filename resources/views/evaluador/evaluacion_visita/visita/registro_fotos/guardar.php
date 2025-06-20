<?php
session_start();
include '../../../../../conn/conexion.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos necesarios
    if (isset($_FILES['foto']) && isset($_POST['tipo'])) {
        // Recibir los datos del formulario
        $tipo = $_POST['tipo'];
        $id_cedula = $_SESSION['id_cedula'];
        
        // Configurar la ubicación de carga para la foto
        $directorio_destino = "../../../../../public/images/evidencia_fotografica/" . $id_cedula . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        
        // Obtener información del archivo
        $nombre_archivo = $_FILES['foto']['name'];
        $tipo_archivo = $_FILES['foto']['type'];
        $tamaño_archivo = $_FILES['foto']['size'];
        $temp_archivo = $_FILES['foto']['tmp_name'];
        $error_archivo = $_FILES['foto']['error'];

        // Generar un nombre único para la foto
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $nombre_foto = 'foto_' . $tipo . '_' . $id_cedula . '_' . time() . '.' . $extension;

        // Verificar si ya existe una foto de este tipo
        $sql_check = "SELECT * FROM evidencia_fotografica WHERE id_cedula = '$id_cedula' AND tipo = $tipo";
        $result_check = $mysqli->query($sql_check);
        $foto_existente = $result_check->fetch_assoc();

        // Si existe una foto anterior, eliminarla del servidor
        if ($foto_existente) {
            $ruta_foto_anterior = $directorio_destino . $foto_existente['nombre'];
            if (file_exists($ruta_foto_anterior)) {
                unlink($ruta_foto_anterior);
            }
        }

        // Comprobar si la foto se cargó correctamente
        if ($error_archivo === UPLOAD_ERR_OK) {
            // Mover el archivo cargado al directorio de destino
            if (move_uploaded_file($temp_archivo, $directorio_destino . $nombre_foto)) {
                // Preparar la ruta relativa
                $ruta_relativa = "public/images/evidencia_fotografica/" . $id_cedula . "/";
                
                if ($foto_existente) {
                    // Actualizar registro existente
                    $sql = "UPDATE evidencia_fotografica SET ruta = '$ruta_relativa', nombre = '$nombre_foto' 
                           WHERE id_cedula = '$id_cedula' AND tipo = $tipo";
                    $mensaje = "Foto actualizada exitosamente.";
                } else {
                    // Insertar nuevo registro
                    $sql = "INSERT INTO evidencia_fotografica (id_cedula, ruta, nombre, tipo) VALUES 
                           ('$id_cedula', '$ruta_relativa', '$nombre_foto', $tipo)";
                    $mensaje = "Foto registrada exitosamente.";
                }
                
                // Ejecutar la consulta
                if ($mysqli->query($sql) === TRUE) {
                    $_SESSION['success'] = $mensaje;
                } else {
                    $_SESSION['error'] = "Error al registrar: " . $mysqli->error;
                }
                
                // Redirigir de vuelta al formulario
                header("Location: registro_fotos.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al mover el archivo.";
                header("Location: registro_fotos.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Error al cargar la foto.";
            header("Location: registro_fotos.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Faltan datos del formulario.";
        header("Location: registro_fotos.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Acceso denegado.";
    header("Location: registro_fotos.php");
    exit();
}
?>
