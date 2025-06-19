<?php
/**
 * INFORME VISITA DOMICILIARIA - GENERADOR DE PDF
 * Versión optimizada y automatizada
 * 
 * @author Sistema de Informes
 * @version 2.0
 * @date 2024
 */

// Configuración de errores y seguridad
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    die('Error: Sesión no válida. Redirigiendo al login...');
}

// Configuración de constantes
define('REPORT_VERSION', '2.0');
define('REPORT_CODE', 'PSI-FR-11');
define('REPORT_VALIDITY', 'Enero 2024');
define('COMPANY_NAME', 'EL NOMBRE DE SU EMPRESA');
define('MAX_IMAGE_WIDTH', 1107);
define('MAX_IMAGE_HEIGHT', 206);
define('PROFILE_IMAGE_WIDTH', 200);
define('PROFILE_IMAGE_HEIGHT', 177);

// Cargar dependencias
require_once(__DIR__ . '/../../../../../../librery/tcpdf.php');
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Cargar módulos de datos
$modules = [
    'evaluados', 'perfil', 'camara_comercio', 'estado_salud', 
    'composion_familiar', 'pareja', 'tipo_vivienda', 'inventario',
    'servicos', 'patrimonio', 'cuenta_bancaria', 'pasivos',
    'aportante', 'ingresos', 'gastos', 'estudios',
    'informacion_judicial', 'exp_laboral', 'imagen_ubicacion',
    'regristo_fotos', 'concepto_final'
];

foreach ($modules as $module) {
    $file = "sql/{$module}.php";
    if (file_exists($file)) {
        include $file;
    } else {
        error_log("Warning: Module file not found: {$file}");
    }
}

// Variables globales
$id_cedula = $_SESSION['id_cedula'];
$fecha_actual = date('Y-m-d');

/**
 * CLASE PRINCIPAL PARA GENERACIÓN DE INFORME
 */
class InformeVisitaDomiciliaria {
    private $pdf;
    private $data;
    private $config;
    private $logger;
    
    public function __construct() {
        $this->config = new ConfiguracionInforme();
        $this->logger = new Logger();
        $this->data = new DataManager();
        $this->initializePDF();
    }
    
    /**
     * Inicializar configuración del PDF
     */
    private function initializePDF() {
        $this->pdf = new InformePDF();
        $this->pdf->SetTitle('Informe Visita Domiciliaria');
        $this->pdf->SetAuthor('Sistema de Informes');
        $this->pdf->SetCreator('Informe Generator v' . REPORT_VERSION);
        $this->pdf->AddPage();
    }
    
