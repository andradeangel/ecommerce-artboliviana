<?php
// Esta función ya espera que $conn esté disponible globalmente desde db.php

function historialCompras($id_comprador) {
    global $conn;
    
    $sql = $conn->prepare("SELECT pc.*, p.nombre as producto_nombre, p.precio, pag.monto, pag.estado_pago
                           FROM pedido_carrito pc
                           INNER JOIN producto p ON pc.id_producto = p.id_producto
                           INNER JOIN pago pag ON pc.id_pago = pag.id_pago
                           WHERE pc.id_comprador = ?
                           AND pc.estado_pedido != 'pendiente'
                           ORDER BY pc.fecha_pedido DESC");
    $sql->bind_param("i", $id_comprador);
    $sql->execute();
    $result = $sql->get_result();
    
    echo '<div class="main-card">';
    echo '<h2 class="section-title"><i class="fas fa-history me-2"></i>Historial de Compras</h2>';
    echo '<p class="text-muted mb-4">Todos tus pedidos: pendientes, completados, entregados y cancelados.</p>';
    
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-modern">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID Pedido</th>';
        echo '<th>Producto</th>';
        echo '<th>Fecha</th>';
        echo '<th>Cantidad</th>';
        echo '<th>Precio Unit.</th>';
        echo '<th>Subtotal</th>';
        echo '<th>Costo Envío</th>';
        echo '<th>Total</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['cantidad'] * $row['precio'];
            $total = $subtotal + ($row['costo_envio'] ?? 0);
            
            // Clase para estado del pedido
            $estado_class = 'badge-' . strtolower($row['estado_pedido']);
            // Clase para estado del pago
            $pago_class = 'badge-' . strtolower($row['estado_pago']);
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id_pedido_carrito']) . '</td>';
            echo '<td>' . htmlspecialchars($row['producto_nombre']) . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['fecha_pedido'])) . '</td>';
            echo '<td>' . htmlspecialchars($row['cantidad']) . '</td>';
            echo '<td>Bs. ' . number_format($row['precio'], 2) . '</td>';
            echo '<td>Bs. ' . number_format($subtotal, 2) . '</td>';
            echo '<td>Bs. ' . number_format($row['costo_envio'] ?? 0, 2) . '</td>';
            echo '<td><strong>Bs. ' . number_format($total, 2) . '</strong></td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="empty-state">';
        echo '<i class="fas fa-inbox"></i>';
        echo '<h4>No tienes compras registradas</h4>';
        echo '<p>Aún no has realizado ninguna compra.</p>';
        echo '</div>';
    }
    
    echo '</div>';
    $sql->close();
}

function misPedidos($id_comprador) {
    global $conn;
    
    // Mis Pedidos: Solo pedidos PENDIENTES (en proceso, no entregados ni cancelados)
    $sql = $conn->prepare("SELECT pc.*, p.nombre as producto_nombre, p.precio, pag.estado_pago
                           FROM pedido_carrito pc
                           INNER JOIN producto p ON pc.id_producto = p.id_producto
                           INNER JOIN pago pag ON pc.id_pago = pag.id_pago
                           WHERE pc.id_comprador = ? 
                           AND pc.estado_pedido = 'pendiente'
                           ORDER BY pc.fecha_pedido DESC");
    $sql->bind_param("i", $id_comprador);
    $sql->execute();
    $result = $sql->get_result();
    
    echo '<div class="main-card">';
    echo '<h2 class="section-title"><i class="fas fa-shopping-cart me-2"></i>Mis Pedidos Pendientes</h2>';
    echo '<p class="text-muted mb-4">Pedidos que están en proceso de preparación y envío.</p>';
    
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-modern">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID Pedido</th>';
        echo '<th>Producto</th>';
        echo '<th>Fecha Pedido</th>';
        echo '<th>Cantidad</th>';
        echo '<th>Precio Unit.</th>';
        echo '<th>Subtotal</th>';
        echo '<th>Costo Envío</th>';
        echo '<th>Total</th>';
        echo '<th>Verificación Pago</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['cantidad'] * $row['precio'];
            $total = $subtotal + ($row['costo_envio'] ?? 0);
            
            // Clase para estado del pago
            $pago_class = 'badge-' . strtolower($row['estado_pago']);
            
            echo '<tr>';
            echo '<td><strong>#' . htmlspecialchars($row['id_pedido_carrito']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($row['producto_nombre']) . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['fecha_pedido'])) . '</td>';
            echo '<td>' . htmlspecialchars($row['cantidad']) . '</td>';
            echo '<td>Bs. ' . number_format($row['precio'], 2) . '</td>';
            echo '<td>Bs. ' . number_format($subtotal, 2) . '</td>';
            echo '<td>Bs. ' . number_format($row['costo_envio'] ?? 0, 2) . '</td>';
            echo '<td><strong>Bs. ' . number_format($total, 2) . '</strong></td>';
            echo '<td><span class="badge badge-status ' . $pago_class . '">' . ucfirst($row['estado_pago']) . '</span></td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="empty-state">';
        echo '<i class="fas fa-shopping-bag"></i>';
        echo '<h4>No tienes pedidos pendientes</h4>';
        echo '<p>Actualmente no tienes pedidos en proceso. Revisa tu <a href="?seccion=historial">historial de compras</a> para ver todos tus pedidos.</p>';
        echo '</div>';
    }
    
    echo '</div>';
    $sql->close();
}

