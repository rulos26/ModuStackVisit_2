<?php
/**
 * MÓDULO DE GESTIÓN DE BASE DE DATOS
 * Manejo centralizado de conexiones y consultas a la base de datos
 * 
 * @author Sistema de Informes
 * @version 1.0
 * @date 2024
 */

require_once __DIR__ . '/Logger.php';

class DatabaseManager {
    private $mysqli;
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->connect();
    }
    
    private function connect() {
        try {
            if (!defined('BASE_PATH')) {
                define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2');
            }
            
            $conexionPath = BASE_PATH . '/conn/conexion.php';
            if (!file_exists($conexionPath)) {
                throw new Exception('Archivo de conexión no encontrado: ' . $conexionPath);
            }
            
            require_once $conexionPath;
            
            if (!isset($mysqli) || !$mysqli) {
                throw new Exception('No se pudo establecer conexión con la base de datos');
            }
            
            $this->mysqli = $mysqli;
            $this->logger->logInfo('Conexión a base de datos establecida correctamente');
            
        } catch (Exception $e) {
            $this->logger->logError('Error al conectar a la base de datos: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getConnection() {
        return $this->mysqli;
    }
    
    public function prepare($query) {
        return $this->mysqli->prepare($query);
    }
    
    public function getError() {
        return $this->mysqli->error;
    }
    
    public function closeConnection() {
        if ($this->mysqli) {
            $this->mysqli->close();
            $this->logger->logInfo('Conexión a base de datos cerrada');
        }
    }
    
    public function isConnected() {
        return $this->mysqli && !$this->mysqli->connect_error;
    }
}
?> 