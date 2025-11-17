<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');

// Log incoming request
error_log('Received request: ' . file_get_contents('php://input'));

try {
    // Get and validate input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    if (!isset($data['id_token'])) {
        throw new Exception('Token no recibido');
    }

    // Verify token with Google
    $id_token = $data['id_token'];
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($id_token);
    $response = @file_get_contents($url);

    if ($response === false) {
        throw new Exception('Error al verificar el token con Google');
    }

    $user_info = json_decode($response, true);
    
    if (!isset($user_info['email']) || !$user_info['email_verified']) {
        throw new Exception('Email no verificado');
    }

    require_once 'db.php';

    mysqli_begin_transaction($conn);

    try {
        $email = mysqli_real_escape_string($conn, $user_info['email']);
        $nombre = mysqli_real_escape_string($conn, $user_info['given_name'] ?? '');
        $apellido = mysqli_real_escape_string($conn, $user_info['family_name'] ?? '');

        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];
            $message = 'Inicio de sesión exitoso';
        } else {
            // Create new user
            $password = password_hash(uniqid(), PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido, correo, contraseña, rol, fecha_registro) VALUES (?, ?, ?, ?, 'comprador', NOW())");
            $stmt->bind_param("ssss", $nombre, $apellido, $email, $password);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al crear usuario: ' . $stmt->error);
            }
            
            $id_usuario = $stmt->insert_id;
            
            // Create comprador record
            $stmt = $conn->prepare("INSERT INTO comprador (id_comprador) VALUES (?)");
            $stmt->bind_param("i", $id_usuario);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al crear comprador: ' . $stmt->error);
            }

            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['rol'] = 'comprador';
            $message = 'Registro exitoso';
        }

        mysqli_commit($conn);
        
        // Log session data
        error_log('Session data: ' . print_r($_SESSION, true));

        echo json_encode([
            'success' => true,
            'message' => $message,
            'redirect' => 'index.php',
            'session_id' => session_id()
        ]);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }

} catch (Exception $e) {
    error_log('Error in google_login.php: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>