function misResenas($id_usuario) {
    global $conn;
    
    $sql = $conn->prepare("SELECT DISTINCT r.id_reseña, r.fecha_publicación, r.comentario, r.calificacion, 
                                  p.nombre AS producto_nombre, p.id_producto
                           FROM reseña r
                           INNER JOIN producto p ON r.id_producto = p.id_producto 
                           INNER JOIN pedido_carrito pc ON p.id_producto = pc.id_producto
                           WHERE r.id_usuario = ?
                           AND pc.id_comprador = ?
                           AND pc.estado_pedido = 'completado'
                           ORDER BY r.fecha_publicación DESC");
    
    $sql->bind_param("ii", $id_usuario, $id_usuario);
    $sql->execute();
    $result = $sql->get_result();
    
    echo '<div class="main-card">';
    echo '<h2 class="section-title"><i class="fas fa-star me-2"></i>Mis Reseñas</h2>';
    
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-modern">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Producto</th>';
        echo '<th>Fecha</th>';
        echo '<th>Calificación</th>';
        echo '<th>Comentario</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            // Generar estrellas
            $estrellas = '';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $row['calificacion']) {
                    $estrellas .= '<i class="fas fa-star text-warning"></i>';
                } else {
                    $estrellas .= '<i class="far fa-star text-muted"></i>';
                }
            }
            
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($row['producto_nombre']) . '</strong></td>';
            echo '<td>' . date('d/m/Y', strtotime($row['fecha_publicación'])) . '</td>';
            echo '<td>' . $estrellas . ' <span class="text-muted">(' . $row['calificacion'] . '/5)</span></td>';
            echo '<td>' . htmlspecialchars($row['comentario'] ?? 'Sin comentario') . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="empty-state">';
        echo '<i class="fas fa-star"></i>';
        echo '<h4>No has escrito ninguna reseña</h4>';
        echo '<p>Aún no has calificado ningún producto.</p>';
        echo '</div>';
    }
    
    echo '</div>';
    $sql->close();
}

function sinResenas($id_usuario) {
    global $conn;
    
    $sql = $conn->prepare("SELECT DISTINCT p.id_producto, p.nombre AS producto_nombre, 
                          pc.fecha_pedido, pc.id_pedido_carrito
                          FROM pedido_carrito pc
                          INNER JOIN producto p ON pc.id_producto = p.id_producto
                          LEFT JOIN reseña r ON p.id_producto = r.id_producto AND r.id_usuario = ?
                          WHERE pc.id_comprador = ?
                          AND pc.estado_pedido = 'completado'
                          AND r.id_reseña IS NULL
                          ORDER BY pc.fecha_pedido DESC");
    
    $sql->bind_param("ii", $id_usuario, $id_usuario);
    $sql->execute();
    $result = $sql->get_result();
    
    echo '<div class="main-card mt-4">';
    echo '<h2 class="section-title"><i class="far fa-star me-2"></i>Productos Sin Reseñas</h2>';
    echo '<p class="text-muted mb-4">Productos completados que aún no has calificado.</p>';
    
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-modern">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Producto</th>';
        echo '<th>Fecha de Compra</th>';
        echo '<th>Acción</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($row['producto_nombre']) . '</strong></td>';
            echo '<td>' . date('d/m/Y', strtotime($row['fecha_pedido'])) . '</td>';
            echo '<td>';
            echo '<button class="btn btn-primary btn-sm" onclick="escribirResena(' . $row['id_producto'] . ', ' . $row['id_pedido_carrito'] . ')">';
            echo '<i class="fas fa-pencil-alt me-1"></i>Escribir Reseña';
            echo '</button>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="empty-state">';
        echo '<i class="far fa-check-circle"></i>';
        echo '<h4>¡Has calificado todos tus productos!</h4>';
        echo '<p>No tienes productos pendientes por calificar.</p>';
        echo '</div>';
    }
    
    echo '</div>';
    $sql->close();
}

// Add this function to render the review modal
function renderReviewModal() {
    echo <<<HTML
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="reviewForm" class="review-form">
                        <input type="hidden" id="producto_id" name="producto_id">
                        <input type="hidden" id="pedido_id" name="pedido_id">
                        
                        <div class="mb-3">
                            <label>Calificación:</label>
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
                            <label>Comentario:</label>
                            <textarea name="comentario" rows="4" maxlength="255" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
HTML;
}
?>

<!-- Script de reseñas movido a Dashboard_comprador.php para evitar duplicación -->
