<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
class DemoPdfController {
    public static function generarEjemplo() {
        $db = Database::getInstance()->getConnection();
        $cedula = '1231211322';
        $sql = "SELECT `id`, `cedula`, `nombres`, `direccion`, `localidad`, `barrio`, `telefono`, `celular`, `fecha`, `autorizacion`, `correo` FROM `autorizaciones` WHERE cedula=:cedula LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

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