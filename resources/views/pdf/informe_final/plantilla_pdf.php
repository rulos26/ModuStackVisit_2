<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Evaluado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        .header {
            border: 2px solid rgb(96, 196, 30);
            padding: 5px;
            margin-bottom: 10px;
        }
        /* Contenedor del logo - espacios minimizados al máximo con prioridad forzada */
        .logo-container {
            border: 1px solid rgb(175,0,0);
            text-align: center;
            padding: 0px !important;
            margin-bottom: 1px !important;
            margin-top: 0 !important;
        }
        .logo {
            max-width: 100%;
            height: auto;
            max-height: 103px;
        }
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
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <?php if (!empty($logo_b64)): ?>
                <img src="<?= $logo_b64 ?>" alt="Logo" class="logo">
            <?php else: ?>
                <span style="color: #888;">Logo no disponible</span>
            <?php endif; ?>
        </div>
        
        <?php if ($evaluado): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            <center>INFORMACIÓN PERSONAL</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Nombres</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['nombres'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Apellidos</td>
                        <td colspan="4" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['apellidos'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Tipo de Documento</td>
                        <td colspan="4" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['tipo_documento_nombre'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">No. Documento</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['id_cedula'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Lugar de expedición</td>
                        <td colspan="4" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['ciudad_nombre'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Edad</td>
                        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['edad'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Fecha de Nacimiento</td>
                        <td colspan="4" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['fecha_expedicion'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Lugar de Nacimiento</td>
                        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['lugar_nacimiento_municipio'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Grupo Sanguíneo</td>
                        <td colspan="1" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['rh_nombre'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estatura</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['estatura_nombre'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Peso</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['peso_kg'] ?? '') ?> kg</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estado Civil actual</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['estado_civil_nombre'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Hace cuánto tiempo</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['hacer_cuanto'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">N° de Hijos</td>
                        <td colspan="1" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['numero_hijos'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Dirección de Residencia</td>
                        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['direccion'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Localidad</td>
                        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['localidad'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Barrio</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['barrio'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Ciudad</td>
                        <td colspan="2" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['ciudad_nombre'] ?? '') ?></td>
                        <td colspan="2" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estrato</td>
                        <td colspan="1" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['estrato_nombre'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Teléfono Fijo</td>
                        <td colspan="4" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['telefono'] ?? '') ?></td>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Celular</td>
                        <td colspan="3" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['celular_1'] ?? '') ?>/<?= htmlspecialchars($evaluado['celular_2'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">E. Mail</td>
                        <td colspan="10" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['correo'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Cargo Actual</td>
                        <td colspan="10" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['cargo'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Observaciones</td>
                        <td colspan="10" style="border: 1px solid black;"><?= htmlspecialchars($evaluado['observacion'] ?? '') ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; color: red; font-weight: bold;">
                No se encontraron datos para la cédula: <?= htmlspecialchars($cedula) ?>
            </div>
        <?php endif; ?>

        <?php if ($camara_comercio): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            <center>CÁMARA DE COMERCIO</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Tiene Cámara de Comercio?</td>
                        <td colspan="6" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($camara_comercio['tiene_camara'] ?? 'N/A') ?></td>
                    </tr>
                    <?php 
                    $mostrar_datos = ($camara_comercio['tiene_camara'] ?? '') !== 'No';
                    ?>
                    <tr>
                        <td colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Nombre de la Empresa</td>
                        <td colspan="6" style="border: 1px solid black; text-align: center;">
                            <?= $mostrar_datos ? htmlspecialchars($camara_comercio['nombre'] ?: 'N/A') : 'N/A' ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Razón Social</td>
                        <td colspan="6" style="border: 1px solid black; text-align: center;">
                            <?= $mostrar_datos ? htmlspecialchars($camara_comercio['razon'] ?: 'N/A') : 'N/A' ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Actividad</td>
                        <td colspan="6" style="border: 1px solid black; text-align: center;">
                            <?= $mostrar_datos ? htmlspecialchars($camara_comercio['activdad'] ?: 'N/A') : 'N/A' ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Observación</td>
                        <td colspan="6" style="border: 1px solid black; text-align: center;">
                            <?= $mostrar_datos ? htmlspecialchars($camara_comercio['observacion'] ?: 'N/A') : 'N/A' ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>