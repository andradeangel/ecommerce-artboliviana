<?php
/**
 * Script para procesar la solicitud de recuperación de contraseña vía AJAX
 */

// Deshabilitar mostrar errores en pantalla (solo enviar JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Configurar headers JSON ANTES de cualquier salida
header('Content-Type: application/json; charset=utf-8');

// Manejar errores como JSON
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    require_once 'db.php';
    require_once 'config/email_config.php';
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar la configuración: ' . $e->getMessage()
    ]);
    exit;
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener datos JSON
try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    if (!isset($data['email']) || empty(trim($data['email']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, ingresa tu correo electrónico.'
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
    exit;
}

$email = trim($data['email']);
$email = mysqli_real_escape_string($conn, $email);

// Verificar si el usuario existe
try {
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    $query = "SELECT id_usuario, nombre FROM usuario WHERE correo = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) === 0) {
        // El email NO existe en la base de datos
        echo json_encode([
            'success' => false,
            'message' => 'El usuario con el email <strong>' . htmlspecialchars($email) . '</strong> no está registrado en nuestro sistema.'
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar el usuario: ' . $e->getMessage()
    ]);
    exit;
}

// El usuario existe, proceder con la recuperación
$usuario = mysqli_fetch_assoc($result);
$id_usuario = $usuario['id_usuario'];

// Generar token único y seguro
$token = bin2hex(random_bytes(32)); // Token de 64 caracteres

// Calcular fecha de expiración (24 horas desde ahora)
$fecha_expiracion = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRATION_HOURS . ' hours'));

// Limpiar tokens anteriores no usados del mismo usuario y insertar nuevo token
try {
    // Verificar si la tabla existe
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'recuperacion_password'");
    if (!$check_table || mysqli_num_rows($check_table) === 0) {
        throw new Exception('La tabla recuperacion_password no existe. Por favor ejecuta el script SQL crear_tabla_recuperacion.sql');
    }
    
    // Limpiar tokens anteriores no usados del mismo usuario
    $cleanup = "DELETE FROM recuperacion_password WHERE id_usuario = $id_usuario AND usado = 0";
    mysqli_query($conn, $cleanup);
    
    // Insertar nuevo token
    $insert = "INSERT INTO recuperacion_password (id_usuario, token, fecha_expiracion) 
               VALUES ($id_usuario, '$token', '$fecha_expiracion')";
    
    if (!mysqli_query($conn, $insert)) {
        throw new Exception('Error al guardar el token: ' . mysqli_error($conn));
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
    exit;
}

// Preparar email con el enlace de recuperación
$reset_link = APP_URL . '/reset_password.php?token=' . $token;

$subject = 'Recuperación de Contraseña - ArtesaníaBoliviana';
$body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #e65b50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .button { display: inline-block; padding: 12px 30px; background-color: #e65b50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .link-box { background-color: #fff; padding: 15px; border-left: 4px solid #e65b50; margin: 20px 0; word-break: break-all; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Recuperación de Contraseña</h2>
        </div>
        <div class='content'>
            <p>Hola <strong>" . htmlspecialchars($usuario['nombre']) . "</strong>,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña. Si fuiste tú, haz clic en el siguiente botón:</p>
            <p style='text-align: center;'>
                <a href='$reset_link' class='button'>Restablecer Contraseña</a>
            </p>
            <p>O copia y pega este enlace en tu navegador:</p>
            <div class='link-box'>
                <p style='margin: 0; color: #666;'>$reset_link</p>
            </div>
            <p><strong>⚠️ Este enlace expirará en " . TOKEN_EXPIRATION_HOURS . " horas.</strong></p>
            <p>Si <strong>NO</strong> solicitaste este cambio, ignora este email y tu contraseña no será modificada.</p>
        </div>
        <div class='footer'>
            <p>&copy; 2024 ArtesaníaBoliviana. Todos los derechos reservados.</p>
            <p style='font-size: 10px; color: #999;'>Este es un correo automático, por favor no respondas.</p>
        </div>
    </div>
</body>
</html>
";

// Enviar email
try {
    if (!function_exists('enviarEmail')) {
        throw new Exception('La función enviarEmail no está disponible. Verifica config/email_config.php');
    }
    
    // Verificar configuración antes de intentar enviar
    if (SMTP_PASSWORD == 'tu_contraseña_aplicacion' || empty(trim(SMTP_PASSWORD))) {
        echo json_encode([
            'success' => false,
            'message' => 'Error de configuración: La contraseña SMTP no está configurada. Por favor, configura SMTP_PASSWORD en config/email_config.php con tu contraseña de aplicación de Gmail (sin espacios).'
        ]);
        exit;
    }
    
    if (enviarEmail($email, $subject, $body)) {
        echo json_encode([
            'success' => true,
            'message' => 'Se ha enviado un correo electrónico a <strong>' . htmlspecialchars($email) . '</strong> con las instrucciones para restablecer tu contraseña. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).'
        ]);
    } else {
        // Leer el último error de los logs
        $log_file = __DIR__ . '/error.log';
        $error_msg = 'Error al enviar el correo. ';
        
        // Intentar obtener más detalles del error
        if (file_exists($log_file)) {
            $lines = file($log_file);
            $last_error = end($lines);
            if ($last_error && strpos($last_error, 'SMTP') !== false) {
                $error_msg .= 'Detalle: ' . trim($last_error);
            } else {
                $error_msg .= 'Verifica que: 1) La contraseña de aplicación sea correcta y sin espacios, 2) La verificación en 2 pasos esté activada en tu cuenta Gmail, 3) Tu firewall/perímetro no bloquee la conexión SMTP.';
            }
        } else {
            $error_msg .= 'Verifica la configuración SMTP (usuario y contraseña de aplicación sin espacios). Revisa los logs de error de PHP para más detalles.';
        }
        
        echo json_encode([
            'success' => false,
            'message' => $error_msg
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar el correo: ' . $e->getMessage()
    ]);
}

