<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
include '../../../../../conn/conexion.php';
// Verificar si el usuario ha iniciado sesión


?>
<div class="container mt-4">
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title">VISITA DOMICILIARÍA - INFORMACIÓN PERSONAL</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" width="65%" height="55%">

                </div>
                <div class="col-6">

                </div>
            </div>
            <form action="guardar.php" method="POST">
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="id_cedula" class="form-label">Número de Documento:</label>
                        <input type="number" class="form-control" id="id_cedula" name="id_cedula" value="<?php echo $_SESSION['id_cedula'] ; ?>" disabled required>
                    </div>
                    <div class="col-md-4">
                        <label for="id_tipo_documentos" class="form-label">Tipo de Documento:</label>
                        <select class="form-select" id="id_tipo_documentos" name="id_tipo_documentos">
                            <?php
                            // Consulta a la tabla de tipo de documentos
                            $consulta_tipo_documentos = "SELECT id, nombre FROM opc_tipo_documentos";
                            $resultado_tipo_documentos = $mysqli->query($consulta_tipo_documentos);

                            // Mostrar opciones en el selectbox
                            while ($fila_tipo_documento = $resultado_tipo_documentos->fetch_assoc()) {
                                echo "<option value='" . $fila_tipo_documento['id'] . "'>" . $fila_tipo_documento['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="cedula_expedida" class="form-label">Cédula expedida:</label>
                        <select class="form-select" id="cedula_expedida" name="cedula_expedida">
                            <?php
                            // Consulta a la tabla de municipios
                            // Reemplaza 'localhost', 'usuario', 'contraseña', 'nombre_base_de_datos' por tus propios datos

                            $consulta = "SELECT id_municipio, municipio FROM municipios";
                            $resultado = $mysqli->query($consulta);

                            // Mostrar opciones en el selectbox
                            while ($fila = $resultado->fetch_assoc()) {
                                echo "<option value='" . $fila['id_municipio'] . "'>" . $fila['municipio'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="nombres" class="form-label">Nombres:</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required>
                    </div>
                    <div class="col-md-4">
                        <label for="apellidos" class="form-label">Apellidos:</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="col-md-4">
                        <label for="edad" class="form-label">Edad:</label>
                        <input type="number" class="form-control" id="edad" name="edad" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_expedicion" class="form-label">Fecha de Expedición:</label>
                        <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" required>
                    </div>
                    <div class="col-md-4">
                        <label for="lugar_nacimiento" class="form-label">Lugar de Nacimiento:</label>
                        <select class="form-select" id="lugar_nacimiento" name="lugar_nacimiento">
                            <?php
                            // Consulta a la tabla de municipios para obtener el lugar de nacimiento
                            $consulta_lugar_nacimiento = "SELECT id_municipio, municipio FROM municipios";
                            $resultado_lugar_nacimiento = $mysqli->query($consulta_lugar_nacimiento);

                            // Mostrar opciones en el selectbox
                            while ($fila_lugar_nacimiento = $resultado_lugar_nacimiento->fetch_assoc()) {
                                echo "<option value='" . $fila_lugar_nacimiento['id_municipio'] . "'>" . $fila_lugar_nacimiento['municipio'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="celular_1" class="form-label">Celular 1:</label>
                        <input type="tel" class="form-control" id="celular_1" name="celular_1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="celular_2" class="form-label">Celular 2:</label>
                        <input type="tel" class="form-control" id="celular_2" name="celular_2">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="col-md-4">
                        <label for="id_rh" class="form-label">Tipo de RH:</label>
                        <select class="form-select" id="id_rh" name="id_rh">
                            <?php
                            // Consulta a la tabla de RH para obtener los tipos de RH
                            $consulta_rh = "SELECT id, nombre FROM opc_rh";
                            $resultado_rh = $mysqli->query($consulta_rh);

                            // Mostrar opciones en el selectbox
                            while ($fila_rh = $resultado_rh->fetch_assoc()) {
                                echo "<option value='" . $fila_rh['id'] . "'>" . $fila_rh['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="id_estatura" class="form-label">Estatura:</label>
                        <select class="form-select" id="id_estatura" name="id_estatura">
                            <?php
                            // Consulta a la tabla de estaturas para obtener las opciones de estatura
                            $consulta_estaturas = "SELECT id, nombre FROM opc_estaturas";
                            $resultado_estaturas = $mysqli->query($consulta_estaturas);

                            // Mostrar opciones en el selectbox
                            while ($fila_estatura = $resultado_estaturas->fetch_assoc()) {
                                echo "<option value='" . $fila_estatura['id'] . "'>" . $fila_estatura['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="peso_kg" class="form-label">Peso (kg):</label>
                        <select class="form-select" id="peso_kg" name="peso_kg">
                            <?php
                            // Consulta a la tabla de peso para obtener las opciones de peso en kg
                            $consulta_peso = "SELECT id, nombre FROM opc_peso";
                            $resultado_peso = $mysqli->query($consulta_peso);

                            // Mostrar opciones en el selectbox
                            while ($fila_peso = $resultado_peso->fetch_assoc()) {
                                echo "<option value='" . $fila_peso['id'] . "'>" . $fila_peso['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="id_estado_civil" class="form-label">Estado Civil:</label>
                        <select class="form-select" id="id_estado_civil" name="id_estado_civil">
                            <?php
                            // Consulta a la tabla de estado civil para obtener las opciones
                            $consulta_estado_civil = "SELECT id, nombre FROM opc_estado_civiles";
                            $resultado_estado_civil = $mysqli->query($consulta_estado_civil);

                            // Mostrar opciones en el selectbox
                            while ($fila_estado_civil = $resultado_estado_civil->fetch_assoc()) {
                                echo "<option value='" . $fila_estado_civil['id'] . "'>" . $fila_estado_civil['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="hacer_cuanto" class="form-label">Hace cuánto tiempo</label>
                        <input type="number" class="form-control" id="hacer_cuanto" name="hacer_cuanto">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_hijos" class="form-label">Número de Hijos:</label>
                        <input type="number" class="form-control" id="numero_hijos" name="numero_hijos">
                    </div>
                    <div class="col-md-4">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                    <div class="col-md-4">
                        <label for="id_ciudad" class="form-label">Ciudad:</label>
                        <select class="form-select" id="id_ciudad" name="id_ciudad">
                            <?php
                            // Consulta a la tabla de municipios para obtener las ciudades
                            $consulta_ciudad = "SELECT id_municipio, municipio FROM municipios";
                            $resultado_ciudad = $mysqli->query($consulta_ciudad);

                            // Mostrar opciones en el selectbox
                            while ($fila_ciudad = $resultado_ciudad->fetch_assoc()) {
                                echo "<option value='" . $fila_ciudad['id_municipio'] . "'>" . $fila_ciudad['municipio'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="localidad" class="form-label">Localidad:</label>
                        <input type="text" class="form-control" id="localidad" name="localidad">
                    </div>
                    <div class="col-md-4">
                        <label for="barrio" class="form-label">Barrio:</label>
                        <input type="text" class="form-control" id="barrio" name="barrio">
                    </div>
                    <div class="col-md-4">
                        <label for="id_estrato" class="form-label">Estrato:</label>
                        <select class="form-select" id="id_estrato" name="id_estrato">
                            <?php
                            // Consulta SQL para obtener las opciones de estrato
                            $sql_estrato = "SELECT `id`,`nombre` FROM `opc_estratos`";
                            $result_estrato = $mysqli->query($sql_estrato);

                            // Generar las opciones del select box de estrato
                            while ($row_estrato = $result_estrato->fetch_assoc()) {
                                echo "<option value='" . $row_estrato['id'] . "'>" . $row_estrato['nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="correo" class="form-label">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                    <div class="col-md-4">
                        <label for="cargo" class="form-label">Cargo:</label>
                        <input type="text" class="form-control" id="cargo" name="cargo">
                    </div>
                    <div class="col-md-12">
                        <label for="cargo" class="form-label">observación:</label>
                        <textarea id="observacion" class="form-control" name="observacion" rows="12" required></textarea>
                    </div>
                    <!-- fin de row -->
                </div>
                <button type="submit" class="btn btn-primary mt-3">Siguiente</button>
            </form>

        </div>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script src="../../../../../js/toggleMenu.js"></script>
<script src="../../../../../js/active_link.js"></script>
<script src="../../../../../js/autorizacion,js"></script>
<script src="../../../../../js/validar_password.js"></script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>