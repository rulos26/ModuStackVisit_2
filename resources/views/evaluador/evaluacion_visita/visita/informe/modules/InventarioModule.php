<?php
/**
 * MÓDULO DE INVENTARIO
 * Maneja el inventario de enseres del evaluado
 */

require_once 'BaseModule.php';

class InventarioModule extends BaseModule {
    private $datos_inventario;
    
    protected function obtenerDatos() {
        $sql = "SELECT 
        ie.televisor_cant, ie.dvd_cant, ie.teatro_casa_cant, ie.equipo_sonido_cant, 
        ie.computador_cant, ie.impresora_cant, ie.movil_cant,ie.estufa_cant,  ie.nevera_cant, 
        ie.lavadora_cant, ie.microondas_cant, ie.moto_cant, ie.carro_cant, ie.observacion,
        oe1.nombre AS televisor_nombre_cant,
        oe2.nombre AS dvd_nombre_cant,
        oe3.nombre AS teatro_casa_nombre_cant,
        oe4.nombre AS equipo_sonido_nombre_cant,
        oe5.nombre AS computador_nombre_cant,
        oe6.nombre AS impresora_nombre_cant,
        oe7.nombre AS movil_nombre_cant,
        oe8.nombre AS estufa_nombre_cant,
        oe9.nombre AS nevera_nombre_cant,
        oe10.nombre AS lavadora_nombre_cant,
        oe11.nombre AS microondas_nombre_cant,
        oe12.nombre AS moto_nombre_cant,
        oe13.nombre AS carro_nombre_cant
        FROM 
        inventario_enseres ie
        LEFT JOIN 
        opc_parametro oe1 ON ie.televisor_cant = oe1.id
        LEFT JOIN 
        opc_parametro oe2 ON ie.dvd_cant = oe2.id
        LEFT JOIN 
        opc_parametro oe3 ON ie.teatro_casa_cant = oe3.id
        LEFT JOIN 
        opc_parametro oe4 ON ie.equipo_sonido_cant = oe4.id
        LEFT JOIN 
        opc_parametro oe5 ON ie.computador_cant = oe5.id
        LEFT JOIN 
        opc_parametro oe6 ON ie.impresora_cant = oe6.id
        LEFT JOIN 
        opc_parametro oe7 ON ie.movil_cant = oe7.id
        LEFT JOIN 
        opc_parametro oe8 ON ie.estufa_cant = oe8.id
        LEFT JOIN 
        opc_parametro oe9 ON ie.nevera_cant = oe9.id
        LEFT JOIN 
        opc_parametro oe10 ON ie.lavadora_cant = oe10.id
        LEFT JOIN 
        opc_parametro oe11 ON ie.microondas_cant = oe11.id
        LEFT JOIN 
        opc_parametro oe12 ON ie.moto_cant = oe12.id
        LEFT JOIN 
        opc_parametro oe13 ON ie.carro_cant = oe13.id
        WHERE 
        ie.id_cedula = '{$this->id_cedula}'";
        
        $result = $this->ejecutarConsulta($sql);
        
        // Inicializar con valores por defecto
        $this->datos_inventario = [
            'id' => '',
            'id_cedula' => $this->id_cedula,
            'televisor_cant' => '',
            'dvd_cant' => '',
            'teatro_casa_cant' => '',
            'equipo_sonido_cant' => '',
            'computador_cant' => '',
            'impresora_cant' => '',
            'movil_cant' => '',
            'estufa_cant' => '',
            'nevera_cant' => '',
            'lavadora_cant' => '',
            'microondas_cant' => '',
            'moto_cant' => '',
            'carro_cant' => '',
            'observacion' => '',
            'televisor_nombre_cant' => 'No disponible',
            'dvd_nombre_cant' => 'No disponible',
            'teatro_casa_nombre_cant' => 'No disponible',
            'equipo_sonido_nombre_cant' => 'No disponible',
            'computador_nombre_cant' => 'No disponible',
            'impresora_nombre_cant' => 'No disponible',
            'movil_nombre_cant' => 'No disponible',
            'estufa_nombre_cant' => 'No disponible',
            'nevera_nombre_cant' => 'No disponible',
            'lavadora_nombre_cant' => 'No disponible',
            'microondas_nombre_cant' => 'No disponible',
            'moto_nombre_cant' => 'No disponible',
            'carro_nombre_cant' => 'No disponible'
        ];
        
        if ($result && $result->num_rows > 0) {
            $temp_data = $result->fetch_assoc();
            $this->datos_inventario = array_merge($this->datos_inventario, $temp_data);
            $this->datos_inventario = $this->limpiarDatos($this->datos_inventario);
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        if (empty($this->datos_inventario) || $this->datos_inventario['televisor_nombre_cant'] === 'No disponible') {
            return $this->getSeccionVacia('INVENTARIO DE ENSERES');
        }
        
        $headers = ['Artículo', 'Cantidad'];
        $data = [
            ['Televisor', $this->datos_inventario['televisor_nombre_cant']],
            ['DVD', $this->datos_inventario['dvd_nombre_cant']],
            ['Teatro en Casa', $this->datos_inventario['teatro_casa_nombre_cant']],
            ['Equipo de Sonido', $this->datos_inventario['equipo_sonido_nombre_cant']],
            ['Computador', $this->datos_inventario['computador_nombre_cant']],
            ['Impresora', $this->datos_inventario['impresora_nombre_cant']],
            ['Móvil', $this->datos_inventario['movil_nombre_cant']],
            ['Estufa', $this->datos_inventario['estufa_nombre_cant']],
            ['Nevera', $this->datos_inventario['nevera_nombre_cant']],
            ['Lavadora', $this->datos_inventario['lavadora_nombre_cant']],
            ['Microondas', $this->datos_inventario['microondas_nombre_cant']],
            ['Moto', $this->datos_inventario['moto_nombre_cant']],
            ['Carro', $this->datos_inventario['carro_nombre_cant']]
        ];
        
        $tabla = $this->config->generarTabla('INVENTARIO DE ENSERES', $headers, $data, ['70%', '30%']);
        
        return $this->envolverTabla($tabla);
    }
} 