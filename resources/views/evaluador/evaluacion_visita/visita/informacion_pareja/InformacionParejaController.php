<?php
namespace App\Controllers;

require_once __DIR__ . '/../../../../../../app/Database/Database.php';
use App\Database\Database;
use PDOException;
use Exception;

class InformacionParejaController {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sanitizarDatos($datos) {
        $sanitizados = [];
        foreach ($datos as $clave => $valor) {
            if (is_string($valor)) {
                $sanitizados[$clave] = trim(strip_tags($valor));
            } else {
                $sanitizados[$clave] = $valor;
            }
        }
        return $sanitizados;
    }

    public function validarDatos($datos) {
        $errores = [];
        
        // Validar si tiene pareja (único campo obligatorio)
        if (empty($datos['tiene_pareja']) || $datos['tiene_pareja'] == '0') {
            $errores[] = 'Debe seleccionar si está en relación sentimental actual.';
            return $errores; // Si no selecciona, no validar más campos
        }
        
        // Si tiene pareja (valor 2), validar los campos adicionales
        if ($datos['tiene_pareja'] == '2') {
            // Validar cédula
            if (empty(trim($datos['cedula'])) || !is_numeric($datos['cedula'])) {
                $errores[] = 'La cédula de la pareja es obligatoria y debe ser numérica.';
            } elseif (strlen($datos['cedula']) < 8 || strlen($datos['cedula']) > 10) {
                $errores[] = 'La cédula debe tener entre 8 y 10 dígitos.';
            }
            
            // Validar tipo de documento
            if (empty($datos['id_tipo_documentos']) || $datos['id_tipo_documentos'] == '0' || $datos['id_tipo_documentos'] == '') {
                $errores[] = 'Debe seleccionar el tipo de documento.';
            }
            
            // Validar cédula expedida
            if (empty($datos['cedula_expedida']) || $datos['cedula_expedida'] == '0' || $datos['cedula_expedida'] == '') {
                $errores[] = 'Debe seleccionar dónde fue expedida la cédula.';
            }
            
            // Validar nombres
            if (empty(trim($datos['nombres'])) || strlen(trim($datos['nombres'])) < 2) {
                $errores[] = 'Los nombres completos son obligatorios y deben tener al menos 2 caracteres.';
            } elseif (strlen(trim($datos['nombres'])) > 100) {
                $errores[] = 'Los nombres no pueden exceder 100 caracteres.';
            }
            
            // Validar edad
            if (empty($datos['edad']) || !is_numeric($datos['edad']) || $datos['edad'] < 18 || $datos['edad'] > 120) {
                $errores[] = 'La edad debe estar entre 18 y 120 años.';
            }
            
            // Validar género
            if (empty($datos['id_genero']) || $datos['id_genero'] == '0' || $datos['id_genero'] == '') {
                $errores[] = 'Debe seleccionar el género.';
            }
            
            // Validar nivel académico
            if (empty($datos['id_nivel_academico']) || $datos['id_nivel_academico'] == '0' || $datos['id_nivel_academico'] == '') {
                $errores[] = 'Debe seleccionar el nivel académico.';
            }
            
            // Validar actividad
            if (empty(trim($datos['actividad'])) || strlen(trim($datos['actividad'])) < 2) {
                $errores[] = 'La actividad es obligatoria y debe tener al menos 2 caracteres.';
            } elseif (strlen(trim($datos['actividad'])) > 100) {
                $errores[] = 'La actividad no puede exceder 100 caracteres.';
            }
            
            // Validar empresa
            if (empty(trim($datos['empresa'])) || strlen(trim($datos['empresa'])) < 2) {
                $errores[] = 'La empresa es obligatoria y debe tener al menos 2 caracteres.';
            } elseif (strlen(trim($datos['empresa'])) > 100) {
                $errores[] = 'La empresa no puede exceder 100 caracteres.';
            }
            
            // Validar antigüedad
            if (empty(trim($datos['antiguedad'])) || strlen(trim($datos['antiguedad'])) < 1) {
                $errores[] = 'La antigüedad es obligatoria.';
            } elseif (strlen(trim($datos['antiguedad'])) > 50) {
                $errores[] = 'La antigüedad no puede exceder 50 caracteres.';
            }
            
            // Validar dirección empresa
            if (empty(trim($datos['direccion_empresa'])) || strlen(trim($datos['direccion_empresa'])) < 5) {
                $errores[] = 'La dirección de la empresa es obligatoria y debe tener al menos 5 caracteres.';
            } elseif (strlen(trim($datos['direccion_empresa'])) > 200) {
                $errores[] = 'La dirección de la empresa no puede exceder 200 caracteres.';
            }
            
            // Validar teléfono 1
            if (empty(trim($datos['telefono_1'])) || !preg_match('/^[0-9]{7,10}$/', trim($datos['telefono_1']))) {
                $errores[] = 'El teléfono 1 es obligatorio y debe tener entre 7 y 10 dígitos numéricos.';
            }
            
            // Validar teléfono 2 (opcional)
            if (!empty(trim($datos['telefono_2'])) && !preg_match('/^[0-9]{7,10}$/', trim($datos['telefono_2']))) {
                $errores[] = 'El teléfono 2 debe tener entre 7 y 10 dígitos numéricos.';
            }
            
            // Validar vive con candidato
            if (empty($datos['vive_candidato']) || $datos['vive_candidato'] == '0' || $datos['vive_candidato'] == '') {
                $errores[] = 'Debe seleccionar si vive con el candidato.';
            }
            
            // Validar observación (opcional pero si se ingresa debe tener límite)
            if (!empty(trim($datos['observacion'])) && strlen(trim($datos['observacion'])) > 1000) {
                $errores[] = 'La observación no puede exceder 1000 caracteres.';
            }
        }
        
        return $errores;
    }

