<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Botón de hamburguesa para dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">holaaaaaa</span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Menú de navegación -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
            </ul>

            <!-- Nombre de la persona conectada y su rol -->
            <div class="navbar-text me-3">
                <?php
                // Iniciar la sesión
                //session_start();

                // Verificar si hay una sesión iniciada
                if (isset($_SESSION['username']) && isset($_SESSION['rol'])) {
                    echo "Bienvenido, " . $_SESSION['username'] . "  ";
                } else {
                    echo "Bienvenido";
                }
                ?>
            </div>

            <!-- Botón de cerrar sesión -->
            <form class="d-flex">
                <a href="../../../../../../logout.php" class="btn btn-outline-danger me-2" type="submit">Cerrar Sesión</a>
            </form>

            <!-- Menú desplegable -->
            <div class="dropdown">
                <!-- <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Opciones
                </button> -->
                <!--  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Opción 1</a></li>
                    <li><a class="dropdown-item" href="#">Opción 2</a></li>
                    <li><a class="dropdown-item" href="#">Opción 3</a></li>
                </ul> -->
            </div>
        </div>
    </div>
</nav>
