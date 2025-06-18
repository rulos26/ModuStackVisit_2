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
        // Consulta de autorizaciones
        $sql1 = "SELECT `id`, `cedula`, `nombres`, `direccion`, `localidad`, `barrio`, `telefono`, `celular`, `fecha`, `autorizacion`, `correo` FROM `autorizaciones` WHERE cedula=:cedula LIMIT 1";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bindParam(':cedula', $cedula);
        $stmt1->execute();
        $row1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

        // Consulta de ubicacion_autorizacion
        $sql2 = "SELECT * FROM ubicacion_autorizacion WHERE id_cedula=:cedula LIMIT 1";
        $stmt2 = $db->prepare($sql2);
        $stmt2->bindParam(':cedula', $cedula);
        $stmt2->execute();
        $row2 = $stmt2->fetch(\PDO::FETCH_ASSOC);

        // Consulta de firmas
        $sql3 = "SELECT * FROM firmas WHERE id_cedula=:cedula LIMIT 1";
        $stmt3 = $db->prepare($sql3);
        $stmt3->bindParam(':cedula', $cedula);
        $stmt3->execute();
        $row3 = $stmt3->fetch(\PDO::FETCH_ASSOC);

        // Consulta de foto_perfil_autorizacion
        $sql4 = "SELECT * FROM foto_perfil_autorizacion WHERE id_cedula=:cedula LIMIT 1";
        $stmt4 = $db->prepare($sql4);
        $stmt4->bindParam(':cedula', $cedula);
        $stmt4->execute();
        $row4 = $stmt4->fetch(\PDO::FETCH_ASSOC);

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        // Contenido HTML con los datos
        $html = '<h1>Datos de la Autorización</h1>';
        if ($row1) {
            $html .= '<h2>Tabla: autorizaciones</h2><ul>';
            foreach ($row1 as $key => $value) {
                $html .= '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No se encontraron datos en autorizaciones.</p>';
        }
        $html .= '<hr><h2>Tabla: ubicacion_autorizacion</h2>';
        if ($row2) {
            $html .= '<ul>';
            foreach ($row2 as $key => $value) {
                $html .= '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No se encontraron datos en ubicacion_autorizacion.</p>';
        }
        $html .= '<hr><h2>Tabla: firmas</h2>';
        if ($row3) {
            $html .= '<ul>';
            foreach ($row3 as $key => $value) {
                $html .= '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No se encontraron datos en firmas.</p>';
        }
        $html .= '<hr><h2>Tabla: foto_perfil_autorizacion</h2>';
        if ($row4) {
            $html .= '<ul>';
            foreach ($row4 as $key => $value) {
                $html .= '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No se encontraron datos en foto_perfil_autorizacion.</p>';
        }
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('ejemplo.pdf', ["Attachment" => false]);
        exit;
    }
} 