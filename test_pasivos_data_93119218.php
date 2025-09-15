<?php
session_start();

// Configurar la cédula específica para la prueba
$cedula = '93119218';
$_SESSION['id_cedula'] = $cedula;

require_once 'app/Database/Database.php';
use App\Database\Database;

echo "<h2>Prueba de Datos de Pasivos - Cédula: $cedula</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Consulta de pasivos con la nueva lógica corregida
    $sql_pasivos = "SELECT 
        p.item, 
        p.id_entidad, 
        p.id_tipo_inversion, 
        p.id_ciudad, 
        p.deuda, 
        p.cuota_mes,
        m.id_municipio, 
        m.municipio
    FROM pasivos p
    LEFT JOIN municipios m ON p.id_ciudad = m.id_municipio
    WHERE p.id_cedula = :cedula AND p.item != 'N/A' AND p.item IS NOT NULL AND p.item != ''";
    
    $stmt_pasivos = $db->prepare($sql_pasivos);
    $stmt_pasivos->bindParam(':cedula', $cedula);
    $stmt_pasivos->execute();
    $pasivos = $stmt_pasivos->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "<h3>Resultados de la Consulta Corregida:</h3>";
    echo "<p><strong>Número de registros encontrados:</strong> " . count($pasivos) . "</p>";
    
    if (count($pasivos) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Item</th><th>Entidad</th><th>Tipo Inversión</th><th>Ciudad</th><th>Deuda</th><th>Cuota Mes</th>";
        echo "</tr>";
        
        $total_deuda = 0;
        $total_cuota = 0;
        
        foreach ($pasivos as $index => $pasivo) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($pasivo['item']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['id_entidad']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['id_tipo_inversion']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['municipio']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['deuda']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['cuota_mes']) . "</td>";
            echo "</tr>";
            
            if (is_numeric($pasivo['deuda'])) $total_deuda += $pasivo['deuda'];
            if (is_numeric($pasivo['cuota_mes'])) $total_cuota += $pasivo['cuota_mes'];
        }
        
        echo "<tr style='background-color: #e0e0e0; font-weight: bold;'>";
        echo "<td colspan='4'>TOTALES</td>";
        echo "<td>$" . number_format($total_deuda, 0, ',', '.') . "</td>";
        echo "<td>$" . number_format($total_cuota, 0, ',', '.') . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p style='color: blue;'><strong>No se encontraron registros de pasivos válidos para esta cédula.</strong></p>";
    }
    
    // También mostrar todos los registros (incluyendo los N/A) para comparación
    echo "<h3>Consulta Original (incluyendo registros N/A):</h3>";
    $sql_original = "SELECT 
        p.item, 
        p.id_entidad, 
        p.id_tipo_inversion, 
        p.id_ciudad, 
        p.deuda, 
        p.cuota_mes,
        m.municipio
    FROM pasivos p
    LEFT JOIN municipios m ON p.id_ciudad = m.id_municipio
    WHERE p.id_cedula = :cedula";
    
    $stmt_original = $db->prepare($sql_original);
    $stmt_original->bindParam(':cedula', $cedula);
    $stmt_original->execute();
    $pasivos_original = $stmt_original->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "<p><strong>Número de registros en consulta original:</strong> " . count($pasivos_original) . "</p>";
    
    if (count($pasivos_original) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Item</th><th>Entidad</th><th>Tipo Inversión</th><th>Ciudad</th><th>Deuda</th><th>Cuota Mes</th>";
        echo "</tr>";
        
        foreach ($pasivos_original as $pasivo) {
            $style = ($pasivo['item'] === 'N/A') ? "style='background-color: #ffcccc;'" : "";
            echo "<tr $style>";
            echo "<td>" . htmlspecialchars($pasivo['item']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['id_entidad']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['id_tipo_inversion']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['municipio']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['deuda']) . "</td>";
            echo "<td>" . htmlspecialchars($pasivo['cuota_mes']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><em>Los registros con fondo rojo son los que se filtran en la consulta corregida.</em></p>";
    }
    
    echo "<hr>";
    echo "<h3>Enlaces para Pruebas:</h3>";
    echo "<p><a href='test_pdf_cedula_93119218.php' target='_blank'>Generar PDF Completo</a></p>";
    echo "<p><a href='/ModuStackVisit_2/app/Controllers/InformeFinalPdfController.php?action=generarInforme' target='_blank'>Generar PDF Directo</a></p>";
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
