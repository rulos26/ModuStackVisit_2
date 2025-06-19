<?php
/**
 * CONTROLADOR PARA GENERACIÓN DE INFORMES PDF FINALES
 * Maneja la lógica de negocio para la generación de informes de visita domiciliaria
 * 
 * @author Sistema de Informes
 * @version 3.0
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

class InformeFinalPdfController {
    
    private $mysqli;
    private $id_cedula;
    private $logger;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        try {
            // Inicializar logger primero
            $this->logger = new Logger();
            
            // Incluir conexión a base de datos
            $conexionPath = BASE_PATH . '/conn/conexion.php';
            if (!file_exists($conexionPath)) {
                throw new Exception('Archivo de conexión no encontrado: ' . $conexionPath);
            }
            
            require_once $conexionPath;
            
            // Verificar que la conexión existe
            if (!isset($mysqli) || !$mysqli) {
                throw new Exception('No se pudo establecer conexión con la base de datos');
            }
            
            $this->mysqli = $mysqli;
            
            // Verificar sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Obtener ID de cédula de la sesión
            $this->id_cedula = $_SESSION['id_cedula'] ?? null;
            
            // Validar autenticación
            if (!$this->id_cedula) {
                $this->logger->logError('Usuario no autenticado');
                throw new Exception('Usuario no autenticado. Por favor, inicie sesión.');
            }
            
            $this->logger->log('Controlador inicializado correctamente para cédula: ' . $this->id_cedula);
            
        } catch (Exception $e) {
            if (isset($this->logger)) {
                $this->logger->logError('Error en constructor: ' . $e->getMessage());
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
            $this->logger->log('Iniciando generación de informe para cédula: ' . $this->id_cedula);
            
            // Verificar que tenemos conexión
            if (!$this->mysqli || $this->mysqli->connect_error) {
                throw new Exception('Error de conexión a la base de datos');
            }
            
            // Obtener datos del evaluado
            $datosEvaluado = $this->obtenerDatosEvaluado();
            
            // Obtener datos de todos los módulos
            $datosCompletos = $this->obtenerDatosCompletos();
            
            // Generar PDF
            $this->generarPDF($datosCompletos);
            
            $this->logger->log('Informe generado exitosamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar informe: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener datos básicos del evaluado
     */
    private function obtenerDatosEvaluado() {
        try {
            $query = "
                SELECT 
                    e.*,
                    td.nombre as tipo_documento_nombre,
                    c.nombre as ciudad_nombre,
                    rh.nombre as rh_nombre,
                    est.nombre as estatura_nombre,
                    ec.nombre as estado_civil_nombre,
                    m.nombre as lugar_nacimiento_municipio
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
    
    /**
     * Obtener datos completos de todos los módulos
     */
    private function obtenerDatosCompletos() {
        try {
            $datos = [
                'evaluado' => $this->obtenerDatosEvaluado(),
                'perfil' => $this->obtenerDatosPerfil(),
                'camara_comercio' => $this->obtenerDatosCamaraComercio(),
                'estado_salud' => $this->obtenerDatosEstadoSalud(),
                'composicion_familiar' => $this->obtenerDatosComposicionFamiliar(),
                'informacion_pareja' => $this->obtenerDatosInformacionPareja(),
                'tipo_vivienda' => $this->obtenerDatosTipoVivienda(),
                'inventario' => $this->obtenerDatosInventario(),
                'servicios' => $this->obtenerDatosServicios(),
                'patrimonio' => $this->obtenerDatosPatrimonio(),
                'cuentas_bancarias' => $this->obtenerDatosCuentasBancarias(),
                'pasivos' => $this->obtenerDatosPasivos(),
                'aportantes' => $this->obtenerDatosAportantes(),
                'ingresos' => $this->obtenerDatosIngresos(),
                'gastos' => $this->obtenerDatosGastos(),
                'estudios' => $this->obtenerDatosEstudios(),
                'experiencia_laboral' => $this->obtenerDatosExperienciaLaboral(),
                'informacion_judicial' => $this->obtenerDatosInformacionJudicial(),
                'concepto_final' => $this->obtenerDatosConceptoFinal(),
                'ubicacion' => $this->obtenerDatosUbicacion(),
                'evidencias_fotograficas' => $this->obtenerDatosEvidenciasFotograficas()
            ];
            
            return $datos;
            
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos completos: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener datos de perfil
     */
    private function obtenerDatosPerfil() {
        try {
            $query = "SELECT * FROM perfil WHERE evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de perfil: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de cámara de comercio
     */
    private function obtenerDatosCamaraComercio() {
        try {
            $query = "
                SELECT 
                    cc.*,
                    tc.nombre as tiene_camara
                FROM camara_comercio cc
                LEFT JOIN opc_estados tc ON cc.tiene_camara_id = tc.id
                WHERE cc.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de cámara de comercio: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de estado de salud
     */
    private function obtenerDatosEstadoSalud() {
        try {
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
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de estado de salud: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de composición familiar
     */
    private function obtenerDatosComposicionFamiliar() {
        try {
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
            $this->logger->logError('Error al obtener datos de composición familiar: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de información de pareja
     */
    private function obtenerDatosInformacionPareja() {
        try {
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
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de información de pareja: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de tipo de vivienda
     */
    private function obtenerDatosTipoVivienda() {
        try {
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
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de tipo de vivienda: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de inventario
     */
    private function obtenerDatosInventario() {
        try {
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
            $this->logger->logError('Error al obtener datos de inventario: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de servicios
     */
    private function obtenerDatosServicios() {
        try {
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
            $this->logger->logError('Error al obtener datos de servicios: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de patrimonio
     */
    private function obtenerDatosPatrimonio() {
        try {
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
            $this->logger->logError('Error al obtener datos de patrimonio: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de cuentas bancarias
     */
    private function obtenerDatosCuentasBancarias() {
        try {
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
            $this->logger->logError('Error al obtener datos de cuentas bancarias: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de pasivos
     */
    private function obtenerDatosPasivos() {
        try {
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
            $this->logger->logError('Error al obtener datos de pasivos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de aportantes
     */
    private function obtenerDatosAportantes() {
        try {
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
            $this->logger->logError('Error al obtener datos de aportantes: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de ingresos
     */
    private function obtenerDatosIngresos() {
        try {
            $query = "
                SELECT 
                    i.*,
                    ti.nombre as tipo_ingreso_nombre
                FROM ingresos_mensuales i
                LEFT JOIN opc_ocupacion ti ON i.tipo_ingreso_id = ti.id
                WHERE i.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
                ORDER BY i.id
            ";
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
            $this->logger->logError('Error al obtener datos de ingresos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de gastos
     */
    private function obtenerDatosGastos() {
        try {
            $query = "
                SELECT 
                    g.*,
                    tg.nombre as tipo_gasto_nombre
                FROM gasto g
                LEFT JOIN opc_ocupacion tg ON g.tipo_gasto_id = tg.id
                WHERE g.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
                ORDER BY g.id
            ";
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
            $this->logger->logError('Error al obtener datos de gastos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de estudios
     */
    private function obtenerDatosEstudios() {
        try {
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
            $this->logger->logError('Error al obtener datos de estudios: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de experiencia laboral
     */
    private function obtenerDatosExperienciaLaboral() {
        try {
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
            $this->logger->logError('Error al obtener datos de experiencia laboral: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de información judicial
     */
    private function obtenerDatosInformacionJudicial() {
        try {
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
            $this->logger->logError('Error al obtener datos de información judicial: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de concepto final
     */
    private function obtenerDatosConceptoFinal() {
        try {
            $query = "
                SELECT 
                    cf.*,
                    c.nombre as concepto_nombre
                FROM concepto_final_evaluador cf
                LEFT JOIN opc_concepto_final c ON cf.concepto_id = c.id
                WHERE cf.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de concepto final: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de ubicación
     */
    private function obtenerDatosUbicacion() {
        try {
            $query = "
                SELECT 
                    u.*,
                    d.nombre as departamento_nombre,
                    m.nombre as municipio_nombre
                FROM ubicacion u
                LEFT JOIN departamento d ON u.departamento_id = d.id
                LEFT JOIN municipios m ON u.municipio_id = m.id
                WHERE u.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc() ?: [];
            }
            return [];
        } catch (Exception $e) {
            $this->logger->logError('Error al obtener datos de ubicación: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos de evidencias fotográficas
     */
    private function obtenerDatosEvidenciasFotograficas() {
        try {
            $query = "
                SELECT 
                    ef.*,
                    te.nombre as tipo_evidencia_nombre
                FROM evidencia_fotografica ef
                LEFT JOIN opc_estados te ON ef.tipo_evidencia_id = te.id
                WHERE ef.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
                ORDER BY ef.id
            ";
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
            $this->logger->logError('Error al obtener datos de evidencias fotográficas: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar PDF con los datos obtenidos
     */
    private function generarPDF($datos) {
        try {
            // Incluir el sistema modularizado
            $informePath = BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
            
            if (!file_exists($informePath)) {
                throw new Exception('Archivo InformeModular.php no encontrado: ' . $informePath);
            }
            
            require_once $informePath;
            
            // Crear instancia del generador de informes
            // La clase InformeVisitaDomiciliariaModular ya maneja la obtención de datos internamente
            $generador = new InformeVisitaDomiciliariaModular($this->id_cedula, $this->mysqli);
            
            // Generar el PDF
            $generador->generarInforme();
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar PDF: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Método alternativo que usa directamente el sistema modularizado
     */
    public function generarInformeModular() {
        try {
            $this->logger->log('Iniciando generación de informe modular para cédula: ' . $this->id_cedula);
            
            // Incluir el sistema modularizado
            $informePath = BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
            
            if (!file_exists($informePath)) {
                throw new Exception('Archivo InformeModular.php no encontrado: ' . $informePath);
            }
            
            require_once $informePath;
            
            // Crear instancia del generador de informes
            $generador = new InformeVisitaDomiciliariaModular($this->id_cedula, $this->mysqli);
            
            // Generar el PDF
            $generador->generarInforme();
            
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
                'cedula' => $this->id_cedula
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
            // Verificar que el usuario esté autenticado
            if (!$this->id_cedula) {
                return false;
            }
            
            // Verificar que existe en la base de datos
            $query = "SELECT id FROM evaluados WHERE id_cedula = ?";
            $stmt = $this->mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('s', $this->id_cedula);
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