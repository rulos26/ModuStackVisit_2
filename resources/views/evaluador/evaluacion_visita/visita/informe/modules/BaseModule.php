<?php
/**
 * CLASE BASE PARA MÓDULOS DEL INFORME
 * Proporciona funcionalidad común para todos los módulos
 */

abstract class BaseModule {
    protected $id_cedula;
    protected $mysqli;
    protected $config;
    protected $formatter;
    
    public function __construct($id_cedula, $mysqli) {
        $this->id_cedula = $id_cedula;
        $this->mysqli = $mysqli;
        $this->config = new ConfiguracionInforme();
        $this->formatter = new DataFormatter();
    }
    
    /**
     * Obtener dato seguro de un array
     */
    protected function getDatoSeguro($array, $key, $default = 'No disponible') {
        return isset($array[$key]) && !empty($array[$key]) ? $array[$key] : $default;
    }
    
    /**
     * Envolver contenido en tabla estándar
     */
    protected function envolverTabla($contenido) {
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td width='100%' style='border: 1px solid rgb(255, 255, 255);'>{$contenido}</td>
            </tr>
        </table>";
    }
    
    /**
     * Generar sección vacía
     */
    protected function getSeccionVacia($titulo) {
        $tabla = $this->config->generarTabla($titulo, ['Estado'], [['No hay datos disponibles']], ['100%']);
        return $this->envolverTabla($tabla);
    }
    
    /**
     * Validar y limpiar array de datos
     */
    protected function limpiarDatos($array) {
        $clean_array = [];
        foreach ($array as $key => $value) {
            $clean_array[$key] = ($value === null || $value === '') ? 'No disponible' : $value;
        }
        return $clean_array;
    }
    
    /**
     * Ejecutar consulta SQL de forma segura
     */
    protected function ejecutarConsulta($sql) {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            error_log("Error en consulta SQL: " . $this->mysqli->error);
            return false;
        }
        return $result;
    }
    
    /**
     * Método abstracto que debe implementar cada módulo
     */
    abstract public function generarSeccion();
    
    /**
     * Método para obtener datos del módulo
     */
    abstract protected function obtenerDatos();
} 