<?php
/**
 * CONTROLADOR PRINCIPAL MODULAR PARA GENERACIÓN DE INFORMES PDF FINALES
 * Sistema modularizado usando archivos separados para cada responsabilidad
 * 
 * @author Sistema de Informes
 * @version 5.0 - Modular con Archivos Separados
 * @date 2024
 */

// Verificar si estamos en el contexto correcto
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
}

// Incluir todos los módulos necesarios
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/DatabaseManager.php';
require_once __DIR__ . '/SessionManager.php';
require_once __DIR__ . '/EvaluadoDataProvider.php';
require_once __DIR__ . '/ModuloDataProvider.php';
require_once __DIR__ . '/PdfGenerator.php';

/**
 * CLASE PRINCIPAL DEL CONTROLADOR MODULAR
 */
class InformeFinalPdfController {
    
    private $logger;
    private $dbManager;
    private $sessionManager;
    private $evaluadoProvider;
    private $moduloProvider;
    private $pdfGenerator;
    
    /**
     * Constructor del controlador modular
     */
    public function __construct() {
        try {
            // Inicializar logger primero
            $this->logger = new Logger();
            
            // Inicializar componentes modulares
            $this->dbManager = new DatabaseManager($this->logger);
            $this->sessionManager = new SessionManager($this->logger);
            
            // Obtener conexión y cédula
            $mysqli = $this->dbManager->getConnection();
            $id_cedula = $this->sessionManager->getCedula();
            
            // Inicializar proveedores de datos
            $this->evaluadoProvider = new EvaluadoDataProvider($mysqli, $id_cedula, $this->logger);
            $this->moduloProvider = new ModuloDataProvider($mysqli, $id_cedula, $this->logger);
            $this->pdfGenerator = new PdfGenerator($this->logger);
            
            $this->logger->logInfo('Controlador modular inicializado correctamente');
            
        } catch (Exception $e) {
            if (isset($this->logger)) {
                $this->logger->logError('Error en constructor modular: ' . $e->getMessage());
            } else {
                error_log('Error en constructor InformeFinalPdfController: ' . $e->getMessage());
            }
            throw $e;
        }
    }
    
    /**
     * Método principal para generar informe
     */
    public function generarInforme() {
        try {
            $this->logger->logInfo('Iniciando generación de informe modular');
            
            $mysqli = $this->dbManager->getConnection();
            $id_cedula = $this->sessionManager->getCedula();
            
            // Obtener datos del evaluado
            $datosEvaluado = $this->evaluadoProvider->obtenerDatosEvaluado();
            
            // Obtener datos de todos los módulos
            $datosCompletos = $this->obtenerDatosCompletos();
            
            // Generar PDF
            $this->pdfGenerator->generarPDF($id_cedula, $mysqli);
            
            $this->logger->logInfo('Informe generado exitosamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar informe: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener datos completos de todos los módulos
     */
    private function obtenerDatosCompletos() {
        try {
            $datos = [
                'evaluado' => $this->evaluadoProvider->obtenerDatosEvaluado(),
                'perfil' => $this->moduloProvider->obtenerDatosPerfil(),
                'camara_comercio' => $this->moduloProvider->obtenerDatosCamaraComercio(),
                'estado_salud' => $this->moduloProvider->obtenerDatosEstadoSalud(),
                'composicion_familiar' => $this->moduloProvider->obtenerDatosComposicionFamiliar(),
                'informacion_pareja' => $this->moduloProvider->obtenerDatosInformacionPareja(),
                'tipo_vivienda' => $this->moduloProvider->obtenerDatosTipoVivienda(),
                'inventario' => $this->moduloProvider->obtenerDatosInventario(),
                'servicios' => $this->moduloProvider->obtenerDatosServicios(),
                'patrimonio' => $this->moduloProvider->obtenerDatosPatrimonio(),
                'cuentas_bancarias' => $this->moduloProvider->obtenerDatosCuentasBancarias(),
                'pasivos' => $this->moduloProvider->obtenerDatosPasivos(),
                'aportantes' => $this->moduloProvider->obtenerDatosAportantes(),
                'ingresos' => $this->moduloProvider->obtenerDatosIngresos(),
                'gastos' => $this->moduloProvider->obtenerDatosGastos(),
                'estudios' => $this->moduloProvider->obtenerDatosEstudios(),
                'experiencia_laboral' => $this->moduloProvider->obtenerDatosExperienciaLaboral(),
                'informacion_judicial' => $this->moduloProvider->obtenerDatosInformacionJudicial(),
                'concepto_final' => $this->moduloProvider->obtenerDatosConceptoFinal(),
                'ubicacion' => $this->moduloProvider->obtenerDatosUbicacion(),
                'evidencias_fotograficas' => $this->moduloProvider->obtenerDatosEvidenciasFotograficas()
            ];
            
            return $datos;
            
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos completos: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Método alternativo que usa directamente el sistema modularizado
     */
    public function generarInformeModular() {
        try {
            $this->logger->logInfo('Iniciando generación de informe modular directo');
            
            $mysqli = $this->dbManager->getConnection();
            $id_cedula = $this->sessionManager->getCedula();
            
            $this->pdfGenerator->generarPDF($id_cedula, $mysqli);
            
            $this->logger->logInfo('Informe modular generado exitosamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar informe modular: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener estadísticas del informe
     */
    public function obtenerEstadisticas() {
        try {
            $datos = $this->obtenerDatosCompletos();
            
            $estadisticas = [
                'total_modulos' => count($datos),
                'modulos_con_datos' => 0,
                'modulos_vacios' => 0,
                'fecha_generacion' => date('Y-m-d H:i:s'),
                'cedula' => $this->sessionManager->getCedula()
            ];
            
            foreach ($datos as $modulo => $data) {
                if (!empty($data) && (is_array($data) ? count($data) > 0 : true)) {
                    $estadisticas['modulos_con_datos']++;
                } else {
                    $estadisticas['modulos_vacios']++;
                }
            }
            
            return $estadisticas;
            
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener estadísticas: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Validar si el usuario tiene permisos para generar informe
     */
    public function validarPermisos() {
        try {
            $id_cedula = $this->sessionManager->getCedula();
            
            if (!$id_cedula) {
                return false;
            }
            
            $mysqli = $this->dbManager->getConnection();
            $query = "SELECT id FROM evaluados WHERE id_cedula = ?";
            $stmt = $mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->num_rows > 0;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->logError('Error al validar permisos: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener información del sistema modular
     */
    public function obtenerInfoSistema() {
        return [
            'version' => '5.0 - Modular con Archivos Separados',
            'modulos' => [
                'Logger' => 'Manejo de logs',
                'DatabaseManager' => 'Gestión de base de datos',
                'SessionManager' => 'Gestión de sesiones',
                'EvaluadoDataProvider' => 'Datos del evaluado',
                'ModuloDataProvider' => 'Datos de módulos específicos',
                'PdfGenerator' => 'Generación de PDF'
            ],
            'fecha_creacion' => '2024',
            'autor' => 'Sistema de Informes'
        ];
    }
    
    /**
     * Cerrar conexiones y limpiar recursos
     */
    public function __destruct() {
        if (isset($this->dbManager)) {
            $this->dbManager->closeConnection();
        }
    }
}
?> 