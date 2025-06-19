<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;

class InformeFinalPdfController {
    
    public static function generarInforme($cedula = '1231211322') {
        $db = Database::getInstance()->getConnection();
        
        // Consulta del evaluado
        $sql_evaluado = "SELECT 
            e.id, e.id_cedula, e.id_tipo_documentos, e.cedula_expedida, e.nombres, e.apellidos, 
            e.edad, e.fecha_expedicion, e.lugar_nacimiento, e.celular_1, e.celular_2, e.telefono, 
            e.id_rh, e.id_estatura, e.peso_kg, e.id_estado_civil, e.hacer_cuanto, e.numero_hijos, e.direccion, 
            e.id_ciudad, e.localidad, e.barrio, e.id_estrato, e.correo, e.cargo, e.observacion,
            td.nombre AS tipo_documento_nombre,
            m1.municipio AS lugar_nacimiento_municipio,
            m2.municipio AS ciudad_nombre,
            rh.nombre AS rh_nombre,
            est.nombre AS estatura_nombre,
            ec.nombre AS estado_civil_nombre,
            es.nombre AS estrato_nombre
        FROM evaluados e
        LEFT JOIN opc_tipo_documentos td ON e.id_tipo_documentos = td.id
        LEFT JOIN municipios m1 ON e.lugar_nacimiento = m1.id_municipio
        LEFT JOIN municipios m2 ON e.id_ciudad = m2.id_municipio
        LEFT JOIN opc_rh rh ON e.id_rh = rh.id
        LEFT JOIN opc_estaturas est ON e.id_estatura = est.id
        LEFT JOIN opc_estado_civiles ec ON e.id_estado_civil = ec.id
        LEFT JOIN opc_estratos es ON e.id_estrato = es.id
        WHERE e.id_cedula = :cedula";
        
        $stmt = $db->prepare($sql_evaluado);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $evaluado = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Función para convertir imagen a base64
        function img_to_base64($img_path) {
            if (!file_exists($img_path)) return '';
            $info = pathinfo($img_path);
            $ext = strtolower($info['extension']);
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
            $data = base64_encode(file_get_contents($img_path));
            return 'data:' . $mime . ';base64,' . $data;
        }

        // Header - Logo
        $logo_path = __DIR__ . '/../../public/images/header.jpg';
        $logo_b64 = img_to_base64($logo_path);

        // --- Renderizado usando plantilla externa ---
        $data = [
            'cedula' => $cedula,
            'logo_b64' => $logo_b64,
            'evaluado' => $evaluado
        ];
        extract($data);
        ob_start();
        include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
        $html = ob_get_clean();
        // --- Fin renderizado plantilla ---

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('informe_cedula_' . $cedula . '.pdf', ["Attachment" => false]);
        exit;
    }
}

// Manejar la acción desde el menú
if (isset($_GET['action']) && $_GET['action'] === 'generarInforme') {
    $cedula = $_GET['cedula'] ?? '1231211322';
    InformeFinalPdfController::generarInforme($cedula);
}

?> 