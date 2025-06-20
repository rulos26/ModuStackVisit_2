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
                            CÁMARA DE COMERCIO
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

        <?php if ($estado_salud): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="6" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            Estado de Salud del Aspirante
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estado de salud</td>
                        <td colspan="3" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['nombre_estado_salud']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Padece algún tipo de enfermedad?</td>
                        <td colspan="1" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['tipo_enfermedad']) ?></td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['tipo_enfermedad_cual']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Tiene alguna limitación física?</td>
                        <td colspan="1" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['limitacion_fisica']) ?></td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['limitacion_fisica_cual']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Toma algún tipo de medicamento?</td>
                        <td colspan="1" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['tipo_medicamento']) ?></td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['tipo_medicamento_cual']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Ingiere alcohol?</td>
                        <td colspan="1" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['ingiere_alcohol']) ?></td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['ingiere_alcohol_cual']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">¿Fuma?</td>
                        <td colspan="3" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['fuma']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Observaciones</td>
                        <td colspan="3" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($estado_salud['observacion']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($composicion_familiar)): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="7" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            COMPOSICIÓN FAMILIAR (con quién vive, y familia de origen)
                        </th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Nombre</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Parentesco</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Edad</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Ocupación</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Teléfono</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Conviven</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($composicion_familiar as $familiar): ?>
                        <tr>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['nombre']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['nombre_parentesco']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['edad']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['nombre_ocupacion']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['telefono']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['nombre_parametro']) ?></td>
                            <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($familiar['observacion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="7" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            COMPOSICIÓN FAMILIAR (con quién vive, y familia de origen)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" style="border: 1px solid black; text-align: center;">
                            Lo sentimos, el aspirante no tiene familia registrada
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($informacion_pareja): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="14" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            Información de la Pareja (Cónyuge, compañera sentimental)
                        </th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Cédula</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Tipo Documento</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Cédula Expedida</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Nombres</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Edad</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Género</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Nivel Académico</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Actividad</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Empresa</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Antigüedad</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Dirección Empresa</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Teléfono 1</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Teléfono 2</th>
                        <th style="border: 1px solid black; text-align: center; font-weight: bold; background-color: #ABABAB;">Vive con Candidato</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['cedula']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['tipo_documento_nombre']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['cedula_expedida']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['nombres']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['edad']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['nombre_genero']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['nombre_nivel_academico']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['actividad']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['empresa']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['antiguedad']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['direccion_empresa']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['telefono_1']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['telefono_2']) ?></td>
                        <td style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($informacion_pareja['vive_candidato']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="14" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            Información de la Pareja (Cónyuge, compañera sentimental)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="14" style="border: 1px solid black; text-align: center;">
                            Lo sentimos, el aspirante no tiene pareja registrada
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tipo_vivienda): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            TIPO DE VIVIENDA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Tipo de Vivienda</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['nombre_tipo_vivienda']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Sector</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['nombre_sector']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Propietario</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['nombre_propiedad']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Número de Familias que habitan la vivienda</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['numero_de_familia']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Número de hogares habitan en la vivienda</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['personas_nucleo_familiar']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Tiempo en años de Residencia en el Sector</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['tiempo_sector']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Número de Pisos de la Vivienda</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($tipo_vivienda['numero_de_pisos']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estado de la vivienda</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;">N/A</td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            TIPO DE VIVIENDA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="12" style="border: 1px solid black; text-align: center;">
                            No se encontró información sobre la vivienda
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($inventario_enseres): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            INVENTARIO DE ENSERES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Televisor</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['televisor_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">D.V.D</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['dvd_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Teatro en Casa</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['teatro_casa_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Equipo de Sonido</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['equipo_sonido_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Computador</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['computador_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Impresora</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['impresora_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Móvil</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['movil_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Estufa</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['estufa_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Nevera</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['nevera_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Lavadora</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['lavadora_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Microondas</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['microondas_nombre_cant']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Moto</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['moto_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Carro</td>
                        <td colspan="8" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['carro_nombre_cant']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Observaciones</td>
                        <td colspan="8" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($inventario_enseres['observacion']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            INVENTARIO DE ENSERES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="12" style="border: 1px solid black; text-align: center;">
                            No se encontró información sobre el inventario de enseres
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($servicios_publicos): ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            SERVICIOS PÚBLICOS Y OTROS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Agua</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_agua']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Luz</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_luz']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Gas</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_gas']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Alcantarillado</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_alcantarillado']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Internet</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_internet']) ?></td>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Administración</td>
                        <td colspan="2" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_administracion']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Parqueadero</td>
                        <td colspan="8" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['nombre_parqueadero']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black;">Observaciones</td>
                        <td colspan="8" style="border: 1px solid black; text-align: center;"><?= htmlspecialchars($servicios_publicos['observacion']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <table class="customTable" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="12" style="font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;">
                            SERVICIOS PÚBLICOS Y OTROS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="12" style="border: 1px solid black; text-align: center;">
                            No se encontró información sobre servicios públicos
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>