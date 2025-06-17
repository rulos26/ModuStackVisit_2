
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class CerrarSesionController
{
    public function cerrar()
    {
       /*  if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /index.php');
        exit;  */
        session_start();
        session_unset();
        session_destroy();
        header('Location: https://concolombiaenlinea.com.co/ModuStackVisit_2/public/login.php');
        exit;
    }
}
