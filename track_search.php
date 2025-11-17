<?php
session_start();

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "bdproduc_artesanales");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['search_term'])) {
    $search_term = strtolower(trim($_POST['search_term']));
    
    if (strlen($search_term) > 2) {
        // Buscar productos que coincidan con el término de búsqueda
        $search_like = '%' . $conn->real_escape_string($search_term) . '%';
        
        $sql = "SELECT DISTINCT id_categoria 
                FROM PRODUCTO 
                WHERE LOWER(nombre) LIKE ? 
                   OR LOWER(caracteristica) LIKE ?
                LIMIT 5";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $search_like, $search_like);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Inicializar el array de preferencias si no existe
            if (!isset($_SESSION['preferencias_categorias'])) {
                $_SESSION['preferencias_categorias'] = array();
            }
            
            // Incrementar el peso de las categorías encontradas
            while ($row = $result->fetch_assoc()) {
                $id_categoria = (int)$row['id_categoria'];
                
                if (!isset($_SESSION['preferencias_categorias'][$id_categoria])) {
                    $_SESSION['preferencias_categorias'][$id_categoria] = 0;
                }
                
                // Aumentar el peso por búsqueda (menos que por compra)
                $_SESSION['preferencias_categorias'][$id_categoria] += 2;
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>