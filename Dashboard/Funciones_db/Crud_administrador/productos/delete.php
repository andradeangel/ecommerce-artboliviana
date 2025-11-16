<?php
include("../includes/db.php");
include("auth_products.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];

if (isset($_GET['id_producto'])) {
    $id_producto = (int) $_GET['id_producto'];
    
    if ($id_producto <= 0) {
        $_SESSION['message'] = 'Producto no válido';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    if (!canManageProduct($conn, $user_id, $id_producto)) {
        $_SESSION['message'] = 'No tienes permiso para eliminar este producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    mysqli_begin_transaction($conn);
    
    try {
        $stmtEsta = $conn->prepare("DELETE FROM esta WHERE id_producto = ?");
        if (!$stmtEsta) {
            throw new Exception($conn->error);
        }
        $stmtEsta->bind_param("i", $id_producto);
        if (!$stmtEsta->execute()) {
            throw new Exception($stmtEsta->error);
        }
        
        $stmtProducto = $conn->prepare("DELETE FROM producto WHERE id_producto = ?");
        if (!$stmtProducto) {
            throw new Exception($conn->error);
        }
        $stmtProducto->bind_param("i", $id_producto);
        if (!$stmtProducto->execute()) {
            throw new Exception($stmtProducto->error);
        }
        
        mysqli_commit($conn);
        
        $_SESSION['message'] = 'Producto eliminado correctamente';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        
        $_SESSION['message'] = 'Error al eliminar el producto: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header("Location: index.php");
    exit();
}
?>