<?php
/**
 * CONTROLADOR PARA GENERACIÓN DE INFORMES PDF FINALES
 * Maneja la lógica de negocio para la generación de informes de visita domiciliaria
 * 
 * @author Sistema de Informes
 * @version 3.0
 * @date 2024
 */

namespace App\Controllers;

class InformeFinalPdfController {
    
    private $mysqli;
    private $id_cedula;
    private $logger;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        // Incluir conexión a base de datos
        require_once $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';
        global $mysqli;
        $this->mysqli = $mysqli;
        
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Obtener ID de cédula de la sesión
        $this->id_cedula = $_SESSION['id_cedula'] ?? null;
        
        // Inicializar logger
        $this->logger = new Logger();
        
        // Validar autenticación
        if (!$this->id_cedula) {
            $this->logger->logError('Usuario no autenticado');
            throw new \Exception('Usuario no autenticado');
        }
    }
    
    /**
     * Método principal para generar informe
     */
    public function generarInforme() {
        try {
            $this->logger->log('Iniciando generación de informe para cédula: ' . $this->id_cedula);
            
            // Obtener datos del evaluado
            $datosEvaluado = $this->obtenerDatosEvaluado();
            
            // Obtener datos de todos los módulos
            $datosCompletos = $this->obtenerDatosCompletos();
            
            // Generar PDF
            $this->generarPDF($datosCompletos);
            
            $this->logger->log('Informe generado exitosamente');
            
        } catch (\Exception $e) {
            $this->logger->logError('Error al generar informe: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener datos básicos del evaluado
     */
    private function obtenerDatosEvaluado() {
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
            LEFT JOIN tipo_documento td ON e.tipo_documento_id = td.id
            LEFT JOIN ciudades c ON e.ciudad_id = c.id
            LEFT JOIN rh ON e.rh_id = rh.id
            LEFT JOIN estatura est ON e.estatura_id = est.id
            LEFT JOIN estado_civil ec ON e.estado_civil_id = ec.id
            LEFT JOIN municipios m ON e.lugar_nacimiento_municipio_id = m.id
            WHERE e.id_cedula = ?
        ";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new \Exception('No se encontraron datos para la cédula: ' . $this->id_cedula);
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Obtener datos completos de todos los módulos
     */
    private function obtenerDatosCompletos() {
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
    }
    
    /**
     * Obtener datos de perfil
     */
    private function obtenerDatosPerfil() {
        $query = "SELECT * FROM perfil WHERE evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de cámara de comercio
     */
    private function obtenerDatosCamaraComercio() {
        $query = "
            SELECT 
                cc.*,
                tc.nombre as tiene_camara
            FROM camara_comercio cc
            LEFT JOIN tipo_respuesta tc ON cc.tiene_camara_id = tc.id
            WHERE cc.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de estado de salud
     */
    private function obtenerDatosEstadoSalud() {
        $query = "
            SELECT 
                es.*,
                es_estado.nombre as nombre_estado_salud,
                es_enfermedad.nombre as nombre_tipo_enfermedad,
                es_limitacion.nombre as nombre_limitacion_fisica,
                es_medicamento.nombre as nombre_tipo_medicamento,
                es_alcohol.nombre as nombre_ingiere_alcohol,
                es_fuma.nombre as nombre_fuma
            FROM estado_salud es
            LEFT JOIN tipo_respuesta es_estado ON es.estado_salud_id = es_estado.id
            LEFT JOIN tipo_respuesta es_enfermedad ON es.tipo_enfermedad_id = es_enfermedad.id
            LEFT JOIN tipo_respuesta es_limitacion ON es.limitacion_fisica_id = es_limitacion.id
            LEFT JOIN tipo_respuesta es_medicamento ON es.tipo_medicamento_id = es_medicamento.id
            LEFT JOIN tipo_respuesta es_alcohol ON es.ingiere_alcohol_id = es_alcohol.id
            LEFT JOIN tipo_respuesta es_fuma ON es.fuma_id = es_fuma.id
            WHERE es.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de composición familiar
     */
    private function obtenerDatosComposicionFamiliar() {
        $query = "
            SELECT 
                cf.*,
                p.nombre as parentesco_nombre,
                e.nombre as estado_civil_nombre
            FROM composicion_familiar cf
            LEFT JOIN parentesco p ON cf.parentesco_id = p.id
            LEFT JOIN estado_civil e ON cf.estado_civil_id = e.id
            WHERE cf.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY cf.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de información de pareja
     */
    private function obtenerDatosInformacionPareja() {
        $query = "
            SELECT 
                ip.*,
                td.nombre as tipo_documento_nombre,
                c.nombre as ciudad_nombre,
                e.nombre as estado_civil_nombre
            FROM informacion_pareja ip
            LEFT JOIN tipo_documento td ON ip.tipo_documento_id = td.id
            LEFT JOIN ciudades c ON ip.ciudad_id = c.id
            LEFT JOIN estado_civil e ON ip.estado_civil_id = e.id
            WHERE ip.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de tipo de vivienda
     */
    private function obtenerDatosTipoVivienda() {
        $query = "
            SELECT 
                tv.*,
                t.nombre as tipo_vivienda_nombre,
                e.nombre as estado_vivienda_nombre
            FROM tipo_vivienda tv
            LEFT JOIN tipo_vivienda t ON tv.tipo_vivienda_id = t.id
            LEFT JOIN estado_vivienda e ON tv.estado_vivienda_id = e.id
            WHERE tv.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de inventario
     */
    private function obtenerDatosInventario() {
        $query = "
            SELECT 
                i.*,
                c.nombre as categoria_nombre,
                e.nombre as estado_nombre
            FROM inventario i
            LEFT JOIN categoria_inventario c ON i.categoria_id = c.id
            LEFT JOIN estado_inventario e ON i.estado_id = e.id
            WHERE i.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY i.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de servicios
     */
    private function obtenerDatosServicios() {
        $query = "
            SELECT 
                s.*,
                ts.nombre as tipo_servicio_nombre,
                e.nombre as estado_nombre
            FROM servicios s
            LEFT JOIN tipo_servicio ts ON s.tipo_servicio_id = ts.id
            LEFT JOIN estado_servicio e ON s.estado_id = e.id
            WHERE s.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY s.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de patrimonio
     */
    private function obtenerDatosPatrimonio() {
        $query = "
            SELECT 
                p.*,
                tp.nombre as tipo_patrimonio_nombre,
                e.nombre as estado_nombre
            FROM patrimonio p
            LEFT JOIN tipo_patrimonio tp ON p.tipo_patrimonio_id = tp.id
            LEFT JOIN estado_patrimonio e ON p.estado_id = e.id
            WHERE p.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY p.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de cuentas bancarias
     */
    private function obtenerDatosCuentasBancarias() {
        $query = "
            SELECT 
                cb.*,
                b.nombre as banco_nombre,
                tc.nombre as tipo_cuenta_nombre
            FROM cuentas_bancarias cb
            LEFT JOIN bancos b ON cb.banco_id = b.id
            LEFT JOIN tipo_cuenta tc ON cb.tipo_cuenta_id = tc.id
            WHERE cb.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY cb.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de pasivos
     */
    private function obtenerDatosPasivos() {
        $query = "
            SELECT 
                p.*,
                tp.nombre as tipo_pasivo_nombre,
                e.nombre as estado_nombre
            FROM pasivos p
            LEFT JOIN tipo_pasivo tp ON p.tipo_pasivo_id = tp.id
            LEFT JOIN estado_pasivo e ON p.estado_id = e.id
            WHERE p.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY p.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de aportantes
     */
    private function obtenerDatosAportantes() {
        $query = "
            SELECT 
                a.*,
                ta.nombre as tipo_aportante_nombre,
                p.nombre as parentesco_nombre
            FROM aportantes a
            LEFT JOIN tipo_aportante ta ON a.tipo_aportante_id = ta.id
            LEFT JOIN parentesco p ON a.parentesco_id = p.id
            WHERE a.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY a.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de ingresos
     */
    private function obtenerDatosIngresos() {
        $query = "
            SELECT 
                i.*,
                ti.nombre as tipo_ingreso_nombre
            FROM ingresos i
            LEFT JOIN tipo_ingreso ti ON i.tipo_ingreso_id = ti.id
            WHERE i.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY i.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de gastos
     */
    private function obtenerDatosGastos() {
        $query = "
            SELECT 
                g.*,
                tg.nombre as tipo_gasto_nombre
            FROM gastos g
            LEFT JOIN tipo_gasto tg ON g.tipo_gasto_id = tg.id
            WHERE g.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY g.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de estudios
     */
    private function obtenerDatosEstudios() {
        $query = "
            SELECT 
                e.*,
                n.nombre as nivel_educativo_nombre,
                e_estado.nombre as estado_nombre
            FROM estudios e
            LEFT JOIN nivel_educativo n ON e.nivel_educativo_id = n.id
            LEFT JOIN estado_estudio e_estado ON e.estado_id = e_estado.id
            WHERE e.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY e.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de experiencia laboral
     */
    private function obtenerDatosExperienciaLaboral() {
        $query = "
            SELECT 
                el.*,
                c.nombre as cargo_nombre,
                e.nombre as estado_nombre
            FROM experiencia_laboral el
            LEFT JOIN cargos c ON el.cargo_id = c.id
            LEFT JOIN estado_experiencia e ON el.estado_id = e.id
            WHERE el.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY el.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de información judicial
     */
    private function obtenerDatosInformacionJudicial() {
        $query = "
            SELECT 
                ij.*,
                tj.nombre as tipo_judicial_nombre,
                e.nombre as estado_nombre
            FROM informacion_judicial ij
            LEFT JOIN tipo_judicial tj ON ij.tipo_judicial_id = tj.id
            LEFT JOIN estado_judicial e ON ij.estado_id = e.id
            WHERE ij.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY ij.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Obtener datos de concepto final
     */
    private function obtenerDatosConceptoFinal() {
        $query = "
            SELECT 
                cf.*,
                c.nombre as concepto_nombre
            FROM concepto_final cf
            LEFT JOIN conceptos c ON cf.concepto_id = c.id
            WHERE cf.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de ubicación
     */
    private function obtenerDatosUbicacion() {
        $query = "
            SELECT 
                u.*,
                d.nombre as departamento_nombre,
                m.nombre as municipio_nombre
            FROM ubicacion u
            LEFT JOIN departamentos d ON u.departamento_id = d.id
            LEFT JOIN municipios m ON u.municipio_id = m.id
            WHERE u.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }
    
    /**
     * Obtener datos de evidencias fotográficas
     */
    private function obtenerDatosEvidenciasFotograficas() {
        $query = "
            SELECT 
                ef.*,
                te.nombre as tipo_evidencia_nombre
            FROM evidencias_fotograficas ef
            LEFT JOIN tipo_evidencia te ON ef.tipo_evidencia_id = te.id
            WHERE ef.evaluado_id = (SELECT id FROM evaluados WHERE id_cedula = ?)
            ORDER BY ef.id
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $this->id_cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    
    /**
     * Generar PDF con los datos obtenidos
     */
    private function generarPDF($datos) {
        // Incluir el sistema modularizado
        require_once $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
        
        // Crear instancia del generador de informes
        $generador = new InformeModular($datos);
        
        // Generar el PDF
        $generador->generarInforme();
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
            
        } catch (\Exception $e) {
            $this->logger->logError('Error al obtener estadísticas: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Validar si el usuario tiene permisos para generar informe
     */
    public function validarPermisos() {
        // Aquí puedes agregar lógica de validación de permisos
        // Por ejemplo, verificar roles, permisos específicos, etc.
        return true;
    }
}

/**
 * CLASE PARA LOGGING
 */
class Logger {
    private $logFile = 'informe_controller_errors.log';
    
    public function log($message, $type = 'INFO') {
        $logMessage = date('[Y-m-d H:i:s]') . " [{$type}] {$message}\n";
        error_log($logMessage, 3, $logFile);
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
?> 