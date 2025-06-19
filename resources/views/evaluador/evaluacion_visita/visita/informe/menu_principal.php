<?php
/**
 * MENÚ PRINCIPAL - SISTEMA DE INFORMES
 * Interfaz para probar y aprobar el sistema modularizado
 */

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../../public/index.php');
    exit;
}

$id_cedula = $_SESSION['id_cedula'];
$fecha_actual = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Informes - Menú Principal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 800px;
            width: 90%;
            text-align: center;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }
        
        .header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .user-info h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .user-info p {
            color: #666;
            margin: 5px 0;
        }
        
        .options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .option-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .option-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }
        
        .option-card.recommended {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
        }
        
        .option-card.recommended:hover {
            border-color: #28a745;
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.2);
        }
        
        .option-card.legacy {
            border-color: #ffc107;
            background: linear-gradient(135deg, #fffbf0 0%, #fff3cd 100%);
        }
        
        .option-card.legacy:hover {
            border-color: #ffc107;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.2);
        }
        
        .option-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .option-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .option-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .option-features {
            text-align: left;
            font-size: 0.9em;
            color: #555;
        }
        
        .option-features ul {
            list-style: none;
            padding: 0;
        }
        
        .option-features li {
            padding: 3px 0;
            position: relative;
            padding-left: 20px;
        }
        
        .option-features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .badge.recommended {
            background: #28a745;
            color: white;
        }
        
        .badge.legacy {
            background: #ffc107;
            color: #333;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 0.9em;
        }
        
        .version-info {
            background: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9em;
        }
        
        .version-info h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .version-info p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Sistema de Informes</h1>
            <p>Generador de Informes de Visita Domiciliaria</p>
        </div>
        
        <div class="user-info">
            <h3>👤 Información del Usuario</h3>
            <p><strong>Cédula:</strong> <?php echo htmlspecialchars($id_cedula); ?></p>
            <p><strong>Fecha:</strong> <?php echo $fecha_actual; ?></p>
            <p><strong>Sesión:</strong> Activa</p>
        </div>
        
        <div class="options">
            <a href="InformeModular.php" class="option-card recommended">
                <div class="badge recommended">RECOMENDADO</div>
                <div class="option-icon">🚀</div>
                <div class="option-title">Sistema Modularizado</div>
                <div class="option-description">
                    Nueva versión completamente modularizada con mejor rendimiento y mantenibilidad.
                </div>
                <div class="option-features">
                    <ul>
                        <li>Sin warnings de TCPDF</li>
                        <li>Código modular y reutilizable</li>
                        <li>Validación completa de datos</li>
                        <li>Fácil mantenimiento</li>
                        <li>Logging avanzado</li>
                        <li>Estructura escalable</li>
                    </ul>
                </div>
            </a>
            
            <a href="index.php" class="option-card legacy">
                <div class="badge legacy">LEGACY</div>
                <div class="option-icon">📄</div>
                <div class="option-title">Sistema Original</div>
                <div class="option-description">
                    Versión original del sistema (mantenida para compatibilidad).
                </div>
                <div class="option-features">
                    <ul>
                        <li>Sistema probado y estable</li>
                        <li>Compatibilidad total</li>
                        <li>Funcionalidad completa</li>
                        <li>Archivos SQL originales</li>
                    </ul>
                </div>
            </a>
        </div>
        
        <div class="version-info">
            <h4>📊 Información de Versiones</h4>
            <p><strong>Sistema Modular:</strong> v3.0 - Completamente reescrito</p>
            <p><strong>Sistema Original:</strong> v2.0 - Versión estable</p>
            <p><strong>Última actualización:</strong> Enero 2024</p>
        </div>
        
        <div class="footer">
            <p>© 2024 Sistema de Informes - Todos los derechos reservados</p>
            <p>Desarrollado con ❤️ para mejorar la experiencia del usuario</p>
            <div style="margin-top: 20px;">
                <a href="comparacion_sistemas.php" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                    📊 Ver Comparación Detallada
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Agregar efectos de hover adicionales
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Mostrar mensaje de carga al hacer clic
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('click', function() {
                this.innerHTML += '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 10px 20px; border-radius: 5px;">Generando informe...</div>';
            });
        });
    </script>
</body>
</html> 