<?php
/**
 * MÓDULO DE ESTADO DE SALUD
 * Maneja la información médica del evaluado
 */

require_once 'BaseModule.php';

class EstadoSaludModule extends BaseModule {
    private $datos_salud;
    
    protected function obtenerDatos() {
        $sql = "SELECT e.id, e.id_cedula, oe1.nombre AS nombre_estado_salud, oe2.nombre AS nombre_tipo_enfermedad,
        e.tipo_enfermedad_cual, oe3.nombre AS nombre_limitacion_fisica, e.limitacion_fisica_cual,
        oe4.nombre AS nombre_tipo_medicamento, e.tipo_medicamento_cual, oe5.nombre AS nombre_ingiere_alcohol,
        e.ingiere_alcohol_cual, oe6.nombre AS nombre_fuma, e.observacion
        FROM estados_salud e
        LEFT JOIN opc_estados oe1 ON e.id_estado_salud = oe1.id
        LEFT JOIN opc_parametro oe2 ON e.tipo_enfermedad = oe2.id
        LEFT JOIN opc_parametro oe3 ON e.limitacion_fisica = oe3.id
        LEFT JOIN opc_parametro oe4 ON e.tipo_medicamento = oe4.id
        LEFT JOIN opc_parametro oe5 ON e.ingiere_alcohol = oe5.id
        LEFT JOIN opc_parametro oe6 ON e.fuma = oe6.id
        WHERE e.id_cedula = '{$this->id_cedula}'";
        
        $result = $this->ejecutarConsulta($sql);
        
        // Inicializar con valores por defecto
        $this->datos_salud = [
            'id' => '',
            'id_cedula' => $this->id_cedula,
            'nombre_estado_salud' => 'No disponible',
            'nombre_tipo_enfermedad' => 'No disponible',
            'tipo_enfermedad_cual' => 'No disponible',
            'nombre_limitacion_fisica' => 'No disponible',
            'limitacion_fisica_cual' => 'No disponible',
            'nombre_tipo_medicamento' => 'No disponible',
            'tipo_medicamento_cual' => 'No disponible',
            'nombre_ingiere_alcohol' => 'No disponible',
            'ingiere_alcohol_cual' => 'No disponible',
            'nombre_fuma' => 'No disponible',
            'observacion' => ''
        ];
        
        if ($result && $result->num_rows > 0) {
            $temp_data = $result->fetch_assoc();
            $this->datos_salud = array_merge($this->datos_salud, $temp_data);
            $this->datos_salud = $this->limpiarDatos($this->datos_salud);
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        if (empty($this->datos_salud) || $this->datos_salud['nombre_estado_salud'] === 'No disponible') {
            return $this->getSeccionVacia('ESTADO DE SALUD');
        }
        
        $headers = ['Campo', 'Valor', 'Detalle'];
        $data = [
            ['Estado de salud', $this->datos_salud['nombre_estado_salud'], ''],
            ['¿Padece algún tipo de enfermedad?', $this->datos_salud['nombre_tipo_enfermedad'], $this->datos_salud['tipo_enfermedad_cual']],
            ['¿Tiene alguna limitación física?', $this->datos_salud['nombre_limitacion_fisica'], $this->datos_salud['limitacion_fisica_cual']],
            ['¿Toma algún tipo de medicamento?', $this->datos_salud['nombre_tipo_medicamento'], $this->datos_salud['tipo_medicamento_cual']],
            ['¿Ingiere alcohol?', $this->datos_salud['nombre_ingiere_alcohol'], $this->datos_salud['ingiere_alcohol_cual']],
            ['¿Fuma?', $this->datos_salud['nombre_fuma'], ''],
            ['Observaciones', $this->datos_salud['observacion'], '']
        ];
        
        $tabla = $this->config->generarTabla('ESTADO DE SALUD DEL ASPIRANTE', $headers, $data, ['40%', '30%', '30%']);
        
        return $this->envolverTabla($tabla);
    }
} 