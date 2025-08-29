<?php
namespace App\Services;

class LoggerService {
    private $logFile;
    private $logLevel;
    
    // Niveles de log
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_CRITICAL = 4;
    
    public function __construct($logFile = null, $logLevel = self::LEVEL_INFO) {
        $this->logLevel = $logLevel;
        
        if ($logFile === null) {
            $this->logFile = __DIR__ . '/../../logs/app.log';
        } else {
            $this->logFile = $logFile;
        }
        
        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Log de nivel debug
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = []) {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * Log de nivel info
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = []) {
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Log de nivel warning
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = []) {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * Log de nivel error
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = []) {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Log de nivel critical
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = []) {
        $this->log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * Método principal de logging
     * @param int $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, array $context = []) {
        // Verificar nivel de log
        if ($level < $this->logLevel) {
            return;
        }
        
        // Obtener nivel como string
        $levelName = $this->getLevelName($level);
        
        // Crear entrada de log
        $logEntry = $this->formatLogEntry($levelName, $message, $context);
        
        // Escribir al archivo
        $this->writeToFile($logEntry);
    }
    
    /**
     * Obtener nombre del nivel de log
     * @param int $level
     * @return string
     */
    private function getLevelName($level) {
        switch ($level) {
            case self::LEVEL_DEBUG:
                return 'DEBUG';
            case self::LEVEL_INFO:
                return 'INFO';
            case self::LEVEL_WARNING:
                return 'WARNING';
            case self::LEVEL_ERROR:
                return 'ERROR';
            case self::LEVEL_CRITICAL:
                return 'CRITICAL';
            default:
                return 'UNKNOWN';
        }
    }
    
    /**
     * Formatear entrada de log
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    private function formatLogEntry($level, $message, array $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $logLine = sprintf(
            "[%s] [%s] [IP: %s] [UA: %s] %s",
            $timestamp,
            $level,
            $ip,
            $this->truncateString($userAgent, 50),
            $message
        );
        
        // Agregar contexto si existe
        if (!empty($context)) {
            $contextStr = json_encode($context, JSON_UNESCAPED_UNICODE);
            $logLine .= " | Context: " . $contextStr;
        }
        
        return $logLine . PHP_EOL;
    }
    
    /**
     * Truncar string a longitud específica
     * @param string $string
     * @param int $length
     * @return string
     */
    private function truncateString($string, $length) {
        if (strlen($string) <= $length) {
            return $string;
        }
        
        return substr($string, 0, $length - 3) . '...';
    }
    
    /**
     * Escribir entrada al archivo de log
     * @param string $logEntry
     */
    private function writeToFile($logEntry) {
        try {
            // Usar file_put_contents con LOCK_EX para evitar conflictos
            file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // Si falla el logging, no queremos que rompa la aplicación
            error_log("Failed to write to log file: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener logs recientes
     * @param int $lines
     * @return array
     */
    public function getRecentLogs($lines = 100) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $logs = [];
        $file = new \SplFileObject($this->logFile);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $line = trim($file->current());
            if (!empty($line)) {
                $logs[] = $line;
            }
            $file->next();
        }
        
        return array_reverse($logs);
    }
    
    /**
     * Limpiar logs antiguos
     * @param int $daysToKeep
     * @return bool
     */
    public function cleanOldLogs($daysToKeep = 30) {
        if (!file_exists($this->logFile)) {
            return true;
        }
        
        $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);
        $tempFile = $this->logFile . '.tmp';
        
        $input = fopen($this->logFile, 'r');
        $output = fopen($tempFile, 'w');
        
        if (!$input || !$output) {
            return false;
        }
        
        while (($line = fgets($input)) !== false) {
            // Extraer timestamp del log
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                if ($logTime >= $cutoffTime) {
                    fwrite($output, $line);
                }
            } else {
                // Si no podemos parsear la fecha, mantener la línea
                fwrite($output, $line);
            }
        }
        
        fclose($input);
        fclose($output);
        
        return rename($tempFile, $this->logFile);
    }
    
    /**
     * Obtener estadísticas de logs
     * @return array
     */
    public function getLogStats() {
        if (!file_exists($this->logFile)) {
            return [
                'total_lines' => 0,
                'file_size' => 0,
                'last_modified' => null,
                'levels' => []
            ];
        }
        
        $stats = [
            'total_lines' => 0,
            'file_size' => filesize($this->logFile),
            'last_modified' => date('Y-m-d H:i:s', filemtime($this->logFile)),
            'levels' => [
                'DEBUG' => 0,
                'INFO' => 0,
                'WARNING' => 0,
                'ERROR' => 0,
                'CRITICAL' => 0
            ]
        ];
        
        $file = new \SplFileObject($this->logFile);
        foreach ($file as $line) {
            $stats['total_lines']++;
            
            // Contar por nivel
            foreach ($stats['levels'] as $level => &$count) {
                if (strpos($line, "[$level]") !== false) {
                    $count++;
                    break;
                }
            }
        }
        
        return $stats;
    }
}
