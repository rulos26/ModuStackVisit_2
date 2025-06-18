<?php
// Incluir archivo de conexión si BASE_PATH está definida
if (defined('BASE_PATH')) {
    require_once(BASE_PATH . '/conn/conexion.php');
} else {
    die("Error: BASE_PATH no está definida");
}

// Obtener la cédula de la sesión o del parámetro GET
$id_cedula = isset($_SESSION['id_cedula']) ? $_SESSION['id_cedula'] : (isset($_GET['cedula']) ? $_GET['cedula'] : null);

if (!$id_cedula) {
    die("Error: No se ha proporcionado una cédula");
}

$evaluado = "SELECT * FROM `autorizaciones` WHERE cedula = ?";
$stmt = $mysqli->prepare($evaluado);
$stmt->bind_param("s", $id_cedula);
$stmt->execute();
$data_evaluados = $stmt->get_result();

if ($data_evaluados->num_rows > 0) {
    $row = $data_evaluados->fetch_assoc();
} else {
    die("Error: No se encontraron datos para la cédula: " . htmlspecialchars($id_cedula));
}

