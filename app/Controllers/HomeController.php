<?php
namespace App\Controllers;

class HomeController {
    
    public function index() {
        // Redirigir al login principal
        header('Location: /index.php');
        exit();
    }
}

// Instanciar y ejecutar
$controller = new HomeController();
$controller->index();
