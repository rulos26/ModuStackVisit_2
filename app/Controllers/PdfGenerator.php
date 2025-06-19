<?php
/**
 * MÓDULO DE GENERACIÓN DE PDF
 * Manejo de la generación de informes PDF usando el sistema modularizado
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

class PdfGenerator {
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    public function generarPDF($id_cedula, $mysqli) {
        try {
            if (!defined('BASE_PATH')) {
                define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
            }
            
            $informePath = BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
            
            if (!file_exists($informePath)) {
                throw new Exception('Archivo InformeModular.php no encontrado: ' . $informePath);
            }
            
            require_once $informePath;
            
            $generador = new InformeVisitaDomiciliariaModular($id_cedula, $mysqli);
            $generador->generarInforme();
            
            $this->logger->logInfo('PDF generado exitosamente para cédula: ' . $id_cedula);
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar PDF: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function validarArchivoInforme() {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
        }
        
        $informePath = BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
        return file_exists($informePath);
    }
    
    public function obtenerRutaInforme() {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
        }
        
        return BASE_PATH . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
    }
}
?> 