<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once __DIR__ . '/../../../../app/Controllers/DemoPdfController.php';
use App\Controllers\DemoPdfController;

DemoPdfController::generarEjemplo(); 