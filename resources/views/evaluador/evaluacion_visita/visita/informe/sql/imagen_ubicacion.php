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
$ruta_imagen_ubi = '';
$nombre_imagen_ubi = '';

//consulta foto ubicación
$sql = "SELECT * FROM ubicacion_autorizacion where id_cedula= $id_cedula";
$foto_ubi = $mysqli->query($sql);

if ($foto_ubi && $foto_ubi->num_rows > 0) {
    $foto_ubi_data = $foto_ubi->fetch_assoc();
    // Validar que los datos no sean nulos
    if (isset($foto_ubi_data['ruta']) && isset($foto_ubi_data['nombre']) && 
        !empty($foto_ubi_data['ruta']) && !empty($foto_ubi_data['nombre'])) {
        $ruta_imagen_ubi = $foto_ubi_data['ruta'] . $foto_ubi_data['nombre'];
        $nombre_imagen_ubi = $foto_ubi_data['nombre'];
    }
}