<?php
$servername = "localhost"; // Servidor de base de datos
$dbname = "u130454517_modulo_vista"; // Cambia nombre_base_de_datos por el nombre de tu base de datos
$username = 'u130454517_root';
$password = '0382646740Ju*';
$charset = 'utf8mb4';

//dd
// Crear conexión
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}
?>