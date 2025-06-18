<?php
// Iniciar sesión al principio
session_start();

// Evitar cualquier salida antes del PDF
ob_start();

// Configuración para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir la ruta base del proyecto si no está definida
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/home/u130454517/domains/concolombiaenlinea.com.co/public_html/modulo_vista');
}

// Definir la ruta base para las imágenes
define('IMG_PATH', BASE_PATH . '/pages/view/evaluador/carta_atorizacion/informe/img');

// Función para convertir PNG a JPG
function convert_png_to_jpg($png_path) {
    if (!file_exists($png_path)) {
        error_log("Archivo no encontrado: " . $png_path);
        return false;
    }

    try {
        $image = @imagecreatefrompng($png_path);
        if (!$image) {
            error_log("Error al crear imagen desde PNG: " . $png_path);
            return false;
        }

        // Crear un fondo blanco
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        if (!$bg) {
            error_log("Error al crear fondo blanco");
            imagedestroy($image);
            return false;
        }

        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        // Generar nombre para el archivo JPG
        $jpg_path = str_replace('.png', '.jpg', $png_path);
        
        // Guardar como JPG
        if (!imagejpeg($bg, $jpg_path, 90)) {
            error_log("Error al guardar JPG: " . $jpg_path);
            imagedestroy($image);
            imagedestroy($bg);
            return false;
        }
        
        // Liberar memoria
        imagedestroy($image);
        imagedestroy($bg);
        
        return $jpg_path;
    } catch (Exception $e) {
        error_log("Error en convert_png_to_jpg: " . $e->getMessage());
        return false;
    }
}

// NUEVAS FUNCIONES PARA RUTAS DE IMÁGENES
function get_image_path($type, $id_cedula, $filename) {
    $base_dir = __DIR__ . "/../../../../../public/images/";
    $type_dir = [
        'firma' => 'firma',
        'perfil' => 'registro_fotografico',
        'ubicacion' => 'ubicacion_autorizacion'
    ];
    if (!isset($type_dir[$type])) return false;
    $ruta = $base_dir . $type_dir[$type] . "/" . $id_cedula . "/" . $filename;
    return file_exists($ruta) ? $ruta : false;
}

function get_default_image($type) {
    $base_dir = __DIR__ . "/../../../../../public/images/";
    $type_dir = [
        'firma' => 'firma',
        'perfil' => 'registro_fotografico',
        'ubicacion' => 'ubicacion_autorizacion'
    ];
    $default_path = $base_dir . $type_dir[$type] . "/default_" . $type . ".jpg";
    if (!file_exists($default_path)) {
        // Crear una imagen por defecto si no existe
        $img = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        imagejpeg($img, $default_path, 90);
        imagedestroy($img);
    }
    return $default_path;
}

function is_valid_image($file_path, $type, $id_cedula) {
    // Si la ruta es relativa o solo el nombre, la completamos
    if (!file_exists($file_path)) {
        $filename = basename($file_path);
        $ruta = get_image_path($type, $id_cedula, $filename);
        if ($ruta) return $ruta;
        return get_default_image($type);
    }
    return $file_path;
}

// Obtener la cédula de la sesión o del parámetro GET
$id_cedula = isset($_SESSION['id_cedula']) ? $_SESSION['id_cedula'] : (isset($_GET['cedula']) ? $_GET['cedula'] : null);

// Verificar si se recibió la cédula
if (!$id_cedula) {
    die("Error: No se ha proporcionado una cédula. Por favor, regrese a la página anterior y seleccione un evaluado.");
}

// Ruta absoluta a TCPDF
$tcpdf_path = BASE_PATH . '/librery/tcpdf.php';
if (!file_exists($tcpdf_path)) {
    die("Error: No se encontró la librería TCPDF en: " . $tcpdf_path);
}
require_once($tcpdf_path);

// Incluir archivo de conexión
$conexion_path = BASE_PATH . '/conn/conexion.php';
if (!file_exists($conexion_path)) {
    die("Error: No se encontró el archivo de conexión en: " . $conexion_path);
}
require_once($conexion_path);

// Incluir archivos SQL
include 'sql/evaluados.php';
include 'sql/perfil.php';
include 'sql/imagen_ubicacion.php';
include 'sql/regristo_fotos.php';

// Verificar si se encontraron datos del evaluado
if (!isset($row) || empty($row)) {
    die("Error: No se encontraron datos para la cédula: " . htmlspecialchars($id_cedula));
}

// Verificar y ajustar rutas de imágenes con manejo de errores
try {
    $ruta_firma = isset($ruta_firma) ? is_valid_image($ruta_firma, 'firma', $id_cedula) : get_default_image('firma');
    $ruta_imagen_ubi = isset($ruta_imagen_ubi) ? is_valid_image($ruta_imagen_ubi, 'ubicacion', $id_cedula) : get_default_image('ubicacion');
    $foto1 = isset($foto1) ? is_valid_image($foto1, 'perfil', $id_cedula) : get_default_image('perfil');
} catch (Exception $e) {
    error_log("Error al procesar imágenes: " . $e->getMessage());
    $ruta_firma = get_default_image('firma');
    $ruta_imagen_ubi = get_default_image('ubicacion');
    $foto1 = get_default_image('perfil');
}

$fecha_actual = date('Y-m-d'); // Formato: Año-Mes-Día
$texto_con_saltos = str_replace('.', ".<br>", $row['autorizacion']);

