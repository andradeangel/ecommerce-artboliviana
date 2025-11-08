<?php
session_start();
require 'db.php'; // Conexión a la base de datos

// Agregar registro de depuración
$debug_log = fopen('google_login_debug.log', 'a');
fwrite($debug_log, "=== " . date('Y-m-d H:i:s') . " ===\n");
fwrite($debug_log, "Raw input: " . file_get_contents('php://input') . "\n");

// Verificar si el usuario está logueado
function usuarioLogueado() {
    return isset($_SESSION['id_usuario']);
}

// Obtener información del usuario si está logueado
$usuario_nombre = '';
$usuario_tipo = '';
if (usuarioLogueado()) {
    $id_usuario = $_SESSION['id_usuario'];
    $query = "SELECT nombre, rol FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $usuario_nombre = $row['nombre'];
        $usuario_tipo = $row['rol'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtesaníaBoliviana</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluye los scripts de Bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Script de Google para Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <link rel="stylesheet" href="css/style_web.css">
    <link rel="stylesheet" href="css/styles.css">
    
</head>
<body class="font-sans bg-gray-100">
    <header class="bg-white shadow-md fixed w-full z-10">
        <nav class="container mx-auto px-6 py-1 flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/logo_l.png" alt="Logo ArtesaníaBolivia" class="h-20 w-20 mr-3">
                <span class="font-bold text-xl" style="color: #e65b50;">ArtesaníaBoliviana</span>
            </div>
            <div class="flex items-center">
                <a href="index.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Inicio</a>
                <a href="productos-artesania-bolivia.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Productos</a>
                <a href="carrito.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Carrito</a>
                <a href="aboutus.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Sobre Nosotros</a>
            
                <?php if (usuarioLogueado()): ?>
                <div class="flex items-center">
                    <?php
                        // Determinar la URL del dashboard según el tipo de usuario
                        $dashboard_url = 'dashboard.php'; // URL por defecto
                        switch($usuario_tipo) {
                            case 'comprador':
                                $dashboard_url = 'Dashboard/Dashboard_comprador.php';
                            break;
                            case 'vendedor':
                                $dashboard_url = 'Dashboard/Dashboard_comunario.php';
                            break;
                            case 'delivery':
                                $dashboard_url = 'Dashboard/Dashboard_delivery.php';
                            break;
                            case 'administrador':
                                $dashboard_url = 'Dashboard/Dashboard_administrador.php';
                            break;
                        }
                    ?>
                    <!-- Menú de usuario -->
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center focus:outline-none " onclick="toggleUserMenu()">
                            <span class="mr-2"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</a>
                        </div>
                    </div>
                    <!-- Imagen de Perfil (clickable) -->
                    <a href="<?php echo $dashboard_url; ?>" class="mx-3 cursor-pointer hover:opacity-80 transition-opacity" title="Ver Perfil">
                        <img src="img/user.png" alt="Perfil" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                    </a>
                </div>
                <?php else: ?>

                <!-- Botones para abrir los modales -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#registroModal">Registrarse</button>

                <?php endif; ?>
            </div>
        </nav>
    </header>
    

    <main class="pt-16">
        <section class="hero-pattern py-20">
            <div class="container mx-auto px-6 text-center">
                <h1 class="text-5xl font-bold text-gray-800 mb-4">Descubre la Artesanía Boliviana</h1>
                <p class="text-xl text-gray-600 mb-8">Conectamos artesanos tradicionales con amantes del arte de todo el mundo</p>
                <a href="productos-artesania-bolivia.php" class="btn-primary text-white px-8 py-3 rounded-full text-lg shadow-lg no-underline">Explorar Productos</a>
            </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-12">Categorías Destacadas</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/Aguayo.jpeg" alt="Textiles" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Textiles</h3>
                            <p class="text-gray-600">Descubre hermosos tejidos y bordados tradicionales.</p>
                        </div>
                    </div>
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/Tazas.jpeg" alt="Cerámica" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Cerámica</h3>
                            <p class="text-gray-600">Explora piezas únicas hechas a mano por artesanos locales.</p>
                        </div>
                    </div>
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/joyeria.jpg" alt="Joyería" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Joyería</h3>
                            <p class="text-gray-600">Adórnate con joyas inspiradas en la cultura boliviana.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        

        
        <section><div class="fondomedio"></div></section>
        


        <section class="py-16 bg-indigo-100">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-semibold text-gray-800 mb-12">¿Por qué elegir ArtesaníaBolivia?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div>
                        <img src="img/Autenticidad.jpeg" alt="Ícono de autenticidad" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Autenticidad Garantizada</h3>
                        <p class="text-gray-600">Cada pieza es única y hecha a mano por artesanos bolivianos.</p>
                    </div>
                    <div>
                        <img src="img/Apoyo.jpeg" alt="Ícono de apoyo" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Apoyo Directo</h3>
                        <p class="text-gray-600">Tu compra beneficia directamente a los artesanos y sus comunidades.</p>
                    </div>
                    <div>
                        <img src="img/Calidad.jpeg" alt="Ícono de calidad" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Calidad Superior</h3>
                        <p class="text-gray-600">Productos cuidadosamente seleccionados por su calidad y belleza.</p>
                    </div>
                </div>
            </div>
        </section>
        
    </main>
    


    <!-- Modal para Iniciar Sesión -->
    <div class="modal fade" id="loginModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        
                        <button type="submit" class="btn btn-primary mt-3">Iniciar Sesión</button>
                    </form>
                    <div class="text-center my-3">
                        <p class="text-muted">O inicia sesión con</p>
                        <hr>
                    </div>
                    <!-- Cambiado el ID para el botón de login -->
                    <div id="google-signin-button-login" class="d-flex justify-content-center mb-3"></div>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">¿Olvidaste tu contraseña?</a>
                    <p class="mt-3">¿No tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#registroModal" data-bs-dismiss="modal">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Recuperar Contraseña -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
                    <div id="forgotPasswordMessage"></div>
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="recovery_email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="recovery_email" name="email" 
                                   placeholder="tu@email.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="btnSendRecovery">Enviar Enlace de Recuperación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registro -->
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="registro.php" method="POST" id="formRegistro">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required
                                       placeholder="Ingrese su nombre" minlength="2" maxlength="50">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required
                                       placeholder="Ingrese su apellido" minlength="2" maxlength="50">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   placeholder="ejemplo@correo.com">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" id="telefono" name="telefono" class="form-control" required
                                       placeholder="Ej: 77123456" pattern="[0-9]{8}"
                                       title="Ingrese un número de teléfono válido de 8 dígitos">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="fecha_naci" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                <input type="date" id="fecha_naci" name="fecha_naci" class="form-control" required
                                       max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                                <small class="text-muted">Debe ser mayor de 18 años</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control" required
                                       minlength="8" placeholder="Mínimo 8 caracteres">
                                <small class="text-muted">Mínimo 8 caracteres</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required
                                       minlength="8" placeholder="Repita la contraseña">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terminos" required>
                            <label class="form-check-label" for="terminos">
                                Acepto los <a href="#" target="_blank">términos y condiciones</a> <span class="text-danger">*</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-2">Registrarse</button>
                    </form>
                    <div class="text-center my-3">
                        <p class="text-muted">O regístrate con</p>
                        <hr>
                    </div>
                    <!-- Nuevo ID para el botón de registro -->
                    <div id="google-signin-button-register" class="d-flex justify-content-center mb-3"></div>
                    <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap justify-between">
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">ArtesaníaBolivia</h3>
                    <p class="text-gray-400">Conectando tradición y modernidad a través del arte.</p>
                </div>
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Inicio</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Productos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Artesanos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Sobre Nosotros</a></li>
                    </ul>
                </div>
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <p class="text-gray-400 mb-2">Email: info@artesaniabolivia.com</p>
                    <p class="text-gray-400">Teléfono: +591 2 1234567</p>
                </div>
                <div class="w-full md:w-1/4">
                    <h3 class="text-lg font-semibold mb-4">Síguenos</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Facebook</a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Instagram</a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 ArtesaníaBolivia. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>


    <script>
        // Inicializar Google Sign-In
        function initializeGoogleSignIn() {
            google.accounts.id.initialize({
                client_id: '483219139081-2fqjpmji0tr9m7djadpf9n5p64n21slo.apps.googleusercontent.com',
                callback: handleCredentialResponse,
                auto_select: false,
                cancel_on_tap_outside: true
            });
            
            // Renderizar botón en el modal de login
            google.accounts.id.renderButton(
                document.getElementById('google-signin-button-login'),
                { theme: 'outline', size: 'large', text: 'signin_with' }
            );
            
            // Renderizar botón en el modal de registro
            google.accounts.id.renderButton(
                document.getElementById('google-signin-button-register'),
                { theme: 'outline', size: 'large', text: 'signup_with' }
            );
        }

        // Modificar el handleCredentialResponse para identificar de dónde viene la acción
        function handleCredentialResponse(response) {
            const isRegistration = document.getElementById('registroModal').classList.contains('show');
            
            const data = { 
                id_token: response.credential,
                action: isRegistration ? 'register' : 'login'
            };
            
            fetch('google_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data),
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta del servidor:', data); // Para debugging

                // Cerrar los modales primero
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                const registroModal = bootstrap.Modal.getInstance(document.getElementById('registroModal'));
                if (loginModal) loginModal.hide();
                if (registroModal) registroModal.hide();

                // Forzar recarga de la página para actualizar la sesión
                setTimeout(() => {
                    window.location.reload();
                }, 500); // Pequeño delay para asegurar que los modales se cierren
            })
            .catch(error => {
                console.error('Error:', error);

                // Cerrar los modales y recargar la página de todos modos
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                const registroModal = bootstrap.Modal.getInstance(document.getElementById('registroModal'));
                if (loginModal) loginModal.hide();
                if (registroModal) registroModal.hide();

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        }

        window.onload = initializeGoogleSignIn;

        // Toggle del menú de usuario
        function toggleUserMenu() {
            var menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        // Cerrar el menú si se hace clic fuera de él
        window.onclick = function(event) {
            if (!event.target.matches('#userMenuButton') && !event.target.closest('#userMenu')) {
                var menu = document.getElementById('userMenu');
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            }
        }

        // Manejar formulario de recuperación de contraseña
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('recovery_email').value;
            const messageDiv = document.getElementById('forgotPasswordMessage');
            const btnSend = document.getElementById('btnSendRecovery');
            
            // Limpiar mensaje anterior
            messageDiv.innerHTML = '';
            btnSend.disabled = true;
            btnSend.textContent = 'Enviando...';
            
            // Enviar petición AJAX
            fetch('procesar_recuperacion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    document.getElementById('forgotPasswordForm').reset();
                    // Cerrar modal después de 3 segundos
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                        if (modal) modal.hide();
                    }, 3000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud. Por favor, intenta nuevamente.</div>';
                console.error('Error:', error);
            })
            .finally(() => {
                btnSend.disabled = false;
                btnSend.textContent = 'Enviar Enlace de Recuperación';
            });
        });
    </script>
    

</body>
</html>
