<?php
// ...existing code...

<!-- Sección Mis Pedidos -->
<div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
    <h3>Mis Pedidos</h3>
    <?php
    $id_usuario = $_SESSION['id_usuario'];
    $sql_pedidos = "SELECT pc.*, p.nombre, p.precio, p.imagen 
                    FROM pedido_carrito pc 
                    INNER JOIN productos p ON pc.id_producto = p.id_producto 
                    WHERE pc.id_usuario = $id_usuario 
                    AND pc.estado_pedido = 'pendiente'
                    ORDER BY pc.fecha_pedido DESC";
    $resultado_pedidos = mysqli_query($conexion, $sql_pedidos);
    
    if(mysqli_num_rows($resultado_pedidos) > 0) {
        while($pedido = mysqli_fetch_assoc($resultado_pedidos)) {
            // Mostrar los pedidos pendientes
            // ...existing code for displaying orders...
            
            // Agregar el formulario de reseña
            ?>
            <form id="reviewForm" onsubmit="return submitReview(event)">
                <input type="hidden" name="producto_id" id="reviewProductoId" value="<?php echo $pedido['id_producto']; ?>">
                <input type="hidden" name="pedido_id" id="reviewPedidoId" value="<?php echo $pedido['id_pedido']; ?>">
                
                <div class="form-group mb-3">
                    <label>Calificación</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 estrellas">☆</label>
                        <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 estrellas">☆</label>
                        <input type="radio" id="star3" name="rating" value="3" checked><label for="star3" title="3 estrellas">☆</label>
                        <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 estrellas">☆</label>
                        <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 estrella">☆</label>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="comentario">Comentario</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Enviar Reseña</button>
            </form>
            
            <script>
            function submitReview(event) {
                event.preventDefault();
                
                const form = event.target;
                const formData = new FormData(form);
                
                fetch('guardar_resena.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('¡Reseña guardada exitosamente!');
                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                        modal.hide();
                        // Recargar solo la sección de reseñas
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'No se pudo guardar la reseña'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar la reseña');
                });
                
                return false;
            }
            
            // Función para abrir el modal de reseña
            function escribirResena(productoId, pedidoId) {
                document.getElementById('reviewProductoId').value = productoId;
                document.getElementById('reviewPedidoId').value = pedidoId;
                const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
                modal.show();
            }
            </script>
            <?php
        }
    } else {
        echo "<p>No tienes pedidos pendientes</p>";
    }
    ?>
</div>

<!-- Sección Historial de Compras -->
<div class="tab-pane fade" id="v-pills-history" role="tabpanel" aria-labelledby="v-pills-history-tab">
    <h3>Historial de Compras</h3>
    <?php
    $sql_historial = "SELECT pc.*, p.nombre, p.precio, p.imagen 
                      FROM pedido_carrito pc 
                      INNER JOIN productos p ON pc.id_producto = p.id_producto 
                      WHERE pc.id_usuario = $id_usuario 
                      AND pc.estado_pedido != 'pendiente'
                      ORDER BY pc.fecha_pedido DESC";
    $resultado_historial = mysqli_query($conexion, $sql_historial);
    
    if(mysqli_num_rows($resultado_historial) > 0) {
        while($historial = mysqli_fetch_assoc($resultado_historial)) {
            // Mostrar el historial de compras
            // ...existing code for displaying history...
        }
    } else {
        echo "<p>No tienes historial de compras</p>";
    }
    ?>
</div>

// ...existing code...