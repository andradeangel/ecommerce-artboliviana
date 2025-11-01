<?php
session_start();
require_once 'db.php';

$mensaje = '';
$tipo_mensaje = '';
$token_valido = false;
$token = '';

// Verificar si hay un token en la URL
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Verificar si el token existe y es válido
    $query = "SELECT rp.id_recuperacion, rp.id_usuario, rp.fecha_expiracion, rp.usado, u.correo, u.nombre
              FROM recuperacion_password rp
              INNER JOIN usuario u ON rp.id_usuario = u.id_usuario
              WHERE rp.token = '$token' 
              AND rp.usado = 0
              AND rp.fecha_expiracion > NOW()
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $token_valido = true;
        $token_data = mysqli_fetch_assoc($result);
        $id_usuario = $token_data['id_usuario'];
        
        // Procesar cambio de contraseña si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            if (empty($password) || empty($password_confirm)) {
                $mensaje = 'Por favor, completa todos los campos.';
                $tipo_mensaje = 'error';
            } elseif (strlen($password) < 8) {
                $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
                $tipo_mensaje = 'error';
            } elseif ($password !== $password_confirm) {
                $mensaje = 'Las contraseñas no coinciden.';
                $tipo_mensaje = 'error';
            } else {
                // Hash de la nueva contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Actualizar contraseña del usuario
                $update = "UPDATE usuario SET contraseña = '$password_hash' WHERE id_usuario = $id_usuario";
                
                if (mysqli_query($conn, $update)) {
                    // Marcar el token como usado
                    $id_recuperacion = $token_data['id_recuperacion'];
                    $mark_used = "UPDATE recuperacion_password SET usado = 1 WHERE id_recuperacion = $id_recuperacion";
                    mysqli_query($conn, $mark_used);
                    
                    // Limpiar otros tokens no usados del mismo usuario
                    $cleanup = "DELETE FROM recuperacion_password WHERE id_usuario = $id_usuario AND usado = 0";
                    mysqli_query($conn, $cleanup);
                    
                    $mensaje = '¡Contraseña actualizada exitosamente! Ya puedes iniciar sesión con tu nueva contraseña.';
                    $tipo_mensaje = 'success';
                    $token_valido = false; // Ya no mostrar el formulario
                } else {
                    $mensaje = 'Error al actualizar la contraseña. Por favor, intenta nuevamente.';
                    $tipo_mensaje = 'error';
                }
            }
        }
    } else {
        $mensaje = 'El enlace de recuperación no es válido o ha expirado. Por favor, solicita uno nuevo.';
        $tipo_mensaje = 'error';
    }
} else {
    $mensaje = 'No se proporcionó un token válido.';
    $tipo_mensaje = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - ArtesaníaBoliviana</title>
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
                            <h2 class="card-title">Restablecer Contraseña</h2>
                            <?php if ($token_valido && isset($token_data)): ?>
                                <p class="text-muted">Hola, <strong><?php echo htmlspecialchars($token_data['nombre']); ?></strong></p>
                                <p class="text-muted">Ingresa tu nueva contraseña</p>
                            <?php endif; ?>
                        </div>

                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($mensaje); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($token_valido && !isset($_POST['password'])): ?>
                            <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Mínimo 8 caracteres" required minlength="8">
                                    <small class="text-muted">La contraseña debe tener al menos 8 caracteres</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                           placeholder="Repite la contraseña" required minlength="8">
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Restablecer Contraseña</button>
                            </form>
                        <?php elseif ($tipo_mensaje == 'success'): ?>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-primary">Ir al Inicio de Sesión</a>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <a href="forgot_password.php" class="btn btn-primary">Solicitar Nuevo Enlace</a>
                                <br><br>
                                <a href="index.php" class="text-decoration-none">← Volver al inicio</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en el cliente
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirm').value;
                
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden.');
                    return false;
                }
            });
        }
    </script>
</body>
</html>

