<?php
require_once('../../../../../librery/tcpdf.php');
include '../../../../../conn/conexion.php';
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
$foto1;
$foto2;
$foto3;
$foto3;
$foto4;
$foto5;
$foto6;
$foto7;
$foto8;
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
$image_file = "$ruta_imagen"; // Ruta de tu imagen
// Dimensiones de la imagen
$ancho = 30.2; // Ancho en mm
$alto = 41.4; // Alto en mm

// Agregar la imagen con dimensiones especificadas
$perfil = '<img src="' . $ruta_imagen . '" alt="Logo"  style="border: 2px solid black; height: 177px; width: 200px;">'; // Cambiar la ruta según la ubicación del logo

//$pdf->Image($image_file, $x = 15, $y = 70, $w = $ancho, $h = $alto, '', '', '', false, 300, '', false, false, 0);
$image_file_ubi = "$ruta_imagen_ubi";
// Dimensiones de la imagen
$ancho_ubi = 180.2; // Ancho en mm
$alto_ubi = 50.4; // Alto en mm

//estilos css


//$pdf->Image($image_file, $x = 15, $y = 15, $w = 180, $h = 0, '', '', '', false, 300, '', false, false, 0);

//echo $perfil;
$title = '<h1 style="text-align: center;">INFORME VISITA DOMICILIARIA</h1>';
$info = '
<table cellpadding="5" style="width: 100%;">
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">Código: PSI-FR-11</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">Versión: 03</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">Fecha de vigencia: Enero 2024</td>
    </tr>
</table>';


// Nueva fila con información adicional
$fechaEmpresa = '
<table cellpadding="5" style="width: 100%; font-size: 12px;">
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td width="40%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); "></td>
        <td width="20%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"></td>
        <td width="40%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');" text-align: right;>EL NOMBRE DE SU EMPRESA</td>
    </tr>
</table>';
$info_vistador = '
<table cellpadding="5" style="width: 100%;">
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); font-weight: bold; text-align: right;">' . $row['nombres'] . '</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); font-weight: bold; text-align: right;">' . $row['cargo'] . '</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); font-weight: bold; text-align: right;">' . $row['id_cedula'] . '</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); font-weight: bold; text-align: right;">' . $row['edad'] . ' años</td>
    </tr>
    <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
        <td style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . '); font-weight: bold; text-align: right;">Fecha visita: ' . $fecha_actual . ' </td>
    </tr>
</table>';


//fila  de foto de perfil
$fila_perfil = '
<table cellpadding="5" style="width: 100%; ">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="40%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">' . $perfil . '</td>
            <td width="20%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"></td>
            <td width="40%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">' . $info_vistador . '</td>
        </tr>
    </table>';

//data informacion pesonal 
$data_info_personal = ' 


