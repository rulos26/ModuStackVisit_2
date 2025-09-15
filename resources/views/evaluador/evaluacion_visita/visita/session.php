<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la cédula del formulario
    $id_cedula = trim($_POST['id_cedula']);
    
    try {
        // Incluir la conexión a la base de datos
        require_once __DIR__ . '/../../../../../app/Database/Database.php';
        
        $db = \App\Database\Database::getInstance()->getConnection();
        
        // Paso 1: Validar formato del documento
        if (!is_numeric($id_cedula) || $id_cedula <= 0) {
            $_SESSION['error'] = 'Número de documento inválido. Ingrese una cédula válida (7-10 dígitos).';
            header("Location: index.php");
            exit;
        }
        
        $longitud = strlen($id_cedula);
        if ($longitud < 7 || $longitud > 10) {
            $_SESSION['error'] = 'Número de documento inválido. Ingrese una cédula válida (7-10 dígitos).';
            header("Location: index.php");
            exit;
        }
        
        // Paso 2: Buscar en tabla evaluados
        $sql = "SELECT * FROM evaluados WHERE id_cedula = :cedula LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':cedula', $id_cedula);
        $stmt->execute();
        $evaluado = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($evaluado) {
            // Evaluado encontrado
            $_SESSION['id_cedula'] = $id_cedula;
            $_SESSION['success'] = 'Evaluado encontrado. Redirigiendo a Información Personal…';
            header("Location: informacion_personal/informacion_personal.php");
            exit;
        }
        
        // Paso 3: Buscar en tabla autorizaciones
        $sql = "SELECT * FROM autorizaciones WHERE cedula = :cedula LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':cedula', $id_cedula);
        $stmt->execute();
        $autorizacion = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($autorizacion) {
            // Crear evaluado desde autorización
            $sql = "INSERT INTO evaluados (
                id_cedula, nombres, direccion, localidad, barrio, 
                telefono, celular_1, correo, fecha_creacion
            ) VALUES (
                :id_cedula, :nombres, :direccion, :localidad, :barrio,
                :telefono, :celular_1, :correo, NOW()
            )";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_cedula', $autorizacion['cedula']);
            $stmt->bindParam(':nombres', $autorizacion['nombres']);
            $stmt->bindParam(':direccion', $autorizacion['direccion']);
            $stmt->bindParam(':localidad', $autorizacion['localidad']);
            $stmt->bindParam(':barrio', $autorizacion['barrio']);
            $stmt->bindParam(':telefono', $autorizacion['telefono']);
            $stmt->bindParam(':celular_1', $autorizacion['celular']);
            $stmt->bindParam(':correo', $autorizacion['correo']);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $_SESSION['id_cedula'] = $id_cedula;
                $_SESSION['success'] = 'Se creó el evaluado a partir de la carta de autorización. Continúe con Información Personal.';
                header("Location: informacion_personal/informacion_personal.php");
                exit;
            } else {
                $_SESSION['error'] = 'Error al crear evaluado desde autorización.';
                header("Location: index.php");
                exit;
            }
        }
        
        // Paso 4: No encontrado en ninguna tabla
        $_SESSION['error'] = 'No se encontró ninguna cédula asociada con carta de autorización.';
        header("Location: ../../carta_visita/index_carta.php");
        exit;
        
    } catch (Exception $e) {
        error_log("ERROR session.php: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor. Intente nuevamente.";
        header("Location: index.php");
        exit;
    }
}
?>