    public function guardar($datos) {
        try {
            $id_cedula = $_SESSION['id_cedula'];
            $tiene_pareja = $datos['tiene_pareja'];
            
            // Si el usuario indica que NO tiene pareja
            if ($tiene_pareja == '1') {
                $existe = $this->obtenerPorCedula($id_cedula);
                $observacion_no_pareja = 'N/A - El usuario indica no tener pareja.';
                
                if ($existe) {
                    // Si ya existe un registro, se actualiza para limpiar los campos
                    $sql = "UPDATE informacion_pareja SET 
                            cedula = '00', id_tipo_documentos = NULL, cedula_expedida = NULL, 
                            nombres = '', edad = NULL, id_genero = NULL, 
                            id_nivel_academico = NULL, actividad = '', empresa = '', 
                            antiguedad = '', direccion_empresa = '', telefono_1 = '', 
                            telefono_2 = '', vive_candidato = NULL, 
                            observacion = :observacion
                            WHERE id_cedula = :id_cedula";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id_cedula', $id_cedula);
                    $stmt->bindParam(':observacion', $observacion_no_pareja);
                } else {
                    // Si no existe, se inserta un nuevo registro con campos vacíos/nulos
                    $sql = "INSERT INTO informacion_pareja (id_cedula, cedula, id_tipo_documentos, cedula_expedida, nombres, edad, id_genero, id_nivel_academico, actividad, empresa, antiguedad, direccion_empresa, telefono_1, telefono_2, vive_candidato, observacion) 
                            VALUES (:id_cedula, '00', NULL, NULL, '', NULL, NULL, NULL, '', '', '', '', '', '', NULL, :observacion)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':id_cedula', $id_cedula);
                    $stmt->bindParam(':observacion', $observacion_no_pareja);
                }
                $ok = $stmt->execute();
                
                if ($ok) {
                    return ['success' => true, 'message' => 'Se ha registrado que no tiene pareja.'];
                } else {
                    return ['success' => false, 'message' => 'Error al registrar la información.'];
                }
            }
            
            // Si tiene pareja, guardar toda la información
            $cedula = $datos['cedula'];
            $id_tipo_documentos = $datos['id_tipo_documentos'];
            $cedula_expedida = $datos['cedula_expedida'];
            $nombres = $datos['nombres'];
            $edad = $datos['edad'];
            $id_genero = $datos['id_genero'];
            $id_nivel_academico = $datos['id_nivel_academico'];
            $actividad = $datos['actividad'];
            $empresa = $datos['empresa'];
            $antiguedad = $datos['antiguedad'];
            $direccion_empresa = $datos['direccion_empresa'];
            $telefono_1 = $datos['telefono_1'];
            $telefono_2 = $datos['telefono_2'] ?? '';
            $vive_candidato = $datos['vive_candidato'];
            $observacion = $datos['observacion'] ?? '';

            $existe = $this->obtenerPorCedula($id_cedula);
            if ($existe) {
                $sql = "UPDATE informacion_pareja SET 
                        cedula = :cedula, id_tipo_documentos = :id_tipo_documentos, 
                        cedula_expedida = :cedula_expedida, nombres = :nombres, edad = :edad, 
                        id_genero = :id_genero, id_nivel_academico = :id_nivel_academico, actividad = :actividad, 
                        empresa = :empresa, antiguedad = :antiguedad, direccion_empresa = :direccion_empresa, 
                        telefono_1 = :telefono_1, telefono_2 = :telefono_2, vive_candidato = :vive_candidato, 
                        observacion = :observacion 
                        WHERE id_cedula = :id_cedula";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':cedula', $cedula);
                $stmt->bindParam(':id_tipo_documentos', $id_tipo_documentos);
                $stmt->bindParam(':cedula_expedida', $cedula_expedida);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':edad', $edad);
                $stmt->bindParam(':id_genero', $id_genero);
                $stmt->bindParam(':id_nivel_academico', $id_nivel_academico);
                $stmt->bindParam(':actividad', $actividad);
                $stmt->bindParam(':empresa', $empresa);
                $stmt->bindParam(':antiguedad', $antiguedad);
                $stmt->bindParam(':direccion_empresa', $direccion_empresa);
                $stmt->bindParam(':telefono_1', $telefono_1);
                $stmt->bindParam(':telefono_2', $telefono_2);
                $stmt->bindParam(':vive_candidato', $vive_candidato);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO informacion_pareja (id_cedula, cedula, id_tipo_documentos, cedula_expedida, nombres, edad, id_genero, id_nivel_academico, actividad, empresa, antiguedad, direccion_empresa, telefono_1, telefono_2, vive_candidato, observacion) 
                        VALUES (:id_cedula, :cedula, :id_tipo_documentos, :cedula_expedida, :nombres, :edad, :id_genero, :id_nivel_academico, :actividad, :empresa, :antiguedad, :direccion_empresa, :telefono_1, :telefono_2, :vive_candidato, :observacion)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cedula', $id_cedula);
                $stmt->bindParam(':cedula', $cedula);
                $stmt->bindParam(':id_tipo_documentos', $id_tipo_documentos);
                $stmt->bindParam(':cedula_expedida', $cedula_expedida);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':edad', $edad);
                $stmt->bindParam(':id_genero', $id_genero);
                $stmt->bindParam(':id_nivel_academico', $id_nivel_academico);
                $stmt->bindParam(':actividad', $actividad);
                $stmt->bindParam(':empresa', $empresa);
                $stmt->bindParam(':antiguedad', $antiguedad);
                $stmt->bindParam(':direccion_empresa', $direccion_empresa);
                $stmt->bindParam(':telefono_1', $telefono_1);
                $stmt->bindParam(':telefono_2', $telefono_2);
                $stmt->bindParam(':vive_candidato', $vive_candidato);
                $stmt->bindParam(':observacion', $observacion);
                $ok = $stmt->execute();
            }
            
            if ($ok) {
                return ['success'=>true, 'message'=>'Información de pareja guardada exitosamente.'];
            } else {
                return ['success'=>false, 'message'=>'Error al guardar la información de pareja.'];
            }
            
        } catch (PDOException $e) {
            return ['success'=>false, 'message'=>'Error de base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Error: ' . $e->getMessage()];
        }
    }

    public function obtenerPorCedula($id_cedula) {
        try {
            $sql = "SELECT * FROM informacion_pareja WHERE id_cedula = :id_cedula";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_cedula', $id_cedula);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerOpciones($tipo) {
        try {
            $tablas = [
                'parametro' => 'opc_parametro',
                'tipo_documentos' => 'opc_tipo_documentos',
                'municipios' => 'municipios',
                'genero' => 'opc_genero',
                'nivel_academico' => 'opc_nivel_academico'
            ];
            
            if (!isset($tablas[$tipo])) {
                throw new Exception("Tipo de opción no válido: $tipo");
            }
            
            $tabla = $tablas[$tipo];
            
            // Consulta específica para municipios
            if ($tipo === 'municipios') {
                $sql = "SELECT id_municipio, municipio FROM $tabla ORDER BY municipio";
            } else {
                $sql = "SELECT id, nombre FROM $tabla ORDER BY nombre";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
} 