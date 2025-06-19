<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once(__DIR__ . '/../../../../../../librery/tcpdf.php');
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
include 'sql/evaluados.php';
include 'sql/perfil.php';
include 'sql/camara_comercio.php';
include 'sql/estado_salud.php';
include 'sql/composion_familiar.php';
include 'sql/pareja.php';
include 'sql/tipo_vivienda.php';
include 'sql/inventario.php';
include 'sql/servicos.php';
include 'sql/patrimonio.php';
include 'sql/cuenta_bancaria.php';
include 'sql/pasivos.php';
include 'sql/aportante.php';
include 'sql/ingresos.php';
include 'sql/gastos.php';
include 'sql/estudios.php';
include 'sql/informacion_judicial.php';
include 'sql/exp_laboral.php';
include 'sql/imagen_ubicacion.php';
include 'sql/regristo_fotos.php';
include 'sql/concepto_final.php';
$id_cedula = $_SESSION['id_cedula'];
$fecha_actual = date('Y-m-d'); // Formato: Año-Mes-Día

// Inicializar variables para evitar errores
$ruta_imagen = '';
$ruta_imagen_ubi = '';
$row_viv = array();

// Verificar si existe foto de perfil
if (isset($foto) && $foto->num_rows > 0) {
    $foto_data = $foto->fetch_assoc();
    $ruta_imagen = $foto_data['ruta'] . $foto_data['nombre'];
}

// Verificar si existe imagen de ubicación
if (isset($foto_ubi) && $foto_ubi->num_rows > 0) {
    $foto_ubi_data = $foto_ubi->fetch_assoc();
    $ruta_imagen_ubi = $foto_ubi_data['ruta'] . $foto_ubi_data['nombre'];
}

// Verificar si existe estado de vivienda
if (isset($data_vivi) && $data_vivi->num_rows > 0) {
    $row_viv = $data_vivi->fetch_assoc();
}

$foto1;
$foto2;
$foto3;
$foto3;
$foto4;
$foto5;
$foto6;
$foto7;
$foto8;

// Definir $data_info_personal si no está definida
if (!isset($data_info_personal)) {
    $data_info_personal = '<span style="color:red">Información personal no disponible</span>';
}

// Clase para configuración del PDF
class PDFConfig {
    const PAGE_FORMAT = 'A4';
    const ORIENTATION = 'P';
    const UNIT = 'mm';
    const ENCODING = 'UTF-8';
    const MARGIN_LEFT = 15;
    const MARGIN_TOP = 15;
    const MARGIN_RIGHT = 15;
    const MARGIN_BOTTOM = 15;
    const FONT_SIZE_TITLE = 14;
    const FONT_SIZE_HEADER = 12;
    const FONT_SIZE_CONTENT = 10;
    
    public static function getDefaultConfig() {
        return [
            'format' => self::PAGE_FORMAT,
            'orientation' => self::ORIENTATION,
            'unit' => self::UNIT,
            'unicode' => true,
            'encoding' => self::ENCODING,
            'margins' => [
                'left' => self::MARGIN_LEFT,
                'top' => self::MARGIN_TOP,
                'right' => self::MARGIN_RIGHT,
                'bottom' => self::MARGIN_BOTTOM
            ]
        ];
    }
}

// Clase para generación del PDF
class PDFGenerator extends TCPDF {
    private $headerTitle;
    private $headerLogo;
    
    public function __construct($headerTitle = '', $headerLogo = '') {
        $config = PDFConfig::getDefaultConfig();
        parent::__construct(
            $config['orientation'],
            $config['unit'],
            $config['format'],
            $config['unicode'],
            $config['encoding']
        );
        
        $this->headerTitle = $headerTitle;
        $this->headerLogo = $headerLogo;
        
        $this->SetMargins(
            $config['margins']['left'],
            $config['margins']['top'],
            $config['margins']['right']
        );
        
        $this->SetAutoPageBreak(TRUE, $config['margins']['bottom']);
        $this->setHeaderMargin(5);
        $this->setFooterMargin(10);
        
        // Configuración adicional
        $this->SetCreator('Sistema de Informes');
        $this->SetAuthor('Evaluador');
        $this->SetTitle('Informe de Visita Domiciliaria');
    }
    
    public function Header() {
        if ($this->headerLogo && file_exists($this->headerLogo)) {
            $this->Image($this->headerLogo, 10, 10, 50);
        }
        
        $this->SetFont('helvetica', 'B', PDFConfig::FONT_SIZE_TITLE);
        $this->Cell(0, 15, $this->headerTitle, 0, false, 'C');
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
    
    public function addSection($title, $content) {
        $this->SetFont('helvetica', 'B', PDFConfig::FONT_SIZE_HEADER);
        $this->Ln(10);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        
        $this->SetFont('helvetica', '', PDFConfig::FONT_SIZE_CONTENT);
        $this->writeHTML($content);
    }
    
    public function addImage($path, $x = null, $y = null, $w = 50) {
        if (file_exists($path)) {
            if ($x === null) {
                $x = $this->GetX();
            }
            if ($y === null) {
                $y = $this->GetY();
            }
            
            $this->Image($path, $x, $y, $w);
            $this->Ln($w + 5);
        }
    }
}

// Generar el PDF
try {
    // Inicializar el generador de PDF
    $pdf = new PDFGenerator('INFORME VISITA DOMICILIARIA', 'header/header.jpg');
    $pdf->AddPage();
    
    // Agregar información personal
    $pdf->addSection('Información Personal', $data_info_personal);
    
    // Agregar foto de perfil si existe
    if (!empty($profileImagePath)) {
        $pdf->addImage($profileImagePath);
    }
    
    // Agregar ubicación
    if (!empty($locationImagePath)) {
        $pdf->addSection('Ubicación', '');
        $pdf->addImage($locationImagePath);
    }
    
    // Agregar evidencias fotográficas
    if (!empty($evidenceImages)) {
        $pdf->addSection('Evidencias Fotográficas', '');
        foreach ($evidenceImages as $image) {
            if (!empty($image)) {
                $pdf->addImage($image);
            }
        }
    }
    
    // Generar el archivo
    $pdfFilename = 'informe_' . date('Y-m-d_H-i-s') . '.pdf';
    $pdf->Output($pdfFilename, 'I');
    
} catch (Exception $e) {
    echo "Error al generar el informe PDF: " . $e->getMessage();
}

// ... existing code ...
