<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
use Exception;
//pon el manejode errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class InformeFinalPdfController {
    
    public static function generarInforme() {
        session_start();
        $cedula = $_SESSION['id_cedula'] ?? '1231211322';
        
        $db = Database::getInstance()->getConnection();
        
        // Consulta del evaluado
        $sql_evaluado = "SELECT 
            e.id, e.id_cedula, e.id_tipo_documentos, e.cedula_expedida, e.nombres, e.apellidos, 
            e.edad, e.fecha_expedicion, e.lugar_nacimiento, e.celular_1, e.celular_2, e.telefono, 
            e.id_rh, e.id_estatura, e.peso_kg, e.id_estado_civil, e.hacer_cuanto, e.numero_hijos, e.direccion, 
            e.id_ciudad, e.localidad, e.barrio, e.id_estrato, e.correo, e.cargo, e.observacion,
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
        WHERE e.id_cedula = :cedula";
        
        $stmt = $db->prepare($sql_evaluado);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $evaluado = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Consulta de cámara de comercio
        $sql_camara = "SELECT 
            tiene_camara,
            nombre,
            razon,
            activdad,
            observacion
        FROM camara_comercio 
        WHERE id_cedula = :cedula";
        
        $stmt_camara = $db->prepare($sql_camara);
        $stmt_camara->bindParam(':cedula', $cedula);
        $stmt_camara->execute();
        $camara_comercio = $stmt_camara->fetch(\PDO::FETCH_ASSOC);

        // Consulta de estado de salud
        $sql_salud = "SELECT 
            es.id,
            es.id_cedula,
            es.id_estado_salud,
            oe.nombre AS nombre_estado_salud,
            es.tipo_enfermedad,
            es.tipo_enfermedad_cual,
            es.limitacion_fisica,
            es.limitacion_fisica_cual,
            es.tipo_medicamento,
            es.tipo_medicamento_cual,
            es.ingiere_alcohol,
            es.ingiere_alcohol_cual,
            es.fuma,
            es.observacion
        FROM estados_salud es
        JOIN opc_estados oe ON es.id_estado_salud = oe.id
        WHERE es.id_cedula = :cedula";
        
        $stmt_salud = $db->prepare($sql_salud);
        $stmt_salud->bindParam(':cedula', $cedula);
        $stmt_salud->execute();
        $estado_salud = $stmt_salud->fetch(\PDO::FETCH_ASSOC);

        // Función para convertir valores binarios a Sí/No
        function convertirSiNo($valor) {
            if ($valor === null || $valor === '') return 'N/A';
            return $valor == '0' ? 'Sí' : 'No';
        }

        // Procesar los campos de estado de salud
        if ($estado_salud) {
            $estado_salud['tipo_enfermedad'] = convertirSiNo($estado_salud['tipo_enfermedad']);
            $estado_salud['limitacion_fisica'] = convertirSiNo($estado_salud['limitacion_fisica']);
            $estado_salud['tipo_medicamento'] = convertirSiNo($estado_salud['tipo_medicamento']);
            $estado_salud['ingiere_alcohol'] = convertirSiNo($estado_salud['ingiere_alcohol']);
            $estado_salud['fuma'] = convertirSiNo($estado_salud['fuma']);
            
            // Convertir campos vacíos a N/A
            $campos_texto = ['tipo_enfermedad_cual', 'limitacion_fisica_cual', 'tipo_medicamento_cual', 
                           'ingiere_alcohol_cual', 'observacion', 'nombre_estado_salud'];
            foreach ($campos_texto as $campo) {
                $estado_salud[$campo] = empty($estado_salud[$campo]) ? 'N/A' : $estado_salud[$campo];
            }
        }

        // Consulta de composición familiar
        $sql_familia = "SELECT 
            cf.id, 
            cf.id_cedula, 
            cf.nombre, 
            cf.id_parentesco, 
            cf.edad, 
            cf.id_ocupacion, 
            cf.telefono, 
            cf.id_conviven,
            cf.observacion,
            op.nombre AS nombre_parentesco,
            oo.nombre AS nombre_ocupacion,
            opa.nombre AS nombre_parametro 
        FROM composicion_familiar cf
        LEFT JOIN opc_parentesco op ON cf.id_parentesco = op.id
        LEFT JOIN opc_ocupacion oo ON cf.id_ocupacion = oo.id
        LEFT JOIN opc_parametro opa ON cf.id_conviven = opa.id
        WHERE cf.id_cedula = :cedula";
        
        $stmt_familia = $db->prepare($sql_familia);
        $stmt_familia->bindParam(':cedula', $cedula);
        $stmt_familia->execute();
        $composicion_familiar = $stmt_familia->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de composición familiar
        if ($composicion_familiar) {
            foreach ($composicion_familiar as &$familiar) {
                // Convertir campos vacíos a N/A
                $campos_texto = ['nombre', 'nombre_parentesco', 'edad', 'nombre_ocupacion', 
                               'telefono', 'nombre_parametro', 'observacion'];
                foreach ($campos_texto as $campo) {
                    $familiar[$campo] = empty($familiar[$campo]) ? 'N/A' : $familiar[$campo];
                }
            }
            unset($familiar); // Romper la referencia
        }

        // Consulta de información de la pareja
        $sql_pareja = "SELECT 
            IP.id, 
            IP.id_cedula,
            IP.cedula, 
            IP.id_tipo_documentos, 
            IP.cedula_expedida, 
            IP.nombres, 
            IP.edad, 
            IP.id_genero,
            IP.id_nivel_academico, 
            IP.actividad, 
            IP.empresa, 
            IP.antiguedad, 
            IP.direccion_empresa, 
            IP.telefono_1, 
            IP.telefono_2, 
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
        WHERE IP.id_cedula = :cedula";
        
        $stmt_pareja = $db->prepare($sql_pareja);
        $stmt_pareja->bindParam(':cedula', $cedula);
        $stmt_pareja->execute();
        $informacion_pareja = $stmt_pareja->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de información de la pareja
        if ($informacion_pareja) {
            // Convertir campos vacíos a N/A
            $campos_texto = [
                'cedula', 'tipo_documento_nombre', 'cedula_expedida', 'nombres', 
                'edad', 'nombre_genero', 'nombre_nivel_academico', 'actividad', 
                'empresa', 'antiguedad', 'direccion_empresa', 'telefono_1', 
                'telefono_2'
            ];
            foreach ($campos_texto as $campo) {
                $informacion_pareja[$campo] = empty($informacion_pareja[$campo]) ? 'N/A' : $informacion_pareja[$campo];
            }

            // Convertir campo binario
            $informacion_pareja['vive_candidato'] = $informacion_pareja['vive_candidato'] === '0' ? 'Sí' : 
                                                   ($informacion_pareja['vive_candidato'] === '1' ? 'No' : 'N/A');
        }

        // Consulta de tipo de vivienda
        $sql_vivienda = "SELECT 
            tv.id,
            tv.id_cedula,
            tv.id_tipo_vivienda,
            tv.id_sector,
            tv.id_propietario,
            tv.numero_de_familia, 
            tv.personas_nucleo_familiar,
            tv.tiempo_sector,
            tv.numero_de_pisos,
            otv.nombre AS nombre_tipo_vivienda,
            os.nombre AS nombre_sector,
            op.nombre AS nombre_propiedad
        FROM tipo_vivienda AS tv
        JOIN opc_tipo_vivienda AS otv ON tv.id_tipo_vivienda = otv.id
        JOIN opc_sector AS os ON tv.id_sector = os.id
        JOIN opc_propiedad AS op ON tv.id_propietario = op.id
        WHERE tv.id_cedula = :cedula";
        
        $stmt_vivienda = $db->prepare($sql_vivienda);
        $stmt_vivienda->bindParam(':cedula', $cedula);
        $stmt_vivienda->execute();
        $tipo_vivienda = $stmt_vivienda->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de tipo de vivienda
        if ($tipo_vivienda) {
            // Convertir campos vacíos a N/A
            $campos_texto = [
                'nombre_tipo_vivienda',
                'nombre_sector',
                'nombre_propiedad',
                'numero_de_familia',
                'personas_nucleo_familiar',
                'tiempo_sector',
                'numero_de_pisos'
            ];
            foreach ($campos_texto as $campo) {
                $tipo_vivienda[$campo] = empty($tipo_vivienda[$campo]) ? 'N/A' : $tipo_vivienda[$campo];
            }
        }

        // Consulta de inventario de enseres
        $sql_inventario = "SELECT 
            ie.televisor_cant, ie.dvd_cant, ie.teatro_casa_cant, ie.equipo_sonido_cant, 
            ie.computador_cant, ie.impresora_cant, ie.movil_cant, ie.estufa_cant, ie.nevera_cant, 
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
        FROM inventario_enseres ie
        LEFT JOIN opc_parametro oe1 ON ie.televisor_cant = oe1.id
        LEFT JOIN opc_parametro oe2 ON ie.dvd_cant = oe2.id
        LEFT JOIN opc_parametro oe3 ON ie.teatro_casa_cant = oe3.id
        LEFT JOIN opc_parametro oe4 ON ie.equipo_sonido_cant = oe4.id
        LEFT JOIN opc_parametro oe5 ON ie.computador_cant = oe5.id
        LEFT JOIN opc_parametro oe6 ON ie.impresora_cant = oe6.id
        LEFT JOIN opc_parametro oe7 ON ie.movil_cant = oe7.id
        LEFT JOIN opc_parametro oe8 ON ie.estufa_cant = oe8.id
        LEFT JOIN opc_parametro oe9 ON ie.nevera_cant = oe9.id
        LEFT JOIN opc_parametro oe10 ON ie.lavadora_cant = oe10.id
        LEFT JOIN opc_parametro oe11 ON ie.microondas_cant = oe11.id
        LEFT JOIN opc_parametro oe12 ON ie.moto_cant = oe12.id
        LEFT JOIN opc_parametro oe13 ON ie.carro_cant = oe13.id
        WHERE ie.id_cedula = :cedula";
        
        $stmt_inventario = $db->prepare($sql_inventario);
        $stmt_inventario->bindParam(':cedula', $cedula);
        $stmt_inventario->execute();
        $inventario_enseres = $stmt_inventario->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de inventario de enseres
        if ($inventario_enseres) {
            // Lista de campos a procesar
            $campos_inventario = [
                'televisor_nombre_cant', 'dvd_nombre_cant', 'teatro_casa_nombre_cant',
                'equipo_sonido_nombre_cant', 'computador_nombre_cant', 'impresora_nombre_cant',
                'movil_nombre_cant', 'estufa_nombre_cant', 'nevera_nombre_cant',
                'lavadora_nombre_cant', 'microondas_nombre_cant', 'moto_nombre_cant',
                'carro_nombre_cant', 'observacion'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_inventario as $campo) {
                $inventario_enseres[$campo] = empty($inventario_enseres[$campo]) ? 'N/A' : $inventario_enseres[$campo];
            }
        }

        // Consulta de servicios públicos
        $sql_servicios = "SELECT sp.agua, sp.luz, sp.gas, sp.telefono, sp.alcantarillado, sp.internet, sp.administracion, sp.parqueadero, sp.observacion,
            op1.nombre AS nombre_agua, op2.nombre AS nombre_luz, op3.nombre AS nombre_gas, op4.nombre AS nombre_telefono,
            op5.nombre AS nombre_alcantarillado, op6.nombre AS nombre_internet, op7.nombre AS nombre_administracion,
            op8.nombre AS nombre_parqueadero
        FROM servicios_publicos sp
        LEFT JOIN opc_parametro op1 ON sp.agua = op1.id
        LEFT JOIN opc_parametro op2 ON sp.luz = op2.id
        LEFT JOIN opc_parametro op3 ON sp.gas = op3.id
        LEFT JOIN opc_parametro op4 ON sp.telefono = op4.id
        LEFT JOIN opc_parametro op5 ON sp.alcantarillado = op5.id
        LEFT JOIN opc_parametro op6 ON sp.internet = op6.id
        LEFT JOIN opc_parametro op7 ON sp.administracion = op7.id
        LEFT JOIN opc_parametro op8 ON sp.parqueadero = op8.id
        WHERE sp.id_cedula = :cedula";
        
        $stmt_servicios = $db->prepare($sql_servicios);
        $stmt_servicios->bindParam(':cedula', $cedula);
        $stmt_servicios->execute();
        $servicios_publicos = $stmt_servicios->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de servicios públicos
        if ($servicios_publicos) {
            // Lista de campos a procesar
            $campos_servicios = [
                'nombre_agua', 'nombre_luz', 'nombre_gas', 'nombre_telefono',
                'nombre_alcantarillado', 'nombre_internet', 'nombre_administracion',
                'nombre_parqueadero', 'observacion'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_servicios as $campo) {
                $servicios_publicos[$campo] = empty($servicios_publicos[$campo]) ? 'N/A' : $servicios_publicos[$campo];
            }
        }

        // Consulta de patrimonio
        $sql_patrimonio = "SELECT id, id_cedula, valor_vivienda, direccion,
            id_vehiculo, id_marca, id_modelo, id_ahorro, otros, observacion 
        FROM patrimonio 
        WHERE id_cedula = :cedula";
        
        $stmt_patrimonio = $db->prepare($sql_patrimonio);
        $stmt_patrimonio->bindParam(':cedula', $cedula);
        $stmt_patrimonio->execute();
        $patrimonio = $stmt_patrimonio->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de patrimonio
        if ($patrimonio) {
            // Lista de campos a procesar
            $campos_patrimonio = [
                'valor_vivienda', 'direccion', 'id_vehiculo', 'id_marca',
                'id_modelo', 'id_ahorro', 'otros', 'observacion'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_patrimonio as $campo) {
                $patrimonio[$campo] = empty($patrimonio[$campo]) ? 'N/A' : $patrimonio[$campo];
            }
        }

        // Consulta de cuentas bancarias
        $sql_cuentas = "SELECT 
            cb.id, 
            cb.id_cedula, 
            cb.id_entidad, 
            cb.id_tipo_cuenta, 
            cb.id_ciudad, 
            m.municipio AS ciudad,
            cb.observaciones
        FROM cuentas_bancarias AS cb
        LEFT JOIN municipios AS m ON cb.id_ciudad = m.id_municipio
        WHERE cb.id_cedula = :cedula";
        
        $stmt_cuentas = $db->prepare($sql_cuentas);
        $stmt_cuentas->bindParam(':cedula', $cedula);
        $stmt_cuentas->execute();
        $cuentas_bancarias = $stmt_cuentas->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de cuentas bancarias
        if ($cuentas_bancarias) {
            foreach ($cuentas_bancarias as &$cuenta) {
                // Lista de campos a procesar
                $campos_cuenta = [
                    'id_entidad', 'id_tipo_cuenta', 'ciudad', 'observaciones'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_cuenta as $campo) {
                    $cuenta[$campo] = empty($cuenta[$campo]) ? 'N/A' : $cuenta[$campo];
                }
            }
        }

        // Consulta de pasivos
        $sql_pasivos = "SELECT 
            p.item, 
            p.id_entidad, 
            p.id_tipo_inversion, 
            p.id_ciudad, 
            p.deuda, 
            p.cuota_mes,
            m.id_municipio, 
            m.municipio
        FROM pasivos p
        LEFT JOIN municipios m ON p.id_ciudad = m.id_municipio
        WHERE p.id_cedula = :cedula";
        
        $stmt_pasivos = $db->prepare($sql_pasivos);
        $stmt_pasivos->bindParam(':cedula', $cedula);
        $stmt_pasivos->execute();
        $pasivos = $stmt_pasivos->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de pasivos
        if ($pasivos) {
            foreach ($pasivos as &$pasivo) {
                // Lista de campos a procesar
                $campos_pasivo = [
                    'item', 'id_entidad', 'id_tipo_inversion', 'municipio', 'deuda', 'cuota_mes'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_pasivo as $campo) {
                    $pasivo[$campo] = empty($pasivo[$campo]) ? 'N/A' : $pasivo[$campo];
                }
            }
        }

        // Consulta de aportantes
        $sql_aportantes = "SELECT id, id_cedula, nombre, valor 
        FROM aportante 
        WHERE id_cedula = :cedula";
        
        $stmt_aportantes = $db->prepare($sql_aportantes);
        $stmt_aportantes->bindParam(':cedula', $cedula);
        $stmt_aportantes->execute();
        $aportantes = $stmt_aportantes->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de aportantes
        if ($aportantes) {
            foreach ($aportantes as &$aportante) {
                // Lista de campos a procesar
                $campos_aportante = [
                    'nombre', 'valor'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_aportante as $campo) {
                    $aportante[$campo] = empty($aportante[$campo]) ? 'N/A' : $aportante[$campo];
                }
            }
        }

        // Consulta de data crédito
        $sql_data_credito = "SELECT id_cedula, entidad, cuotas, pago_mensual, deuda 
        FROM data_credito 
        WHERE id_cedula = :cedula";
        
        $stmt_data_credito = $db->prepare($sql_data_credito);
        $stmt_data_credito->bindParam(':cedula', $cedula);
        $stmt_data_credito->execute();
        $data_credito = $stmt_data_credito->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de data crédito
        if ($data_credito) {
            foreach ($data_credito as &$credito) {
                // Lista de campos a procesar
                $campos_credito = [
                    'entidad', 'cuotas', 'pago_mensual', 'deuda'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_credito as $campo) {
                    $credito[$campo] = empty($credito[$campo]) ? 'N/A' : $credito[$campo];
                }
            }
        }

        // Consulta de ingresos mensuales
        $sql_ingresos = "SELECT * FROM ingresos_mensuales WHERE id_cedula = :cedula";
        
        $stmt_ingresos = $db->prepare($sql_ingresos);
        $stmt_ingresos->bindParam(':cedula', $cedula);
        $stmt_ingresos->execute();
        $ingresos_mensuales = $stmt_ingresos->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de ingresos mensuales
        if ($ingresos_mensuales) {
            // Lista de campos a procesar
            $campos_ingresos = [
                'salario_val', 'pension_val', 'arriendo_val', 'trabajo_independiente_val', 'otros_val'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_ingresos as $campo) {
                $ingresos_mensuales[$campo] = empty($ingresos_mensuales[$campo]) ? 'N/A' : $ingresos_mensuales[$campo];
            }
        }

        // Consulta de gastos
        $sql_gastos = "SELECT * FROM gasto WHERE id_cedula = :cedula";
        
        $stmt_gastos = $db->prepare($sql_gastos);
        $stmt_gastos->bindParam(':cedula', $cedula);
        $stmt_gastos->execute();
        $gastos = $stmt_gastos->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de gastos
        if ($gastos) {
            // Lista de campos a procesar
            $campos_gastos = [
                'alimentacion_val', 'educacion_val', 'salud_val', 'recreacion_val',
                'cuota_creditos_val', 'arriendo_val', 'servicios_publicos_val', 'otros_val'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_gastos as $campo) {
                $gastos[$campo] = empty($gastos[$campo]) ? 'N/A' : $gastos[$campo];
            }
        }

        // Consulta de estudios
        $sql_estudios = "SELECT e.id, e.id_cedula, e.centro_estudios, e.id_jornada, e.id_ciudad, e.anno, e.titulos, e.id_resultado,
            m.id_municipio, m.municipio
        FROM estudios e
        LEFT JOIN municipios m ON e.id_ciudad = m.id_municipio
        WHERE e.id_cedula = :cedula";
        
        $stmt_estudios = $db->prepare($sql_estudios);
        $stmt_estudios->bindParam(':cedula', $cedula);
        $stmt_estudios->execute();
        $estudios = $stmt_estudios->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de estudios
        if ($estudios) {
            foreach ($estudios as &$estudio) {
                // Lista de campos a procesar
                $campos_estudio = [
                    'centro_estudios', 'id_jornada', 'municipio', 'anno', 'titulos', 'id_resultado'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_estudio as $campo) {
                    $estudio[$campo] = empty($estudio[$campo]) ? 'N/A' : $estudio[$campo];
                }
            }
        }

        // Consulta de información judicial
        $sql_judicial = "SELECT
            ij.id,
            ij.id_cedula,
            ij.denuncias_opc,
            ij.denuncias_desc,
            ij.procesos_judiciales_opc,
            ij.procesos_judiciales_desc,
            ij.preso_opc,
            ij.preso_desc,
            ij.familia_detenido_opc,
            ij.familia_detenido_desc,
            ij.centros_penitenciarios_opc,
            ij.centros_penitenciarios_desc,
            ij.revi_fiscal,
            op1.nombre AS nombre_opcion1,
            op2.nombre AS nombre_opcion2,
            op3.nombre AS nombre_opcion3,
            op4.nombre AS nombre_opcion4,
            op5.nombre AS nombre_opcion5
        FROM informacion_judicial AS ij
        LEFT JOIN opc_parametro AS op1 ON ij.denuncias_opc = op1.id
        LEFT JOIN opc_parametro AS op2 ON ij.procesos_judiciales_opc = op2.id
        LEFT JOIN opc_parametro AS op3 ON ij.preso_opc = op3.id
        LEFT JOIN opc_parametro AS op4 ON ij.familia_detenido_opc = op4.id
        LEFT JOIN opc_parametro AS op5 ON ij.centros_penitenciarios_opc = op5.id
        WHERE ij.id_cedula = :cedula";
        
        $stmt_judicial = $db->prepare($sql_judicial);
        $stmt_judicial->bindParam(':cedula', $cedula);
        $stmt_judicial->execute();
        $informacion_judicial = $stmt_judicial->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de información judicial
        if ($informacion_judicial) {
            // Lista de campos a procesar
            $campos_judicial = [
                'denuncias_desc', 'procesos_judiciales_desc', 'preso_desc', 
                'familia_detenido_desc', 'centros_penitenciarios_desc', 'revi_fiscal',
                'nombre_opcion1', 'nombre_opcion2', 'nombre_opcion3', 'nombre_opcion4', 'nombre_opcion5'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_judicial as $campo) {
                $informacion_judicial[$campo] = empty($informacion_judicial[$campo]) ? 'N/A' : $informacion_judicial[$campo];
            }
        }

        // Consulta de experiencia laboral
        $sql_experiencia = "SELECT id, id_cedula, empresa, tiempo, cargo, salario, retiro, concepto,
            nombre, numero 
        FROM experiencia_laboral 
        WHERE id_cedula = :cedula";
        
        $stmt_experiencia = $db->prepare($sql_experiencia);
        $stmt_experiencia->bindParam(':cedula', $cedula);
        $stmt_experiencia->execute();
        $experiencia_laboral = $stmt_experiencia->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar los campos de experiencia laboral
        if ($experiencia_laboral) {
            foreach ($experiencia_laboral as &$experiencia) {
                // Lista de campos a procesar
                $campos_experiencia = [
                    'empresa', 'tiempo', 'cargo', 'salario', 'retiro', 'concepto', 'nombre', 'numero'
                ];

                // Convertir campos vacíos a N/A
                foreach ($campos_experiencia as $campo) {
                    $experiencia[$campo] = empty($experiencia[$campo]) ? 'N/A' : $experiencia[$campo];
                }
            }
        }

        // Consulta de concepto final del evaluador
        $sql_concepto_final = "SELECT c.id, c.id_cedula, c.actitud, c.condiciones_vivienda, c.dinamica_familiar, c.condiciones_economicas, 
            c.condiciones_academicas, c.evaluacion_experiencia_laboral, c.observaciones, c.id_concepto_final, 
            c.nombre_evaluador, c.id_concepto_seguridad, e.nombre AS estado_nombre
        FROM concepto_final_evaluador AS c
        LEFT JOIN opc_estados AS e ON c.id_concepto_final = e.id
        WHERE c.id_cedula = :cedula";
        
        $stmt_concepto_final = $db->prepare($sql_concepto_final);
        $stmt_concepto_final->bindParam(':cedula', $cedula);
        $stmt_concepto_final->execute();
        $concepto_final = $stmt_concepto_final->fetch(\PDO::FETCH_ASSOC);

        // Procesar los campos de concepto final
        if ($concepto_final) {
            // Lista de campos a procesar
            $campos_concepto = [
                'actitud', 'condiciones_vivienda', 'dinamica_familiar', 'condiciones_economicas',
                'condiciones_academicas', 'evaluacion_experiencia_laboral', 'observaciones', 
                'id_concepto_final', 'nombre_evaluador', 'id_concepto_seguridad', 'estado_nombre'
            ];

            // Convertir campos vacíos a N/A
            foreach ($campos_concepto as $campo) {
                $concepto_final[$campo] = empty($concepto_final[$campo]) ? 'N/A' : $concepto_final[$campo];
            }
        }

        // Consulta de ubicación foto
        $sql_ubicacion = "SELECT nombre 
        FROM ubicacion_autorizacion
        WHERE id_cedula = :cedula";
        
        $stmt_ubicacion = $db->prepare($sql_ubicacion);
        $stmt_ubicacion->bindParam(':cedula', $cedula);
        $stmt_ubicacion->execute();
        $ubicacion_foto = $stmt_ubicacion->fetch(\PDO::FETCH_ASSOC);
        
        // Consulta de evidencia fotográfica por tipo
        $sql_evidencia_fotografia = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 1";
        $sql_evidencia_fotografia_2 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 2";
        $sql_evidencia_fotografia_3 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 3";
        $sql_evidencia_fotografia_4 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 4";
        $sql_evidencia_fotografia_5 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 5";
        $sql_evidencia_fotografia_6 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 6";
        $sql_evidencia_fotografia_7 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 7";
        $sql_evidencia_fotografia_8 = "SELECT nombre from evidencia_fotografica where id_cedula = :cedula and tipo = 8";
        
        $stmt_evidencia_fotografia = $db->prepare($sql_evidencia_fotografia);
        $stmt_evidencia_fotografia->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia->execute();
        $evidencia_fotografia = $stmt_evidencia_fotografia->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_evidencia_fotografia_2 = $db->prepare($sql_evidencia_fotografia_2);
        $stmt_evidencia_fotografia_2->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_2->execute();
        $evidencia_fotografia_2 = $stmt_evidencia_fotografia_2->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt_evidencia_fotografia_3 = $db->prepare($sql_evidencia_fotografia_3);
        $stmt_evidencia_fotografia_3->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_3->execute();
        $evidencia_fotografia_3 = $stmt_evidencia_fotografia_3->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt_evidencia_fotografia_4 = $db->prepare($sql_evidencia_fotografia_4);
        $stmt_evidencia_fotografia_4->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_4->execute();
        $evidencia_fotografia_4 = $stmt_evidencia_fotografia_4->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_evidencia_fotografia_5 = $db->prepare($sql_evidencia_fotografia_5);
        $stmt_evidencia_fotografia_5->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_5->execute();
        $evidencia_fotografia_5 = $stmt_evidencia_fotografia_5->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_evidencia_fotografia_6 = $db->prepare($sql_evidencia_fotografia_6);
        $stmt_evidencia_fotografia_6->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_6->execute();
        $evidencia_fotografia_6 = $stmt_evidencia_fotografia_6->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_evidencia_fotografia_7 = $db->prepare($sql_evidencia_fotografia_7);
        $stmt_evidencia_fotografia_7->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_7->execute();
        $evidencia_fotografia_7 = $stmt_evidencia_fotografia_7->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_evidencia_fotografia_8 = $db->prepare($sql_evidencia_fotografia_8);
        $stmt_evidencia_fotografia_8->bindParam(':cedula', $cedula);
        $stmt_evidencia_fotografia_8->execute();
        $evidencia_fotografia_8 = $stmt_evidencia_fotografia_8->fetchAll(\PDO::FETCH_ASSOC);

        // Función para convertir imagen a base64
        function img_to_base64($img_path) {
            if (!file_exists($img_path)) return '';
            $info = pathinfo($img_path);
            $ext = strtolower($info['extension']);
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
            $data = base64_encode(file_get_contents($img_path));
            return 'data:' . $mime . ';base64,' . $data;
        }

        // Procesar evidencia fotográfica tipo 1
        $evidencia_fotografia_b64 = [];
        if ($evidencia_fotografia) {
            foreach ($evidencia_fotografia as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 2
        $evidencia_fotografia_2_b64 = [];
        if ($evidencia_fotografia_2) {
            foreach ($evidencia_fotografia_2 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_2_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 3
        $evidencia_fotografia_3_b64 = [];
        if ($evidencia_fotografia_3) {
            foreach ($evidencia_fotografia_3 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_3_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 4
        $evidencia_fotografia_4_b64 = [];
        if ($evidencia_fotografia_4) {
            foreach ($evidencia_fotografia_4 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_4_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 5
        $evidencia_fotografia_5_b64 = [];
        if ($evidencia_fotografia_5) {
            foreach ($evidencia_fotografia_5 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_5_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 6
        $evidencia_fotografia_6_b64 = [];
        if ($evidencia_fotografia_6) {
            foreach ($evidencia_fotografia_6 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_6_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 7
        $evidencia_fotografia_7_b64 = [];
        if ($evidencia_fotografia_7) {
            foreach ($evidencia_fotografia_7 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_7_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar evidencia fotográfica tipo 8
        $evidencia_fotografia_8_b64 = [];
        if ($evidencia_fotografia_8) {
            foreach ($evidencia_fotografia_8 as $evidencia) {
                $nombre_evidencia = $evidencia['nombre'];
                $evidencia_path = __DIR__ . '/../../public/images/evidencia_fotografica/'.$cedula.'/'.$nombre_evidencia;
                $evidencia_fotografia_8_b64[] = img_to_base64($evidencia_path);
            }
        }

        // Procesar la imagen de ubicación
        $fotoo_ubicacion_b64 = '';
        if ($ubicacion_foto && !empty($ubicacion_foto['nombre'])) {
            $nombre_ubicacion_foto = $ubicacion_foto['nombre'];
            $fotoo_ubicacion_path = __DIR__ . '/../../public/images/ubicacion_autorizacion/'.$cedula.'/'.$nombre_ubicacion_foto;
            $fotoo_ubicacion_b64 = img_to_base64($fotoo_ubicacion_path);
        }

        // Header - Logo
        $logo_path = __DIR__ . '/../../public/images/header.jpg';
        $logo_b64 = img_to_base64($logo_path);

        // --- Renderizado usando plantilla externa ---
        $data = [
            'cedula' => $cedula,
            'logo_b64' => $logo_b64,
            'evaluado' => $evaluado,
            'camara_comercio' => $camara_comercio,
            'estado_salud' => $estado_salud,
            'composicion_familiar' => $composicion_familiar,
            'informacion_pareja' => $informacion_pareja,
            'tipo_vivienda' => $tipo_vivienda,
            'inventario_enseres' => $inventario_enseres,
            'servicios_publicos' => $servicios_publicos,
            'patrimonio' => $patrimonio,
            'cuentas_bancarias' => $cuentas_bancarias,
            'pasivos' => $pasivos,
            'aportantes' => $aportantes,
            'data_credito' => $data_credito,
            'ingresos_mensuales' => $ingresos_mensuales,
            'gastos' => $gastos,
            'estudios' => $estudios,
            'informacion_judicial' => $informacion_judicial,
            'experiencia_laboral' => $experiencia_laboral,
            'concepto_final' => $concepto_final,
            'fotoo_ubicacion_b64' => $fotoo_ubicacion_b64,
            'evidencia_fotografia_b64' => $evidencia_fotografia_b64,
            'evidencia_fotografia_2_b64' => $evidencia_fotografia_2_b64,
            'evidencia_fotografia_3_b64' => $evidencia_fotografia_3_b64,
            'evidencia_fotografia_4_b64' => $evidencia_fotografia_4_b64,
            'evidencia_fotografia_5_b64' => $evidencia_fotografia_5_b64,
            'evidencia_fotografia_6_b64' => $evidencia_fotografia_6_b64,
            'evidencia_fotografia_7_b64' => $evidencia_fotografia_7_b64,
            'evidencia_fotografia_8_b64' => $evidencia_fotografia_8_b64
        ];
        extract($data);
        ob_start();
        include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
        $html = ob_get_clean();
        // --- Fin renderizado plantilla ---

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Enviar el PDF al navegador
        $dompdf->stream('informe_cedula_' . $cedula . '.pdf', ["Attachment" => false]);
        
        // Agregar JavaScript para redirección después de 5 segundos
        echo '<script>
            setTimeout(function() {
                window.location.href = "/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/index_evaluacion.php";
            }, 5000);
        </script>';
        
        exit;
    }
}

// Manejar la acción desde el menú
if (isset($_GET['action']) && $_GET['action'] === 'generarInforme') {
    InformeFinalPdfController::generarInforme();
}

?> 