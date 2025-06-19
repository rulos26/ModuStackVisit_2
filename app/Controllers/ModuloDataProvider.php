<?php
/**
 * MÓDULO DE PROVEEDOR DE DATOS DE MÓDULOS ESPECÍFICOS
 * Obtención de datos de todos los módulos del sistema de informes
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

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
?> 