<table class="customTable" style="border: 1px solid black; ">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;" > <center>INFORMACIÓN PERSONAL</center></th>

    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Nombres </td>
        <td colspan="2" style="border: 1px solid black;">' . $row['nombres'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Apellidos </td>
        <td colspan="4" style="border: 1px solid black;">' . $row['apellidos'] . '</td>


    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Tipo de Documento</td>
        <td colspan="4" style="border: 1px solid black;">' . $row['tipo_documento_nombre'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >No. Documento </td>
        <td colspan="2" style="border: 1px solid black;">' . $row['id_cedula'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Lugar de expedición</td>
        <td colspan="4" style="border: 1px solid black;">' . $row['ciudad_nombre'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Edad </td>
        <td colspan="3" style="border: 1px solid black;">' . $row['edad'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Fecha de Nacimiento</td>
        <td colspan="4" style="border: 1px solid black;">' . $row['fecha_expedicion'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Lugar de Nacimiento</td>
        <td colspan="3" style="border: 1px solid black;">' . $row['lugar_nacimiento_municipio'] . '</td>


    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Grupo Sanguíneo </td>
        <td colspan="1" style="border: 1px solid black;">' . $row['rh_nombre'] . '</td>
        <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Estatura</td>
        <td colspan="2" style="border: 1px solid black;">' . $row['estatura_nombre'] . '</td>
        <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Peso</td>
        <td colspan="2" style="border: 1px solid black;">' . $row['estatura_nombre'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Estado Civil actual </td>
        <td colspan="2" style="border: 1px solid black;">' . $row['estado_civil_nombre'] . '</td>
        <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Hace cuánto tiempo </td>
        <td colspan="2" style="border: 1px solid black;">' . $row['hacer_cuanto'] . '</td> 
        <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >N° de Hijos </td>
        <td colspan="1" style="border: 1px solid black;">' . $row['numero_hijos'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Dirección de Residencia</td>
        <td colspan="3" style="border: 1px solid black;">' . $row['direccion'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Localidad </td>
        <td colspan="3" style="border: 1px solid black;">' . $row['localidad'] . '</td>
       

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Barrio</td>
        <td colspan="2" style="border: 1px solid black;">' . $row['barrio'] . '</td>
         <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Ciudad </td>
        <td colspan="2" style="border: 1px solid black;">' . $row['ciudad_nombre'] . '</td>
        <td colspan="2" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Estrato </td>
        <td colspan="1" style="border: 1px solid black;">' . $row['estrato_nombre'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Teléfono Fijo</td>
        <td colspan="4" style="border: 1px solid black;">' . $row['telefono'] . '</td>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Celular </td>
        <td colspan="3" style="border: 1px solid black;">' . $row['celular_1'] . '/' . $row['celular_2'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >E. Mail </td>
        <td colspan="10" style="border: 1px solid black;">' . $row['correo'] . '</td>


    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Cargo Actual</td>
        <td colspan="10" style="border: 1px solid black;">' . $row['cargo'] . '</td>


    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; " >Observaciones</td>
        <td colspan="10" style="border: 1px solid black;">' . $row['observacion'] . '</td>


    </tr>
</tbody>
</table>';
//fila informacion personal
$info_personal = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_info_personal . '</td>
         </tr>
    </table>';

$data_camara = '<table class="customTable">
 <thead>
     <tr>
         <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">Camara de Comercio</th>

     </tr>
 </thead>
 <tbody>
     <tr>
         <td colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Tiene Camara de Comercio?</td>
         <td colspan="6" style="border: 1px solid black;text-align: center; ">' . $data_row['tiene_camara'] . '</td>

     </tr>
     <tr>
         <td colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; ">Nombre de la Empresa</td>
         <td colspan="6" style="border: 1px solid black; text-align: center;">' . $data_row['nombre'] . '</td>
         </tr>
     <tr>
          <td colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; ">Razon social</td>
         <td colspan="6" style="border: 1px solid black; text-align: center;">' . $data_row['razon'] . '</td>
   
     </tr>
     <tr>
         <td colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; ">Actividad</td>
         <td colspan="6" style="border: 1px solid black; text-align: center;">' . $data_row['activdad'] . '</td>

     </tr>
     <tr>
         <td colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; ">observacion</td>
         <td colspan="6" style="border: 1px solid black; text-align: center;">' . $data_row['observacion'] . '</td>

     </tr>
 </tbody>
</table>';
//informacion cmara de comercio    
$info_camara = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_camara . '</td>
         </tr>
    </table>';

