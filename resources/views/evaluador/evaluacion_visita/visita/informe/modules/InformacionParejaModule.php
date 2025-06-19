<?php
/**
 * MÓDULO DE INFORMACIÓN DE PAREJA
 * Maneja la información de la pareja del evaluado
 */

require_once 'BaseModule.php';

class InformacionParejaModule extends BaseModule {
    private $datos_pareja;
    
    protected function obtenerDatos() {
        $sql = "SELECT IP.id, IP.id_cedula,IP.cedula, IP.id_tipo_documentos, 
        IP.cedula_expedida, IP.nombres, IP.edad, IP.id_genero,
        IP.id_nivel_academico, IP.actividad, IP.empresa, IP.antiguedad, 
        IP.direccion_empresa, IP.telefono_1, IP.telefono_2, 
        IP.vive_candidato, 
        TD.nombre AS tipo_documento_nombre,
        G.id AS id_genero_pareja, 
        G.nombre AS nombre_genero, 
        NA.id AS id_nivel_academico_pareja, 
        NA.nombre AS nombre_nivel_academico 
        FROM informacion_pareja AS IP
        LEFT JOIN opc_tipo_documentos AS TD ON IP.id_tipo_documentos = TD.id 
        LEFT JOIN opc_genero AS G ON IP.id_genero = G.id 
        LEFT JOIN opc_nivel_academico AS NA ON IP.id_nivel_academico = NA.id
        WHERE IP.id_cedula = '{$this->id_cedula}'";
        
        $result = $this->ejecutarConsulta($sql);
        
        $this->datos_pareja = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clean_row = $this->limpiarDatos($row);
                $this->datos_pareja[] = $clean_row;
            }
        } else {
            // Si no hay datos, crear un registro vacío para evitar warnings
            $this->datos_pareja[] = [
                'id' => '',
                'id_cedula' => $this->id_cedula,
                'cedula' => 'No disponible',
                'id_tipo_documentos' => '',
                'cedula_expedida' => '',
                'nombres' => 'No disponible',
                'edad' => 'No disponible',
                'id_genero' => '',
                'id_nivel_academico' => '',
                'actividad' => 'No disponible',
                'empresa' => 'No disponible',
                'antiguedad' => 'No disponible',
                'direccion_empresa' => 'No disponible',
                'telefono_1' => 'No disponible',
                'telefono_2' => 'No disponible',
                'vive_candidato' => 'No disponible',
                'tipo_documento_nombre' => 'No disponible',
                'id_genero_pareja' => '',
                'nombre_genero' => 'No disponible',
                'id_nivel_academico_pareja' => '',
                'nombre_nivel_academico' => 'No disponible'
            ];
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        if (empty($this->datos_pareja) || count($this->datos_pareja) === 0) {
            return $this->getSeccionVacia('INFORMACIÓN DE PAREJA');
        }
        
        $headers = ['Nombre', 'Cédula', 'Edad', 'Género', 'Nivel Académico', 'Actividad', 'Empresa', 'Teléfono'];
        $data = [];
        
        foreach ($this->datos_pareja as $pareja) {
            $data[] = [
                $pareja['nombres'],
                $pareja['cedula'],
                $pareja['edad'],
                $pareja['nombre_genero'],
                $pareja['nombre_nivel_academico'],
                $pareja['actividad'],
                $pareja['empresa'],
                $pareja['telefono_1']
            ];
        }
        
        $tabla = $this->config->generarTabla('INFORMACIÓN DE PAREJA', $headers, $data, ['20%', '15%', '10%', '10%', '15%', '15%', '10%', '5%']);
        
        return $this->envolverTabla($tabla);
    }
} 