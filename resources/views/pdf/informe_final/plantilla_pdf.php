<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Evaluado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            border: 2px solid rgb(175, 0, 0);
            padding: 12px;
            margin-bottom: 20px;
        }
        .logo-container {
            border: 1px solid rgb(175,0,0);
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 100%;
            height: auto;
            max-height: 103px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #222;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #ABABAB;
            font-weight: bold;
            text-align: center;
        }
        .section-title {
            background-color: #ABABAB;
            color: #000;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border: 1px solid #222;
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
        
        <div class="section-title">
            INFORMACIÓN DEL EVALUADO - Cédula: <?= htmlspecialchars($cedula) ?>
        </div>
        
        <?php if ($evaluado): ?>
            <table class="data-table">
                <tr>
                    <th colspan="2">DATOS PERSONALES</th>
                </tr>
                <tr>
                    <td><strong>Nombres:</strong></td>
                    <td><?= htmlspecialchars($evaluado['nombres'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Apellidos:</strong></td>
                    <td><?= htmlspecialchars($evaluado['apellidos'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Tipo Documento:</strong></td>
                    <td><?= htmlspecialchars($evaluado['tipo_documento_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong></td>
                    <td><?= htmlspecialchars($evaluado['cedula_expedida'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Edad:</strong></td>
                    <td><?= htmlspecialchars($evaluado['edad'] ?? '') ?> años</td>
                </tr>
                <tr>
                    <td><strong>Fecha Expedición:</strong></td>
                    <td><?= htmlspecialchars($evaluado['fecha_expedicion'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Lugar Nacimiento:</strong></td>
                    <td><?= htmlspecialchars($evaluado['lugar_nacimiento_municipio'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Estado Civil:</strong></td>
                    <td><?= htmlspecialchars($evaluado['estado_civil_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Número de Hijos:</strong></td>
                    <td><?= htmlspecialchars($evaluado['numero_hijos'] ?? '') ?></td>
                </tr>
            </table>
            
            <table class="data-table">
                <tr>
                    <th colspan="2">DATOS DE CONTACTO</th>
                </tr>
                <tr>
                    <td><strong>Dirección:</strong></td>
                    <td><?= htmlspecialchars($evaluado['direccion'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Ciudad:</strong></td>
                    <td><?= htmlspecialchars($evaluado['ciudad_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Localidad:</strong></td>
                    <td><?= htmlspecialchars($evaluado['localidad'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Barrio:</strong></td>
                    <td><?= htmlspecialchars($evaluado['barrio'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Estrato:</strong></td>
                    <td><?= htmlspecialchars($evaluado['estrato_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Teléfono:</strong></td>
                    <td><?= htmlspecialchars($evaluado['telefono'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Celular 1:</strong></td>
                    <td><?= htmlspecialchars($evaluado['celular_1'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Celular 2:</strong></td>
                    <td><?= htmlspecialchars($evaluado['celular_2'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Correo:</strong></td>
                    <td><?= htmlspecialchars($evaluado['correo'] ?? '') ?></td>
                </tr>
            </table>
            
            <table class="data-table">
                <tr>
                    <th colspan="2">DATOS LABORALES Y FÍSICOS</th>
                </tr>
                <tr>
                    <td><strong>Cargo:</strong></td>
                    <td><?= htmlspecialchars($evaluado['cargo'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Hacer Cuánto:</strong></td>
                    <td><?= htmlspecialchars($evaluado['hacer_cuanto'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Estatura:</strong></td>
                    <td><?= htmlspecialchars($evaluado['estatura_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Peso:</strong></td>
                    <td><?= htmlspecialchars($evaluado['peso_kg'] ?? '') ?> kg</td>
                </tr>
                <tr>
                    <td><strong>RH:</strong></td>
                    <td><?= htmlspecialchars($evaluado['rh_nombre'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Observaciones:</strong></td>
                    <td><?= htmlspecialchars($evaluado['observacion'] ?? '') ?></td>
                </tr>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; color: red; font-weight: bold;">
                No se encontraron datos para la cédula: <?= htmlspecialchars($cedula) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>