$data_salud = '<table class="customTable">
<thead>
    <tr>
        <th colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;"> Estado de salud del Aspirante</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Estado de salud</td>
        <td colspan="3" style="border: 1px solid black; text-align: center;">' . $fila_salud['nombre_estado_salud'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Padece algún tipo de enfermedad?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['nombre_tipo_enfermedad'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;" > ' . $fila_salud['tipo_enfermedad_cual'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Tiene alguna limitación física?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['nombre_limitacion_fisica'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['limitacion_fisica_cual'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Toma algún tipo de medicamento ?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;" >' . $fila_salud['nombre_tipo_medicamento'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['tipo_medicamento_cual'] . '</td>
    </tr>
   
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Ingiere alcohol?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['nombre_ingiere_alcohol'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $fila_salud['ingiere_alcohol_cual'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Fuma?</td>
        <td colspan="3" style="border: 1px solid black; text-align: center;">' . $fila_salud['nombre_fuma'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Observaciones</td>
        <td colspan="3" style="border: 1px solid black; text-align: center;">' . $fila_salud['observacion'] . '</td>
    </tr>
</tbody>
</table>
';
//informacion estado de salud 
$info_salud = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_salud . '</td>
         </tr>
    </table>';

$data_familia = '
    <table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> COMPOSICION FAMILIAR (con quién vive, y familia de origen)</th>
        </tr>
        <tr>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nombre</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Parentesco</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Edad</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Ocupación</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Teléfono</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">Conviven</th>
            <th style="border: 1px solid black; text-align: center; font-weight: bold;">observacion</th>
            
        </tr>
    </thead>
    <tbody>';

if ($familia_data->num_rows > 0) {
    while ($familia_row = $familia_data->fetch_assoc()) {
        $data_familia .= '<tr>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['nombre'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['nombre_parentesco'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['edad'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['nombre_ocupacion'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['telefono'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['nombre_parametro'] . '</td>';
        $data_familia .= '<td style="border: 1px solid black; text-align: center;">' . $familia_row['observacion'] . '</td>';
        $data_familia .= '</tr>';
    }
} else {
    $data_familia .= '<tr>';
    $data_familia .= '<td style="border: 1px solid black; text-align: center;"> lo sentimos el aspirante no tien familia</td>';
    $data_familia .= '</tr>';
}


$data_familia .= '</tbody>
    </table>';


//info_familia
$info_familia = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_familia . '</td>
         </tr>
    </table>';

$data_pareja = '
 <table class="table table-bordered">
 <thead>
     <tr>
         <th colspan="7" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> Información de la pareja (Conyuge, compañera sentimental)</th>
     </tr>
     <tr>
        
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Cédula</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Tipo Documento</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Cédula Expedida</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nombres</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Edad</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Género</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nivel Académico</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Actividad</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Empresa</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Antigüedad</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Dirección Empresa</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Teléfono 1</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Teléfono 2</th>
         <th style="border: 1px solid black; text-align: center; font-weight: bold;">Vive Candidato</th>
         
     </tr>
 </thead>
 <tbody>';

if ($pareja_data->num_rows > 0) {
    while ($pareja_row = $pareja_data->fetch_assoc()) {
        $data_pareja .= '<tr>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['cedula'] . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['tipo_documento_nombre'] . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $row['ciudad_nombre']  . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['nombres'] . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['edad'] . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['nombre_genero'] . '</td>';
        $data_pareja .= '<td style="border: 1px solid black; text-align: center;">' . $pareja_row['nombre_nivel_academico'] . '</td>';
        $data_pareja .= '</tr>';
    }
} else {
    $data_pareja .= '<tr>';
    $data_pareja .= '<td style="border: 1px solid black; text-align: center;"> lo sentimos el aspirante no tien pareja</td>';
    $data_pareja .= '</tr>';
}


$data_pareja .= '</tbody>
 </table>';
//informacion pareja
$info_pareja  = '
 <table cellpadding="5" style="width: 100%;">
         <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
             <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_pareja . '</td>
          </tr>
     </table>';

//data vvienda
$data_vivienda = '  <table class="customTable">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">TIPO DE VIVENDA</th>

    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">tipo de Vivienda</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_vivienda['nombre_tipo_vivienda'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Sector</td>
        <td colspan="2" style="border: 1px solid black;"> ' . $filas_vivienda['nombre_sector'] . '</td>
    
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">propietario</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_vivienda['nombre_propiedad'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Número de Familias que habitan la vivienda</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_vivienda['numero_de_familia'] . '</td>
  
        </tr>
      <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Número de hogares habitan en la vivienda</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_vivienda['personas_nucleo_familiar'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Tiempo en años de Residencia en el Sector</td>
        <td colspan="2" style="border: 1px solid black;"> ' . $filas_vivienda['tiempo_sector'] . '</td>
   
        </tr>
   
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Número de Pisos de la Vivienda:</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_vivienda['numero_de_pisos'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Estado de la vivienda:</td>
        <td colspan="2" style="border: 1px solid black;">' . $row_viv['nombre'] . '</td>
   
        </tr>
  
</tbody>
</table>';

//informacion vivienda
$info_vivienda  = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_vivienda . '</td>
         </tr>
    </table>';

$data_inventario = ' <table class="customTable">
  <thead>
      <tr>
          <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">INVENTARIO DE ENSERES</th>
  
      </tr>
  </thead>
  <tbody>
      <tr>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Televisor</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['televisor_nombre_cant'] . '</td>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">D.V.D</td>
          <td colspan="2" style="border: 1px solid black;"> ' . $filas_invetario['dvd_nombre_cant'] . '</td>
    
      </tr>
       <tr>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Teatro en Casa</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['teatro_casa_nombre_cant'] . '</td>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Equipo de Sonido</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['equipo_sonido_nombre_cant'] . '</td>
    
          </tr>
      
      <tr>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Computador</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['computador_nombre_cant'] . '</td>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Impresora</td>
          <td colspan="2" style="border: 1px solid black;"> ' . $filas_invetario['impresora_nombre_cant'] . '</td>
      </tr>
       <tr>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Movil</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['movil_nombre_cant'] . '</td>
          <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Estufa</td>
          <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['estufa_nombre_cant'] . '</td>
   
          </tr>
       <tr>
      <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Nevera</td>
      <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['nevera_nombre_cant'] . '</td>
      <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Lavadora</td>
      <td colspan="2" style="border: 1px solid black;">' . $filas_invetario['lavadora_nombre_cant'] . '</td>
    
      </tr>
<tr>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Microondas</td>
<td colspan="2" style="border: 1px solid black;">' . $filas_invetario['microondas_nombre_cant'] . '</td>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Moto</td>
<td colspan="2" style="border: 1px solid black;">' . $filas_invetario['moto_nombre_cant'] . '</td>

</tr>
<tr>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Carro</td>
<td colspan="8" style="border: 1px solid black;">' . $filas_invetario['carro_nombre_cant'] . '</td>
</tr>
<tr>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">observaciones</td>
<td colspan="8" style="border: 1px solid black;">' . $filas_invetario['observacion'] . '</td>
</tr>
  </tbody>
  </table>';

//informacion invenario
$info_inventario = '
 <table cellpadding="5" style="width: 100%;">
         <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
             <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_inventario . '</td>
          </tr>
     </table> <br> <br>';
// informacion servicos publicos
$data_servicios = ' <table class="customTable">
 <thead>
     <tr>
         <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">SERVICIOS PÚBLICOS Y OTROS</th>
 
     </tr>
 </thead>
 <tbody>
     <tr>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Agua</td>
         <td colspan="2" style="border: 1px solid black;">' . $filas_servicios['nombre_agua'] . '</td>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Luz</td>
         <td colspan="2" style="border: 1px solid black;"> ' . $filas_servicios['nombre_luz'] . '</td>
  
     </tr>
     <tr>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Gas</td>
         <td colspan="2" style="border: 1px solid black;">' . $filas_servicios['nombre_gas'] . '</td>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Alcantarillado</td>
         <td colspan="2" style="border: 1px solid black;">' . $filas_servicios['nombre_alcantarillado'] . '</td>
   
     
         </tr>
     <tr>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Internet</td>
         <td colspan="2" style="border: 1px solid black;">' . $filas_servicios['nombre_internet'] . '</td>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Administración</td>
         <td colspan="2" style="border: 1px solid black;"> ' . $filas_servicios['nombre_administracion'] . '</td>
   
         </tr>
     <tr>
         <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Parqueadero</td>
         <td colspan="8" style="border: 1px solid black;">' . $filas_servicios['nombre_parqueadero'] . '</td>
     </tr>
     
<tr>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">observaciones</td>
<td colspan="8" style="border: 1px solid black;">' . $filas_servicios['observacion'] . '</td>
</tr>
 </tbody>
 </table>';
//informacion de servicos publublicos
$info_servicios = '
 <table cellpadding="5" style="width: 100%;">
         <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
             <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' .  $data_servicios . '</td>
          </tr>
     </table>';
$data_patrimonio = ' <table class="customTable">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">PATRIMONIO</th>

    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Valor Vivienda</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_patrimonio['valor_vivienda'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Dirección</td>
        <td colspan="2" style="border: 1px solid black;"> ' . $filas_patrimonio['direccion'] . '</td>
 
    </tr>
    
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Vehiculo</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_patrimonio['id_vehiculo'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Marca</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_patrimonio['id_marca'] . '</td>
 
        </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Modelo</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_patrimonio['id_modelo'] . '</td>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Ahorro (CDT, Inversiones)</td>
        <td colspan="2" style="border: 1px solid black;">' . $filas_patrimonio['id_ahorro'] . '</td>
 
        </tr>
    <tr>
    <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Otros</td>
    <td colspan="8" style="border: 1px solid black;"> ' . $filas_patrimonio['otros'] . '</td>
</tr>

<tr>
<td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">observaciones</td>
<td colspan="8" style="border: 1px solid black;">' . $filas_patrimonio['observacion'] . '</td>
</tr>
</tbody>
</table>';

//informacion patrimonio
$info_patrimonio = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_patrimonio . '</td>
         </tr>
    </table>';

$data_cuentas = '
    <table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> CUENTAS BANCARIAS</th>
        </tr>
        <tr>           
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">ENTIDAD</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">TIPO CUENTA</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">CIUDAD</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">OBSERVACIONES</th>
          
        </tr>
    </thead>
    <tbody>';

if ($cuenta_data->num_rows > 0) {
    while ($cuentas_row = $cuenta_data->fetch_assoc()) {
        $data_cuentas .= '<tr>';
        $data_cuentas .= '<td style="border: 1px solid black; text-align: center;">' . $cuentas_row['id_entidad'] . '</td>';
        $data_cuentas .= '<td style="border: 1px solid black; text-align: center;">' . $cuentas_row['id_tipo_cuenta'] . '</td>';
        $data_cuentas .= '<td style="border: 1px solid black; text-align: center;">' . $cuentas_row['ciudad']  . '</td>';
        $data_cuentas .= '<td style="border: 1px solid black; text-align: center;">' . $cuentas_row['observaciones'] . '</td>';
        $data_cuentas .= '</tr>';
    }
} else {
    $data_cuentas .= '<tr>';
    $data_cuentas .= '<td colspan="4" style="border: 1px solid black; text-align: center;">Lo sentimos, el aspirante no tiene pareja.</td>';
    $data_cuentas .= '</tr>';
}

$data_cuentas .= '</tbody>
    </table>';

//info cunentas
$info_cuentas = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_cuentas . '</td>
         </tr>
    </table>';
// dat pasivo
$data_pasivos = '<table class="table table-bordered">
<thead>
    <tr>
        <th colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> PASIVOS</th>
    </tr>
    <tr>           
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">PRODUCTOS</th> 
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">ENTIDAD</th>
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">TIPO INVERSION</th>
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">CIUDAD</th>
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">DEUDA</th> 
    <th style="border: 1px solid black; text-align: center; font-weight: bold;">CUOTA MES</th> 
           
    </tr>
</thead>
<tbody>';

if ($data_pasivo->num_rows > 0) {
    while ($pasivo_row = $data_pasivo->fetch_assoc()) {
        $data_pasivos .= '<tr>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['item'] . '</td>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['id_entidad'] . '</td>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['id_tipo_inversion']  . '</td>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['municipio']  . '</td>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['deuda'] . '</td>';
        $data_pasivos .= '<td style="border: 1px solid black; text-align: center;">' . $pasivo_row['cuota_mes'] . '</td>';
        $data_pasivos .= '</tr>';
    }
} else {
    $data_pasivos .= '<tr>';
    $data_pasivos .= '<td colspan="6" style="border: 1px solid black; text-align: center;">Lo sentimos, el aspirante no tiene pareja.</td>';
    $data_pasivos .= '</tr>';
}

$data_pasivos .= '</tbody>
</table>';




//informacion pasivos 
$info_pasivo = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_pasivos . '</td>
         </tr>
    </table> ';

//data aportantes
$data_aportante = '<table class="table table-bordered">
<thead>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> PERSONAS QUE APORTAN ECONOMICAMENTE AL HOGAR</th>
    </tr>
    <tr>           
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">NOMBRE</th> 
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">VALOR</th>
                     
    </tr>
</thead>
<tbody>';

if ($data_apor->num_rows > 0) {
    while ($aportante_row = $data_apor->fetch_assoc()) {
        $data_aportante .= '<tr>';
        $data_aportante .= '<td style="border: 1px solid black; text-align: center;">' . $aportante_row['nombre'] . '</td>';
        $data_aportante .= '<td style="border: 1px solid black; text-align: center;">' . $aportante_row['valor'] . '</td>';
        $data_aportante .= '</tr>';
    }
} else {
    $data_aportante .= '<tr>';
    $data_aportante .= '<td colspan="6" style="border: 1px solid black; text-align: center;">Lo sentimos, el aspirante no tiene pareja.</td>';
    $data_aportante .= '</tr>';
}

$data_aportante .= '</tbody>
</table>';


//informacion aportantes
$info_aportantes = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_aportante . '</td>
         </tr>
    </table> ';



//informacion  dat credito
$info_credito = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_pasivos . '</td>
         </tr>
    </table>';
//dat_ingreos
$data_ingreso = '  <table class="customTable">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">INGRESOS MENSUALES DEL NUCLEO FAMILIAR</th>

    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">SALARIO</td>
        <td colspan="8" style="border: 1px solid black;">' . $ingresos_row['salario_val'] . '</td>

    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">PENSIÓN</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $ingresos_row['pension_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">ARRIENDOS</td>
        <td colspan="8" style="border: 1px solid black;">' . $ingresos_row['arriendo_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">TRABAJO INDEPENDIENTE</td>
        <td colspan="8" style="border: 1px solid black;">' . $ingresos_row['trabajo_independiente_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">OTROS</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $ingresos_row['otros_val'] . '</td>
    </tr>
    
</tbody>
</table>';
//ingreos
$info_ingreso = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_ingreso . '</td>
         </tr>
    </table>';
//data gastos 
$data_gasto =  '  <table class="customTable">
<thead>
    <tr>
        <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">GASTOS O DEUDAS MENSUALES DEL NUCLEO FAMILIAR</th>

    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">ALIMENTACIÓN</td>
        <td colspan="8" style="border: 1px solid black;">' . $gastos_row['alimentacion_val'] . '</td>

    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">EDUCACIÓN</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $gastos_row['educacion_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">SALUD</td>
        <td colspan="8" style="border: 1px solid black;">' . $gastos_row['salud_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">RECREACIÓN</td>
        <td colspan="8" style="border: 1px solid black;">' . $gastos_row['recreacion_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">CUOT/CREDITOS</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $gastos_row['cuota_creditos_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">ARRIENDO</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $gastos_row['arriendo_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">SERVICIOS PUBLICOS</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $gastos_row['servicios_publicos_val'] . '</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">OTROS</td>
        <td colspan="8" style="border: 1px solid black;"> ' . $gastos_row['otros_val'] . '</td>
    </tr>
    
</tbody>
</table>';
//gasto
$info_gasto = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_gasto . '</td>
         </tr>
    </table>';

$data_acade = '<table class="table table-bordered">
<thead>
    <tr>
        <th colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> EXPERIENCIA ACADEMICA</th>
    </tr>
    <tr>           
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">CENTRO EDUCATIVO</th> 
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">JORNADA</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">CIUDAD</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">AÑO</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">TÍTULO OBTENIDO</th>
        <th style="border: 1px solid black; text-align: center; font-weight: bold;">RESULTADO</th>
                    
    </tr>
</thead>
<tbody>';

if ($estudios_data->num_rows > 0) {
    while ($estudios_row = $estudios_data->fetch_assoc()) {
        $data_acade .= '<tr>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['centro_estudios'] . '</td>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['id_jornada'] . '</td>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['municipio'] . '</td>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['anno'] . '</td>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['titulos'] . '</td>';
        $data_acade .= '<td style="border: 1px solid black; text-align: center;">' . $estudios_row['id_resultado'] . '</td>';
        $data_acade .= '</tr>';
    }
} else {
    $data_acade .= '<tr>';
    $data_acade .= '<td colspan="6" style="border: 1px solid black; text-align: center;">Lo sentimos, el aspirante no tiene pareja.</td>';
    $data_acade .= '</tr>';
}

$data_acade .= '</tbody>
</table>';

$info_acade = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_acade . '</td>
         </tr>
    </table>';
//data judi
$data_judi =  '<table class="customTable">
<thead>
    <tr>
        <th colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">INFORMACIÓN JUDICIAL</th>
    </tr>
</thead>
<tbody> 
<tr>
<td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-decoration: underline; color: #890303;  ">ANTECEDENTES JUDICIALES Y DISCIPLINARIOS POLICÍA  Y CONTRALORÍA, PROCURADURÍA, LISTAS CLINTON, INTERPOL ORFAC</td>
<td colspan="3" style="border: 1px solid black; text-align: center;">' . $judi_row['revi_fiscal'] . '</td>
</tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Ha presentado denuncias o demandas a persona natural o persona juridica?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;">' . $judi_row['nombre_opcion1'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;">' . $judi_row['denuncias_desc'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Presenta procesos judiciales o disciplinarios en contra?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $judi_row['nombre_opcion2'] . '</td>
        <td colspan="2" style="border: 1px solid black;"> ' . $judi_row['procesos_judiciales_desc'] . '</td>

    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Ha sido privado de la libertad? (Policia, Fiscalia)</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $judi_row['nombre_opcion3'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $judi_row['preso_desc'] . '</td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Algún miembro de la familia ha sido de la libertad por algún delito?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;" >' . $judi_row['nombre_opcion4'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $judi_row['familia_detenido_desc'] . '</td>
    </tr>
   
    <tr>
        <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">¿Ha visitado centros penitenciarios?</td>
        <td colspan="1" style="border: 1px solid black; text-align: center;"> ' . $judi_row['nombre_opcion5'] . '</td>
        <td colspan="2" style="border: 1px solid black; text-align: center;"> ' . $judi_row['centros_penitenciarios_desc'] . '</td>
    </tr>
   
</tbody>
</table>
';


// info judicial
$info_judi = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_judi . '</td>
         </tr>
    </table>';

$data_exp_laboral = '<table class="table table-bordered">
 <thead>
     <tr>
         <th colspan="7" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;"> EXPERIENCIA LABORAL</th>
     </tr>
     <tr>           
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">EMPRESA</th> 
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">TIEMPO LABORADO</th>
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">CARGO DESEMPEÑADO</th>
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">SALARIO</th>
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">CONCEPTO EMITIDO</th>
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">NOMBRE CONTACTO</th>
     <th style="border: 1px solid black; text-align: center; font-weight: bold;">NÚMERO CONTACTO</th>

                     
     </tr>
 </thead>
 <tbody>';

if ($exp_laboral_data->num_rows > 0) {
    while ($exp_laboral_row = $exp_laboral_data->fetch_assoc()) {
        $data_exp_laboral .= '<tr>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['empresa'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['tiempo'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['cargo'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['salario'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['retiro'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['concepto'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['nombre'] . '</td>';
        $data_exp_laboral .= '<td style="border: 1px solid black; text-align: center;">' . $exp_laboral_row['numero'] . '</td>';

        $data_exp_laboral .= '</tr>';
    }
} else {
    $data_exp_laboral .= '<tr>';
    $data_exp_laboral .= '<td colspan="6" style="border: 1px solid black; text-align: center;">Lo sentimos, el aspirante no tiene pareja.</td>';
    $data_exp_laboral .= '</tr>';
}

$data_exp_laboral .= '</tbody>
 </table>';

$exp_laboral = '
 <table cellpadding="5" style="width: 100%;">
         <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
             <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_exp_laboral . '</td>
          </tr>
     </table>';

$data_concepto_final = '<table class="customTable">
 <thead>
     <tr>
         <th colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">CONCEPTO FINAL DEL PROFESIONAL O EVALUADOR</th>
     </tr>
 </thead>
 <tbody>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Actitud del evaluado y su grupo familiar</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;">' . $row_concepto_final['actitud'] . '</td>
     </tr>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Condiciones de vivienda</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['condiciones_vivienda'] . '</td>
    
     </tr>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Dinámica familiar</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['dinamica_familiar'] . '</td>
     </tr>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Condiciones socio económicas</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;" >' . $row_concepto_final['condiciones_economicas'] . '</td>
     </tr>
    
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Condiciones académicas</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['condiciones_academicas'] . '</td>
     </tr>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Evaluación de experiencia laboral</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['evaluacion_experiencia_laboral'] . '</td>
   
         </tr>
     <tr>
         <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Observaciones</td>
         <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['observaciones'] . '</td>
     </tr>
     <tr>
     <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">Concepto Final</td>
     <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['id_concepto_final'] . '</td>
    </tr>
    <tr>
    <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">NOMBRE EVALUADOR</td>
    <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['nombre_evaluador'] . '</td>
   </tr>
    <tr>
     <td colspan="3" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black;  ">CONCEPTO DE SEGURIDAD</td>
     <td colspan="3" style="border: 1px solid black; text-align: center;"> ' . $row_concepto_final['id_concepto_seguridad'] . '</td>
    </tr>
    
 </tbody>
 </table>
 ';
$concepto_final = '
 <table cellpadding="5" style="width: 100%;">
         <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
             <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_concepto_final . '</td>
          </tr>
     </table>';
$data_fotos_unicacion = '
<table class="customTable">
 <thead>
     <tr>
         <th colspan="6" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">UBICACIÓN EN TIEMPO REAL</th>
     </tr>
 </thead>
 <tbody>
     <tr>
         <td colspan="6" style="border: 1px solid black; text-align: center;">
         <img src="' . $image_file_ubi . '" alt="Logo"  style="border: 2px solid black; height: 270px; width: 1006px;">
         </td>
     </tr>
        
    
 </tbody>
 </table>

'; // Cambiar la ruta según la ubicación del logo';

$ubicacion_foto = '
<table cellpadding="5" style="width: 100%;">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_fotos_unicacion . '</td>
         </tr>
    </table> <br><br>' ;


    $data_evidencia ='
    <table class="customTable">
     <thead>
         <tr>
             <th colspan="24" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">UBICACIÓN EN TIEMPO REAL</th>
         </tr>
     </thead>
     <tbody>
         <tr>
                  
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto1 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 165px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto2 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto3 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
         </tr>
         <tr>
                  
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto4 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 165px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto5 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto6 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
         </tr>
            
         <tr>
                  
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto7 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 165px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto8 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
             <td  style="border: 1px solid black; text-align: center; 165px; width: 165px;">
             <img src="' . $foto8 . '" alt="Logo"  style="border: 2px solid black; height: 160px; width: 166px;">
             </td>
         </tr>
        
     </tbody>
     </table>
    
    ';
$evidencia_foto = '
    <table cellpadding="5" style="width: 100%;">
            <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
                <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');"> ' . $data_evidencia . '</td>
             </tr>
        </table>';

// Contenedor que alberga las dos tablas
$containerStyle = 'width: 100%; margin-bottom: 0;'; // Estilo del contenedor

// Contenido HTML
$htmlContent = '
<div style="border: 2px solid rgb(175, 0, 0);">
    <table cellpadding="5" style="width: 100%; ">
        <tr style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">
            <td width="100%" style="border: ' . $borderWidth . 'px solid rgb(' . implode(',', $borderColor) . ');">' . $logo . '</td>
            </tr>
           
    </table>
    ' . $fila_perfil . $info_personal . $info_camara . $info_salud . $info_familia . $info_pareja . $info_vivienda . $info_inventario . $info_servicios . $info_patrimonio . $info_cuentas . $info_pasivo . $info_aportantes . $info_ingreso . $info_gasto . $info_acade . $exp_laboral .  $info_judi . $concepto_final . $ubicacion_foto .$evidencia_foto. '
</div>
';


// Agregar el contenido HTML al PDF
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Salida del PDF (descarga directa)
$pdf->Output('Fila_con_Columnas.pdf', 'I');
