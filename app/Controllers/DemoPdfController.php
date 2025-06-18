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

        // Función para obtener la ruta de la imagen
        function get_image_path($row, $type, $cedula) {
            $base = [
                'firma' => 'public/images/firma/' . $cedula . '/',
                'perfil' => 'public/images/registro_fotografico/' . $cedula . '/',
                'ubicacion' => 'public/images/ubicacion_autorizacion/' . $cedula . '/'
            ];
            $default = [
                'firma' => 'public/images/firma/' . $cedula . '/default_firma.jpg',
                'perfil' => 'public/images/registro_fotografico/' . $cedula . '/default_perfil.jpg',
                'ubicacion' => 'public/images/ubicacion_autorizacion/' . $cedula . '/default_ubicacion.jpg'
            ];
            if ($row && !empty($row['ruta']) && !empty($row['nombre'])) {
                $ruta = $row['ruta'];
                $nombre = $row['nombre'];
                $full = $ruta;
                if (substr($ruta, -1) !== '/' && substr($nombre, 0, 1) !== '/') {
                    $full .= '/';
                }
                $full .= $nombre;
                if (file_exists(__DIR__ . '/../../' . $full)) {
                    return $full;
                }
            }
            return $default[$type];
        }

        $img_ubicacion = get_image_path($row2, 'ubicacion', $cedula);
        $img_firma = get_image_path($row3, 'firma', $cedula);
        $img_perfil = get_image_path($row4, 'perfil', $cedula);

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
        $html .= '<hr><h2>Imágenes asociadas</h2>';
        $html .= '<div style="display:flex;gap:20px;">';
        $html .= '<div><strong>Ubicación:</strong><br><img src="../../' . $img_ubicacion . '" style="max-width:150px;max-height:150px;"></div>';
        $html .= '<div><strong>Firma:</strong><br><img src="../../' . $img_firma . '" style="max-width:150px;max-height:150px;"></div>';
        $html .= '<div><strong>Foto Perfil:</strong><br><img src="../../' . $img_perfil . '" style="max-width:150px;max-height:150px;"></div>';
        $html .= '</div>';
        $html .= '<hr><h2>Debug de rutas de imágenes</h2>';
        $html .= '<ul>';
        $html .= '<li><strong>Ruta imagen ubicación:</strong> ' . htmlspecialchars($img_ubicacion) . '</li>';
        $html .= '<li><strong>Ruta imagen firma:</strong> ' . htmlspecialchars($img_firma) . '</li>';
        $html .= '<li><strong>Ruta imagen perfil:</strong> ' . htmlspecialchars($img_perfil) . '</li>';
        $html .= '</ul>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('ejemplo.pdf', ["Attachment" => false]);
        exit;
    }
} 