    /**
     * Generar el informe completo
     */
    public function generarInforme() {
        try {
            $this->logger->log('Iniciando generación de informe');
            
            // Construir contenido del informe
            $contenido = $this->construirContenido();
            
            // Escribir contenido al PDF
            $this->pdf->writeHTML($contenido, true, false, true, false, '');
            
            // Generar archivo
            $filename = $this->generarNombreArchivo();
            $this->pdf->Output($filename, 'I');
            
            $this->logger->log('Informe generado exitosamente: ' . $filename);
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar informe: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Construir contenido completo del informe
     */
    private function construirContenido() {
        $modulos = [
            'encabezado' => $this->generarEncabezado(),
            'perfil' => $this->generarSeccionPerfil(),
            'informacion_personal' => $this->generarInformacionPersonal(),
            'camara_comercio' => $this->generarCamaraComercio(),
            'estado_salud' => $this->generarEstadoSalud(),
            'composicion_familiar' => $this->generarComposicionFamiliar(),
            'pareja' => $this->generarInformacionPareja(),
            'vivienda' => $this->generarTipoVivienda(),
            'inventario' => $this->generarInventario(),
            'servicios' => $this->generarServicios(),
            'patrimonio' => $this->generarPatrimonio(),
            'cuentas_bancarias' => $this->generarCuentasBancarias(),
            'pasivos' => $this->generarPasivos(),
            'aportantes' => $this->generarAportantes(),
            'ingresos' => $this->generarIngresos(),
            'gastos' => $this->generarGastos(),
            'estudios' => $this->generarEstudios(),
            'experiencia_laboral' => $this->generarExperienciaLaboral(),
            'informacion_judicial' => $this->generarInformacionJudicial(),
            'concepto_final' => $this->generarConceptoFinal(),
            'ubicacion' => $this->generarUbicacion(),
            'evidencias' => $this->generarEvidenciasFotograficas()
        ];
        
        return $this->construirHTML($modulos);
    }
    
    /**
     * Generar nombre de archivo único
     */
    private function generarNombreArchivo() {
        $timestamp = date('Y-m-d_H-i-s');
        $cedula = $this->data->getCedula();
        return "informe_visita_{$cedula}_{$timestamp}.pdf";
    }
    
    /**
     * Construir HTML final
     */
    private function construirHTML($modulos) {
        $html = '<div style="border: 2px solid rgb(175, 0, 0);">';
        
        foreach ($modulos as $modulo) {
            if (!empty($modulo)) {
                $html .= $modulo;
            }
        }
        
        $html .= '</div>';
        return $html;
    }
    
    // Métodos para generar cada sección...
    private function generarEncabezado() {
        return $this->config->getEncabezadoHTML();
    }
    
    private function generarSeccionPerfil() {
        return $this->data->getSeccionPerfil();
    }
    
    private function generarInformacionPersonal() {
        return $this->data->getInformacionPersonal();
    }
    
    private function generarCamaraComercio() {
        return $this->data->getCamaraComercio();
    }
    
    private function generarEstadoSalud() {
        return $this->data->getEstadoSalud();
    }
    
    private function generarComposicionFamiliar() {
        return $this->data->getComposicionFamiliar();
    }
    
    private function generarInformacionPareja() {
        return $this->data->getInformacionPareja();
    }
    
    private function generarTipoVivienda() {
        return $this->data->getTipoVivienda();
    }
    
    private function generarInventario() {
        return $this->data->getInventario();
    }
    
    private function generarServicios() {
        return $this->data->getServicios();
    }
    
    private function generarPatrimonio() {
        return $this->data->getPatrimonio();
    }
    
    private function generarCuentasBancarias() {
        return $this->data->getCuentasBancarias();
    }
    
    private function generarPasivos() {
        return $this->data->getPasivos();
    }
    
    private function generarAportantes() {
        return $this->data->getAportantes();
    }
    
    private function generarIngresos() {
        return $this->data->getIngresos();
    }
    
    private function generarGastos() {
        return $this->data->getGastos();
    }
    
    private function generarEstudios() {
        return $this->data->getEstudios();
    }
    
    private function generarExperienciaLaboral() {
        return $this->data->getExperienciaLaboral();
    }
    
    private function generarInformacionJudicial() {
        return $this->data->getInformacionJudicial();
    }
    
    private function generarConceptoFinal() {
        return $this->data->getConceptoFinal();
    }
    
    private function generarUbicacion() {
        return $this->data->getUbicacion();
    }
    
    private function generarEvidenciasFotograficas() {
        return $this->data->getEvidenciasFotograficas();
    }
}

/**
 * CLASE PARA CONFIGURACIÓN DEL INFORME
 */
class ConfiguracionInforme {
    private $borderColor;
    private $borderWidth;
    private $styles;
    
    public function __construct() {
        $this->borderColor = [255, 255, 255];
        $this->borderWidth = 1;
        $this->styles = $this->getEstilosCSS();
    }
    
    /**
     * Obtener estilos CSS centralizados
     */
    private function getEstilosCSS() {
        return [
            'table' => 'width: 100%; border-collapse: collapse; margin-bottom: 10px;',
            'header' => 'font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;',
            'cell' => 'border: 1px solid black; padding: 5px; text-align: center;',
            'logo' => 'width: ' . MAX_IMAGE_WIDTH . 'px; height: ' . MAX_IMAGE_HEIGHT . 'px;',
            'profile' => 'border: 2px solid black; height: ' . PROFILE_IMAGE_HEIGHT . 'px; width: ' . PROFILE_IMAGE_WIDTH . 'px;'
        ];
    }
    
    /**
     * Generar HTML del encabezado
     */
    public function getEncabezadoHTML() {
        $logo = $this->getLogoHTML();
        $info = $this->getInfoHTML();
        
        return "
        <table cellpadding='5' style='{$this->styles['table']}'>
            <tr style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>
                <td width='100%' style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>{$logo}</td>
    </tr>
        </table>
        {$info}";
    }
    
    /**
     * Generar HTML del logo
     */
    private function getLogoHTML() {
        return '<img src="header/header.jpg" alt="Logo" style="' . $this->styles['logo'] . '">';
    }
    
    /**
     * Generar HTML de información del documento
     */
    private function getInfoHTML() {
        return "
        <table cellpadding='5' style='{$this->styles['table']}'>
            <tr style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>
                <td style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>Código: " . REPORT_CODE . "</td>
        </tr>
            <tr style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>
                <td style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>Versión: " . REPORT_VERSION . "</td>
        </tr>
            <tr style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>
                <td style='border: {$this->borderWidth}px solid rgb(" . implode(',', $this->borderColor) . ");'>Fecha de vigencia: " . REPORT_VALIDITY . "</td>
</tr>
        </table>";
    }
    
    /**
     * Generar tabla HTML estándar
     */
    public function generarTabla($titulo, $headers, $data, $columnWidths = []) {
        $html = "<table class='customTable' style='{$this->styles['table']}'>";
        
        // Título
        if ($titulo) {
            $colspan = count($headers);
            $html .= "<thead><tr><th colspan='{$colspan}' style='{$this->styles['header']}'>{$titulo}</th></tr>";
        }
        
        // Headers
        if (!empty($headers)) {
            $html .= "<tr>";
            foreach ($headers as $index => $header) {
                $width = isset($columnWidths[$index]) ? "width: {$columnWidths[$index]};" : '';
                $html .= "<th style='{$this->styles['header']} {$width}'>{$header}</th>";
            }
            $html .= "</tr>";
        }
        
        // Datos
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $index => $cell) {
                $width = isset($columnWidths[$index]) ? "width: {$columnWidths[$index]};" : '';
                $html .= "<td style='{$this->styles['cell']} {$width}'>" . htmlspecialchars($cell) . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        return $html;
    }
}

/**
 * CLASE PARA MANEJO DE DATOS
 */
class DataManager {
    private $cedula;
    private $data;
    private $formatter;
    
    public function __construct() {
        global $id_cedula, $row, $mysqli;
        $this->cedula = $id_cedula;
        $this->data = $row ?? [];
        $this->formatter = new DataFormatter();
    }
    
    /**
     * Obtener cédula
     */
    public function getCedula() {
        return $this->cedula;
    }
    
    /**
     * Obtener dato seguro
     */
    private function getDato($key, $default = 'No disponible') {
        return isset($this->data[$key]) && !empty($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    /**
     * Generar sección de perfil
     */
    public function getSeccionPerfil() {
        global $ruta_imagen, $fecha_actual;
        
        $perfil = $this->getImagenPerfil($ruta_imagen);
        $infoEvaluador = $this->getInfoEvaluador();
        
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td width='40%' style='border: 1px solid rgb(255, 255, 255);'>{$perfil}</td>
                <td width='20%' style='border: 1px solid rgb(255, 255, 255);'></td>
                <td width='40%' style='border: 1px solid rgb(255, 255, 255);'>{$infoEvaluador}</td>
    </tr>
        </table>";
    }
    
    /**
     * Obtener imagen de perfil
     */
    private function getImagenPerfil($ruta) {
        if (!empty($ruta) && file_exists($ruta)) {
            return '<img src="' . htmlspecialchars($ruta) . '" alt="Foto de perfil" style="border: 2px solid black; height: 177px; width: 200px;">';
        }
        return '<span>No hay imagen de perfil disponible.</span>';
    }
    
    /**
     * Obtener información del evaluador
     */
    private function getInfoEvaluador() {
        global $fecha_actual;
        
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>" . $this->getDato('nombres') . "</td>
         </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>" . $this->getDato('cargo') . "</td>
    </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>" . $this->getDato('id_cedula') . "</td>
</tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>" . $this->getDato('edad') . " años</td>
    </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>Fecha visita: {$fecha_actual}</td>
    </tr>
        </table>";
    }
    
    /**
     * Generar información personal
     */
    public function getInformacionPersonal() {
        $config = new ConfiguracionInforme();
        $headers = ['Campo', 'Valor'];
        $data = [
            ['Nombres', $this->getDato('nombres')],
            ['Apellidos', $this->getDato('apellidos')],
            ['Tipo de Documento', $this->getDato('tipo_documento_nombre')],
            ['No. Documento', $this->getDato('id_cedula')],
            ['Lugar de expedición', $this->getDato('ciudad_nombre')],
            ['Edad', $this->getDato('edad')],
            ['Fecha de Nacimiento', $this->formatter->formatDate($this->getDato('fecha_expedicion'))],
            ['Lugar de Nacimiento', $this->getDato('lugar_nacimiento_municipio')],
            ['Grupo Sanguíneo', $this->getDato('rh_nombre')],
            ['Estatura', $this->getDato('estatura_nombre')],
            ['Estado Civil', $this->getDato('estado_civil_nombre')],
            ['Dirección', $this->getDato('direccion')],
            ['Teléfono', $this->formatter->formatPhone($this->getDato('telefono'))],
            ['Celular', $this->formatter->formatPhone($this->getDato('celular_1'))],
            ['Email', $this->formatter->validateEmail($this->getDato('correo'))],
            ['Cargo', $this->getDato('cargo')],
            ['Observaciones', $this->getDato('observacion')]
        ];
        
        $tabla = $config->generarTabla('INFORMACIÓN PERSONAL', $headers, $data, ['30%', '70%']);
        
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td width='100%' style='border: 1px solid rgb(255, 255, 255);'>{$tabla}</td>
         </tr>
        </table>";
    }
    
    // Métodos para otras secciones...
    public function getCamaraComercio() {
        global $data_row;
        if (!isset($data_row) || empty($data_row)) {
            return $this->getSeccionVacia('CÁMARA DE COMERCIO');
        }
        
        $config = new ConfiguracionInforme();
        $headers = ['Campo', 'Valor'];
        $data = [
            ['¿Tiene Cámara de Comercio?', $this->getDatoSeguro($data_row, 'tiene_camara')],
            ['Nombre de la Empresa', $this->getDatoSeguro($data_row, 'nombre')],
            ['Razón Social', $this->getDatoSeguro($data_row, 'razon')],
            ['Actividad', $this->getDatoSeguro($data_row, 'activdad')],
            ['Observaciones', $this->getDatoSeguro($data_row, 'observacion')]
        ];
        
        $tabla = $config->generarTabla('CÁMARA DE COMERCIO', $headers, $data, ['50%', '50%']);
        
        return $this->envolverTabla($tabla);
    }
    
    public function getEstadoSalud() {
        global $fila_salud;
        if (!isset($fila_salud) || empty($fila_salud)) {
            return $this->getSeccionVacia('ESTADO DE SALUD');
        }
        
        $config = new ConfiguracionInforme();
        $headers = ['Campo', 'Valor', 'Detalle'];
        $data = [
            ['Estado de salud', $this->getDatoSeguro($fila_salud, 'nombre_estado_salud'), ''],
            ['¿Padece algún tipo de enfermedad?', $this->getDatoSeguro($fila_salud, 'nombre_tipo_enfermedad'), $this->getDatoSeguro($fila_salud, 'tipo_enfermedad_cual')],
            ['¿Tiene alguna limitación física?', $this->getDatoSeguro($fila_salud, 'nombre_limitacion_fisica'), $this->getDatoSeguro($fila_salud, 'limitacion_fisica_cual')],
            ['¿Toma algún tipo de medicamento?', $this->getDatoSeguro($fila_salud, 'nombre_tipo_medicamento'), $this->getDatoSeguro($fila_salud, 'tipo_medicamento_cual')],
            ['¿Ingiere alcohol?', $this->getDatoSeguro($fila_salud, 'nombre_ingiere_alcohol'), $this->getDatoSeguro($fila_salud, 'ingiere_alcohol_cual')],
            ['¿Fuma?', $this->getDatoSeguro($fila_salud, 'nombre_fuma'), ''],
            ['Observaciones', $this->getDatoSeguro($fila_salud, 'observacion'), '']
        ];
        
        $tabla = $config->generarTabla('ESTADO DE SALUD DEL ASPIRANTE', $headers, $data, ['40%', '30%', '30%']);
        
        return $this->envolverTabla($tabla);
    }
    
    // Métodos auxiliares
    private function getDatoSeguro($array, $key, $default = 'No disponible') {
        return isset($array[$key]) && !empty($array[$key]) ? $array[$key] : $default;
    }
    
    private function envolverTabla($tabla) {
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td width='100%' style='border: 1px solid rgb(255, 255, 255);'>{$tabla}</td>
     </tr>
        </table>";
    }
    
    private function getSeccionVacia($titulo) {
        $config = new ConfiguracionInforme();
        $tabla = $config->generarTabla($titulo, ['Estado'], [['No hay datos disponibles']], ['100%']);
        return $this->envolverTabla($tabla);
    }
    
    // Implementar métodos para las demás secciones...
    public function getComposicionFamiliar() { return $this->getSeccionVacia('COMPOSICIÓN FAMILIAR'); }
    public function getInformacionPareja() { return $this->getSeccionVacia('INFORMACIÓN DE PAREJA'); }
    public function getTipoVivienda() { return $this->getSeccionVacia('TIPO DE VIVIENDA'); }
    public function getInventario() { return $this->getSeccionVacia('INVENTARIO'); }
    public function getServicios() { return $this->getSeccionVacia('SERVICIOS'); }
    public function getPatrimonio() { return $this->getSeccionVacia('PATRIMONIO'); }
    public function getCuentasBancarias() { return $this->getSeccionVacia('CUENTAS BANCARIAS'); }
    public function getPasivos() { return $this->getSeccionVacia('PASIVOS'); }
    public function getAportantes() { return $this->getSeccionVacia('APORTANTES'); }
    public function getIngresos() { return $this->getSeccionVacia('INGRESOS'); }
    public function getGastos() { return $this->getSeccionVacia('GASTOS'); }
    public function getEstudios() { return $this->getSeccionVacia('ESTUDIOS'); }
    public function getExperienciaLaboral() { return $this->getSeccionVacia('EXPERIENCIA LABORAL'); }
    public function getInformacionJudicial() { return $this->getSeccionVacia('INFORMACIÓN JUDICIAL'); }
    public function getConceptoFinal() { return $this->getSeccionVacia('CONCEPTO FINAL'); }
    public function getUbicacion() { return $this->getSeccionVacia('UBICACIÓN'); }
    public function getEvidenciasFotograficas() { return $this->getSeccionVacia('EVIDENCIAS FOTOGRÁFICAS'); }
}

/**
 * CLASE PARA FORMATEO DE DATOS
 */
class DataFormatter {
    public function formatDate($date) {
        if (empty($date)) return 'No disponible';
        return date('d/m/Y', strtotime($date));
    }
    
    public function formatPhone($phone) {
        if (empty($phone)) return 'No disponible';
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $phone);
    }
    
    public function validateEmail($email) {
        if (empty($email)) return 'No disponible';
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'Email inválido';
    }
    
    public function formatMoney($amount) {
        if (empty($amount)) return '$ 0,00';
        return '$ ' . number_format($amount, 2, ',', '.');
    }
}

/**
 * CLASE PARA LOGGING
 */
class Logger {
    private $logFile = 'informe_errors.log';
    
    public function log($message, $type = 'INFO') {
        $logMessage = date('[Y-m-d H:i:s]') . " [{$type}] {$message}\n";
        error_log($logMessage, 3, $this->logFile);
    }
    
    public function logError($message, $exception = null) {
        $errorMessage = $message;
        if ($exception) {
            $errorMessage .= "\nException: " . $exception->getMessage();
            $errorMessage .= "\nStack Trace: " . $exception->getTraceAsString();
        }
        $this->log($errorMessage, 'ERROR');
    }
}

/**
 * CLASE PDF PERSONALIZADA
 */
class InformePDF extends TCPDF {
    public function __construct() {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(TRUE, 15);
        $this->setHeaderMargin(5);
        $this->setFooterMargin(10);
    }
    
    public function Header() {
        $image_file = 'header/header.jpg';
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'INFORME VISITA DOMICILIARIA', 0, false, 'C');
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

// EJECUCIÓN PRINCIPAL
try {
    $informe = new InformeVisitaDomiciliaria();
    $informe->generarInforme();
} catch (Exception $e) {
    error_log('Error crítico en generación de informe: ' . $e->getMessage());
    echo "Error al generar el informe: " . $e->getMessage();
}
?> 