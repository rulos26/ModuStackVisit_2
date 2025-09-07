<?php
// Procesador simple sin dependencias complejas
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en la respuesta JSON
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.']);
    exit();
}

// Obtener la acción solicitada
$accion = $_POST['accion'] ?? '';

// Función para enviar respuesta JSON con manejo de errores
function enviarRespuesta($data) {
    // Limpiar cualquier salida previa
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Función para manejar errores
function manejarError($mensaje, $codigo = 500) {
    http_response_code($codigo);
    enviarRespuesta(['error' => $mensaje]);
}

try {
    // Conexión directa a la base de datos
    $pdo = new PDO(
        "mysql:host=localhost;dbname=u130454517_modulo_vista;charset=utf8mb4",
        'u130454517_root',
        '0382646740Ju*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    switch ($accion) {
        case 'obtener_usuarios_evaluados':
            // Primero verificar si la tabla evaluados existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'evaluados'");
            if ($stmt->rowCount() == 0) {
                echo json_encode(['error' => 'La tabla "evaluados" no existe en la base de datos']);
                break;
            }
            
            // Verificar la estructura de la tabla
            $stmt = $pdo->query("DESCRIBE evaluados");
            $columnas = $stmt->fetchAll();
            $columnasExistentes = array_column($columnas, 'Field');
            
            // Verificar si las columnas necesarias existen
            $columnasRequeridas = ['id_cedula', 'nombres', 'apellidos'];
            $columnasFaltantes = [];
            
            foreach ($columnasRequeridas as $columna) {
                if (!in_array($columna, $columnasExistentes)) {
                    $columnasFaltantes[] = $columna;
                }
            }
            
            if (!empty($columnasFaltantes)) {
                echo json_encode([
                    'error' => "Las siguientes columnas no existen en la tabla evaluados: " . implode(', ', $columnasFaltantes) . 
                              ". Columnas disponibles: " . implode(', ', $columnasExistentes)
                ]);
                break;
            }
            
            // Obtener los usuarios
            $stmt = $pdo->query("SELECT id_cedula, nombres, apellidos FROM evaluados WHERE id_cedula IS NOT NULL ORDER BY nombres, apellidos");
            $usuarios = $stmt->fetchAll();
            
            enviarRespuesta($usuarios);
            break;
            
        case 'verificar_tablas_con_datos':
            $idCedula = $_POST['id_cedula'] ?? '';
            
            if (empty($idCedula) || !is_numeric($idCedula)) {
                manejarError('ID de cédula inválido', 400);
            }
            
            // Lista de tablas relacionadas con sus campos de identificación
            $tablasRelacionadas = [
                'autorizaciones' => 'cedula',
                'camara_comercio' => 'id_cedula',
                'composicion_familiar' => 'id_cedula',
                'concepto_final_evaluador' => 'id_cedula',
                'cuentas_bancarias' => 'id_cedula',
                'data_credito' => 'id_cedula',
                'estados_salud' => 'id_cedula',
                'estado_vivienda' => 'id_cedula',
                'estudios' => 'id_cedula',
                'evidencia_fotografica' => 'id_cedula',
                'experiencia_laboral' => 'id_cedula',
                'firmas' => 'id_cedula',
                'foto_perfil_autorizacion' => 'id_cedula',
                'gasto' => 'id_cedula',
                'informacion_judicial' => 'id_cedula',
                'informacion_pareja' => 'id_cedula',
                'ingresos_mensuales' => 'id_cedula',
                'inventario_enseres' => 'id_cedula',
                'pasivos' => 'id_cedula',
                'patrimonio' => 'id_cedula',
                'servicios_publicos' => 'id_cedula',
                'tipo_vivienda' => 'id_cedula',
                'ubicacion' => 'id_cedula',
                'ubicacion_autorizacion' => 'id_cedula',
                'ubicacion_foto' => 'id_cedula',
                'foto_perfil_visita' => 'id_cedula'
            ];
            
            $tablasConDatos = [];
            
            // Verificar en evaluados
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM evaluados WHERE id_cedula = ?");
            $stmt->execute([$idCedula]);
            $result = $stmt->fetch();
            if ($result['count'] > 0) {
                $tablasConDatos[] = 'evaluados';
            }
            
            // Verificar en tablas relacionadas
            foreach ($tablasRelacionadas as $tabla => $campo) {
                try {
                    // Verificar si la tabla existe
                    $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                    if ($stmt->rowCount() > 0) {
                        // Verificar si tiene datos para este id_cedula usando el campo correcto
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$tabla` WHERE `$campo` = ?");
                        $stmt->execute([$idCedula]);
                        $result = $stmt->fetch();
                        if ($result['count'] > 0) {
                            $tablasConDatos[] = $tabla;
                        }
                    }
                } catch (Exception $e) {
                    // Si hay error con una tabla específica, continuar con las demás
                    error_log("Error verificando tabla $tabla: " . $e->getMessage());
                    continue;
                }
            }
            
            enviarRespuesta($tablasConDatos);
            break;
            
        case 'eliminar_usuario_completo':
            $idCedula = $_POST['id_cedula'] ?? '';
            $confirmacion = $_POST['confirmacion'] ?? '';
            
            if (empty($idCedula) || !is_numeric($idCedula)) {
                manejarError('ID de cédula inválido', 400);
            }
            
            if ($confirmacion !== 'ELIMINAR_USUARIO_COMPLETO') {
                manejarError('Confirmación incorrecta', 400);
            }
            
            // Lista de tablas relacionadas con sus campos de identificación
            $tablasRelacionadas = [
                'autorizaciones' => 'cedula',
                'camara_comercio' => 'id_cedula',
                'composicion_familiar' => 'id_cedula',
                'concepto_final_evaluador' => 'id_cedula',
                'cuentas_bancarias' => 'id_cedula',
                'data_credito' => 'id_cedula',
                'estados_salud' => 'id_cedula',
                'estado_vivienda' => 'id_cedula',
                'estudios' => 'id_cedula',
                'evidencia_fotografica' => 'id_cedula',
                'experiencia_laboral' => 'id_cedula',
                'firmas' => 'id_cedula',
                'foto_perfil_autorizacion' => 'id_cedula',
                'gasto' => 'id_cedula',
                'informacion_judicial' => 'id_cedula',
                'informacion_pareja' => 'id_cedula',
                'ingresos_mensuales' => 'id_cedula',
                'inventario_enseres' => 'id_cedula',
                'pasivos' => 'id_cedula',
                'patrimonio' => 'id_cedula',
                'servicios_publicos' => 'id_cedula',
                'tipo_vivienda' => 'id_cedula',
                'ubicacion' => 'id_cedula',
                'ubicacion_autorizacion' => 'id_cedula',
                'ubicacion_foto' => 'id_cedula',
                'foto_perfil_visita' => 'id_cedula'
            ];
            
            // Tablas que contienen archivos físicos
            $tablasConArchivos = ['firmas', 'foto_perfil_autorizacion', 'foto_perfil_visita', 'ubicacion_autorizacion', 'ubicacion_foto'];
            
            $pdo->beginTransaction();
            
            try {
                $archivosEliminados = [];
                $erroresArchivos = [];
                $registrosEliminados = 0;
                
                // Eliminar archivos físicos primero
                foreach ($tablasConArchivos as $tabla) {
                    try {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                        if ($stmt->rowCount() > 0) {
                            $campo = $tablasRelacionadas[$tabla];
                            $stmt = $pdo->prepare("SELECT ruta, nombre FROM `$tabla` WHERE `$campo` = ?");
                            $stmt->execute([$idCedula]);
                            $archivos = $stmt->fetchAll();
                            
                            foreach ($archivos as $archivo) {
                                if (!empty($archivo['ruta']) && !empty($archivo['nombre'])) {
                                    $rutaCompleta = $archivo['ruta'] . '/' . $archivo['nombre'];
                                    if (file_exists($rutaCompleta)) {
                                        if (unlink($rutaCompleta)) {
                                            $archivosEliminados[] = $rutaCompleta;
                                        } else {
                                            $erroresArchivos[] = "No se pudo eliminar: $rutaCompleta";
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $erroresArchivos[] = "Error procesando tabla $tabla: " . $e->getMessage();
                    }
                }
                
                // Eliminar registros de tablas relacionadas
                foreach ($tablasRelacionadas as $tabla => $campo) {
                    try {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                        if ($stmt->rowCount() > 0) {
                            $stmt = $pdo->prepare("DELETE FROM `$tabla` WHERE `$campo` = ?");
                            $stmt->execute([$idCedula]);
                            $registrosEliminados += $stmt->rowCount();
                        }
                    } catch (Exception $e) {
                        error_log("Error eliminando de tabla $tabla: " . $e->getMessage());
                    }
                }
                
                // Eliminar de la tabla evaluados
                $stmt = $pdo->prepare("DELETE FROM evaluados WHERE id_cedula = ?");
                $stmt->execute([$idCedula]);
                $registrosEliminados += $stmt->rowCount();
                
                $pdo->commit();
                
                enviarRespuesta([
                    'success' => true,
                    'mensaje' => 'Usuario eliminado completamente del sistema',
                    'id_cedula' => $idCedula,
                    'registros_eliminados' => $registrosEliminados,
                    'archivos_eliminados' => $archivosEliminados,
                    'errores_archivos' => $erroresArchivos
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'vaciar_todas_las_tablas':
            $confirmacion = $_POST['confirmacion'] ?? '';
            
            if ($confirmacion !== 'VACIAR_TODAS_LAS_TABLAS') {
                manejarError('Confirmación incorrecta', 400);
            }
            
            // Lista de tablas relacionadas
            $tablasRelacionadas = [
                'autorizaciones',
                'camara_comercio',
                'composicion_familiar',
                'concepto_final_evaluador',
                'cuentas_bancarias',
                'data_credito',
                'estados_salud',
                'estado_vivienda',
                'estudios',
                'evidencia_fotografica',
                'experiencia_laboral',
                'firmas',
                'foto_perfil_autorizacion',
                'gasto',
                'informacion_judicial',
                'informacion_pareja',
                'ingresos_mensuales',
                'inventario_enseres',
                'pasivos',
                'patrimonio',
                'servicios_publicos',
                'tipo_vivienda',
                'ubicacion',
                'ubicacion_autorizacion',
                'ubicacion_foto',
                'foto_perfil_visita'
            ];
            
            // Tablas que contienen archivos físicos
            $tablasConArchivos = ['firmas', 'foto_perfil_autorizacion', 'foto_perfil_visita', 'ubicacion_autorizacion', 'ubicacion_foto'];
            
            $pdo->beginTransaction();
            
            try {
                $archivosEliminados = [];
                $erroresArchivos = [];
                $tablasTruncadas = [];
                
                // Eliminar archivos físicos primero
                foreach ($tablasConArchivos as $tabla) {
                    try {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                        if ($stmt->rowCount() > 0) {
                            $stmt = $pdo->query("SELECT ruta, nombre FROM `$tabla`");
                            $archivos = $stmt->fetchAll();
                            
                            foreach ($archivos as $archivo) {
                                if (!empty($archivo['ruta']) && !empty($archivo['nombre'])) {
                                    $rutaCompleta = $archivo['ruta'] . '/' . $archivo['nombre'];
                                    if (file_exists($rutaCompleta)) {
                                        if (unlink($rutaCompleta)) {
                                            $archivosEliminados[] = $rutaCompleta;
                                        } else {
                                            $erroresArchivos[] = "No se pudo eliminar: $rutaCompleta";
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $erroresArchivos[] = "Error procesando tabla $tabla: " . $e->getMessage();
                    }
                }
                
                // Truncar tablas relacionadas
                foreach ($tablasRelacionadas as $tabla) {
                    try {
                        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
                        if ($stmt->rowCount() > 0) {
                            $pdo->exec("TRUNCATE TABLE `$tabla`");
                            $tablasTruncadas[] = $tabla;
                        }
                    } catch (Exception $e) {
                        error_log("Error truncando tabla $tabla: " . $e->getMessage());
                    }
                }
                
                // Truncar tabla evaluados
                $pdo->exec("TRUNCATE TABLE evaluados");
                $tablasTruncadas[] = 'evaluados';
                
                $pdo->commit();
                
                enviarRespuesta([
                    'success' => true,
                    'mensaje' => 'Todas las tablas han sido vaciadas completamente',
                    'tablas_truncadas' => $tablasTruncadas,
                    'archivos_eliminados' => $archivosEliminados,
                    'errores_archivos' => $erroresArchivos
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        default:
            manejarError('Acción no válida', 400);
    }
    
} catch (Exception $e) {
    // Log del error
    error_log("Error en procesar_simple.php: " . $e->getMessage() . " en línea " . $e->getLine());
    
    manejarError('Error interno del servidor: ' . $e->getMessage());
}
?>
