<?php
// Mostrar errores solo en desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

require_once __DIR__ . '/PatrimonioController.php';
use App\Controllers\PatrimonioController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = PatrimonioController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../cuentas_bancarias/cuentas_bancarias.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
        }
    } catch (Exception $e) {
        error_log("Error en tiene_patrimonio.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
    }
}

// Función para formatear valores monetarios para mostrar
function formatearValorMonetario($valor) {
    if (empty($valor) || $valor === 'N/A') {
        return '';
    }
    
    // Convertir a número si es string
    $numero = is_numeric($valor) ? floatval($valor) : 0;
    
    // Formatear con separadores de miles y decimales
    return number_format($numero, 2, '.', ',');
}

try {
    $controller = PatrimonioController::getInstance();
    $id_cedula = $_SESSION['id_cedula'];
    $datos_existentes = $controller->obtenerPorCedula($id_cedula);
    
    // Formatear valores monetarios para mostrar
    if ($datos_existentes) {
        $datos_existentes['valor_vivienda_formateado'] = formatearValorMonetario($datos_existentes['valor_vivienda'] ?? '');
        $datos_existentes['id_ahorro_formateado'] = formatearValorMonetario($datos_existentes['id_ahorro'] ?? '');
    }
    
    // Obtener opciones para los select
    $parametros = $controller->obtenerOpciones('parametro');
} catch (Exception $e) {
    error_log("Error en tiene_patrimonio.php: " . $e->getMessage());
    $error_message = "Error al cargar los datos: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="../../../../../public/css/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.steps-horizontal { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; width: 100%; gap: 0.5rem; }
.step-horizontal { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.step-horizontal:not(:last-child)::after { content: ''; position: absolute; top: 24px; left: 50%; width: 100%; height: 4px; background: #e0e0e0; z-index: 0; transform: translateX(50%); }
.step-horizontal .step-icon { width: 48px; height: 48px; border-radius: 50%; background: #e0e0e0; color: #888; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.5rem; border: 2px solid #e0e0e0; z-index: 1; transition: all 0.3s; }
.step-horizontal.active .step-icon { background: #4361ee; border-color: #4361ee; color: #fff; box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2); }
.step-horizontal.complete .step-icon { background: #2ecc71; border-color: #2ecc71; color: #fff; }
.step-horizontal .step-title { font-weight: bold; font-size: 1rem; margin-bottom: 0.2rem; }
.step-horizontal .step-description { font-size: 0.85rem; color: #888; text-align: center; }
.step-horizontal.active .step-title, .step-horizontal.active .step-description { color: #4361ee; }
.step-horizontal.complete .step-title, .step-horizontal.complete .step-description { color: #2ecc71; }
</style>

<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-bank me-2"></i>
                VISITA DOMICILIARÍA - PATRIMONIO
            </h5>
        </div>
        <div class="card-body">
            <!-- Indicador de pasos -->
            <div class="steps-horizontal mb-4">
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-user"></i></div>
                    <div class="step-title">Paso 1</div>
                    <div class="step-description">Datos Básicos</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                    <div class="step-title">Paso 2</div>
                    <div class="step-description">Información Personal</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-building"></i></div>
                    <div class="step-title">Paso 3</div>
                    <div class="step-description">Cámara de Comercio</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="step-title">Paso 4</div>
                    <div class="step-description">Salud</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-people"></i></div>
                    <div class="step-title">Paso 5</div>
                    <div class="step-description">Composición Familiar</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-heart"></i></div>
                    <div class="step-title">Paso 6</div>
                    <div class="step-description">Información Pareja</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-home"></i></div>
                    <div class="step-title">Paso 7</div>
                    <div class="step-description">Tipo de Vivienda</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="step-title">Paso 8</div>
                    <div class="step-description">Estado de Vivienda</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-box-seam"></i></div>
                    <div class="step-title">Paso 9</div>
                    <div class="step-description">Inventario de Enseres</div>
                </div>
                <div class="step-horizontal complete">
                    <div class="step-icon"><i class="fas fa-lightning-charge"></i></div>
                    <div class="step-title">Paso 10</div>
                    <div class="step-description">Servicios Públicos</div>
                </div>
                <div class="step-horizontal active">
                    <div class="step-icon"><i class="fas fa-bank"></i></div>
                    <div class="step-title">Paso 11</div>
                    <div class="step-description">Patrimonio</div>
                </div>
            </div>


            <!-- Mensajes de sesión -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($datos_existentes): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Ya existe información de patrimonio registrada para esta cédula. Puede actualizar los datos.
                </div>
            <?php endif; ?>
            
            
            <form action="" method="POST" id="formPatrimonio" novalidate autocomplete="off">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tiene_patrimonio" class="form-label required-field">
                            <i class="bi bi-question-circle me-1"></i>¿Posee usted patrimonio?
                        </label>
                        <select class="form-select" id="tiene_patrimonio" name="tiene_patrimonio" required onchange="toggleFormularioPatrimonio()">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($parametros as $parametro): ?>
                                <option value="<?php echo $parametro['id']; ?>" 
                                    <?php 
                                    // Determinar la selección basándose en los datos existentes
                                    if ($datos_existentes) {
                                        // Si existe un registro y el valor_vivienda no es 'N/A', entonces tiene patrimonio
                                        if ($datos_existentes['valor_vivienda'] != 'N/A' && $parametro['id'] != '1') {
                                            echo 'selected';
                                        } elseif ($datos_existentes['valor_vivienda'] == 'N/A' && $parametro['id'] == '1') {
                                            echo 'selected';
                                        }
                                    }
                                    ?>>
                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Seleccione "No" si no posee patrimonio, o "Sí" para continuar con el formulario detallado.</div>
                    </div>
                </div>
                
                <!-- Campos de patrimonio detallado (se muestran/ocultan dinámicamente) -->
                <div id="camposPatrimonio" class="campos-patrimonio" style="display: <?php echo ($datos_existentes && $datos_existentes['valor_vivienda'] != 'N/A') ? 'block' : 'none'; ?>;">
                    <hr class="my-4">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-bank me-2"></i>Detalles del Patrimonio
                    </h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="valor_vivienda" class="form-label">
                                <i class="bi bi-house-dollar me-1"></i>Valor de la Vivienda:
                            </label>
                            <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                <input type="text" class="form-control" id="valor_vivienda" name="valor_vivienda" 
                                           value="<?php echo $datos_existentes && isset($datos_existentes['valor_vivienda_formateado']) ? htmlspecialchars($datos_existentes['valor_vivienda_formateado']) : ''; ?>"
                                           placeholder="0.00" 
                                           title="Ingrese un valor válido en pesos colombianos (ej: $ 1,500,000.00)">
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Ingrese el valor estimado de su vivienda en pesos colombianos
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="direccion" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Dirección:
                            </label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?php echo $datos_existentes && $datos_existentes['direccion'] != 'N/A' ? htmlspecialchars($datos_existentes['direccion']) : ''; ?>"
                                   placeholder="Dirección de la vivienda" minlength="10">
                            <div class="form-text">Mínimo 10 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_vehiculo" class="form-label">
                                <i class="bi bi-car-front me-1"></i>Vehículo:
                            </label>
                            <input type="text" class="form-control" id="id_vehiculo" name="id_vehiculo" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_vehiculo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_vehiculo']) : ''; ?>"
                                   placeholder="Tipo de vehículo" minlength="3">
                            <div class="form-text">Mínimo 3 caracteres</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="id_marca" class="form-label">
                                <i class="bi bi-tag me-1"></i>Marca:
                            </label>
                            <input type="text" class="form-control" id="id_marca" name="id_marca" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_marca'] != 'N/A' ? htmlspecialchars($datos_existentes['id_marca']) : ''; ?>"
                                   placeholder="Marca del vehículo" minlength="2">
                            <div class="form-text">Mínimo 2 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_modelo" class="form-label">
                                <i class="bi bi-gear me-1"></i>Modelo:
                            </label>
                            <input type="text" class="form-control" id="id_modelo" name="id_modelo" 
                                   value="<?php echo $datos_existentes && $datos_existentes['id_modelo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_modelo']) : ''; ?>"
                                   placeholder="Modelo del vehículo" minlength="2">
                            <div class="form-text">Mínimo 2 caracteres</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_ahorro" class="form-label">
                                <i class="bi bi-piggy-bank me-1"></i>Ahorro (CDT, Inversiones):
                            </label>
                            <div class="currency-input currency-tooltip">
                            <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                <input type="text" class="form-control" id="id_ahorro" name="id_ahorro" 
                                           value="<?php echo $datos_existentes && isset($datos_existentes['id_ahorro_formateado']) ? htmlspecialchars($datos_existentes['id_ahorro_formateado']) : ''; ?>"
                                           placeholder="0.00"
                                           title="Ingrese un valor válido en pesos colombianos (ej: $ 500,000.00)">
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Ingrese el valor total de sus ahorros e inversiones
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="otros" class="form-label">
                                <i class="bi bi-plus-circle me-1"></i>Otros Bienes:
                            </label>
                            <input type="text" class="form-control" id="otros" name="otros" 
                                   value="<?php echo $datos_existentes && $datos_existentes['otros'] != 'N/A' ? htmlspecialchars($datos_existentes['otros']) : ''; ?>"
                                   placeholder="Otros bienes o activos">
                            <div class="form-text">Opcional - Otros bienes o activos que posea</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label for="observacion" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Observación:
                            </label>
                            <textarea class="form-control" id="observacion" name="observacion" 
                                      rows="4" maxlength="1000"><?php echo $datos_existentes && $datos_existentes['observacion'] != 'N/A' ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                            <div class="form-text">Máximo 1000 caracteres. Mínimo 10 caracteres si se llena.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <a href="../servicios_publicos/servicios_publicos.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
    <!-- Cleave.js para formateo de valores monetarios -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/addons/cleave-phone.co.js"></script>
    
<script>
    // Variables globales para los formateadores
    let cleaveValorVivienda, cleaveAhorro;
    
function toggleFormularioPatrimonio() {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    const camposPatrimonioDiv = document.getElementById('camposPatrimonio');
    const campos = camposPatrimonioDiv.querySelectorAll('input, select, textarea');

    // Determinar si debe mostrar los campos basándose en la selección
    // Asumiendo que '1' es "No" y cualquier otro valor es "Sí"
    if (tienePatrimonioSelect.value && tienePatrimonioSelect.value !== '1') {
        camposPatrimonioDiv.style.display = 'block';
            // Reinicializar los formateadores cuando se muestran los campos
            setTimeout(() => {
                inicializarFormateadores();
            }, 100);
    } else {
        camposPatrimonioDiv.style.display = 'none';
        // Limpiar todos los campos cuando se ocultan para no enviar datos antiguos
        campos.forEach(campo => {
            if (campo.type === 'select-one') {
                campo.selectedIndex = 0; // Resetea el select
            } else {
                campo.value = ''; // Limpia inputs y textareas
            }
        });
            // Destruir formateadores cuando se ocultan los campos
            if (cleaveValorVivienda) cleaveValorVivienda.destroy();
            if (cleaveAhorro) cleaveAhorro.destroy();
        }
    }
    
    function inicializarFormateadores() {
        // Destruir formateadores existentes si los hay
        if (cleaveValorVivienda) cleaveValorVivienda.destroy();
        if (cleaveAhorro) cleaveAhorro.destroy();
        
        // Inicializar Cleave.js para valor de vivienda
        const valorViviendaInput = document.getElementById('valor_vivienda');
        if (valorViviendaInput) {
            cleaveValorVivienda = new Cleave(valorViviendaInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: '.',
                delimiter: ',',
                numeralDecimalScale: 2,
                numeralIntegerScale: 10,
                prefix: '$ ',
                rawValueTrimPrefix: true,
                onValueChanged: function(e) {
                    // Remover clases de validación CSS que causan el rojo
                    valorViviendaInput.classList.remove('is-invalid');
                    valorViviendaInput.classList.add('is-valid');
                }
            });
        }
        
        // Inicializar Cleave.js para ahorro
        const ahorroInput = document.getElementById('id_ahorro');
        if (ahorroInput) {
            cleaveAhorro = new Cleave(ahorroInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: '.',
                delimiter: ',',
                numeralDecimalScale: 2,
                numeralIntegerScale: 10,
                prefix: '$ ',
                rawValueTrimPrefix: true,
                onValueChanged: function(e) {
                    // Remover clases de validación CSS que causan el rojo
                    ahorroInput.classList.remove('is-invalid');
                    ahorroInput.classList.add('is-valid');
            }
        });
    }
}

    // Función para inicializar el estado de los campos monetarios
    function inicializarEstadoCampos() {
        const valorVivienda = document.getElementById('valor_vivienda');
        const ahorro = document.getElementById('id_ahorro');
        
        // Si los campos tienen valores, marcarlos como válidos
        if (valorVivienda && valorVivienda.value.trim() !== '') {
            valorVivienda.classList.remove('is-invalid');
            valorVivienda.classList.add('is-valid');
        }
        
        if (ahorro && ahorro.value.trim() !== '') {
            ahorro.classList.remove('is-invalid');
            ahorro.classList.add('is-valid');
        }
    }

    // Ejecutar al cargar la página para establecer el estado inicial correcto
    document.addEventListener('DOMContentLoaded', function() {
        toggleFormularioPatrimonio();
        
        // Inicializar el estado de los campos después de un breve delay
        setTimeout(() => {
            inicializarEstadoCampos();
        }, 500);
    });

    // Función para validar formato monetario
    function validarFormatoMonetario(valor) {
        if (!valor || valor.trim() === '') return false;
        
        // Remover prefijo $ y espacios
        const valorLimpio = valor.replace(/^\$\s*/, '').trim();
        
        // Verificar que tenga formato válido para pesos colombianos
        // Acepta: 1000, 1,000, 1000.00, 1,000.00, etc.
        const regex = /^\d{1,3}(,\d{3})*(\.\d{2})?$/;
        
        if (!regex.test(valorLimpio)) return false;
        
        // Convertir a número y verificar que sea mayor a 0
        const numero = parseFloat(valorLimpio.replace(/,/g, ''));
        return !isNaN(numero) && numero > 0;
    }
    
    // Función para formatear valor monetario para envío
    function formatearValorParaEnvio(valor) {
        if (!valor) return '';
        // Remover símbolo $ y espacios, mantener solo números, comas y punto
        return valor.replace(/[$\s]/g, '').replace(/,/g, '');
    }

// Validación del formulario
document.getElementById('formPatrimonio').addEventListener('submit', function(event) {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    
    // Validar que se haya seleccionado una opción principal
    if (!tienePatrimonioSelect.value || tienePatrimonioSelect.value === '') {
        event.preventDefault();
            mostrarMensajeError('Por favor, seleccione si posee patrimonio.');
        tienePatrimonioSelect.focus();
        return;
    }
    
    // Validar campos de patrimonio solo si se seleccionó "Sí" (cualquier valor diferente a '1')
    if (tienePatrimonioSelect.value && tienePatrimonioSelect.value !== '1') {
        const camposObligatorios = [
                { id: 'valor_vivienda', nombre: 'Valor de la Vivienda', esMonetario: true },
                { id: 'direccion', nombre: 'Dirección', esMonetario: false },
                { id: 'id_vehiculo', nombre: 'Vehículo', esMonetario: false },
                { id: 'id_marca', nombre: 'Marca', esMonetario: false },
                { id: 'id_modelo', nombre: 'Modelo', esMonetario: false },
                { id: 'id_ahorro', nombre: 'Ahorro', esMonetario: true }
            ];
            
            for (const campo of camposObligatorios) {
                const elemento = document.getElementById(campo.id);
                const valor = elemento.value.trim();
                
                if (!valor) {
                    event.preventDefault();
                    mostrarMensajeError(`El campo "${campo.nombre}" es obligatorio.`);
                    elemento.focus();
                    return;
                }
                
                // Validación específica para campos monetarios
                if (campo.esMonetario && !validarFormatoMonetario(valor)) {
                event.preventDefault();
                    mostrarMensajeError(`El campo "${campo.nombre}" debe tener un formato monetario válido (ej: $ 1,500,000.00).`);
                elemento.focus();
                return;
                }
            }
            
            // Formatear valores monetarios antes del envío
            const valorVivienda = document.getElementById('valor_vivienda');
            const ahorro = document.getElementById('id_ahorro');
            
            if (valorVivienda.value) {
                valorVivienda.value = formatearValorParaEnvio(valorVivienda.value);
            }
            if (ahorro.value) {
                ahorro.value = formatearValorParaEnvio(ahorro.value);
            }
        }
    });
    
    // Función para mostrar mensajes de error mejorados
    function mostrarMensajeError(mensaje) {
        // Remover mensaje anterior si existe
        const mensajeAnterior = document.querySelector('.alert-error-temporal');
        if (mensajeAnterior) {
            mensajeAnterior.remove();
        }
        
        // Crear nuevo mensaje de error
        const mensajeError = document.createElement('div');
        mensajeError.className = 'alert alert-danger alert-dismissible fade show alert-error-temporal';
        mensajeError.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insertar el mensaje al inicio del formulario
        const formulario = document.getElementById('formPatrimonio');
        formulario.insertBefore(mensajeError, formulario.firstChild);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (mensajeError.parentNode) {
                mensajeError.remove();
            }
        }, 5000);
    }
</script>

<?php
$contenido = ob_get_clean();

// Verificar si la sesión ya está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: ../../../../../index.php');
    exit();
}

// Verificar que el usuario tenga rol de Evaluador (4)
if ($_SESSION['rol'] != 4) {
    header('Location: ../../../../../index.php');
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Evaluador';
$cedulaUsuario = $_SESSION['cedula'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patrimonio - Dashboard Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .logo-empresa {
            max-width: 200px;
            height: auto;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .steps-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            width: 100%;
            gap: 0.5rem;
        }
        .step-horizontal {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-horizontal:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateX(50%);
        }
        .step-horizontal .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #e0e0e0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-horizontal.active .step-icon {
            background: #4361ee;
            border-color: #4361ee;
            color: #fff;
            box-shadow: 0 0 0 5px rgba(67, 97, 238, 0.2);
        }
        .step-horizontal.complete .step-icon {
            background: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }
        .step-horizontal .step-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .step-horizontal .step-description {
            font-size: 0.85rem;
            color: #888;
            text-align: center;
        }
        .step-horizontal.active .step-title,
        .step-horizontal.active .step-description {
            color: #4361ee;
        }
        .step-horizontal.complete .step-title,
        .step-horizontal.complete .step-description {
            color: #2ecc71;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .invalid-feedback {
            font-size: 0.875rem;
        }
        .valid-feedback {
            font-size: 0.875rem;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        
        /* Estilos específicos para campos monetarios */
        .currency-input {
            position: relative;
        }
        
        .currency-input .form-control {
            padding-left: 2.5rem;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #2c5530;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            border: 2px solid #d4edda;
            transition: all 0.3s ease;
        }
        
        .currency-input .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            background: #ffffff;
        }
        
        .currency-input .input-group-text {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: 2px solid #28a745;
            font-weight: bold;
            border-radius: 8px 0 0 8px;
        }
        
        .currency-input .input-group .form-control {
            border-radius: 0 8px 8px 0;
            border-left: none;
        }
        
        .currency-input .input-group .form-control:focus {
            border-left: none;
        }
        
        /* Animación para campos monetarios */
        .currency-input .form-control:valid,
        .currency-input .form-control.is-valid {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #28a745;
        }
        
        .currency-input .form-control:invalid,
        .currency-input .form-control.is-invalid {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-color: #dc3545;
        }
        
        /* Estado normal para campos monetarios (sin validación automática) */
        .currency-input .form-control:not(:valid):not(:invalid):not(.is-valid):not(.is-invalid) {
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            border-color: #d4edda;
        }
        
        /* Tooltip para campos monetarios */
        .currency-tooltip {
            position: relative;
        }
        
        .currency-tooltip::after {
            content: "Formato: $ 1,234,567.89";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .currency-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Verde -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white text-center mb-4">
                        <i class="bi bi-clipboard-check"></i>
                        Evaluador
                    </h4>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../dashboardEvaluador.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../carta_visita/index_carta.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                Carta de Autorización
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="bi bi-house-door-fill me-2"></i>
                                Evaluación Visita Domiciliaria
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../../../../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Patrimonio</h1>
                            <p class="text-muted mb-0">Formulario de información patrimonial para evaluación</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></small><br>
                            <small class="text-muted">Cédula: <?php echo htmlspecialchars($cedulaUsuario); ?></small>
                        </div>
                    </div>

                    <!-- Contenido del formulario -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bank me-2"></i>
                                VISITA DOMICILIARÍA - PATRIMONIO
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Indicador de pasos -->
                            <div class="steps-horizontal mb-4">
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-user"></i></div>
                                    <div class="step-title">Paso 1</div>
                                    <div class="step-description">Información Personal</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-id-card"></i></div>
                                    <div class="step-title">Paso 2</div>
                                    <div class="step-description">Cámara de Comercio</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-heartbeat"></i></div>
                                    <div class="step-title">Paso 3</div>
                                    <div class="step-description">Salud</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-users"></i></div>
                                    <div class="step-title">Paso 4</div>
                                    <div class="step-description">Composición Familiar</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-heart"></i></div>
                                    <div class="step-title">Paso 5</div>
                                    <div class="step-description">Información Pareja</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-home"></i></div>
                                    <div class="step-title">Paso 6</div>
                                    <div class="step-description">Tipo de Vivienda</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                                    <div class="step-title">Paso 7</div>
                                    <div class="step-description">Estado de Vivienda</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-box-seam"></i></div>
                                    <div class="step-title">Paso 8</div>
                                    <div class="step-description">Inventario de Enseres</div>
                                </div>
                                <div class="step-horizontal complete">
                                    <div class="step-icon"><i class="fas fa-lightning-charge"></i></div>
                                    <div class="step-title">Paso 9</div>
                                    <div class="step-description">Servicios Públicos</div>
                                </div>
                                <div class="step-horizontal active">
                                    <div class="step-icon"><i class="fas fa-bank"></i></div>
                                    <div class="step-title">Paso 10</div>
                                    <div class="step-description">Patrimonio</div>
                                </div>
                            </div>

                            <!-- Mensajes de sesión -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $_SESSION['error']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $_SESSION['success']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($datos_existentes): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Ya existe información de patrimonio registrada para esta cédula. Puede actualizar los datos.
                                </div>
                            <?php endif; ?>
                            
                            <form action="" method="POST" id="formPatrimonio" novalidate autocomplete="off">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tiene_patrimonio" class="form-label required-field">
                                            <i class="bi bi-question-circle me-1"></i>¿Posee usted patrimonio?
                                        </label>
                                        <select class="form-select" id="tiene_patrimonio" name="tiene_patrimonio" required onchange="toggleFormularioPatrimonio()">
                                            <option value="">Seleccione una opción</option>
                                            <?php foreach ($parametros as $parametro): ?>
                                                <option value="<?php echo $parametro['id']; ?>" 
                                                    <?php 
                                                    // Determinar la selección basándose en los datos existentes
                                                    if ($datos_existentes) {
                                                        // Si existe un registro y el valor_vivienda no es 'N/A', entonces tiene patrimonio
                                                        if ($datos_existentes['valor_vivienda'] != 'N/A' && $parametro['id'] != '1') {
                                                            echo 'selected';
                                                        } elseif ($datos_existentes['valor_vivienda'] == 'N/A' && $parametro['id'] == '1') {
                                                            echo 'selected';
                                                        }
                                                    }
                                                    ?>>
                                                    <?php echo htmlspecialchars($parametro['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Seleccione "No" si no posee patrimonio, o "Sí" para continuar con el formulario detallado.</div>
                                    </div>
                                </div>
                                
                                <!-- Campos de patrimonio detallado (se muestran/ocultan dinámicamente) -->
                                <div id="camposPatrimonio" class="campos-patrimonio" style="display: <?php echo ($datos_existentes && $datos_existentes['valor_vivienda'] != 'N/A') ? 'block' : 'none'; ?>;">
                                    <hr class="my-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-bank me-2"></i>Detalles del Patrimonio
                                    </h6>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="valor_vivienda" class="form-label">
                                                <i class="bi bi-house-dollar me-1"></i>Valor de la Vivienda:
                                            </label>
                                            <div class="currency-input currency-tooltip">
                                            <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-currency-dollar"></i>
                                                    </span>
                                                <input type="text" class="form-control" id="valor_vivienda" name="valor_vivienda" 
                                                           value="<?php echo $datos_existentes && isset($datos_existentes['valor_vivienda_formateado']) ? htmlspecialchars($datos_existentes['valor_vivienda_formateado']) : ''; ?>"
                                                           placeholder="0.00" 
                                                           title="Ingrese un valor válido en pesos colombianos (ej: $ 1,500,000.00)">
                                                </div>
                                            </div>
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Ingrese el valor estimado de su vivienda en pesos colombianos
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="direccion" class="form-label">
                                                <i class="bi bi-geo-alt me-1"></i>Dirección:
                                            </label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                                   value="<?php echo $datos_existentes && $datos_existentes['direccion'] != 'N/A' ? htmlspecialchars($datos_existentes['direccion']) : ''; ?>"
                                                   placeholder="Dirección de la vivienda" minlength="10">
                                            <div class="form-text">Mínimo 10 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="id_vehiculo" class="form-label">
                                                <i class="bi bi-car-front me-1"></i>Vehículo:
                                            </label>
                                            <input type="text" class="form-control" id="id_vehiculo" name="id_vehiculo" 
                                                   value="<?php echo $datos_existentes && $datos_existentes['id_vehiculo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_vehiculo']) : ''; ?>"
                                                   placeholder="Tipo de vehículo" minlength="3">
                                            <div class="form-text">Mínimo 3 caracteres</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="id_marca" class="form-label">
                                                <i class="bi bi-tag me-1"></i>Marca:
                                            </label>
                                            <input type="text" class="form-control" id="id_marca" name="id_marca" 
                                                   value="<?php echo $datos_existentes && $datos_existentes['id_marca'] != 'N/A' ? htmlspecialchars($datos_existentes['id_marca']) : ''; ?>"
                                                   placeholder="Marca del vehículo" minlength="2">
                                            <div class="form-text">Mínimo 2 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="id_modelo" class="form-label">
                                                <i class="bi bi-gear me-1"></i>Modelo:
                                            </label>
                                            <input type="text" class="form-control" id="id_modelo" name="id_modelo" 
                                                   value="<?php echo $datos_existentes && $datos_existentes['id_modelo'] != 'N/A' ? htmlspecialchars($datos_existentes['id_modelo']) : ''; ?>"
                                                   placeholder="Modelo del vehículo" minlength="2">
                                            <div class="form-text">Mínimo 2 caracteres</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="id_ahorro" class="form-label">
                                                <i class="bi bi-piggy-bank me-1"></i>Ahorro (CDT, Inversiones):
                                            </label>
                                            <div class="currency-input currency-tooltip">
                                            <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-currency-dollar"></i>
                                                    </span>
                                                <input type="text" class="form-control" id="id_ahorro" name="id_ahorro" 
                                                           value="<?php echo $datos_existentes && isset($datos_existentes['id_ahorro_formateado']) ? htmlspecialchars($datos_existentes['id_ahorro_formateado']) : ''; ?>"
                                                           placeholder="0.00"
                                                           title="Ingrese un valor válido en pesos colombianos (ej: $ 500,000.00)">
                                                </div>
                                            </div>
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Ingrese el valor total de sus ahorros e inversiones
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="otros" class="form-label">
                                                <i class="bi bi-plus-circle me-1"></i>Otros Bienes:
                                            </label>
                                            <input type="text" class="form-control" id="otros" name="otros" 
                                                   value="<?php echo $datos_existentes && $datos_existentes['otros'] != 'N/A' ? htmlspecialchars($datos_existentes['otros']) : ''; ?>"
                                                   placeholder="Otros bienes o activos">
                                            <div class="form-text">Opcional - Otros bienes o activos que posea</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12 mb-3">
                                            <label for="observacion" class="form-label">
                                                <i class="bi bi-chat-text me-1"></i>Observación:
                                            </label>
                                            <textarea class="form-control" id="observacion" name="observacion" 
                                                      rows="4" maxlength="1000"><?php echo $datos_existentes && $datos_existentes['observacion'] != 'N/A' ? htmlspecialchars($datos_existentes['observacion']) : ''; ?></textarea>
                                            <div class="form-text">Máximo 1000 caracteres. Mínimo 10 caracteres si se llena.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg me-2">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <?php echo $datos_existentes ? 'Actualizar' : 'Guardar'; ?>
                                        </button>
                                        <a href="../servicios_publicos/servicios_publicos.php" class="btn btn-secondary btn-lg">
                                            <i class="bi bi-arrow-left me-2"></i>Volver
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/addons/cleave-phone.co.js"></script>
    
<script>
    // Variables globales para los formateadores
    let cleaveValorVivienda, cleaveAhorro;
    
function toggleFormularioPatrimonio() {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    const camposPatrimonioDiv = document.getElementById('camposPatrimonio');
    const campos = camposPatrimonioDiv.querySelectorAll('input, select, textarea');

    // Determinar si debe mostrar los campos basándose en la selección
    // Asumiendo que '1' es "No" y cualquier otro valor es "Sí"
    if (tienePatrimonioSelect.value && tienePatrimonioSelect.value !== '1') {
        camposPatrimonioDiv.style.display = 'block';
            // Reinicializar los formateadores cuando se muestran los campos
            setTimeout(() => {
                inicializarFormateadores();
            }, 100);
    } else {
        camposPatrimonioDiv.style.display = 'none';
        // Limpiar todos los campos cuando se ocultan para no enviar datos antiguos
        campos.forEach(campo => {
            if (campo.type === 'select-one') {
                campo.selectedIndex = 0; // Resetea el select
            } else {
                campo.value = ''; // Limpia inputs y textareas
            }
        });
            // Destruir formateadores cuando se ocultan los campos
            if (cleaveValorVivienda) cleaveValorVivienda.destroy();
            if (cleaveAhorro) cleaveAhorro.destroy();
        }
    }
    
    function inicializarFormateadores() {
        // Destruir formateadores existentes si los hay
        if (cleaveValorVivienda) cleaveValorVivienda.destroy();
        if (cleaveAhorro) cleaveAhorro.destroy();
        
        // Inicializar Cleave.js para valor de vivienda
        const valorViviendaInput = document.getElementById('valor_vivienda');
        if (valorViviendaInput) {
            cleaveValorVivienda = new Cleave(valorViviendaInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: '.',
                delimiter: ',',
                numeralDecimalScale: 2,
                numeralIntegerScale: 10,
                prefix: '$ ',
                rawValueTrimPrefix: true,
                onValueChanged: function(e) {
                    // Remover clases de validación CSS que causan el rojo
                    valorViviendaInput.classList.remove('is-invalid');
                    valorViviendaInput.classList.add('is-valid');
                }
            });
        }
        
        // Inicializar Cleave.js para ahorro
        const ahorroInput = document.getElementById('id_ahorro');
        if (ahorroInput) {
            cleaveAhorro = new Cleave(ahorroInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: '.',
                delimiter: ',',
                numeralDecimalScale: 2,
                numeralIntegerScale: 10,
                prefix: '$ ',
                rawValueTrimPrefix: true,
                onValueChanged: function(e) {
                    // Remover clases de validación CSS que causan el rojo
                    ahorroInput.classList.remove('is-invalid');
                    ahorroInput.classList.add('is-valid');
            }
        });
    }
}

    // Función para inicializar el estado de los campos monetarios
    function inicializarEstadoCampos() {
        const valorVivienda = document.getElementById('valor_vivienda');
        const ahorro = document.getElementById('id_ahorro');
        
        // Si los campos tienen valores, marcarlos como válidos
        if (valorVivienda && valorVivienda.value.trim() !== '') {
            valorVivienda.classList.remove('is-invalid');
            valorVivienda.classList.add('is-valid');
        }
        
        if (ahorro && ahorro.value.trim() !== '') {
            ahorro.classList.remove('is-invalid');
            ahorro.classList.add('is-valid');
        }
    }

    // Ejecutar al cargar la página para establecer el estado inicial correcto
    document.addEventListener('DOMContentLoaded', function() {
        toggleFormularioPatrimonio();
        
        // Inicializar el estado de los campos después de un breve delay
        setTimeout(() => {
            inicializarEstadoCampos();
        }, 500);
    });

    // Función para validar formato monetario
    function validarFormatoMonetario(valor) {
        if (!valor || valor.trim() === '') return false;
        
        // Remover prefijo $ y espacios
        const valorLimpio = valor.replace(/^\$\s*/, '').trim();
        
        // Verificar que tenga formato válido para pesos colombianos
        // Acepta: 1000, 1,000, 1000.00, 1,000.00, etc.
        const regex = /^\d{1,3}(,\d{3})*(\.\d{2})?$/;
        
        if (!regex.test(valorLimpio)) return false;
        
        // Convertir a número y verificar que sea mayor a 0
        const numero = parseFloat(valorLimpio.replace(/,/g, ''));
        return !isNaN(numero) && numero > 0;
    }
    
    // Función para formatear valor monetario para envío
    function formatearValorParaEnvio(valor) {
        if (!valor) return '';
        // Remover símbolo $ y espacios, mantener solo números, comas y punto
        return valor.replace(/[$\s]/g, '').replace(/,/g, '');
    }

// Validación del formulario
document.getElementById('formPatrimonio').addEventListener('submit', function(event) {
    const tienePatrimonioSelect = document.getElementById('tiene_patrimonio');
    
    // Validar que se haya seleccionado una opción principal
    if (!tienePatrimonioSelect.value || tienePatrimonioSelect.value === '') {
        event.preventDefault();
            mostrarMensajeError('Por favor, seleccione si posee patrimonio.');
        tienePatrimonioSelect.focus();
        return;
    }
    
    // Validar campos de patrimonio solo si se seleccionó "Sí" (cualquier valor diferente a '1')
    if (tienePatrimonioSelect.value && tienePatrimonioSelect.value !== '1') {
        const camposObligatorios = [
                { id: 'valor_vivienda', nombre: 'Valor de la Vivienda', esMonetario: true },
                { id: 'direccion', nombre: 'Dirección', esMonetario: false },
                { id: 'id_vehiculo', nombre: 'Vehículo', esMonetario: false },
                { id: 'id_marca', nombre: 'Marca', esMonetario: false },
                { id: 'id_modelo', nombre: 'Modelo', esMonetario: false },
                { id: 'id_ahorro', nombre: 'Ahorro', esMonetario: true }
            ];
            
            for (const campo of camposObligatorios) {
                const elemento = document.getElementById(campo.id);
                const valor = elemento.value.trim();
                
                if (!valor) {
                    event.preventDefault();
                    mostrarMensajeError(`El campo "${campo.nombre}" es obligatorio.`);
                    elemento.focus();
                    return;
                }
                
                // Validación específica para campos monetarios
                if (campo.esMonetario && !validarFormatoMonetario(valor)) {
                event.preventDefault();
                    mostrarMensajeError(`El campo "${campo.nombre}" debe tener un formato monetario válido (ej: $ 1,500,000.00).`);
                elemento.focus();
                return;
                }
            }
            
            // Formatear valores monetarios antes del envío
            const valorVivienda = document.getElementById('valor_vivienda');
            const ahorro = document.getElementById('id_ahorro');
            
            if (valorVivienda.value) {
                valorVivienda.value = formatearValorParaEnvio(valorVivienda.value);
            }
            if (ahorro.value) {
                ahorro.value = formatearValorParaEnvio(ahorro.value);
            }
        }
    });
    
    // Función para mostrar mensajes de error mejorados
    function mostrarMensajeError(mensaje) {
        // Remover mensaje anterior si existe
        const mensajeAnterior = document.querySelector('.alert-error-temporal');
        if (mensajeAnterior) {
            mensajeAnterior.remove();
        }
        
        // Crear nuevo mensaje de error
        const mensajeError = document.createElement('div');
        mensajeError.className = 'alert alert-danger alert-dismissible fade show alert-error-temporal';
        mensajeError.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insertar el mensaje al inicio del formulario
        const formulario = document.getElementById('formPatrimonio');
        formulario.insertBefore(mensajeError, formulario.firstChild);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (mensajeError.parentNode) {
                mensajeError.remove();
            }
        }, 5000);
    }
</script>
</body>
</html>