<?php
/**
 * MÓDULO DE LOGGING
 * Manejo centralizado de logs para el sistema de informes
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

class Logger {
    private $logFile;
    
    public function __construct($logFile = null) {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
        }
        $this->logFile = $logFile ?: BASE_PATH . '/informe_controller_errors.log';
    }
    
    public function log($message, $type = 'INFO') {
        $logMessage = date('[Y-m-d H:i:s]') . " [{$type}] {$message}\n";
        error_log($logMessage, 3, $this->logFile);
    }
    
    public function logError($message, $exception = null) {
        $errorMessage = $message;
        if ($exception) {
            $errorMessage .= "\nException: " . $exception->getMessage();
            $errorMessage .= "\nStack Trace: " . $exception->getTraceAsString();
        }
        $this->log($errorMessage, 'ERROR');
    }
    
    public function logInfo($message) {
        $this->log($message, 'INFO');
    }
    
    public function logWarning($message) {
        $this->log($message, 'WARNING');
    }
    
    public function logDebug($message) {
        $this->log($message, 'DEBUG');
    }
}
?> 