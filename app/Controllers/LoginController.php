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
            var_dump($user);
            exit;
            if ($user) {
                $hash = $user['password'];
                $isPasswordHash = (strlen($hash) > 32); // bcrypt y otros hash modernos son más largos que 32
                $passwordOk = false;
                if ($isPasswordHash) {
                    $passwordOk = password_verify($password, $hash);
                } else {
                    $passwordOk = (md5($password) === $hash);
                }
                if ($passwordOk) {
                    if ($user['rol'] == 1) {
                        $_SESSION['user_id'] = $user['cedula'];
                        $_SESSION['username'] = $user['usuario'];
                        $_SESSION['rol'] = $user['rol'];
                        header('Location: resources/views/admin/dashboardAdmin.php');
                    } elseif ($user['rol'] == 2) {
                        $_SESSION['user_id'] = $user['cedula'];
                        $_SESSION['username'] = $user['usuario'];
                        $_SESSION['rol'] = $user['rol'];
                        header('Location: resources/views/evaluador/dashboardEavaluador.php');
                    } elseif ($user['rol'] == 3) {
                        $_SESSION['user_id'] = $user['cedula'];
                        $_SESSION['username'] = $user['usuario'];
                        $_SESSION['rol'] = $user['rol'];
                        header('Location: resources/views/superadmin/dashboardSuperAdmin.php');
                    } else {
                        return 'Rol de usuario no válido.';
                    }
                    exit();
                } else {
                    return 'Usuario o contraseña incorrectos.';
                }
            } else {
                return 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            return 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
        }
    }
} 