class MYPDF extends TCPDF
{
    // Método para crear la cabecera
    public function Header()
    {
        // Establecer la imagen
        $image_file = 'header/header.jpg'; // Ruta de tu imagen
        $this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// Crear instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Establecer título del documento
$pdf->SetTitle('Ejemplo de Fila y Columnas con TCPDF');

// Agregar página
$pdf->AddPage();

// Establecer color de borde para las columnas y fila
$borderColor = array(255, 255, 255); // Rojo (RGB)

// Establecer ancho de borde para las columnas y fila
$borderWidth = 1;

// Establecer contenido de las columnas
$logo = '<img src="header/header.jpg" alt="Logo" style="width: 1107px; height:206px">'; // Cambiar la ruta según la ubicación del logo
//$perfil= "<img src='$ruta_imagen' alt='$nombre_imagen' width='177px' height='197px' style='border: 1px solid black;'>";
// Agregar una imagen
$image_file = "$ruta_firma"; // Ruta de tu imagen
// Dimensiones de la imagen
$ancho = 30.2; // Ancho en mm
$alto = 41.4; // Alto en mm

// Agregar la imagen con dimensiones especificadas
$perfil = '<img src="' . $image_file . '" alt="Logo"  style="border: 2px solid black; height: 177px; width: 206px;">'; // Cambiar la ruta según la ubicación del logo

//$pdf->Image($image_file, $x = 15, $y = 70, $w = $ancho, $h = $alto, '', '', '', false, 300, '', false, false, 0);
$image_file_ubi = "$ruta_imagen_ubi";
// Dimensiones de la imagen
$ancho_ubi = 180.2; // Ancho en mm
$alto_ubi = 50.4; // Alto en mm

//estilos css


//$pdf->Image($image_file, $x = 15, $y = 15, $w = 180, $h = 0, '', '', '', false, 300, '', false, false, 0);

//echo $perfil;



$data_auto='
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}
.carta {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
}
p {
    margin-bottom: 10px;
}
.firmado {
    margin-top: 20px;
    text-align: right;
}
</style>


<table class="customTable">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">AUTORIZACIÓN</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="12" style="border: 1px solid black; text-align: justify; margin-left: 50px; margin-right: 50px">
             <div class="carta">
             <p><strong>#Yo '.$row['nombres'].'</strong></p>
             <p>Identificado (a) con cédula de ciudadanía No. <strong>'.$row['cedula'].'</strong></p>
             <p>Expedida en: <strong>Bogotá D.C</strong></p>
             <p>Hago constar de manera libre y voluntaria que la información procesada en el presente estudio, obedece a la verdad y <strong>AUTORIZO</strong> plenamente a la empresa <strong>GRUPO DE TAREAS EMPRESARIALES</strong> con NIT <strong>830.142.258-3</strong> para realizar VERIFICACIÓN ACADÉMICA, VERIFICACIÓN JUDICIAL, CENTRAL DE RIESGOS LEY 1266 y LEY 1581 del 2012 habeas data. Para tomar las pruebas necesarias y suficientes, a fin de establecer la veracidad de la información suministrada, para que en el momento que se haga necesaria se utilice como prueba. Contemplando en el <strong>DECRETO 1266 DE 2008</strong>.</p>
             
         </div>
             
        </td>
    </tr>
    <tr>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Direccion</td>
    <td colspan="3" style="border: 1px solid black;">' . $row['direccion'] . '</td>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Barrio </td>
    <td colspan="3" style="border: 1px solid black;">' . $row['barrio'] . '</td>

    </tr>
    <tr>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Localidad:</td>
    <td colspan="3" style="border: 1px solid black;">' . $row['localidad'] . '</td>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Teléfono Fijo: </td>
    <td colspan="3" style="border: 1px solid black;">' . $row['telefono'] . '</td>

    </tr>
    <tr>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Celular:</td>
    <td colspan="3" style="border: 1px solid black;">' . $row['celular'] . '</td>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Correo electronico </td>
    <td colspan="3" style="border: 1px solid black;">' . $row['correo'] . '</td>

    </tr>
    <tr>
        <td colspan="12" style="border: 1px solid black; text-align: justify;">
        <img src="' . $ruta_firma . '" alt="Logo"  style="border: 2px solid black; height: 150px; width: 1006px;">
        
        </td>
    </tr>
</tbody>
</table>';

//fila  de foto de perfil
$fila_perfil = '
<table cellpadding="5" style="width: 100%; ">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="5%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">' . $perfil . '</td>
            <td width="90%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_auto . '
            
            </td>
            <td width="5%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"></td>
        </tr>
    </table>';








     $data_fotos_ubicacion = '
<table class="customTable">
 <thead>
     <tr>
         <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">UBICACIÓN EN TIEMPO REAL</th>
     </tr>
 </thead>
 <tbody>
     <tr>
         <td style="border: 1px solid black; text-align: center; width: 265px;">
             <img src="' . $ruta_imagen_ubi . '" alt="Ubicación" style="border: 2px solid black; height: 140px; width: 160px;">
         </td>
         <td style="border: 1px solid black; text-align: center; width: 265px;">
             <img src="' . $foto1 . '" alt="Foto Perfil" style="border: 2px solid black; height: 145px; width: 166px;">
         </td>
     </tr>
 </tbody>
</table>';


$containerStyle = 'width: 100%; margin-bottom: 0;'; // Estilo del contenedor

// Contenido HTML
$htmlContent = '
<div style="border: 2px solid rgb(175, 0, 0);">
    <table cellpadding="5" style="width: 100%; ">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">' . $logo . '</td>
            </tr>
           
    </table>
    '  . $fila_perfil .  $data_fotos_ubicacion. '
</div>
';


// Agregar el contenido HTML al PDF
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Limpiar cualquier salida anterior
ob_end_clean();

// Salida del PDF (descarga directa)
$pdf->Output('Fila_con_Columnas.pdf', 'I');
