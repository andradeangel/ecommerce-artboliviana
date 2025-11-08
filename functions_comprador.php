<?php
// For "Mis Pedidos" section - Show only pending orders
function obtener_mis_pedidos($id_comprador) {
    global $conexion;
    $query = "SELECT pc.*, p.nombre, p.imagen, p.precio 
              FROM pedido_carrito pc 
              INNER JOIN productos p ON pc.id_producto = p.id 
              WHERE pc.id_comprador = ? 
              AND pc.estado_pedido = 'pendiente'
              ORDER BY pc.fecha_pedido DESC";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_comprador);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
}

// For "Historial de compras" section - Show all non-pending orders
function obtener_historial_compras($id_comprador) {
    global $conexion;
    $query = "SELECT pc.*, p.nombre, p.imagen, p.precio 
              FROM pedido_carrito pc 
              INNER JOIN productos p ON pc.id_producto = p.id 
              WHERE pc.id_comprador = ? 
              AND pc.estado_pedido != 'pendiente'
              ORDER BY pc.fecha_pedido DESC";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_comprador);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
}