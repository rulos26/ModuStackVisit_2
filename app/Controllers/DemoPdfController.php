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
        $sql2 = "SELECT nombre FROM ubicacion_autorizacion WHERE id_cedula=:cedula LIMIT 1";
        $stmt2 = $db->prepare($sql2);
        $stmt2->bindParam(':cedula', $cedula);
        $stmt2->execute();
        $row2 = $stmt2->fetch(\PDO::FETCH_ASSOC);

        // Consulta de firmas
        $sql3 = "SELECT nombre  FROM firmas WHERE id_cedula=:cedula LIMIT 1";
        $stmt3 = $db->prepare($sql3);
        $stmt3->bindParam(':cedula', $cedula);
        $stmt3->execute();
        $row3 = $stmt3->fetch(\PDO::FETCH_ASSOC);

        // Consulta de foto_perfil_autorizacion
        $sql4 = "SELECT nombre FROM foto_perfil_autorizacion WHERE id_cedula=:cedula LIMIT 1";
        $stmt4 = $db->prepare($sql4);
        $stmt4->bindParam(':cedula', $cedula);
        $stmt4->execute();
        $row4 = $stmt4->fetch(\PDO::FETCH_ASSOC);

        // Función para obtener la ruta de la imagen
        function get_image_path($nombre, $type, $cedula) {
            if ($nombre && !empty($nombre)) {
                $ruta = "public/images/{$type}/{$cedula}/{$nombre}";
                if (file_exists(__DIR__ . '/../../' . $ruta)) {
                    return $ruta;
                }
            }
            // Imagen por defecto si no existe
            $default = "public/images/{$type}/{$cedula}/default_{$type}.jpg";
            return $default;
        }

        $img_ubicacion = get_image_path($row2['nombre'] ?? '', 'ubicacion_autorizacion', $cedula);
        $img_firma = get_image_path($row3['nombre'] ?? '', 'firma', $cedula);
        $img_perfil = get_image_path($row4['nombre'] ?? '', 'registro_fotografico', $cedula);

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
        $html .= '<li><strong>Ubicación:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row2['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_ubicacion) . '<br>';
        $html .= '<img src="../../' . $img_ubicacion . '" style="max-width:300px;max-height:200px;border:1px solid #333;">';
        $html .= '</li>';
        $html .= '<li><strong>Firma:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row3['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_firma) . '<br>';
        $html .= '<img src="../../' . $img_firma . '" style="max-width:300px;max-height:200px;border:1px solid #333;">';
        $html .= '</li>';
        $html .= '<li><strong>Perfil:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row4['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_perfil) . '<br>';
        $html .= '<img src="../../' . $img_perfil . '" style="max-width:300px;max-height:200px;border:1px solid #333;">';
        $html .= '<img src="../../public/images/registro_fotografico/1231211322/default_registro_fotografico.jpg" style="max-width:300px;max-height:200px;border:1px solid #333;">';
        $html .= '</li>';
        $html .= '</ul>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('ejemplo.pdf', ["Attachment" => false]);
        exit;
    }
} 