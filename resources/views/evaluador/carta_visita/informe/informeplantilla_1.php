<?php /* Plantilla visual para informe, usando tablas como en el original */ ?>
<style>
.customTable { width: 100%; border-collapse: collapse; margin: 20px 0; font-family: Arial, sans-serif; }
.customTable th, .customTable td { border: 1px solid #222; padding: 8px; }
.customTable th { background: #ABABAB; font-weight: bold; text-align: center; }
.carta { padding: 10px; }
.firmado { margin-top: 20px; text-align: right; }
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
                <p><strong>#Yo <?= htmlspecialchars($row['nombres'] ?? '') ?></strong></p>
                <p>Identificado (a) con cédula de ciudadanía No. <strong><?= htmlspecialchars($row['cedula'] ?? '') ?></strong></p>
                <p>Expedida en: <strong>Bogotá D.C</strong></p>
                <p><?= nl2br(htmlspecialchars($row['autorizacion'] ?? '')) ?></p>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Dirección</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['direccion'] ?? '') ?></td>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Barrio</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['barrio'] ?? '') ?></td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Localidad</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['localidad'] ?? '') ?></td>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Teléfono Fijo</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Celular</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['celular'] ?? '') ?></td>
        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Correo electrónico</td>
        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($row['correo'] ?? '') ?></td>
    </tr>
</tbody>
</table>
<!-- Puedes agregar aquí más tablas o secciones según el informe original --> 