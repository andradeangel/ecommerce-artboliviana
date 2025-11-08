<?php
session_start();
require 'db.php';

function usuarioLogueado() {
    return isset($_SESSION['id_usuario']);
}

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
    <title>Sobre Nosotros - ArtesaníaBoliviana</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
                <a href="aboutus.php" class="nav-link text-gray-800 mx-3" style="color: #e65b50;">Sobre Nosotros</a>
                <?php if (usuarioLogueado()): ?>
                <div class="flex items-center">
                    <?php
                        $dashboard_url = 'dashboard.php';
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
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center focus:outline-none" onclick="toggleUserMenu()">
                            <span class="mr-2"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</a>
                        </div>
                    </div>
                    <a href="<?php echo $dashboard_url; ?>" class="mx-3 cursor-pointer hover:opacity-80 transition-opacity" title="Ver Perfil">
                        <img src="img/user.png" alt="Perfil" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                    </a>
                </div>
                <?php else: ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#registroModal">Registrarse</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="pt-16">
        <section class="py-20 bg-white">
            <div class="container mx-auto px-6 max-w-6xl">
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Sobre Nosotros</h1>
                    <p class="text-lg text-gray-600">Conectamos tradición y modernidad: apoyamos a artesanos bolivianos llevando sus obras al mundo.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                    <div>
                        <img src="img/Calidad.jpeg" alt="Artesanía Boliviana" class="w-full rounded-lg shadow-md object-cover">
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Nuestra Misión</h2>
                        <p class="text-gray-600 mb-6">Empoderar a comunidades artesanas brindándoles una vitrina digital justa y segura, promoviendo el comercio ético y sostenible, y preservando la herencia cultural de Bolivia.</p>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Nuestra Visión</h2>
                        <p class="text-gray-600">Ser la plataforma referente de artesanía boliviana en Latinoamérica, uniendo a compradores conscientes con artesanos excepcionales.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-indigo-50">
            <div class="container mx-auto px-6 max-w-6xl">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-12">Nuestros Valores</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <img src="img/Autenticidad.jpeg" class="mx-auto mb-4 h-28 w-28 object-cover rounded-full" alt="Autenticidad">
                        <h3 class="font-semibold text-xl mb-2">Autenticidad</h3>
                        <p class="text-gray-600">Promovemos piezas únicas, hechas a mano, con identidad cultural real.</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <img src="img/Apoyo.jpeg" class="mx-auto mb-4 h-28 w-28 object-cover rounded-full" alt="Apoyo">
                        <h3 class="font-semibold text-xl mb-2">Comunidad</h3>
                        <p class="text-gray-600">Cada compra impacta directamente en familias y comunidades artesanas.</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <img src="img/Calidad.jpeg" class="mx-auto mb-4 h-28 w-28 object-cover rounded-full" alt="Calidad">
                        <h3 class="font-semibold text-xl mb-2">Calidad</h3>
                        <p class="text-gray-600">Seleccionamos con cuidado productos con altos estándares de acabado.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6 max-w-6xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">¿Cómo trabajamos?</h2>
                        <ul class="text-gray-700 space-y-3">
                            <li>• Firma directa con artesanos y asociaciones.</li>
                            <li>• Curaduría de productos y control de calidad.</li>
                            <li>• Logística responsable y seguimiento de pedidos.</li>
                            <li>• Soporte a compradores y capacitación a artesanos.</li>
                        </ul>
                    </div>
                    <div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-xl font-semibold mb-3">Contacto</h3>
                            <p class="text-gray-600 mb-1"><strong>Email:</strong> info@artesaniabolivia.com</p>
                            <p class="text-gray-600 mb-1"><strong>Teléfono:</strong> +591 2 1234567</p>
                            <p class="text-gray-600"><strong>Redes:</strong> Facebook · Instagram · X</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Inicio</a></li>
                        <li><a href="productos-artesania-bolivia.php" class="text-gray-400 hover:text-white transition duration-300">Productos</a></li>
                        <li><a href="aboutus.php" class="text-gray-400 hover:text-white transition duration-300">Sobre Nosotros</a></li>
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
        function toggleUserMenu() {
            var menu = document.getElementById('userMenu');
            if (menu) menu.classList.toggle('hidden');
        }
        window.onclick = function(event) {
            if (!event.target.matches('#userMenuButton') && !event.target.closest('#userMenu')) {
                var menu = document.getElementById('userMenu');
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            }
        }
    </script>
</body>
</html>
