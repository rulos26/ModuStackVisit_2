<?php

require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "<h2>Conexión exitosa a la base de datos 🎉</h2>";
} catch (PDOException $e) {
    echo "<h2>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</h2>";
} 