<?php
/**
 * Test Web Automatizado para Usuarios Predefinidos
 * 
 * Este script valida la creación y protección de usuarios maestros del sistema:
 * - root (Superadministrador)
 * - admin (Administrador) 
 * - cliente (Cliente)
 * - evaluador (Evaluador)
 * 
 * @version 1.0
 * @author Sistema ModuStack
 * @license MIT
 */

// Configuración de seguridad para producción
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300); // 5 minutos máximo
ini_set('memory_limit', '256M');

// Headers de seguridad
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Verificar que sea una petición autorizada (opcional)
$allowedIPs = ['127.0.0.1', '::1']; // Solo localhost por defecto
if (!in_array($_SERVER['REMOTE_ADDR'] ?? 'unknown', $allowedIPs)) {
    // En producción, puedes descomentar esta línea para mayor seguridad
    // die('Acceso no autorizado desde: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Test Web - Usuarios Predefinidos</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            color: #7f8c8d;
            font-size: 1.1em;
            margin: 10px 0 0 0;
        }
        .status {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 5px solid;
            font-weight: 500;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border-left-color: #28a745;
        }
        .info { 
            background: #d1ecf1; 
            color: #0c5460; 
            border-left-color: #17a2b8;
        }
        .warning { 
            background: #fff3cd; 
            color: #856404; 
            border-left-color: #ffc107;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border-left-color: #dc3545;
        }
        .user-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .user-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .user-card h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-card .role-badge {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .user-card .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .user-card .detail-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .user-card .detail-label {
            font-weight: bold;
            color: #495057;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .user-card .detail-value {
            color: #212529;
            word-break: break-all;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-info { background: #17a2b8; }
        .btn-info:hover { background: #138496; }
        .test-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        .test-section h3 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.5s ease;
            border-radius: 10px;
        }
        .summary {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #28a745;
        }
        .summary h3 {
            color: #155724;
            margin-top: 0;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #28a745;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            color: #155724;
            font-size: 0.9em;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .log-container {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            max-height: 400px;
            overflow-y: auto;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px 0;
            border-bottom: 1px solid #34495e;
        }
        .log-timestamp {
            color: #95a5a6;
            font-size: 0.8em;
        }
        .log-level {
            font-weight: bold;
            margin: 0 10px;
        }
        .log-level.info { color: #3498db; }
        .log-level.success { color: #2ecc71; }
        .log-level.warning { color: #f39c12; }
        .log-level.error { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Test Web - Usuarios Predefinidos</h1>
            <p>Sistema de Validación Automatizada para Entorno de Producción</p>
        </div>

        <div class="status info">
            <strong>📋 Objetivo:</strong> Validar la creación y protección de usuarios maestros del sistema
        </div>

        <div class="test-section">
            <h3>🎯 Usuarios a Validar</h3>
            <div class="user-card">
                <h3>
                    <span style="color: #e74c3c;">👑</span>
                    root - <span class="role-badge">Superadministrador</span>
                </h3>
                <div class="details">
                    <div class="detail-item">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value">root</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Contraseña:</div>
                        <div class="detail-value">root</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Rol:</div>
                        <div class="detail-value">3 - Superadministrador</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Protección:</div>
                        <div class="detail-value">🛡️ NO MODIFICABLE</div>
                    </div>
                </div>
            </div>

            <div class="user-card">
                <h3>
                    <span style="color: #3498db;">⚙️</span>
                    admin - <span class="role-badge">Administrador</span>
                </h3>
                <div class="details">
                    <div class="detail-item">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value">admin</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Contraseña:</div>
                        <div class="detail-value">admin</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Rol:</div>
                        <div class="detail-value">1 - Administrador</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Protección:</div>
                        <div class="detail-value">🛡️ NO MODIFICABLE</div>
                    </div>
                </div>
            </div>

            <div class="user-card">
                <h3>
                    <span style="color: #2ecc71;">👤</span>
                    cliente - <span class="role-badge">Cliente</span>
                </h3>
                <div class="details">
                    <div class="detail-item">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value">cliente</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Contraseña:</div>
                        <div class="detail-value">cliente</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Rol:</div>
                        <div class="detail-value">2 - Cliente</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Protección:</div>
                        <div class="detail-value">🛡️ NO MODIFICABLE</div>
                    </div>
                </div>
            </div>

            <div class="user-card">
                <h3>
                    <span style="color: #f39c12;">📋</span>
                    evaluador - <span class="role-badge">Evaluador</span>
                </h3>
                <div class="details">
                    <div class="detail-item">
                        <div class="detail-label">Usuario:</div>
                        <div class="detail-value">evaluador</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Contraseña:</div>
                        <div class="detail-value">evaluador</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Rol:</div>
                        <div class="detail-value">4 - Evaluador</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Protección:</div>
                        <div class="detail-value">🛡️ NO MODIFICABLE</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h3>🚀 Ejecutar Test</h3>
            <p>Haz clic en el botón para ejecutar la validación completa del sistema:</p>
            
            <button id="runTest" class="btn btn-success">
                🚀 Ejecutar Test Completo
            </button>
            
            <button id="testProtection" class="btn btn-warning">
                🛡️ Test de Protección
            </button>
            
            <button id="viewLogs" class="btn btn-info">
                📋 Ver Logs del Sistema
            </button>
        </div>

        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>Ejecutando test... Por favor espera</p>
        </div>

        <div id="progressContainer" style="display: none;">
            <h3>📊 Progreso del Test</h3>
            <div class="progress-bar">
                <div id="progressFill" class="progress-fill" style="width: 0%"></div>
            </div>
            <p id="progressText">Preparando test...</p>
        </div>

        <div id="results" style="display: none;">
            <!-- Los resultados se mostrarán aquí -->
        </div>

        <div id="logContainer" class="log-container" style="display: none;">
            <h3>📋 Logs del Sistema</h3>
            <div id="logContent">
                <!-- Los logs se mostrarán aquí -->
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let testResults = {};
        let currentStep = 0;
        const totalSteps = 8;

        // Elementos del DOM
        const runTestBtn = document.getElementById('runTest');
        const testProtectionBtn = document.getElementById('testProtection');
        const viewLogsBtn = document.getElementById('viewLogs');
        const loading = document.getElementById('loading');
        const progressContainer = document.getElementById('progressContainer');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const results = document.getElementById('results');
        const logContainer = document.getElementById('logContainer');
        const logContent = document.getElementById('logContent');

        // Event listeners
        runTestBtn.addEventListener('click', runCompleteTest);
        testProtectionBtn.addEventListener('click', testProtectionOnly);
        viewLogsBtn.addEventListener('click', toggleLogs);

        // Función principal del test completo
        async function runCompleteTest() {
            try {
                // Preparar interfaz
                prepareTestInterface();
                
                // Ejecutar test real via API
                const response = await fetch('TestWebUsuariosPredefinidosAPI.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'run_complete_test'
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                // Mostrar resultados reales
                showRealResults(result);
                
            } catch (error) {
                showError('Error durante la ejecución del test: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Función para test de protección únicamente
        async function testProtectionOnly() {
            try {
                prepareTestInterface();
                
                // Ejecutar test de protección real via API
                const response = await fetch('TestWebUsuariosPredefinidosAPI.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'test_protection'
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                // Mostrar resultados reales de protección
                showRealProtectionResults(result);
                
            } catch (error) {
                showError('Error durante el test de protección: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Función para ejecutar un paso del test
        async function executeTestStep(message, step) {
            progressText.textContent = message;
            currentStep = step;
            const progress = (step / totalSteps) * 100;
            progressFill.style.width = progress + '%';
            
            // Simular delay para mostrar progreso
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Aquí irían las llamadas reales a la API
            // Por ahora simulamos el progreso
            logMessage('info', `Paso ${step}: ${message}`);
        }

        // Función para preparar la interfaz del test
        function prepareTestInterface() {
            runTestBtn.disabled = true;
            testProtectionBtn.disabled = true;
            loading.style.display = 'block';
            progressContainer.style.display = 'block';
            results.style.display = 'none';
            logContainer.style.display = 'none';
            
            // Resetear progreso
            currentStep = 0;
            progressFill.style.width = '0%';
            progressText.textContent = 'Preparando test...';
            
            // Limpiar logs
            logContent.innerHTML = '';
        }

        // Función para ocultar loading
        function hideLoading() {
            loading.style.display = 'none';
            runTestBtn.disabled = false;
            testProtectionBtn.disabled = false;
        }

        // Función para mostrar resultados reales
        function showRealResults(result) {
            const summary = result.summary || {};
            const steps = result.steps || [];
            
            let stepsHTML = '';
            steps.forEach(step => {
                const statusClass = step.success ? 'success' : 'error';
                const statusIcon = step.success ? '✅' : '❌';
                
                stepsHTML += `
                    <div class="status ${statusClass}">
                        ${statusIcon} <strong>${step.title}:</strong> ${step.message}
                        <br><small>${step.details}</small>
                    </div>
                `;
            });
            
            const resultsHTML = `
                <div class="summary">
                    <h3>🎉 Test Completado</h3>
                    <div class="summary-stats">
                        <div class="stat-item">
                            <div class="stat-number">${summary.total_steps || 0}</div>
                            <div class="stat-label">Pasos Totales</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">${summary.successful_steps || 0}</div>
                            <div class="stat-label">Pasos Exitosos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">${summary.success_rate || '0%'}</div>
                            <div class="stat-label">Tasa de Éxito</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">${result.success ? '✅' : '❌'}</div>
                            <div class="stat-label">Estado Final</div>
                        </div>
                    </div>
                </div>
                
                <div class="test-section">
                    <h3>📊 Detalle de Pasos</h3>
                    ${stepsHTML}
                </div>
                
                <div class="test-section">
                    <h3>🔗 Acciones Disponibles</h3>
                    <a href="index.php" class="btn btn-success">🏠 Ir al Login</a>
                    <a href="resources/views/superadmin/dashboardSuperAdmin.php" class="btn btn-info">👑 Dashboard Superadmin</a>
                    <button onclick="runCompleteTest()" class="btn btn-warning">🔄 Ejecutar Test Nuevamente</button>
                </div>
            `;
            
            results.innerHTML = resultsHTML;
            results.style.display = 'block';
        }
        
        // Función para mostrar resultados (mantener para compatibilidad)
        function showResults() {
            showRealResults({
                success: true,
                summary: {
                    total_steps: 8,
                    successful_steps: 8,
                    success_rate: '100%'
                },
                steps: [
                    { success: true, title: 'Test Simulado', message: 'Resultado simulado', details: 'Este es un resultado de demostración' }
                ]
            });
        }

        // Función para mostrar resultados reales de protección
        function showRealProtectionResults(result) {
            const summary = result.summary || {};
            const steps = result.steps || [];
            
            let stepsHTML = '';
            steps.forEach(step => {
                const statusClass = step.success ? 'success' : 'error';
                const statusIcon = step.success ? '✅' : '❌';
                
                stepsHTML += `
                    <div class="status ${statusClass}">
                        ${statusIcon} <strong>${step.title}:</strong> ${step.message}
                        <br><small>${step.details}</small>
                    </div>
                `;
            });
            
            const resultsHTML = `
                <div class="summary">
                    <h3>🛡️ Test de Protección Completado</h3>
                    <div class="summary-stats">
                        <div class="stat-item">
                            <div class="stat-number">${summary.total_steps || 0}</div>
                            <div class="stat-label">Pasos Totales</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">${summary.successful_steps || 0}</div>
                            <div class="stat-label">Pasos Exitosos</div>
                        </div>
                        <div class="stat-number">${summary.success_rate || '0%'}</div>
                            <div class="stat-label">Tasa de Éxito</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">${result.success ? '✅' : '❌'}</div>
                            <div class="stat-label">Estado Final</div>
                        </div>
                    </div>
                </div>
                
                <div class="test-section">
                    <h3>📊 Detalle de Pasos de Protección</h3>
                    ${stepsHTML}
                </div>
                
                <div class="test-section">
                    <h3>🔗 Acciones Disponibles</h3>
                    <button onclick="testProtectionOnly()" class="btn btn-warning">🔄 Ejecutar Test de Protección Nuevamente</button>
                    <button onclick="runCompleteTest()" class="btn btn-success">🚀 Ejecutar Test Completo</button>
                </div>
            `;
            
            results.innerHTML = resultsHTML;
            results.style.display = 'block';
        }
        
        // Función para mostrar resultados de protección (mantener para compatibilidad)
        function showProtectionResults() {
            showRealProtectionResults({
                success: true,
                summary: {
                    total_steps: 3,
                    successful_steps: 3,
                    success_rate: '100%'
                },
                steps: [
                    { success: true, title: 'Test Simulado', message: 'Resultado simulado', details: 'Este es un resultado de demostración' }
                ]
            });
        }

        // Función para mostrar errores
        function showError(message) {
            const errorHTML = `
                <div class="status error">
                    ❌ <strong>Error:</strong> ${message}
                </div>
                <div class="test-section">
                    <h3>🔧 Solución de Problemas</h3>
                    <p>Si el error persiste, verifica:</p>
                    <ul>
                        <li>Conexión a la base de datos</li>
                        <li>Permisos de usuario</li>
                        <li>Estructura de tablas</li>
                        <li>Logs del sistema</li>
                    </ul>
                    <button onclick="runCompleteTest()" class="btn btn-warning">🔄 Reintentar</button>
                </div>
            `;
            
            results.innerHTML = errorHTML;
            results.style.display = 'block';
        }

        // Función para mostrar logs
        function toggleLogs() {
            if (logContainer.style.display === 'none') {
                logContainer.style.display = 'block';
                viewLogsBtn.textContent = '📋 Ocultar Logs';
                loadSystemLogs();
            } else {
                logContainer.style.display = 'none';
                viewLogsBtn.textContent = '📋 Ver Logs del Sistema';
            }
        }

        // Función para cargar logs del sistema
        async function loadSystemLogs() {
            try {
                const response = await fetch('TestWebUsuariosPredefinidosAPI.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_system_logs'
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.error) {
                    logContent.innerHTML = `<div class="log-entry"><span class="log-level error">ERROR</span> ${result.error}</div>`;
                    return;
                }
                
                if (result.logs && result.logs.length > 0) {
                    logContent.innerHTML = result.logs.map(log => `
                        <div class="log-entry">
                            <span class="log-timestamp">[${new Date().toLocaleString('es-ES')}]</span>
                            <span class="log-level info">INFO</span>
                            <span>${log}</span>
                        </div>
                    `).join('');
                } else {
                    logContent.innerHTML = '<div class="log-entry"><span class="log-level info">INFO</span> No hay logs disponibles</div>';
                }
                
            } catch (error) {
                logContent.innerHTML = `<div class="log-entry"><span class="log-entry"><span class="log-level error">ERROR</span> Error al cargar logs: ${error.message}</div>`;
            }
        }

        // Función para agregar mensajes al log
        function logMessage(level, message) {
            const timestamp = new Date().toLocaleString('es-ES');
            const logEntry = document.createElement('div');
            logEntry.className = 'log-entry';
            logEntry.innerHTML = `
                <span class="log-timestamp">[${timestamp}]</span>
                <span class="log-level ${level}">${level.toUpperCase()}</span>
                <span>${message}</span>
            `;
            
            logContent.appendChild(logEntry);
            logContent.scrollTop = logContent.scrollHeight;
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            logMessage('info', 'Test Web de Usuarios Predefinidos cargado correctamente');
            logMessage('info', 'Sistema listo para ejecutar validaciones');
        });
    </script>
</body>
</html>
