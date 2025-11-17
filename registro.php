<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario y proteger contra inyecciones SQL
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre']));
    $apellido = mysqli_real_escape_string($conn, trim($_POST['apellido']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $telefono = mysqli_real_escape_string($conn, trim($_POST['telefono']));
    $fecha_naci = mysqli_real_escape_string($conn, $_POST['fecha_naci']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validar formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Por favor, introduce un correo electrónico válido.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (strlen($nombre) < 2 || strlen($nombre) > 50) {
        $error_message = "El nombre debe tener entre 2 y 50 caracteres.";
    } elseif (strlen($apellido) < 2 || strlen($apellido) > 50) {
        $error_message = "El apellido debe tener entre 2 y 50 caracteres.";
    } elseif (!preg_match('/^[0-9]{8}$/', $telefono)) {
        $error_message = "El teléfono debe tener 8 dígitos numéricos.";
    } elseif (empty($fecha_naci)) {
        $error_message = "La fecha de nacimiento es requerida.";
    } else {
        // Validar que el usuario sea mayor de 18 años
        $fecha_actual = new DateTime();
        $fecha_nacimiento = new DateTime($fecha_naci);
        $edad = $fecha_actual->diff($fecha_nacimiento)->y;
        
        if ($edad < 18) {
            $error_message = "Debes ser mayor de 18 años para registrarte.";
        } else {
            // Verificar si el correo ya está registrado
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error_message = "Este correo electrónico ya está registrado.";
            } else {
                // Encriptar la contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Obtener fecha y hora actual para fecha_registro
                $fecha_registro = date('Y-m-d H:i:s');

                // Insertar el nuevo usuario en la base de datos con todos los campos
                $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido, correo, contraseña, telefono, fecha_naci, fecha_registro, estado, rol) VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo', 'comprador')");
                $stmt->bind_param("sssssss", $nombre, $apellido, $email, $hashed_password, $telefono, $fecha_naci, $fecha_registro);
                
                if ($stmt->execute()) {
                    // Obtener el id del usuario recién insertado
                    $id_usuario = $stmt->insert_id;

                    // Insertar en la tabla comprador usando el mismo id que el usuario
                    $stmt_comprador = $conn->prepare("INSERT INTO comprador (id_comprador) VALUES (?)");
                    $stmt_comprador->bind_param("i", $id_usuario);

                    if ($stmt_comprador->execute()) {
                        // Si el registro es exitoso, redirigir al login
                        $_SESSION['mensaje'] = "Registro exitoso. Por favor, inicia sesión.";
                        $_SESSION['mensaje_tipo'] = "success";
                        
                        header('Location: index.php');
                        exit;
                    } else {
                        $error_message = "Error al registrar el comprador. Intenta nuevamente.";
                    }

                    $stmt_comprador->close();
                } else {
                    $error_message = "Error al registrar el usuario. Intenta nuevamente.";
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Registro de Usuario</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="registro.php" method="POST" id="formRegistro">
            <div class="row">
                <div class="col-md-6">
                    <label for="nombre">Nombre <span style="color: red;">*</span></label>
                    <input type="text" id="nombre" name="nombre" required
                           placeholder="Ingrese su nombre" minlength="2" maxlength="50"
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="apellido">Apellido <span style="color: red;">*</span></label>
                    <input type="text" id="apellido" name="apellido" required
                           placeholder="Ingrese su apellido" minlength="2" maxlength="50"
                           value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>">
                </div>
            </div>

            <label for="email">Correo Electrónico <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" required
                   placeholder="ejemplo@correo.com"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <div class="row">
                <div class="col-md-6">
                    <label for="telefono">Teléfono <span style="color: red;">*</span></label>
                    <input type="tel" id="telefono" name="telefono" required
                           placeholder="Ej: 77123456" pattern="[0-9]{8}"
                           title="Ingrese un número de teléfono válido de 8 dígitos"
                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                    <small>8 dígitos numéricos</small>
                </div>
                
                <div class="col-md-6">
                    <label for="fecha_naci">Fecha de Nacimiento <span style="color: red;">*</span></label>
                    <input type="date" id="fecha_naci" name="fecha_naci" required
                           max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                           value="<?php echo isset($_POST['fecha_naci']) ? htmlspecialchars($_POST['fecha_naci']) : ''; ?>">
                    <small>Debe ser mayor de 18 años</small>
                </div>
            </div>
            
            <label for="password">Contraseña <span style="color: red;">*</span></label>
            <input type="password" id="password" name="password" required
                   minlength="8" placeholder="Mínimo 8 caracteres">
            <small>Mínimo 8 caracteres</small>
            
            <label for="password_confirm">Confirmar Contraseña <span style="color: red;">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm" required
                   minlength="8" placeholder="Repita la contraseña">

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="terminos" required>
                <label class="form-check-label" for="terminos">
                    Acepto los <a href="#" target="_blank">términos y condiciones</a> <span style="color: red;">*</span>
                </label>
            </div>
            
            <button type="submit">Registrarse</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        <footer>&copy; 2025 Plataforma Artesanal</footer>
    </div>
</div>

</body>
</html>