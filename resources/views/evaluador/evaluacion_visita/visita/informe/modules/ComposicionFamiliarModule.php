<?php
/**
 * MÓDULO DE COMPOSICIÓN FAMILIAR
 * Maneja la información familiar del evaluado
 */

require_once 'BaseModule.php';

class ComposicionFamiliarModule extends BaseModule {
    private $datos_familia;
    
    protected function obtenerDatos() {
        $sql = "SELECT cf.id, cf.id_cedula, cf.nombre, cf.id_parentesco, cf.edad, cf.id_ocupacion, cf.telefono, cf.id_conviven,cf.observacion,
        op.nombre AS nombre_parentesco,
        oo.nombre AS nombre_ocupacion,
        opa.nombre AS nombre_parametro 
        FROM composicion_familiar cf
        LEFT JOIN opc_parentesco op ON cf.id_parentesco = op.id
        LEFT JOIN opc_ocupacion oo ON cf.id_ocupacion = oo.id
        LEFT JOIN opc_parametro opa ON cf.id_conviven = opa.id
        WHERE cf.id_cedula = '{$this->id_cedula}'";
        
        $result = $this->ejecutarConsulta($sql);
        
        $this->datos_familia = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clean_row = $this->limpiarDatos($row);
                $this->datos_familia[] = $clean_row;
            }
        } else {
            // Si no hay datos, crear un registro vacío para evitar warnings
            $this->datos_familia[] = [
                'id' => '',
                'id_cedula' => $this->id_cedula,
                'nombre' => 'No disponible',
                'id_parentesco' => '',
                'edad' => 'No disponible',
                'id_ocupacion' => '',
                'telefono' => 'No disponible',
                'id_conviven' => '',
                'observacion' => '',
                'nombre_parentesco' => 'No disponible',
                'nombre_ocupacion' => 'No disponible',
                'nombre_parametro' => 'No disponible'
            ];
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        if (empty($this->datos_familia) || count($this->datos_familia) === 0) {
            return $this->getSeccionVacia('COMPOSICIÓN FAMILIAR');
        }
        
        $headers = ['Nombre', 'Parentesco', 'Edad', 'Ocupación', 'Teléfono', 'Convivencia', 'Observaciones'];
        $data = [];
        
        foreach ($this->datos_familia as $familiar) {
            $data[] = [
                $familiar['nombre'],
                $familiar['nombre_parentesco'],
                $familiar['edad'],
                $familiar['nombre_ocupacion'],
                $familiar['telefono'],
                $familiar['nombre_parametro'],
                $familiar['observacion']
            ];
        }
        
        $tabla = $this->config->generarTabla('COMPOSICIÓN FAMILIAR', $headers, $data, ['20%', '15%', '10%', '20%', '15%', '10%', '10%']);
        
        return $this->envolverTabla($tabla);
    }
} 