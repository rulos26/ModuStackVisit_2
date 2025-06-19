<?php
/**
 * MÓDULO DE PERFIL
 * Maneja la información personal y foto del evaluado
 */

require_once 'BaseModule.php';

class PerfilModule extends BaseModule {
    private $datos_perfil;
    private $ruta_imagen;
    
    protected function obtenerDatos() {
        // Obtener datos del evaluado
        $sql = "SELECT 
        e.id,e.id_cedula,e.id_tipo_documentos, e.cedula_expedida, e.nombres, e.apellidos, 
        e.edad, e.fecha_expedicion, e.lugar_nacimiento, e.celular_1, e.celular_2, e.telefono, 
        e.id_rh, e.id_estatura, e.peso_kg, e.id_estado_civil,e.hacer_cuanto, e.numero_hijos, e.direccion, 
        e.id_ciudad, e.localidad, e.barrio, e.id_estrato, e.correo, e.cargo,e.observacion,
        td.nombre AS tipo_documento_nombre,
        m1.municipio AS lugar_nacimiento_municipio,
        m2.municipio AS ciudad_nombre,
        rh.nombre AS rh_nombre,
        est.nombre AS estatura_nombre,
        ec.nombre AS estado_civil_nombre,
        es.nombre AS estrato_nombre
        FROM evaluados e
        LEFT JOIN opc_tipo_documentos td ON e.id_tipo_documentos = td.id
        LEFT JOIN municipios m1 ON e.lugar_nacimiento = m1.id_municipio
        LEFT JOIN municipios m2 ON e.id_ciudad = m2.id_municipio
        LEFT JOIN opc_rh rh ON e.id_rh = rh.id
        LEFT JOIN opc_estaturas est ON e.id_estatura = est.id
        LEFT JOIN opc_estado_civiles ec ON e.id_estado_civil = ec.id
        LEFT JOIN opc_estratos es ON e.id_estrato = es.id
        WHERE e.id_cedula = '{$this->id_cedula}'";
        
        $result = $this->ejecutarConsulta($sql);
        
        // Inicializar con valores por defecto
        $this->datos_perfil = [
            'id' => '',
            'id_cedula' => $this->id_cedula,
            'nombres' => 'No disponible',
            'apellidos' => 'No disponible',
            'edad' => 'No disponible',
            'cargo' => 'No disponible',
            'tipo_documento_nombre' => 'No disponible',
            'ciudad_nombre' => 'No disponible',
            'fecha_expedicion' => '',
            'lugar_nacimiento_municipio' => 'No disponible',
            'rh_nombre' => 'No disponible',
            'estatura_nombre' => 'No disponible',
            'estado_civil_nombre' => 'No disponible',
            'direccion' => 'No disponible',
            'telefono' => 'No disponible',
            'celular_1' => 'No disponible',
            'correo' => 'No disponible',
            'observacion' => ''
        ];
        
        if ($result && $result->num_rows > 0) {
            $temp_data = $result->fetch_assoc();
            $this->datos_perfil = array_merge($this->datos_perfil, $temp_data);
            $this->datos_perfil = $this->limpiarDatos($this->datos_perfil);
        }
        
        // Obtener foto de perfil
        $this->obtenerFotoPerfil();
    }
    
    private function obtenerFotoPerfil() {
        $sql = "SELECT * FROM foto_perfil_autorizacion WHERE id_cedula = {$this->id_cedula}";
        $result = $this->ejecutarConsulta($sql);
        
        $this->ruta_imagen = '';
        
        if ($result && $result->num_rows > 0) {
            $foto_data = $result->fetch_assoc();
            if (isset($foto_data['ruta']) && isset($foto_data['nombre']) && 
                !empty($foto_data['ruta']) && !empty($foto_data['nombre'])) {
                $this->ruta_imagen = $foto_data['ruta'] . $foto_data['nombre'];
            }
        }
    }
    
    public function generarSeccion() {
        $this->obtenerDatos();
        
        $perfil = $this->generarImagenPerfil();
        $infoEvaluador = $this->generarInfoEvaluador();
        
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td width='40%' style='border: 1px solid rgb(255, 255, 255);'>{$perfil}</td>
                <td width='20%' style='border: 1px solid rgb(255, 255, 255);'></td>
                <td width='40%' style='border: 1px solid rgb(255, 255, 255);'>{$infoEvaluador}</td>
            </tr>
        </table>";
    }
    
    private function generarImagenPerfil() {
        if (!empty($this->ruta_imagen) && file_exists($this->ruta_imagen)) {
            return '<img src="' . htmlspecialchars($this->ruta_imagen) . '" alt="Foto de perfil" style="border: 2px solid black; height: 177px; width: 200px;">';
        }
        return '<span>No hay imagen de perfil disponible.</span>';
    }
    
    private function generarInfoEvaluador() {
        global $fecha_actual;
        
        return "
        <table cellpadding='5' style='width: 100%;'>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>{$this->datos_perfil['nombres']}</td>
            </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>{$this->datos_perfil['cargo']}</td>
            </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>{$this->datos_perfil['id_cedula']}</td>
            </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>{$this->datos_perfil['edad']} años</td>
            </tr>
            <tr style='border: 1px solid rgb(255, 255, 255);'>
                <td style='border: 1px solid rgb(255, 255, 255); font-weight: bold; text-align: right;'>Fecha visita: {$fecha_actual}</td>
            </tr>
        </table>";
    }
    
    public function generarInformacionPersonal() {
        $headers = ['Campo', 'Valor'];
        $data = [
            ['Nombres', $this->datos_perfil['nombres']],
            ['Apellidos', $this->datos_perfil['apellidos']],
            ['Tipo de Documento', $this->datos_perfil['tipo_documento_nombre']],
            ['No. Documento', $this->datos_perfil['id_cedula']],
            ['Lugar de expedición', $this->datos_perfil['ciudad_nombre']],
            ['Edad', $this->datos_perfil['edad']],
            ['Fecha de Nacimiento', $this->formatter->formatDate($this->datos_perfil['fecha_expedicion'])],
            ['Lugar de Nacimiento', $this->datos_perfil['lugar_nacimiento_municipio']],
            ['Grupo Sanguíneo', $this->datos_perfil['rh_nombre']],
            ['Estatura', $this->datos_perfil['estatura_nombre']],
            ['Estado Civil', $this->datos_perfil['estado_civil_nombre']],
            ['Dirección', $this->datos_perfil['direccion']],
            ['Teléfono', $this->formatter->formatPhone($this->datos_perfil['telefono'])],
            ['Celular', $this->formatter->formatPhone($this->datos_perfil['celular_1'])],
            ['Email', $this->formatter->validateEmail($this->datos_perfil['correo'])],
            ['Cargo', $this->datos_perfil['cargo']],
            ['Observaciones', $this->datos_perfil['observacion']]
        ];
        
        $tabla = $this->config->generarTabla('INFORMACIÓN PERSONAL', $headers, $data, ['30%', '70%']);
        
        return $this->envolverTabla($tabla);
    }
} 