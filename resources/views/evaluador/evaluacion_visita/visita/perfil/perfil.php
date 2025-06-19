<?php
session_start();
include '../../../../../conn/conexion.php';

$id_cedula = $_SESSION['id_cedula'];
$sql = "SELECT * FROM foto_perfil_visita where id_cedula= $id_cedula";
$foto = $mysqli->query($sql);

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

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBEycUxZYCZ3FpzMyD6y-dx46PFRoX6fLI&libraries=places"></script>
    <style>
        /* Estilos para el mapa */
        #map {
            height: 400px;
            width: 100%;
        }
    </style>

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
                    <h5 class="card-title">VISITA DOMICILIARÍA-</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <img src="../../../../../img/empresa/logo.jpg" alt="Logotipo de la empresa" width="60%" height="50%">

                        </div>
                        <div class="col-6">

                        </div>
                    </div>

                    <form action="guardar.php" method="POST" enctype="multipart/form-data">

                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-header">
                                        Foto de Perfil
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        if ($foto->num_rows > 0) {
                                            echo '<center><img src="../../../../../img/ok.png" alt="" width="50%" height="50%"></center>';
                                            echo '<center><b>La foto de perfil ya se encuentra en el sistemas</b></center>';
                                        } else {
                                            echo  '  <div class="mb-3">
                                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Subir</button>
                                        
                                        ';
                                        }


                                        ?>






                                    </div>
                                </div>
                            </div>
                            <div class="col-2"></div>

                        </div>
                        <?php
                        if ($foto->num_rows > 0) {
                            echo '<a href="../registro_fotos/registro_fotos.php" class="btn btn-primary">Siguiente</a>
                            ';
                         } else {
                            
                        }


                        ?>
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
    <script>
        var rutaImagen = 'ruta_de_tu_imagen.jpg';
    </script>

</body>

</html>