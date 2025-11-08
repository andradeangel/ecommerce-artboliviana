<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y validar datos del formulario
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    $pedido_id = filter_input(INPUT_POST, 'pedido_id', FILTER_VALIDATE_INT);
    $calificacion = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $comentario = trim($_POST['comentario'] ?? '');
    $usuario_id = $_SESSION['id_usuario'];
    
    // Validar datos requeridos
    if (!$producto_id || !$pedido_id || !$calificacion) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos']);
        exit;
    }
    
    // Validar rango de calificación (1-5)
    if ($calificacion < 1 || $calificacion > 5) {
        echo json_encode(['success' => false, 'message' => 'La calificación debe estar entre 1 y 5 estrellas']);
        exit;
    }
    
    // Validar longitud del comentario
    if (empty($comentario) || strlen($comentario) > 255) {
        echo json_encode(['success' => false, 'message' => 'El comentario es requerido y no debe exceder los 255 caracteres']);
        exit;
    }
    
    try {
        // Verificar si ya existe una reseña para este pedido y producto
        $check_sql = "SELECT id_reseña FROM reseña WHERE id_pedido_carrito = ? AND id_producto = ? AND id_usuario = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("iii", $pedido_id, $producto_id, $usuario_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya has enviado una reseña para este producto en este pedido']);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
        
        // Comenzar transacción
        $conn->begin_transaction();
        
        // Insertar la reseña
        $sql = "INSERT INTO reseña (fecha_publicación, comentario, calificacion, id_producto, id_usuario, id_pedido_carrito) 
                VALUES (NOW(), ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiii", $comentario, $calificacion, $producto_id, $usuario_id, $pedido_id);
        
        if ($stmt->execute()) {
            // Confirmar la transacción
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Reseña guardada exitosamente'
            ]);
        } else {
            // Revertir la transacción en caso de error
            $conn->rollback();
            throw new Exception('Error al guardar la reseña en la base de datos');
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log('Error al guardar reseña: ' . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al procesar la reseña: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
}

if (isset($conn)) {
    $conn->close();
}

exit;