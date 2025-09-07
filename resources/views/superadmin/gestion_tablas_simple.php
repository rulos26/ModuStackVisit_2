<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tablas Principales - Versión Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="bi bi-database"></i> Gestión de Tablas Principales - Versión Simple</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- Botones de Acción -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="test_basico.php" class="btn btn-success me-2">
                                            <i class="bi bi-check-circle"></i> Test Básico
                                        </a>
                                        <a href="gestion_tablas_principales.php" class="btn btn-info">
                                            <i class="bi bi-arrow-left"></i> Versión Completa
                                        </a>
                                    </div>
                                    <button class="btn btn-danger" onclick="confirmarVaciarTablas()">
                                        <i class="bi bi-trash3"></i> Vaciar Tablas
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de Usuarios Evaluados -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="bi bi-people"></i> Usuarios Evaluados</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>ID Cédula</th>
                                                        <th>Nombres</th>
                                                        <th>Apellidos</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyUsuarios">
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            <i class="bi bi-hourglass-split"></i> Cargando usuarios...
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Área de Resultados -->
                        <div id="resultadoArea" class="mt-4" style="display: none;">
                            <div class="alert" id="resultadoAlert">
                                <span id="resultadoTexto"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para confirmar eliminación -->
    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>⚠️ ADVERTENCIA CRÍTICA</strong><br>
                        Esta acción eliminará TODOS los datos asociados al usuario seleccionado.
                    </div>
                    
                    <p><strong>Usuario:</strong> <span id="nombreUsuario"></span></p>
                    <p><strong>ID Cédula:</strong> <span id="idCedulaUsuario"></span></p>
                    
                    <div id="tablasConDatos" class="mt-3">
                        <p><strong>Tablas con datos:</strong></p>
                        <ul id="listaTablas"></ul>
                    </div>
                    
                    <div class="mt-3">
                        <label for="confirmacionEliminar" class="form-label">
                            Para confirmar, escribe: <strong>ELIMINAR_USUARIO_COMPLETO</strong>
                        </label>
                        <input type="text" class="form-control" id="confirmacionEliminar" placeholder="Escribe la confirmación aquí">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarEliminacionUsuario()">
                        <i class="bi bi-trash"></i> Eliminar Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para confirmar vaciar tablas -->
    <div class="modal fade" id="modalVaciarTablas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmar Vaciar Todas las Tablas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>🚨 ADVERTENCIA EXTREMA</strong><br>
                        Esta acción eliminará TODOS los datos de TODAS las tablas relacionadas.
                        Esta acción NO se puede deshacer.
                    </div>
                    
                    <div class="mt-3">
                        <label for="confirmacionVaciar" class="form-label">
                            Para confirmar, escribe: <strong>VACIAR_TODAS_LAS_TABLAS</strong>
                        </label>
                        <input type="text" class="form-control" id="confirmacionVaciar" placeholder="Escribe la confirmación aquí">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarVaciarTablas()">
                        <i class="bi bi-trash3"></i> Vaciar Todas las Tablas
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modalEliminarUsuario;
        let modalVaciarTablas;
        let usuarioSeleccionado = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            cargarUsuariosEvaluados();
            modalEliminarUsuario = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
            modalVaciarTablas = new bootstrap.Modal(document.getElementById('modalVaciarTablas'));
        });
        
        // Cargar usuarios evaluados
        function cargarUsuariosEvaluados() {
            fetch('procesar_simple.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'accion=obtener_usuarios_evaluados'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    mostrarError(`Error: ${data.error}<br><br>Haz clic en "Test Básico" para más información.`);
                    return;
                }
                
                const tbody = document.getElementById('tbodyUsuarios');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay usuarios evaluados</td></tr>';
                    return;
                }
                
                data.forEach(usuario => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${usuario.id_cedula}</td>
                        <td>${usuario.nombres}</td>
                        <td>${usuario.apellidos}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminarUsuario(${usuario.id_cedula}, '${usuario.nombres} ${usuario.apellidos}')">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError(`Error de conexión: ${error.message}<br><br>Verifica la conexión a la base de datos.`);
            });
        }
        
        // Confirmar eliminación de usuario
        function confirmarEliminarUsuario(idCedula, nombreCompleto) {
            usuarioSeleccionado = idCedula;
            
            document.getElementById('nombreUsuario').textContent = nombreCompleto;
            document.getElementById('idCedulaUsuario').textContent = idCedula;
            document.getElementById('confirmacionEliminar').value = '';
            
            // Obtener tablas con datos
            fetch('procesar_simple.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=verificar_tablas_con_datos&id_cedula=${idCedula}`
            })
            .then(response => response.json())
            .then(data => {
                const listaTablas = document.getElementById('listaTablas');
                listaTablas.innerHTML = '';
                
                if (data.error) {
                    listaTablas.innerHTML = '<li class="text-danger">Error al verificar tablas: ' + data.error + '</li>';
                } else if (data.length === 0) {
                    listaTablas.innerHTML = '<li class="text-muted">No se encontraron datos en tablas relacionadas</li>';
                } else {
                    data.forEach(tabla => {
                        const li = document.createElement('li');
                        li.textContent = tabla;
                        listaTablas.appendChild(li);
                    });
                }
                
                modalEliminarUsuario.show();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('listaTablas').innerHTML = '<li class="text-danger">Error al verificar tablas</li>';
                modalEliminarUsuario.show();
            });
        }
        
        // Ejecutar eliminación de usuario
        function ejecutarEliminacionUsuario() {
            const confirmacion = document.getElementById('confirmacionEliminar').value;
            
            if (confirmacion !== 'ELIMINAR_USUARIO_COMPLETO') {
                mostrarError('Debes escribir exactamente: ELIMINAR_USUARIO_COMPLETO');
                return;
            }
            
            // Aquí iría la lógica de eliminación
            mostrarResultado('Funcionalidad de eliminación no implementada en esta versión simple', 'warning');
            modalEliminarUsuario.hide();
        }
        
        // Confirmar vaciar tablas
        function confirmarVaciarTablas() {
            document.getElementById('confirmacionVaciar').value = '';
            modalVaciarTablas.show();
        }
        
        // Ejecutar vaciar tablas
        function ejecutarVaciarTablas() {
            const confirmacion = document.getElementById('confirmacionVaciar').value;
            
            if (confirmacion !== 'VACIAR_TODAS_LAS_TABLAS') {
                mostrarError('Debes escribir exactamente: VACIAR_TODAS_LAS_TABLAS');
                return;
            }
            
            // Aquí iría la lógica de vaciar tablas
            mostrarResultado('Funcionalidad de vaciar tablas no implementada en esta versión simple', 'warning');
            modalVaciarTablas.hide();
        }
        
        // Mostrar resultado
        function mostrarResultado(mensaje, tipo = 'success') {
            const resultadoArea = document.getElementById('resultadoArea');
            const resultadoAlert = document.getElementById('resultadoAlert');
            const resultadoTexto = document.getElementById('resultadoTexto');
            
            resultadoAlert.className = `alert alert-${tipo}`;
            resultadoTexto.innerHTML = mensaje;
            resultadoArea.style.display = 'block';
            
            // Scroll al resultado
            resultadoArea.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Mostrar error
        function mostrarError(mensaje) {
            mostrarResultado(mensaje, 'danger');
        }
    </script>
</body>
</html>
