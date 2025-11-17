<?php
session_start();
require_once 'Funciones_db/functions_administrador.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado_pago'])) {
    $id_pago = isset($_POST['id_pago']) ? (int) $_POST['id_pago'] : 0;
    $estado_pago = isset($_POST['estado_pago']) && $_POST['estado_pago'] === 'completado' ? 'completado' : 'pendiente';
    if ($id_pago > 0 && actualizarEstadoPago($id_pago, $estado_pago)) {
        $_SESSION['message'] = 'Estado de pago actualizado correctamente';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'No se pudo actualizar el estado del pago';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: dashboard_administrador.php?seccion=pagos');
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado_entrega'])) {
    $id_pedido = isset($_POST['id_pedido']) ? (int) $_POST['id_pedido'] : 0;
    $estado_pedido = isset($_POST['estado_pedido']) ? $_POST['estado_pedido'] : 'pendiente';
    if ($id_pedido > 0 && actualizarEstadoPedido($id_pedido, $estado_pedido)) {
        $_SESSION['message'] = 'Estado de entrega actualizado correctamente';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'No se pudo actualizar el estado de la entrega';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: dashboard_administrador.php?seccion=entregas');
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_qr'])) {
    $mensaje = 'No se pudo actualizar el código QR';
    $tipo = 'danger';
    if (isset($_FILES['qr_imagen']) && $_FILES['qr_imagen']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['qr_imagen']['tmp_name'])) {
        $tmpName = $_FILES['qr_imagen']['tmp_name'];
        $mime = mime_content_type($tmpName);
        $extension = strtolower(pathinfo($_FILES['qr_imagen']['name'], PATHINFO_EXTENSION));
        if ($mime === 'image/png' && $extension === 'png') {
            $destino = __DIR__ . '/../img/qr_code.png';
            if (move_uploaded_file($tmpName, $destino)) {
                clearstatcache(true, $destino);
                $_SESSION['qr_version'] = time();
                $mensaje = 'Código QR actualizado correctamente';
                $tipo = 'success';
            } else {
                $mensaje = 'No se pudo guardar el archivo del código QR';
            }
        } else {
            $mensaje = 'Solo se permiten imágenes PNG para el código QR';
        }
    } else {
        $mensaje = 'No se recibió una imagen válida';
    }
    $_SESSION['message'] = $mensaje;
    $_SESSION['message_type'] = $tipo;
    header('Location: dashboard_administrador.php?seccion=actualizarQR');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="css/styles.css" rel="stylesheet"> <!-- Tu archivo CSS existente -->
    <link href="css/dashboard.css" rel="stylesheet"> <!-- Nuevo archivo CSS -->
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_administrador.php">E-Commerce Artesanal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dropdown menú de gestión -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="gestionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gestiones
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="gestionesDropdown">
                            <li><a class="dropdown-item" href="?seccion=usuarios">Gestión de Usuarios</a></li>
                            <li><a class="dropdown-item" href="?seccion=productos">Gestión de Productos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=reportes">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=pagos">Solicitud de Pagos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=entregas">Entregas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=actualizarQR">Actualizar QR</a>
                    </li>
                </ul>
                <!-- Usuario autenticado -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-sign-out-alt"></i> Salir del Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes de sesión -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show m-4" role="alert">
            <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <main>
        <?php
        if (isset($_GET['seccion'])) {
            $seccion = $_GET['seccion'];
            switch ($seccion) {
                case 'usuarios':
                    mostrarUsuarios();
                    echo '<a href="Funciones_db/Crud_administrador/usuarios/index.php" class="btn btn-custom mt-3">Gestión de Usuarios</a>';
                    break;
                case 'almacen':
                    mostrarAlmacen();
                    echo '<a href="Funciones_db/Crud_administrador/almacen/index.php" class="btn btn-custom mt-3">Gestión de Almacen</a>';
                    break;
                case 'departamentos':
                    mostrarDepartamentos();
                    echo '<a href="Funciones_db/Crud_administrador/departamento/index.php" class="btn btn-custom mt-3">Gestión de Departamentos</a>';
                    break;
                case 'comunidades':
                    mostrarComunidades();
                    echo '<a href="Funciones_db/Crud_administrador/comunidades/index.php" class="btn btn-custom mt-3">Gestión de Comunidades</a>';
                    break;
                case 'empresaD':
                    mostrarEmpresaD();
                    echo '<a href="Funciones_db/Crud_administrador/empresa_delivery/index.php" class="btn btn-custom mt-3">Gestión de Empresa</a>';
                    break;
                case 'categoriaP':
                    mostrarCategoriaP();
                    echo '<a href="Funciones_db/Crud_administrador/categoria/index.php" class="btn btn-custom mt-3">Gestión de Categoria</a>';
                    break;
                case 'productos':
                    mostrarProductos();
                    echo '<a href="Funciones_db/Crud_administrador/productos/index.php" class="btn btn-custom mt-3">Gestión de Productos</a>';
                    break;        
                case 'reportes':
                    mostrarReportes();
                    echo '<a href="Funciones_db/Crud_administrador/reportes/index.php" class="btn btn-custom mt-3">Gestión de Reportes</a>';
                    break;
                case 'pedidos':
                    mostrarPedidos();
                    echo '<a href="Funciones_db/Crud_administrador/pedidos/index.php" class="btn btn-custom mt-3">Gestión de Pedidos</a>';
                    break;
                case 'pagos':
                    mostrarPagosPendientes();
                    break;
                case 'entregas':
                    mostrarEntregas();
                    break;
                case 'actualizarQR':
                    mostrarFormularioQR();
                    break;
                default:
                    echo "<h2>Bienvenido al Dashboard del Administrador</h2>";
                    break;
            }
        } else {
            echo "<h2>Bienvenido al Dashboard del Administrador</h2>";
        }
        
        ?>
    </main>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
