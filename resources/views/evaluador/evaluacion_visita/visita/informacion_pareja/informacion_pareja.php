<?php
session_start();
include '../../../../../conn/conexion.php';


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Visitas</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="../../../../../css/menu_style.css" rel="stylesheet">
    <link href="../../../../../css/footer.css" rel="stylesheet">
    <link href="../../../../../css/header.css" rel="stylesheet">


</head>

<body>

    <!-- Menú Vertical -->
    <?php include '../menu/menu.php'; ?>

    <!-- Navbar -->
    <?php include '../header/header.php'; ?>




    <!-- Contenido de la página -->
    <div style="margin-left: 250px; padding: 20px;">
        <div class="container">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">VISITA DOMICILIARÍA - INFORMACIÓN DE LA PAREJA (CÓNYUGE, COMPAÑERA SENTIMENTAL)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <form action="guardar.php" method="POST">
                        <div class="row">
                        <div class="col-md-4">
                                <label for="cedula" class="form-label">Cedula :</label>
                                <input type="number" class="form-control" id="cedula" name="ced" required>
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
                                <label for="nombres" class="form-label">Nombres Completo:</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edad" class="form-label">Edad:</label>
                                <input type="number" class="form-control" id="edad" name="edad" required>
                            </div>
                            <div class="col-md-4">
                                <label for="id_genero" class="form-label">Género:</label>
                                <select class="form-select" name="id_genero" id="id_genero">
                                    <?php

                                    // Consulta SQL para obtener los géneros
                                    $sql_genero = "SELECT id, nombre FROM opc_genero";
                                    $resultado_genero = $mysqli->query($sql_genero);

                                    // Mostrar los géneros en el selectbox
                                    while ($fila_genero = $resultado_genero->fetch_assoc()) {
                                        echo "<option value='" . $fila_genero['id'] . "'>" . $fila_genero['nombre'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="id_nivel_academico" class="form-label">Nivel Académico:</label>
                                <select class="form-select" name="id_nivel_academico" id="id_nivel_academico">
                                    <?php
                                    // Consulta SQL para obtener los niveles académicos
                                    $sql_nivel_academico = "SELECT id, nombre FROM opc_nivel_academico";
                                    $resultado_nivel_academico = $mysqli->query($sql_nivel_academico);

                                    // Mostrar los niveles académicos en el selectbox
                                    while ($fila_nivel_academico = $resultado_nivel_academico->fetch_assoc()) {
                                        echo "<option value='" . $fila_nivel_academico['id'] . "'>" . $fila_nivel_academico['nombre'] . "</option>";
                                    }

                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="actividad" class="form-label">Actividad:</label>
                                <input type="text" class="form-control" id="actividad" name="actividad" required>
                            </div>
                            <div class="col-md-4">
                                <label for="empresa" class="form-label">Empresa:</label>
                                <input type="text" class="form-control" id="empresa" name="empresa" required>
                            </div>
                            <div class="col-md-4">
                                <label for="antiguedad" class="form-label">Antigüedad:</label>
                                <input type="text" class="form-control" id="antiguedad" name="antiguedad" required>
                            </div>
                            <div class="col-md-4">
                                <label for="direccion_empresa" class="form-label">Dirección Empresa:</label>
                                <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa" required>
                            </div>
                            <div class="col-md-4">
                                <label for="telefono_1" class="form-label">Teléfono 1:</label>
                                <input type="tel" class="form-control" id="telefono_1" name="telefono_1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="telefono_2" class="form-label">Teléfono 2:</label>
                                <input type="tel" class="form-control" id="telefono_2" name="telefono_2">
                            </div>
                            <div class="col-md-4">
                                <label for="vive_candidato" class="form-label">Vive con el candidato:</label>
                                <select class="form-select" id="vive_candidato" name="vive_candidato">
                                    <?php
                                    // Consulta SQL para obtener los datos de conviven
                                    $sql_conviven = "SELECT `id`, `nombre` FROM `opc_parametro`";

                                    // Ejecutar la consulta
                                    $resultado_conviven = $mysqli->query($sql_conviven);

                                    // Mostrar los resultados en el selectbox
                                    while ($fila_conviven = $resultado_conviven->fetch_assoc()) {
                                        echo "<option value='" . $fila_conviven['id'] . "'>" . $fila_conviven['nombre'] . "</option>";
                                    }

                                    // Cerrar la conexión

                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="cargo" class="form-label">observación:</label>
                                <textarea id="observacion" class="form-control" name="observacion" rows="6" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Siguiente</button>

                    </form>

                </div>
                <div class="card-footer text-body-secondary">
                © 2024 V0.01
                </div>
            </div>
        </div>
        <?php include '../../footer/footer.php'; ?>
    </div>

    <script src="../../../../../js/toggleMenu.js"></script>
    <script src="../../../../../js/active_link.js"></script>
    <script src="../../../../../js/autorizacion,js"></script>
    <script src="../../../../../js/validar_password.js"></script>
    <script>

    </script>

</body>

</html>