<?php
// Iniciar sesión para manejar datos del usuario
session_start();

// Configuración de la base de datos
require_once 'db.php';

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

header('Content-Type: application/json');

try {
    // Validar que todos los campos POST estén presentes
    $campos_requeridos = ['nombre', 'numero_contacto', 'tipo_pago', 'latitud', 'longitud', 'productos'];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            throw new Exception("Campo requerido faltante: $campo");
        }
    }
    
    // Validar archivo de comprobante
    if (!isset($_FILES['comprobante_pago']) || $_FILES['comprobante_pago']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Debe cargar un comprobante de pago válido');
    }
    
    // Obtener y sanitizar datos
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $numero_contacto = htmlspecialchars(trim($_POST['numero_contacto']));
    $tipo_pago = htmlspecialchars(trim($_POST['tipo_pago']));
    $latitud = floatval($_POST['latitud']);
    $longitud = floatval($_POST['longitud']);
    $costo_envio = isset($_POST['costo_envio']) ? max(0, floatval($_POST['costo_envio'])) : 0;
    $direccion = isset($_POST['direccion']) ? htmlspecialchars(trim($_POST['direccion'])) : 'N/A';
    $referencia = isset($_POST['referencia']) ? htmlspecialchars(trim($_POST['referencia'])) : '0';
    $productos_json = $_POST['productos'];
    $productos_decodificados = json_decode($productos_json, true);
    if (!is_array($productos_decodificados) || empty($productos_decodificados)) {
        throw new Exception('No se recibieron productos válidos');
    }
    $id_comprador = $_SESSION['id_usuario'];
    
    // Validaciones adicionales
    
    if (strlen($numero_contacto) < 8) {
        throw new Exception('Número de contacto inválido');
    }
    
    $productos_validados = [];
    $subtotal = 0;
    $stmt_producto = $conn->prepare("SELECT id_producto, precio FROM producto WHERE id_producto = ?");
    if (!$stmt_producto) {
        throw new Exception('Error al preparar consulta de producto: ' . $conn->error);
    }

    foreach ($productos_decodificados as $producto_item) {
        $id_producto_item = isset($producto_item['id_producto']) ? intval($producto_item['id_producto']) : 0;
        $cantidad_item = isset($producto_item['cantidad']) ? intval($producto_item['cantidad']) : 0;

        if ($id_producto_item <= 0 || $cantidad_item <= 0) {
            throw new Exception('Datos de producto inválidos');
        }

        $stmt_producto->bind_param("i", $id_producto_item);
        if (!$stmt_producto->execute()) {
            throw new Exception('Error al obtener información del producto');
        }

        $resultado_producto = $stmt_producto->get_result();
        $producto_bd = $resultado_producto->fetch_assoc();
        if (!$producto_bd) {
            throw new Exception('Producto no encontrado');
        }

        $precio_producto = floatval($producto_bd['precio']);
        $subtotal += $precio_producto * $cantidad_item;

        $productos_validados[] = [
            'id_producto' => (int) $producto_bd['id_producto'],
            'cantidad' => $cantidad_item
        ];
    }
    $stmt_producto->close();

    if ($subtotal <= 0) {
        throw new Exception('El monto debe ser mayor a 0');
    }

    $monto_total = $subtotal + $costo_envio;
    
    // Procesar archivo
    $archivo = $_FILES['comprobante_pago'];
    $directorio_comprobantes = __DIR__ . '/uploads/comprobantes';
    if (!is_dir($directorio_comprobantes)) {
        if (!mkdir($directorio_comprobantes, 0777, true) && !is_dir($directorio_comprobantes)) {
            throw new Exception('No se pudo preparar el directorio para comprobantes');
        }
    }
    $extension_archivo = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_base = uniqid('comprobante_', true);
    $nombre_archivo = $extension_archivo !== '' ? $nombre_base . '.' . strtolower($extension_archivo) : $nombre_base;
    $ruta_destino = $directorio_comprobantes . '/' . $nombre_archivo;
    if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        throw new Exception('No se pudo guardar el comprobante de pago');
    }
    $ruta_comprobante = 'uploads/comprobantes/' . $nombre_archivo;
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // 1. Insertar en tabla pago
    $fecha_pago = date('Y-m-d H:i:s');
    $estado_pago = 'pendiente';
    
    $stmt_pago = $conn->prepare("INSERT INTO pago (monto, fecha_pago, tipo_pago, estado_pago, comprobante_pago) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_pago) {
        throw new Exception('Error en preparación: ' . $conn->error);
    }
    
    $stmt_pago->bind_param("dssss", $monto_total, $fecha_pago, $tipo_pago, $estado_pago, $ruta_comprobante);
    
    if (!$stmt_pago->execute()) {
        throw new Exception('Error al insertar pago: ' . $stmt_pago->error);
    }
    
    $id_pago = $conn->insert_id;
    $stmt_pago->close();
    
    // 2. Insertar en tabla ubicacion
    $departamento = 'La Paz';
    $provincia = 'Murillo';
    $calle = $direccion !== '' ? $direccion : 'N/A';
    $zona = $referencia !== '' ? $referencia : 'Zona Entrega';
    $id_almacen = 2;
    
    $stmt_ubicacion = $conn->prepare(
        "INSERT INTO ubicacion (departamento, provincia, calle, zona, nro, latitud, longitud, id_almacen) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt_ubicacion) {
        throw new Exception('Error en preparación ubicación: ' . $conn->error);
    }
    
    $stmt_ubicacion->bind_param("sssssddi", $departamento, $provincia, $calle, $zona, $numero_contacto, $latitud, $longitud, $id_almacen);
    
    if (!$stmt_ubicacion->execute()) {
        throw new Exception('Error al insertar ubicación: ' . $stmt_ubicacion->error);
    }
    
    $id_ubicacion = $conn->insert_id;
    $stmt_ubicacion->close();
    
    // 3. Insertar en tabla pedido_carrito
    $estado_pedido = 'pendiente';
    $fecha_pedido = date('Y-m-d H:i:s');
    
    $stmt_pedido = $conn->prepare(
        "INSERT INTO pedido_carrito (cantidad, estado_pedido, fecha_pedido, costo_envio, id_producto, id_pago, id_comprador, id_ubicacion) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt_pedido) {
        throw new Exception('Error en preparación pedido: ' . $conn->error);
    }
    
    $ids_pedidos = [];
    foreach ($productos_validados as $indice => $producto_validado) {
        $costo_envio_fila = $indice === 0 ? $costo_envio : 0;
        $cantidad_producto = $producto_validado['cantidad'];
        if ($cantidad_producto <= 0) {
            throw new Exception('La cantidad debe ser mayor a 0');
        }
        $id_producto = $producto_validado['id_producto'];
        $stmt_pedido->bind_param(
            "issdiiii",
            $cantidad_producto,
            $estado_pedido,
            $fecha_pedido,
            $costo_envio_fila,
            $id_producto,
            $id_pago,
            $id_comprador,
            $id_ubicacion
        );
        if (!$stmt_pedido->execute()) {
            throw new Exception('Error al insertar pedido: ' . $stmt_pedido->error);
        }
        $ids_pedidos[] = $conn->insert_id;
    }
    $stmt_pedido->close();
    
    // Confirmar transacción
    $conn->commit();
    
    unset($_SESSION['carrito']);
    
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Pedido procesado correctamente',
        'id_pedidos' => $ids_pedidos,
        'monto_total' => round($monto_total, 2)
    ]);
    
} catch (Exception $e) {
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
} finally {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->close();
    }
}
?>