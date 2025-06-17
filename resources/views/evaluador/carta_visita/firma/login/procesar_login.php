<?php
session_start();
include '../../conn/conexion.php';

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];
$password= md5($password);
// Consulta SQL para verificar las credenciales
$queryUsuarioExist = "SELECT id, usuario,rol, password FROM usuarios WHERE usuario = ?";
$stmtUsuarioExist = $mysqli->prepare($queryUsuarioExist);
$stmtUsuarioExist->bind_param("s", $username);
$stmtUsuarioExist->execute();
$stmtUsuarioExist->store_result();

if ($stmtUsuarioExist->num_rows > 0) {
    // Obtener la contraseña almacenada en la base de datos
    $stmtUsuarioExist->bind_result($id, $usuario, $rol, $contrasena); // Asegúrate de tener el orden correcto de las columnas
    $stmtUsuarioExist->fetch();

    // Verificar la contraseña proporcionada con la contraseña almacenada en la base de datos
    echo $password.'<br>'. $contrasena.'<br>'.$rol.'<br>';
    if ($password === $contrasena) {
        // Credenciales válidas, iniciar sesión y redirigir al usuario
        $_SESSION['id_usuario'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['rol'] = $rol;
        if ($rol === 1 ) {
            echo 'el rol es administrador <br>';
            header("Location: ../view/administrador/index.php");
        }elseif($rol === 2){
            echo 'el rol es evaluador <br>';
            header("Location: ../view/cliente/index.php");
        }elseif($rol === 3){
            echo 'el rol es cliente <br>' ;
            header("Location: ../view/evaluador/index.php");
            
        }else{
            echo 'el rol no esta definido <br>';
            header("Location: ../view/error/error.php");
        }
        //header("Location: pagina_inicio.php");
        exit();
    } else {
        // Credenciales inválidas, mostrar mensaje de error
        header("Location: ../view/error/error.php");
    }
} else {
    // El usuario no existe, mostrar mensaje de error
    header("Location: ../view/error/error.php");
}

// Cerrar la conexión
$mysqli->close();
?>
<a href="../view/evaluador/index.php"></a>