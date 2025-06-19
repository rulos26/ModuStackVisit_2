<?php
/**
 * MÓDULO DE GESTIÓN DE SESIONES
 * Manejo de autenticación y validación de sesiones de usuario
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

class SessionManager {
    private $id_cedula;
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->validateSession();
    }
    
    private function validateSession() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $this->id_cedula = $_SESSION['id_cedula'] ?? null;
            
            if (!$this->id_cedula) {
                $this->logger->logError('Usuario no autenticado');
                throw new Exception('Usuario no autenticado. Por favor, inicie sesión.');
            }
            
            $this->logger->logInfo('Sesión validada correctamente para cédula: ' . $this->id_cedula);
            
        } catch (Exception $e) {
            $this->logger->logError('Error al validar sesión: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getCedula() {
        return $this->id_cedula;
    }
    
    public function isAuthenticated() {
        return !empty($this->id_cedula);
    }
    
    public function logout() {
        session_destroy();
        $this->id_cedula = null;
        $this->logger->logInfo('Usuario ha cerrado sesión');
    }
    
    public function refreshSession() {
        $this->validateSession();
    }
}
?> 