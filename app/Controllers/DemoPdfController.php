<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../conn/conexion.php';

use Dompdf\Dompdf;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
class DemoPdfController {
    public static function generarEjemplo() {
        global $mysqli;
        $cedula = '1231211322';
        $sql = "SELECT `id`, `cedula`, `nombres`, `direccion`, `localidad`, `barrio`, `telefono`, `celular`, `fecha`, `autorizacion`, `correo` FROM `autorizaciones` WHERE cedula=? LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('s', $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        // Contenido HTML con los datos
        if ($row) {
            $html = '<h1>Datos de la Autorización</h1>';
            $html .= '<ul>';
            foreach ($row as $key => $value) {
                $html .= '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html = '<h1>No se encontraron datos para la cédula 1231211322</h1>';
        }
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('ejemplo.pdf', ["Attachment" => false]);
        exit;
    }
} 