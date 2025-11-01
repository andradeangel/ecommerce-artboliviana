<?php
session_start();
require_once 'db.php';

$mensaje = '';
$tipo_mensaje = ''; // 'success' o 'error'

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $mensaje = 'Por favor, ingresa tu correo electrónico.';
        $tipo_mensaje = 'error';
    } else {
        // Limpiar email
        $email = mysqli_real_escape_string($conn, $email);
        
        // Verificar si el usuario existe
        $query = "SELECT id_usuario, nombre FROM usuario WHERE correo = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);
            $id_usuario = $usuario['id_usuario'];
            
            // Generar token único y seguro
            $token = bin2hex(random_bytes(32)); // Token de 64 caracteres
            
            // Calcular fecha de expiración (24 horas desde ahora)
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Limpiar tokens anteriores no usados del mismo usuario
            $cleanup = "DELETE FROM recuperacion_password WHERE id_usuario = $id_usuario AND usado = 0";
            mysqli_query($conn, $cleanup);
            
            // Insertar nuevo token
            $insert = "INSERT INTO recuperacion_password (id_usuario, token, fecha_expiracion) 
                       VALUES ($id_usuario, '$token', '$fecha_expiracion')";
            
            if (mysqli_query($conn, $insert)) {
                // Preparar email
                require_once 'config/email_config.php';
                
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
                        .header { background-color: #e65b50; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { display: inline-block; padding: 12px 30px; background-color: #e65b50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
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
                            <p style='word-break: break-all; color: #666;'>$reset_link</p>
                            <p><strong>Este enlace expirará en 24 horas.</strong></p>
                            <p>Si no solicitaste este cambio, ignora este email y tu contraseña no será modificada.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; 2025 ArtesaníaBoliviana. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";
                
                // Enviar email
                if (enviarEmail($email, $subject, $body)) {
                    $mensaje = 'Se ha enviado un correo electrónico con las instrucciones para restablecer tu contraseña. Por favor, revisa tu bandeja de entrada.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al enviar el correo. Por favor, intenta nuevamente o contacta al administrador.';
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'Error al procesar la solicitud. Por favor, intenta nuevamente.';
                $tipo_mensaje = 'error';
            }
        } else {
            // Por seguridad, mostrar el mismo mensaje aunque el usuario no exista
            $mensaje = 'Si el correo existe en nuestro sistema, recibirás un email con las instrucciones para restablecer tu contraseña.';
            $tipo_mensaje = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - ArtesaníaBoliviana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Recuperar Contraseña</h2>
                            <p class="text-muted">Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
                        </div>

                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($mensaje); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="forgot_password.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="tu@email.com" required autofocus>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Enviar Enlace de Recuperación</button>
                        </form>

                        <div class="text-center">
                            <a href="index.php" class="text-decoration-none">← Volver al inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

