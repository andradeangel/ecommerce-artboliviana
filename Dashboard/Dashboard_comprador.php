<?php
session_start();

require_once '../db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Obtener información del usuario
$id_usuario = $_SESSION['id_usuario'];
$query_usuario = "SELECT u.*, c.id_comprador FROM usuario u 
                  INNER JOIN comprador c ON u.id_usuario = c.id_comprador 
                  WHERE u.id_usuario = ?";
$stmt = $conn->prepare($query_usuario);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../index.php');
    exit;
}

$usuario = $result->fetch_assoc();
$id_comprador = $usuario['id_comprador'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Comprador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e65b50;
            --light-bg: #f8f9fa;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: var(--card-shadow);
        }
        
        .navbar-brand {
            font-weight: 600;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        
        .main-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table-modern thead {
            background: linear-gradient(135deg, var(--primary-color), #d1483d);
            color: white;
        }
        
        .table-modern th {
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-pendiente { background-color: #fff3cd; color: #856404; }
        .badge-completado { background-color: #d4edda; color: #155724; }
        .badge-cancelado { background-color: #f8d7da; color: #721c24; }
        
        .btn-edit-profile {
            background: linear-gradient(135deg, var(--primary-color), #d1483d);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-edit-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 91, 80, 0.3);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #d1483d);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(230, 91, 80, 0.25);
        }
        
        .alert {
            border-radius: 10px;
        }

        .rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
        }

        .rating input {
            display: none;
        }

        .rating label {
            color: #ddd;
            font-size: 24px;
            cursor: pointer;
        }

        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffc107;
        }

        .review-form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .review-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .review-form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="Dashboard_comprador.php">
                <i class="fas fa-user-circle me-2"></i>Perfil - Comprador
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=historial">
                            <i class="fas fa-history me-2"></i>Historial de Compras
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=pedidos">
                            <i class="fas fa-shopping-cart me-2"></i>Mis Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=resenas">
                            <i class="fas fa-star me-2"></i>Mis Reseñas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                            <i class="fas fa-user-edit me-2"></i>Editar Perfil
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Volver al Inicio
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes de sesión -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show m-4" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <main class="container my-4">
        <?php
        include('Funciones_db/functions_comprador.php');
        if (isset($_GET['seccion'])) {
            $seccion = $_GET['seccion'];
            switch ($seccion) {
                case 'historial':
                    historialCompras($id_comprador);
                    break;
                case 'pedidos':
                    misPedidos($id_comprador);
                    break;
                case 'resenas':
                    misResenas($id_usuario);
                    sinResenas($id_usuario);
                    break;
                default:
                    echo '<div class="main-card">';
                    echo '<h2 class="section-title">Bienvenido, ' . htmlspecialchars($usuario['nombre']) . '</h2>';
                    echo '<p class="text-muted">Selecciona una opción del menú para comenzar.</p>';
                    echo '</div>';
                    break;
            }
        } else {
            echo '<div class="main-card">';
            echo '<h2 class="section-title">Bienvenido, ' . htmlspecialchars($usuario['nombre']) . ' ' . htmlspecialchars($usuario['apellido']) . '</h2>';
            echo '<p class="text-muted">Selecciona una opción del menú para gestionar tu cuenta.</p>';
            echo '</div>';
        }
        ?>
    </main>

    <!-- Modal para Editar Perfil -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="procesar_editar_perfil.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellido" name="apellido" 
                                       value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" 
                                       pattern="[0-9]{8}" placeholder="Ej: 77123456">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_naci" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_naci" name="fecha_naci" 
                                       value="<?php echo $usuario['fecha_naci'] ?? ''; ?>"
                                       max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Si deseas cambiar tu contraseña, utiliza la opción "Recuperar Contraseña" desde la página principal.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-edit-profile">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'css/footer.php'; ?>
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star me-2"></i>Escribir Reseña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <input type="hidden" id="producto_id" name="producto_id">
                        <input type="hidden" id="pedido_id" name="pedido_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Calificación <span class="text-danger">*</span></label>
                            <div class="rating">
                                <input type="radio" name="rating" value="5" id="star5" required>
                                <label for="star5">★</label>
                                <input type="radio" name="rating" value="4" id="star4">
                                <label for="star4">★</label>
                                <input type="radio" name="rating" value="3" id="star3">
                                <label for="star3">★</label>
                                <input type="radio" name="rating" value="2" id="star2">
                                <label for="star2">★</label>
                                <input type="radio" name="rating" value="1" id="star1">
                                <label for="star1">★</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Comentario <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="comentario" rows="4" maxlength="255" required placeholder="Cuéntanos tu experiencia con este producto..."></textarea>
                            <small class="text-muted">Máximo 255 caracteres</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Reseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para manejar reseñas -->
    <script>
    function escribirResena(id_producto, id_pedido) {
        document.getElementById('producto_id').value = id_producto;
        document.getElementById('pedido_id').value = id_pedido;
        
        var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        myModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reviewForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                
                // Deshabilitar botón y mostrar estado de carga
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
                
                fetch('../guardar_resena.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Verificar si la respuesta es exitosa
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.text(); // Primero obtener como texto
                })
                .then(text => {
                    // Intentar parsear como JSON
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch (e) {
                        console.error('Respuesta no válida:', text);
                        throw new Error('La respuesta del servidor no es JSON válido');
                    }
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Mostrar mensaje de éxito
                        alert('¡Reseña guardada exitosamente!');
                        
                        // Redirigir después de un breve delay
                        setTimeout(() => {
                            window.location.href = 'dashboard_comprador.php?seccion=resenas';
                        }, 500);
                    } else {
                        alert('Error al guardar la reseña: ' + (data.message || 'Error desconocido'));
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    alert('Error al enviar la reseña. Por favor, inténtalo de nuevo.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        }
    });
    </script>
</body>
</html>
