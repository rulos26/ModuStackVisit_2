<?php /* Plantilla PDF, copia exacta de informeplantilla_1.php */ ?>
<?php if (!isset($logo)) {
    $logo = '<img src="public/images/header.jpg" alt="Logo" style="width: 1107px; height:206px">';
} ?>
<style>
    .customTable {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-family: Arial, sans-serif;
        font-size: 14px !important;
    }

    .customTable th,
    .customTable td {
        border: 1px solid #222;
        padding: 8px;
    }

    .customTable th {
        background: #ABABAB;
        font-weight: bold;
        text-align: center;
    }

    .carta {
        padding: 10px;
    }

    .firmado {
        margin-top: 20px;
        text-align: right;
    }

    .contenedor-rojo {
        border: 2px solid rgb(175, 0, 0) !important;
        padding: 4px !important;
        margin-top: 2px !important;
        margin-bottom: 2px !important;
        margin-left: 2px !important;
        margin-right: 2px !important;
    }
</style>
<div class="contenedor-rojo">
    <table cellpadding="5" style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td width="100%" style="border: 1px solid rgb(175,0,0); text-align: center;">
                <?php if (!empty($logo_b64)): ?>
                    <img src="<?= $logo_b64 ?>" alt="Logo" style=" width: 100%; height:103px;">
                <?php else: ?>
                    <span style="color: #888;">Logo no disponible</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <table class="customTable">
        <thead>
            <tr>
                <th colspan="12" style="font-weight: bold;  background-color: #ABABAB; border: 1px solid black; text-align: center;">AUTORIZACIÓN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="12" style="border: 1px solid black; text-align: justify;">
                    <div class="carta">
                        <p><strong>#Yo <?= htmlspecialchars($row1['nombres'] ?? '') ?></strong></p>
                        <p>Identificado (a) con cédula de ciudadanía No. <strong><?= htmlspecialchars($row1['cedula'] ?? '') ?></strong></p>
                        <p>Expedida en: <strong>Bogotá D.C</strong> Fecha: <strong><?= htmlspecialchars($row1['fecha'] ?? '') ?></strong></p><strong><?= htmlspecialchars($row1['cedula'] ?? '') ?></strong></p></p>
                        <!-- <p></p> -->
                        <p>Manifiesto de manera libre, expresa e informada que autorizo plenamente a la empresa GRUPO DE
TAREAS EMPRESARIALÉS, identificada con NIT 830142.258-3, para llevar a cabo la visita|
domiciliaria, entrevista y verificación de datos. Autorizo la consulta y entrega de la información que
repose ante la Fiscalía General de la Nación (SPOA - SIJUF) asi como las anotaciones y
|antecedentes judiciales y penales, conforme a lo establecido en el artículo 13 de la Ley 1581 de 2012.
Autorizo la consulta en centrales de riesgo, de acuerdo con lo dispuesto en las Leyes Estatutarias
1712 de 2014, 1266 de 2008 y 1581 de 2012. Finalmente, autorizo la verificación de mi historial laboral,
historial académico y referencias personales.</p>
                        <p></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Dirección</td>
                <td colspan="3" style="border: 1px solid black;"><?= !empty($row1['direccion']) ? htmlspecialchars($row1['direccion']) : 'N/A' ?></td>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Barrio</td>
                <td colspan="3" style="border: 1px solid black;"><?= !empty($row1['barrio']) ? htmlspecialchars($row1['barrio']) : 'N/A' ?></td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Localidad</td>
                <td colspan="3" style="border: 1px solid black;"><?= !empty($row1['localidad']) ? htmlspecialchars($row1['localidad']) : 'N/A' ?></td>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Teléfono Fijo</td>
                <td colspan="3" style="border: 1px solid black;"><?= !empty($row1['telefono']) ? htmlspecialchars($row1['telefono']) : 'N/A' ?></td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Celular</td>
                <td colspan="3" style="border: 1px solid black;"><?= !empty($row1['celular']) ? htmlspecialchars($row1['celular']) : 'N/A' ?></td>
                <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Correo electrónico</td>
                <td colspan="3" style="border: 1px solid black;">
                    <?php
                    $correo = $row1['correo'] ?? '';
                    if (!empty($correo)) {
                        $atPos = strpos($correo, '@');
                        if ($atPos !== false && $atPos >= 16) {
                            echo htmlspecialchars(substr($correo, 0, $atPos)) . '<br>@' . htmlspecialchars(substr($correo, $atPos + 1));
                        } else {
                            echo htmlspecialchars($correo);
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="12" style="text-align: center; padding: 16px 0;">
                    <?php if (!empty($img_firma_b64)): ?>
                        <img src="<?= $img_firma_b64 ?>" alt="Firma" style="max-width: 350px; max-height: 120px; border: 1px solid #888; border-radius: 6px;">
                    <?php else: ?>
                        <span style="color: #888;">Firma no disponible</span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="customTable">
        <thead>
            <tr>
                <th colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                    UBICACIÓN EN TIEMPO REAL
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 50%;">
                    <?php if (!empty($img_ubicacion_b64)): ?>
                        <img src="<?= $img_ubicacion_b64 ?>" alt="Ubicación" style="border: 2px solid black; height: 140px; width: 160px;">
                    <?php else: ?>
                        <span style="color: #888;">Ubicación no disponible</span>
                    <?php endif; ?>
                </td>
                <td style="border: 1px solid black; text-align: center; width: 50%;">
                    <?php if (!empty($img_perfil_b64)): ?>
                        <img src="<?= $img_perfil_b64 ?>" alt="Foto Perfil" style="border: 2px solid black; height: 145px; width: 166px;">
                    <?php else: ?>
                        <span style="color: #888;">Foto de perfil no disponible</span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Puedes agregar aquí más tablas o secciones según el informe original -->