<?php
/**
 * Configuración para el envío de emails
 * 
 * ⚠️ IMPORTANTE: Esta configuración es para la CUENTA DE EMAIL DEL SISTEMA
 * No es para los emails de los usuarios. Esta es la cuenta que usará tu aplicación
 * para enviar correos a los usuarios (como los de recuperación de contraseña).
 * 
 * Ejemplo:
 * - Si usas Gmail, crea UNA cuenta para tu aplicación (ej: artesaniaboliviana@gmail.com)
 * - Esa cuenta será la que envíe todos los correos a tus usuarios
 * - Los usuarios recibirán correos EN SU EMAIL PERSONAL, pero ENVIADOS desde esta cuenta
 */

// Configuración SMTP del SERVIDOR DE CORREO del sistema
define('SMTP_HOST', 'smtp.gmail.com');  // Servidor SMTP (Gmail, Outlook, etc.)
define('SMTP_PORT', 587);                // 587 para TLS, 465 para SSL
define('SMTP_USERNAME', 'andradefoldine.10@gmail.com');  // EMAIL DE LA CUENTA DEL SISTEMA (la que enviará los correos)
define('SMTP_PASSWORD', 'zgyxvzwmuuzszpiz');  // Contraseña de aplicación de Gmail (SIN ESPACIOS)
define('SMTP_ENCRYPTION', 'tls');        // 'tls' o 'ssl'
define('SMTP_FROM_EMAIL', 'andradefoldine.10@gmail.com');  // Email desde el cual se enviarán los correos
define('SMTP_FROM_NAME', 'ArtesaníaBoliviana');  // Nombre que aparecerá como remitente

// URL base de la aplicación (sin barra final)
define('APP_URL', 'http://localhost');  // Cambia por tu dominio en producción

// Tiempo de expiración del token (en horas)
define('TOKEN_EXPIRATION_HOURS', 24);

/**
 * Función para enviar email usando PHPMailer o mail() nativo
 * 
 * @param string $to Email destino
 * @param string $subject Asunto del email
 * @param string $body Cuerpo del email (HTML)
 * @return bool True si se envió correctamente, False en caso contrario
 */
function enviarEmail($to, $subject, $body) {
    // Verificar si PHPMailer está disponible
    $phpmailer_path = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($phpmailer_path) && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $result = enviarEmailPHPMailer($to, $subject, $body);
        // Si PHPMailer falla, intentar con mail() nativo
        if ($result === false) {
            return enviarEmailNativo($to, $subject, $body);
        }
        return $result;
    } else {
        // Si PHPMailer no está disponible, usar mail() nativo
        return enviarEmailNativo($to, $subject, $body);
    }
}

/**
 * Envío de email usando PHPMailer (recomendado)
 */
function enviarEmailPHPMailer($to, $subject, $body) {
    // Intentar cargar PHPMailer
    $phpmailer_path = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($phpmailer_path)) {
        require_once $phpmailer_path;
    } else {
        // Si no está instalado PHPMailer, usar mail() nativo
        return false;
    }
    
    try {
        // Usar el nombre completo de la clase sin 'use'
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // Versión texto plano
        
        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Error al enviar email con PHPMailer: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Error general al enviar email: " . $e->getMessage());
        return false;
    }
}

/**
 * Envío de email usando conexión SMTP directa (sin PHPMailer ni mail())
 * Esta función se conecta directamente a Gmail SMTP usando sockets
 */
function enviarEmailNativo($to, $subject, $body) {
    $smtp = null;
    $error_detail = '';
    
    try {
        // Usar stream_socket_client para mejor soporte TLS
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $smtp = @stream_socket_client(
            SMTP_HOST . ':' . SMTP_PORT,
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$smtp) {
            $error_detail = "No se pudo conectar a " . SMTP_HOST . ":" . SMTP_PORT . " - $errstr ($errno)";
            error_log("Error SMTP: $error_detail");
            return false;
        }
        
        // Leer respuesta inicial
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '220') {
            $error_detail = "Respuesta inicial inesperada: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // EHLO
        fputs($smtp, "EHLO " . gethostname() . "\r\n");
        $response = '';
        while ($line = fgets($smtp, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        
        // STARTTLS
        fputs($smtp, "STARTTLS\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '220') {
            $error_detail = "STARTTLS falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // Habilitar encriptación TLS
        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }
        
        if (!@stream_socket_enable_crypto($smtp, true, $crypto_method)) {
            $error_detail = "No se pudo habilitar TLS";
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // EHLO de nuevo después de TLS
        fputs($smtp, "EHLO " . gethostname() . "\r\n");
        $response = '';
        while ($line = fgets($smtp, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        
        // AUTH LOGIN
        fputs($smtp, "AUTH LOGIN\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '334') {
            $error_detail = "AUTH LOGIN falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // Enviar username
        fputs($smtp, base64_encode(SMTP_USERNAME) . "\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '334') {
            $error_detail = "Autenticación de usuario falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // Enviar password (verificar que no tenga espacios)
        $password = trim(str_replace(' ', '', SMTP_PASSWORD));
        fputs($smtp, base64_encode($password) . "\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '235') {
            $error_detail = "Autenticación de contraseña falló. Verifica que la contraseña de aplicación sea correcta y no tenga espacios. Respuesta: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // MAIL FROM
        fputs($smtp, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '250') {
            $error_detail = "MAIL FROM falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // RCPT TO
        fputs($smtp, "RCPT TO: <$to>\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '250') {
            $error_detail = "RCPT TO falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // DATA
        fputs($smtp, "DATA\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '354') {
            $error_detail = "DATA falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // Crear headers del email
        $email_headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
        $email_headers .= "To: $to\r\n";
        $email_headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $email_headers .= "MIME-Version: 1.0\r\n";
        $email_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email_headers .= "Content-Transfer-Encoding: base64\r\n";
        $email_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        // Enviar email
        fputs($smtp, $email_headers . "\r\n" . chunk_split(base64_encode($body)) . "\r\n.\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) !== '250') {
            $error_detail = "Envío de mensaje falló: " . trim($response);
            error_log("Error SMTP: $error_detail");
            fclose($smtp);
            return false;
        }
        
        // QUIT
        fputs($smtp, "QUIT\r\n");
        fclose($smtp);
        
        return true;
        
    } catch (Exception $e) {
        $error_detail = $e->getMessage();
        error_log("Error SMTP Exception: $error_detail");
        if ($smtp && is_resource($smtp)) {
            @fclose($smtp);
        }
        return false;
    } catch (Error $e) {
        $error_detail = $e->getMessage();
        error_log("Error SMTP Fatal: $error_detail");
        if ($smtp && is_resource($smtp)) {
            @fclose($smtp);
        }
        return false;
    }
}

