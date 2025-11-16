<?php
function getUserRole($conn, $user_id) {
    $query = "SELECT rol FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['rol'];
}

function canManageProduct($conn, $user_id, $product_id = null) {
    $role = getUserRole($conn, $user_id);
    
    if ($role === 'administrador') {
        return true;
    }
    
    if (in_array($role, ['comunario', 'vendedor'], true)) {
        if ($product_id === null) {
            return true;
        }
        
        $query = "SELECT id_comunario FROM producto WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if (!$product) {
            return false;
        }
        
        return (int) $product['id_comunario'] === (int) $user_id;
    }
    
    return false;
}

function getProductsForUser($conn, $user_id) {
    $role = getUserRole($conn, $user_id);
    
    $queryBase = "SELECT p.*, u.nombre AS nombre_comunario, u.apellido AS apellido_comunario,
                  c.nombre_categoria, a.nombre AS nombre_almacen
                  FROM producto p
                  LEFT JOIN usuario u ON p.id_comunario = u.id_usuario
                  LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                  LEFT JOIN esta e ON p.id_producto = e.id_producto
                  LEFT JOIN almacen a ON e.id_almacen = a.id_almacen";
    
    if ($role === 'administrador') {
        return mysqli_query($conn, $queryBase);
    }
    
    if (in_array($role, ['comunario', 'vendedor'], true)) {
        $query = $queryBase . " WHERE p.id_comunario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    return mysqli_query($conn, $queryBase . " WHERE 1 = 0");
}
?>