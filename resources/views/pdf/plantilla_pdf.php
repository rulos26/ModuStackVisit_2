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
                        <p>Expedida en: <strong>Bogotá D.C</strong></p>
                        <p><?= nl2br(htmlspecialchars($row1['autorizacion'] ?? '')) ?></p>
                        <p>Hago constar de manera libre y voluntaria que la información procesada en el
                            presente estudio, obedece a la verdad y AUTORIZO plenamente a la empresa
                            GRUPO DE TAREAS EMPRESARIALES con NIT 830.142.258-3 para realizar
                            VERIFICACIÓN ACADÉMICA, VERIFICACIÓN JUDICIAL, CENTRAL DE RIESGOS
                            LEY 1266 y LEY 1581 del 2012 habeas data. Para tomar las pruebas necesarias y
                            suficientes, a fin de establecer la veracidad de la información suministrada, para que
                            en el momento que se haga necesaria se utilice como prueba. Contemplando en el
                            DECRETO 1266 DE 2008. </p>
                        <p> Legislación adicional solicitada (SPOA - SIJUF y centrales de riesgo):
                            La observación hace referencia a la necesidad de incorporar la autorización de consulta ante la Fiscalía
                            General de la Nación (SPOA - SIJUF) y la autorización para consulta en centrales de riesgo, tal como lo
                            exigen las leyes 1266 de 2008, 1581 de 2012 y 172 de 2014.
                            Adjuntamos en este correo el texto sugerido que puede incorporarse tal cual en el formato PDF, de
                            manera que se cumpla con los requerimientos legales.
                            "autorizo la visita domiciliaria y manifiesto de manera libre, expresa e informada que autorizo
                            plenamente a la empresa GRUPO DE TAREAS EMPRESARIALES, con NIT 830.142.258-3, para realizar
                            la verificación de antecedentes judiciales, penales, disciplinarios y fiscales ante las autoridades
                            competentes, incluyendo la Fiscalía General de la Nación mediante consulta en el sistema SPOA – SIJUF,
                            así como la verificación académica, laboral, domiciliaria y la consulta en centrales de información
                            financiera y crediticia (Data crédito, CIFIN u otras), de conformidad con lo establecido en el artículo 13
                            ley 1581 del 2012, la Ley 172 de 2014, Ley 1266 de 2008, Ley 1581 de 2012 y el Decreto 1266 de 2008,
                            Para tomar las pruebas necesarias y suficientes, a fin de establecer la veracidad de la información
                            suministrada, para que en el momento que se haga necesaria se utilice como prueba. Contemplado en el
                            DECRETO 1266 de 2008.</p>
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