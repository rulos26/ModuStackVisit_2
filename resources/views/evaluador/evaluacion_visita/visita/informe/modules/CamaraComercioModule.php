<?php
/**
 * MÓDULO DE CÁMARA DE COMERCIO
 * Maneja la información empresarial del evaluado
 */

require_once 'BaseModule.php';

class CamaraComercioModule extends BaseModule {
    private $datos_comercio;
    
    protected function obtenerDatos() {
        $sql = "SELECT * FROM camara_comercio WHERE id_cedula = {$this->id_cedula}";
        $result = $this->ejecutarConsulta($sql);
        
        // Inicializar con valores por defecto
        $this->datos_comercio = [
            'id' => '',
            'id_cedula' => $this->id_cedula,
            'tiene_camara' => 'No disponible',
            'nombre' => 'No disponible',
            'razon' => 'No disponible',
            'activdad' => 'No disponible',
            'observacion' => ''
        ];
        
        if ($result && $result->num_rows > 0) {
            $temp_data = $result->fetch_assoc();
            $this->datos_comercio = array_merge($this->datos_comercio, $temp_data);
            $this->datos_comercio = $this->limpiarDatos($this->datos_comercio);
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        if (empty($this->datos_comercio) || $this->datos_comercio['nombre'] === 'No disponible') {
            return $this->getSeccionVacia('CÁMARA DE COMERCIO');
        }
        
        $headers = ['Campo', 'Valor'];
        $data = [
            ['¿Tiene Cámara de Comercio?', $this->datos_comercio['tiene_camara']],
            ['Nombre de la Empresa', $this->datos_comercio['nombre']],
            ['Razón Social', $this->datos_comercio['razon']],
            ['Actividad', $this->datos_comercio['activdad']],
            ['Observaciones', $this->datos_comercio['observacion']]
        ];
        
        $tabla = $this->config->generarTabla('CÁMARA DE COMERCIO', $headers, $data, ['50%', '50%']);
        
        return $this->envolverTabla($tabla);
    }
} 