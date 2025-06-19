<?php


include '../../../../../conn/conexion.php';
// Consulta SQL
$sql = "SELECT e.`id`, e.`id_cedula`, e.`id_tipo_documentos`, e.`cedula_expedida`, e.`nombres`, e.`apellidos`, u.`longitud`, u.`latitud`
        FROM `evaluados` e
        INNER JOIN `ubicacion` u ON e.`id_cedula` = u.`id_cedula`
        WHERE e.`id_cedula` = '1110456003'";

$result = $mysqli->query($sql);



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
                    <h5 class="card-title">darsh board</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                   
                </div>
<?php if ($result->num_rows > 0) {
    // Crear la tabla HTML
    echo "<table border='1'>
            <tr>
                 <th>Cédula Expedida</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Longitud</th>
                <th>Latitud</th>
                <th>opciones</th>
            </tr>";

    // Imprimir los datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["cedula_expedida"] . "</td>
                <td>" . $row["nombres"] . "</td>
                <td>" . $row["apellidos"] . "</td>
                <td>" . $row["longitud"] . "</td>
                <td>" . $row["latitud"] . "</td>
                <td> <a href='index.php'>editar</a></td>
            </tr>";
    }

    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}
?>


            </div>
            <div class="card-footer text-body-secondary">
                © 2024 V0.01
            </div>
        </div>

        <?php include '../footer/footer.php'; ?>
    </div>

    <script src="../../../../../js/toggleMenu.js"></script>
    <script src="../../../../../js/active_link.js"></script>



</body>

</html>