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

            if ($user) {
                $hash = $user['password'];
                $isPasswordHash = (strlen($hash) > 32); // bcrypt y otros hash modernos son más largos que 32
                $passwordOk = false;
                if ($isPasswordHash) {
                    $passwordOk = password_verify($password, $hash);
                    echo "<b>Tipo de hash:</b> password_hash (bcrypt, Argon2, etc.)\n";
                    echo "<b>Resultado password_verify:</b> "; var_dump($passwordOk);

                    // Prueba interna con contraseñas quemadas
                    $test1 = password_verify('0382646740Ju*', $hash);
                    $test2 = password_verify('0382646740ju*', $hash);
                    echo "<b>Test password_verify('0382646740Ju*', hash):</b> "; var_dump($test1);
                    echo "<b>Test password_verify('0382646740ju*', hash):</b> "; var_dump($test2);
                } else {
                    $passwordOk = (md5($password) === $hash);
                    echo "<b>Tipo de hash:</b> md5\n";
                    echo "<b>Resultado md5:</b> "; var_dump($passwordOk);

                    // Prueba interna con contraseñas quemadas
                    $test1 = (md5('0382646740Ju*') === $hash);
                    $test2 = (md5('0382646740ju*') === $hash);
                    echo "<b>Test md5('0382646740Ju*') === hash:</b> "; var_dump($test1);
                    echo "<b>Test md5('0382646740ju*') === hash:</b> "; var_dump($test2);
                }
                echo "<b>Rol detectado:</b> ".$user['rol']."\n";
                if ($passwordOk) {
                    if ($user['rol'] == 1) {
                        echo "<b>Redirigiendo a:</b> resources/views/admin/dashboardAdmin.php\n";
                        echo '</pre>';
                        $_SESSION['user_id'] = $user['cedula'];
                        $_SESSION['username'] = $user['usuario'];
                        $_SESSION['rol'] = $user['rol'];
                        header('Location: resources/views/admin/dashboardAdmin.php');
                    } elseif ($user['rol'] == 2) {
                        echo "<b>Redirigiendo a:</b> resources/views/evaluador/dashboardEavaluador.php\n";
                        echo '</pre>';
                        $_SESSION['user_id'] = $user['cedula'];
                        $_SESSION['username'] = $user['usuario'];
                        $_SESSION['rol'] = $user['rol'];
                        header('Location: resources/views/evaluador/dashboardEavaluador.php');
                    } else {
                        echo "<b>Rol de usuario no válido:</b> ".$user['rol']."\n";
                        echo '</pre>';
                        return 'Rol de usuario no válido.';
                    }
                    exit();
                } else {
                    echo "<b>Contraseña incorrecta</b>\n";
                    echo '</pre>';
                    return 'Usuario o contraseña incorrectos.';
                }
            } else {
                echo "<b>No se encontró el usuario</b>\n";
                echo '</pre>';
                return 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            return 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
        }
    }
} 