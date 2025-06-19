<?php
// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Validar que la sesión tenga el id_cedula
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    $id_cedula = '0';
} else {
    $id_cedula = $_SESSION['id_cedula'];
}

// Inicializar variables con valores por defecto
$ruta_imagen = '';
$nombre_imagen = '';

//consulta foto perfil
$sql = "SELECT * FROM foto_perfil_autorizacion where id_cedula= $id_cedula";
$foto = $mysqli->query($sql);

if ($foto && $foto->num_rows > 0) {
    $foto_data = $foto->fetch_assoc();
    // Validar que los datos no sean nulos
    if (isset($foto_data['ruta']) && isset($foto_data['nombre']) && 
        !empty($foto_data['ruta']) && !empty($foto_data['nombre'])) {
        $ruta_imagen = $foto_data['ruta'] . $foto_data['nombre'];
        $nombre_imagen = $foto_data['nombre'];
    }
}