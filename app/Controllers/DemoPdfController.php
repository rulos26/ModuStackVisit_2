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

        // Función para obtener la ruta absoluta de la imagen
        function get_image_path($nombre, $type, $cedula) {
            if ($nombre && !empty($nombre)) {
                $ruta = __DIR__ . "/../../public/images/{$type}/{$cedula}/{$nombre}";
                if (file_exists($ruta)) {
                    return $ruta;
                }
            }
            // Imagen por defecto si no existe
            $default = __DIR__ . "/../../public/images/{$type}/{$cedula}/default_{$type}.jpg";
            return $default;
        }

        // Función para convertir imagen a base64
        function img_to_base64($img_path) {
            if (!file_exists($img_path)) return '';
            $info = pathinfo($img_path);
            $ext = strtolower($info['extension']);
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
            $data = base64_encode(file_get_contents($img_path));
            return 'data:' . $mime . ';base64,' . $data;
        }

        $img_ubicacion_path = get_image_path($row2['nombre'] ?? '', 'ubicacion_autorizacion', $cedula);
        $img_firma_path = get_image_path($row3['nombre'] ?? '', 'firma', $cedula);
        $img_perfil_path = get_image_path($row4['nombre'] ?? '', 'registro_fotografico', $cedula);

        $img_ubicacion_b64 = img_to_base64($img_ubicacion_path);
        $img_firma_b64 = img_to_base64($img_firma_path);
        $img_perfil_b64 = img_to_base64($img_perfil_path);

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        // Contenido HTML con los datos
        $html = '<style>
        .container { max-width: 800px; margin: 30px auto; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 10px; border: 1px solid #ddd; }
        .card-body { padding: 30px; }
        .alert-info { background: #e7f3fe; border: 1px solid #b3e5fc; color: #31708f; border-radius: 8px; padding: 20px; }
        .alert-heading { font-size: 1.5em; margin-bottom: 10px; }
        .text-center { text-align: center; }
        .btn { display: inline-block; padding: 10px 24px; font-size: 1.1em; border-radius: 6px; text-decoration: none; }
        .btn-primary { background: #4361ee; color: #fff; border: none; }
        .img-section { display: flex; justify-content: center; gap: 20px; margin: 30px 0; }
        .img-section img { max-width: 150px; max-height: 150px; border: 2px solid #888; border-radius: 8px; }
        </style>';
        $html .= '<div class="container mt-4">';
        $html .= '<div class="card shadow">';
        $html .= '<div class="card-body">';
        $html .= '<div class="alert alert-info">';
        $html .= '<h4 class="alert-heading">Carta de Autorización</h4>';
        $html .= '<p>Esta es la sección personalizada para la gestión de la carta de autorización. Aquí puedes mostrar formularios, tablas o cualquier contenido específico relacionado con este módulo.</p>';
        $html .= '</div>';
        $html .= '<div class="img-section">';
        $html .= ($img_ubicacion_b64 ? '<div><strong>Ubicación</strong><br><img src="' . $img_ubicacion_b64 . '"></div>' : '');
        $html .= ($img_firma_b64 ? '<div><strong>Firma</strong><br><img src="' . $img_firma_b64 . '"></div>' : '');
        $html .= ($img_perfil_b64 ? '<div><strong>Perfil</strong><br><img src="' . $img_perfil_b64 . '"></div>' : '');
        $html .= '</div>';
        $html .= '</div></div></div>';
        // --- Fin adaptación fiel ---

        // Sección de debug (opcional)
        $html .= '<hr><h2>Debug de rutas de imágenes</h2>';
        $html .= '<ul>';
        $html .= '<li><strong>Ubicación:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row2['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_ubicacion_path) . '<br>';
        $html .= ($img_ubicacion_b64 ? '<img src="' . $img_ubicacion_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>');
        $html .= '</li>';
        $html .= '<li><strong>Firma:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row3['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_firma_path) . '<br>';
        $html .= ($img_firma_b64 ? '<img src="' . $img_firma_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>');
        $html .= '</li>';
        $html .= '<li><strong>Perfil:</strong><br>';
        $html .= 'Nombre BD: ' . htmlspecialchars($row4['nombre'] ?? '(sin valor)') . '<br>';
        $html .= 'Ruta final: ' . htmlspecialchars($img_perfil_path) . '<br>';
        $html .= ($img_perfil_b64 ? '<img src="' . $img_perfil_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>');
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