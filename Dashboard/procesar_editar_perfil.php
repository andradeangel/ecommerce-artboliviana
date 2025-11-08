<?php
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['message'] = 'Debes iniciar sesión para continuar.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Método no permitido.';
    $_SESSION['message_type'] = 'danger';
    header('Location: Dashboard_comprador.php');
    exit;
}

// Obtener y validar datos
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_naci = $_POST['fecha_naci'] ?? null;

// Validaciones
if (empty($nombre) || empty($apellido) || empty($correo)) {
    $_SESSION['message'] = 'Por favor completa todos los campos obligatorios.';
    $_SESSION['message_type'] = 'danger';
    header('Location: Dashboard_comprador.php');
    exit;
}

// Validar email
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = 'El correo electrónico no es válido.';
    $_SESSION['message_type'] = 'danger';
    header('Location: Dashboard_comprador.php');
    exit;
}

// Verificar si el correo ya existe en otro usuario
$check_email = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ? AND id_usuario != ?");
$check_email->bind_param("si", $correo, $id_usuario);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    $_SESSION['message'] = 'El correo electrónico ya está en uso por otro usuario.';
    $_SESSION['message_type'] = 'danger';
    header('Location: Dashboard_comprador.php');
    exit;
}
$check_email->close();

// Validar teléfono si se proporciona
if (!empty($telefono) && !preg_match('/^[0-9]{8}$/', $telefono)) {
    $_SESSION['message'] = 'El teléfono debe tener 8 dígitos numéricos.';
    $_SESSION['message_type'] = 'danger';
    header('Location: Dashboard_comprador.php');
    exit;
}

// Validar fecha de nacimiento si se proporciona
if (!empty($fecha_naci)) {
    $fecha_max = date('Y-m-d', strtotime('-18 years'));
    if ($fecha_naci > $fecha_max) {
        $_SESSION['message'] = 'Debes ser mayor de 18 años.';
        $_SESSION['message_type'] = 'danger';
        header('Location: Dashboard_comprador.php');
        exit;
    }
}

// Actualizar datos del usuario
try {
    // Preparar la consulta
    $query = "UPDATE usuario SET 
              nombre = ?, 
              apellido = ?, 
              correo = ?, 
              telefono = ?";
    
    $params = [$nombre, $apellido, $correo, $telefono];
    $types = "ssss";
    
    // Agregar fecha_naci si se proporciona
    if (!empty($fecha_naci)) {
        $query .= ", fecha_naci = ?";
        $params[] = $fecha_naci;
        $types .= "s";
    } else {
        $query .= ", fecha_naci = NULL";
    }
    
    $query .= " WHERE id_usuario = ?";
    $params[] = $id_usuario;
    $types .= "i";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Perfil actualizado correctamente.';
        $_SESSION['message_type'] = 'success';
        
        // Actualizar nombre en sesión
        $_SESSION['nombre'] = $nombre;
    } else {
        throw new Exception('Error al actualizar: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $_SESSION['message'] = 'Error al actualizar el perfil: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: Dashboard_comprador.php');
exit;
?>

