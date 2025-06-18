<?php /* Plantilla PDF, copia exacta de informeplantilla_1.php */ ?>
<style>
    .customTable {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-family: Arial, sans-serif;
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
</style>
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
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Dirección</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['direccion'] ?? '') ?></td>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Barrio</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['barrio'] ?? '') ?></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Localidad</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['localidad'] ?? '') ?></td>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Teléfono Fijo</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['telefono'] ?? '') ?></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Celular</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['celular'] ?? '') ?></td>
            <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Correo electrónico</td>
            <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row1['correo'] ?? '') ?></td>
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
<!-- Puedes agregar aquí más tablas o secciones según el informe original -->