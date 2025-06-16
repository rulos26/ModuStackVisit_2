<?php
namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;
use PDOException;

class LoginController {
    public static function login($usuario, $password) {
        $db = Database::getInstance()->getConnection();
        try {
            $sql = 'SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $user = $stmt->fetch();

            // DEBUG: Mostrar SQL y resultado
            echo '<pre style="background:#222;color:#fff;padding:10px;">';
            echo "<b>SQL ejecutado:</b> " . $sql . "\n";
            echo "<b>Parámetros:</b> "; var_dump(['usuario' => $usuario]);
            echo "<b>Resultado:</b> "; var_dump($user);
            echo '</pre>';
            // Fin debug

            if ($user && password_verify($password, $user['password'])) {
                // Autenticación exitosa
                $_SESSION['user_id'] = $user['cedula'];
                $_SESSION['username'] = $user['usuario'];
                $_SESSION['rol'] = $user['rol'];
                if ($user['rol'] == 1) {
                    header('Location: resources/views/admin/dashboardAdmin.php');
                } elseif ($user['rol'] == 2) {
                    header('Location: resources/views/evaluador/dashboardEavaluador.php');
                } else {
                    // Rol desconocido
                    return 'Rol de usuario no válido.';
                }
                exit();
            } else {
                return 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            return 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
        }
    }
} 