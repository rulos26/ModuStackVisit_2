<?php
/**
 * CONTROLADOR MODULAR PARA GENERACIÓN DE INFORMES PDF FINALES
 * Sistema modularizado para manejo de informes de visita domiciliaria
 * 
 * @author Sistema de Informes
 * @version 4.0 - Modular
 * @date 2024
 */

// Verificar si estamos en el contexto correcto
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
}

/**
 * CLASE PARA LOGGING
 */
class Logger {
    private $logFile;
    
    public function __construct($logFile = null) {
        $this->logFile = $logFile ?: BASE_PATH . '/informe_controller_errors.log';
    }
    
    public function log($message, $type = 'INFO') {
        $logMessage = date('[Y-m-d H:i:s]') . " [{$type}] {$message}\n";
        error_log($logMessage, 3, $this->logFile);
    }
    
    public function logError($message, $exception = null) {
        $errorMessage = $message;
        if ($exception) {
            $errorMessage .= "\nException: " . $exception->getMessage();
            $errorMessage .= "\nStack Trace: " . $exception->getTraceAsString();
        }
        $this->log($errorMessage, 'ERROR');
    }
}

/**
 * CLASE PARA MANEJO DE CONEXIÓN A BASE DE DATOS
 */
class DatabaseManager {
    private $mysqli;
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->connect();
    }
    
    private function connect() {
        try {
            $conexionPath = BASE_PATH . '/conn/conexion.php';
            if (!file_exists($conexionPath)) {
                throw new Exception('Archivo de conexión no encontrado: ' . $conexionPath);
            }
            
            require_once $conexionPath;
            
            if (!isset($mysqli) || !$mysqli) {
                throw new Exception('No se pudo establecer conexión con la base de datos');
            }
            
            $this->mysqli = $mysqli;
            $this->logger->log('Conexión a base de datos establecida correctamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al conectar a la base de datos: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getConnection() {
        return $this->mysqli;
    }
    
    public function prepare($query) {
        return $this->mysqli->prepare($query);
    }
    
    public function getError() {
        return $this->mysqli->error;
    }
}

/**
 * CLASE PARA VALIDACIÓN DE SESIÓN Y AUTENTICACIÓN
 */
class SessionManager {
    private $id_cedula;
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->validateSession();
    }
    
    private function validateSession() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $this->id_cedula = $_SESSION['id_cedula'] ?? null;
            
            if (!$this->id_cedula) {
                $this->logger->logError('Usuario no autenticado');
                throw new Exception('Usuario no autenticado. Por favor, inicie sesión.');
            }
            
            $this->logger->log('Sesión validada correctamente para cédula: ' . $this->id_cedula);
            
        } catch (Exception $e) {
            $this->logger->logError('Error al validar sesión: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getCedula() {
        return $this->id_cedula;
    }
}

/**
 * CLASE PARA OBTENCIÓN DE DATOS DEL EVALUADO
 */
class EvaluadoDataProvider {
    private $mysqli;
    private $id_cedula;
    private $logger;
    
    public function __construct($mysqli, $id_cedula, $logger) {
        $this->mysqli = $mysqli;
        $this->id_cedula = $id_cedula;
        $this->logger = $logger;
    }
    
    public function obtenerDatosEvaluado() {
        try {
            $query = "
                SELECT 
                    e.*,
                    td.nombre as tipo_documento_nombre,
                    c.nombre as ciudad_nombre,
                    rh.nombre as rh_nombre,
                    est.nombre as estatura_nombre,
                    ec.nombre as estado_civil_nombre,
                    m.municipio as lugar_nacimiento_municipio
                FROM evaluados e
                LEFT JOIN opc_tipo_documentos td ON e.tipo_documento_id = td.id
                LEFT JOIN opciones c ON e.ciudad_id = c.id
                LEFT JOIN opc_rh rh ON e.rh_id = rh.id
                LEFT JOIN opc_estaturas est ON e.estatura_id = est.id
                LEFT JOIN opc_estado_civiles ec ON e.estado_civil_id = ec.id
                LEFT JOIN municipios m ON e.lugar_nacimiento_municipio_id = m.id
                WHERE e.id_cedula = ?
            ";
            
            $stmt = $this->mysqli->prepare($query);
            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . $this->mysqli->error);
            }
            
            $stmt->bind_param('s', $this->id_cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception('No se encontraron datos para la cédula: ' . $this->id_cedula);
            }
            
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos del evaluado: ' . $e->getMessage());
            throw $e;
        }
    }
}

/**
 * CLASE PARA OBTENCIÓN DE DATOS DE MÓDULOS ESPECÍFICOS
 */
class ModuloDataProvider {
    private $mysqli;
    private $id_cedula;
    private $logger;
    
    public function __construct($mysqli, $id_cedula, $logger) {
        $this->mysqli = $mysqli;
        $this->id_cedula = $id_cedula;
        $this->logger = $logger;
    }
    
    public function obtenerDatosPerfil() {
        return $this->ejecutarConsultaSimple("SELECT * FROM perfil WHERE evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)");
    }
    
    public function obtenerDatosCamaraComercio() {
        $query = "
            SELECT 
                cc.*,
                tc.nombre as tiene_camara
            FROM camara_comercio cc
            LEFT JOIN opc_estados tc ON cc.tiene_camara_id = tc.id
            WHERE cc.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosEstadoSalud() {
        $query = "
            SELECT 
                es.*,
                es_estado.nombre as nombre_estado_salud,
                es_enfermedad.nombre as nombre_tipo_enfermedad,
                es_limitacion.nombre as nombre_limitacion_fisica,
                es_medicamento.nombre as nombre_tipo_medicamento,
                es_alcohol.nombre as nombre_ingiere_alcohol,
                es_fuma.nombre as nombre_fuma
            FROM estados_salud es
            LEFT JOIN opc_estados es_estado ON es.estado_salud_id = es_estado.id
            LEFT JOIN opc_estados es_enfermedad ON es.tipo_enfermedad_id = es_enfermedad.id
            LEFT JOIN opc_estados es_limitacion ON es.limitacion_fisica_id = es_limitacion.id
            LEFT JOIN opc_estados es_medicamento ON es.tipo_medicamento_id = es_medicamento.id
            LEFT JOIN opc_estados es_alcohol ON es.ingiere_alcohol_id = es_alcohol.id
            LEFT JOIN opc_estados es_fuma ON es.fuma_id = es_fuma.id
            WHERE es.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosComposicionFamiliar() {
        $query = "
            SELECT 
                cf.*,
                p.nombre as parentesco_nombre,
                e.nombre as estado_civil_nombre
            FROM composicion_familiar cf
            LEFT JOIN opc_parentesco p ON cf.parentesco_id = p.id
            LEFT JOIN opc_estado_civiles e ON cf.estado_civil_id = e.id
            WHERE cf.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY cf.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosInformacionPareja() {
        $query = "
            SELECT 
                ip.*,
                td.nombre as tipo_documento_nombre,
                c.nombre as ciudad_nombre,
                e.nombre as estado_civil_nombre
            FROM informacion_pareja ip
            LEFT JOIN opc_tipo_documentos td ON ip.tipo_documento_id = td.id
            LEFT JOIN opciones c ON ip.ciudad_id = c.id
            LEFT JOIN opc_estado_civiles e ON ip.estado_civil_id = e.id
            WHERE ip.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosTipoVivienda() {
        $query = "
            SELECT 
                tv.*,
                t.nombre as tipo_vivienda_nombre,
                e.nombre as estado_vivienda_nombre
            FROM tipo_vivienda tv
            LEFT JOIN opc_tipo_vivienda t ON tv.tipo_vivienda_id = t.id
            LEFT JOIN opc_estado_vivienda e ON tv.estado_vivienda_id = e.id
            WHERE tv.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosInventario() {
        $query = "
            SELECT 
                i.*,
                c.nombre as categoria_nombre,
                e.nombre as estado_nombre
            FROM inventario_enseres i
            LEFT JOIN opc_inventario_enseres c ON i.categoria_id = c.id
            LEFT JOIN opc_estados e ON i.estado_id = e.id
            WHERE i.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY i.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosServicios() {
        $query = "
            SELECT 
                s.*,
                ts.nombre as tipo_servicio_nombre,
                e.nombre as estado_nombre
            FROM servicios_publicos s
            LEFT JOIN opc_servicios_publicos ts ON s.tipo_servicio_id = ts.id
            LEFT JOIN opc_estados e ON s.estado_id = e.id
            WHERE s.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY s.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosPatrimonio() {
        $query = "
            SELECT 
                p.*,
                tp.nombre as tipo_patrimonio_nombre,
                e.nombre as estado_nombre
            FROM patrimonio p
            LEFT JOIN opc_tipo_inversion tp ON p.tipo_patrimonio_id = tp.id
            LEFT JOIN opc_estados e ON p.estado_id = e.id
            WHERE p.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY p.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosCuentasBancarias() {
        $query = "
            SELECT 
                cb.*,
                b.nombre as banco_nombre,
                tc.nombre as tipo_cuenta_nombre
            FROM cuentas_bancarias cb
            LEFT JOIN opc_entidad b ON cb.banco_id = b.id
            LEFT JOIN opc_tipo_cuenta tc ON cb.tipo_cuenta_id = tc.id
            WHERE cb.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY cb.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosPasivos() {
        $query = "
            SELECT 
                p.*,
                tp.nombre as tipo_pasivo_nombre,
                e.nombre as estado_nombre
            FROM pasivos p
            LEFT JOIN opc_entidad tp ON p.tipo_pasivo_id = tp.id
            LEFT JOIN opc_estados e ON p.estado_id = e.id
            WHERE p.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY p.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosAportantes() {
        $query = "
            SELECT 
                a.*,
                ta.nombre as tipo_aportante_nombre,
                p.nombre as parentesco_nombre
            FROM aportante a
            LEFT JOIN opc_ocupacion ta ON a.tipo_aportante_id = ta.id
            LEFT JOIN opc_parentesco p ON a.parentesco_id = p.id
            WHERE a.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY a.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosIngresos() {
        $query = "
            SELECT 
                i.*,
                ti.nombre as tipo_ingreso_nombre
            FROM ingresos_mensuales i
            LEFT JOIN opc_ocupacion ti ON i.tipo_ingreso_id = ti.id
            WHERE i.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY i.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosGastos() {
        $query = "
            SELECT 
                g.*,
                tg.nombre as tipo_gasto_nombre
            FROM gasto g
            LEFT JOIN opc_ocupacion tg ON g.tipo_gasto_id = tg.id
            WHERE g.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY g.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosEstudios() {
        $query = "
            SELECT 
                e.*,
                n.nombre as nivel_educativo_nombre,
                e_estado.nombre as estado_nombre
            FROM estudios e
            LEFT JOIN opc_nivel_academico n ON e.nivel_educativo_id = n.id
            LEFT JOIN opc_estados e_estado ON e.estado_id = e_estado.id
            WHERE e.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY e.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosExperienciaLaboral() {
        $query = "
            SELECT 
                el.*,
                c.nombre as cargo_nombre,
                e.nombre as estado_nombre
            FROM experiencia_laboral el
            LEFT JOIN opc_ocupacion c ON el.cargo_id = c.id
            LEFT JOIN opc_estados e ON el.estado_id = e.id
            WHERE el.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY el.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosInformacionJudicial() {
        $query = "
            SELECT 
                ij.*,
                tj.nombre as tipo_judicial_nombre,
                e.nombre as estado_nombre
            FROM informacion_judicial ij
            LEFT JOIN opc_informacion_judicial tj ON ij.tipo_judicial_id = tj.id
            LEFT JOIN opc_estados e ON ij.estado_id = e.id
            WHERE ij.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY ij.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    public function obtenerDatosConceptoFinal() {
        $query = "
            SELECT 
                cf.*,
                c.nombre as concepto_nombre
            FROM concepto_final_evaluador cf
            LEFT JOIN opc_concepto_final c ON cf.concepto_id = c.id
            WHERE cf.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosUbicacion() {
        $query = "
            SELECT 
                u.*,
                d.departamento as departamento_nombre,
                m.municipio as municipio_nombre
            FROM ubicacion u
            LEFT JOIN departamento d ON u.departamento_id = d.id
            LEFT JOIN municipios m ON u.municipio_id = m.id
            WHERE u.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        return $this->ejecutarConsultaSimple($query);
    }
    
    public function obtenerDatosEvidenciasFotograficas() {
        $query = "
            SELECT 
                ef.*,
                te.nombre as tipo_evidencia_nombre
            FROM evidencia_fotografica ef
            LEFT JOIN opc_estados te ON ef.tipo_evidencia_id = te.id
            WHERE ef.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY ef.id
        ";
        return $this->ejecutarConsultaMultiple($query);
    }
    
    private function ejecutarConsultaSimple($query) {
        try {
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al ejecutar consulta simple: ' . $e->getMessage());
            return [];
        }
    }
    
    private function ejecutarConsultaMultiple($query) {
        try {
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $datos = [];
                while ($row = $result->fetch_assoc()) {
                    $datos[] = $row;
                }
                return $datos;
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al ejecutar consulta múltiple: ' . $e->getMessage());
            return [];
        }
    }
}

/**
 * CLASE PARA GENERACIÓN DE PDF
 */
class PdfGenerator {
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    public function generarPDF($id_cedula, $mysqli) {
        try {
            $informePath = BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
            
            if (!file_exists($informePath)) {
                throw new Exception('Archivo InformeModular.php no encontrado: ' . $informePath);
            }
            
            require_once $informePath;
            
            $generador = new InformeVisitaDomiciliariaModular($id_cedula, $mysqli);
            $generador->generarInforme();
            
            $this->logger->log('PDF generado exitosamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar PDF: ' . $e->getMessage());
            throw $e;
        }
    }
}

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
            
            $this->logger->log('Controlador modular inicializado correctamente');
            
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
            $this->logger->log('Iniciando generación de informe modular');
            
            $mysqli = $this->dbManager->getConnection();
            $id_cedula = $this->sessionManager->getCedula();
            
            // Obtener datos del evaluado
            $datosEvaluado = $this->evaluadoProvider->obtenerDatosEvaluado();
            
            // Obtener datos de todos los módulos
            $datosCompletos = $this->obtenerDatosCompletos();
            
            // Generar PDF
            $this->pdfGenerator->generarPDF($id_cedula, $mysqli);
            
            $this->logger->log('Informe generado exitosamente');
            
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
            $this->logger->log('Iniciando generación de informe modular directo');
            
            $mysqli = $this->dbManager->getConnection();
            $id_cedula = $this->sessionManager->getCedula();
            
            $this->pdfGenerator->generarPDF($id_cedula, $mysqli);
            
            $this->logger->log('Informe modular generado exitosamente');
            
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
}
?> 