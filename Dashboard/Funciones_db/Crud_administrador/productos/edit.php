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
$id_producto = isset($_GET['id_producto']) ? (int) $_GET['id_producto'] : 0;

if ($id_producto <= 0) {
    $_SESSION['message'] = 'Producto no válido';
    $_SESSION['message_type'] = 'danger';
    header("Location: index.php");
    exit();
}

if (!canManageProduct($conn, $user_id, $id_producto)) {
    $_SESSION['message'] = 'No tienes permiso para editar este producto';
    $_SESSION['message_type'] = 'danger';
    header("Location: index.php");
    exit();
}

$nombre = '';
$caracteristica = '';
$precio = 0;
$stock = 0;
$id_categoria = '';
$id_almacen = '';

$stmt = $conn->prepare("SELECT p.*, e.id_almacen FROM producto p LEFT JOIN esta e ON p.id_producto = e.id_producto WHERE p.id_producto = ?");
if (!$stmt) {
    $_SESSION['message'] = 'No se pudo cargar el producto';
    $_SESSION['message_type'] = 'danger';
    header("Location: index.php");
    exit();
}
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'] ?? '';
    $caracteristica = $row['caracteristica'] ?? '';
    $precio = $row['precio'] ?? 0;
    $stock = $row['stock'] ?? 0;
    $id_categoria = $row['id_categoria'] ?? '';
    $id_almacen = $row['id_almacen'] ?? '';
} else {
    $_SESSION['message'] = 'Producto no encontrado';
    $_SESSION['message_type'] = 'danger';
    header("Location: index.php");
    exit();
}

if (isset($_POST['actualizar'])) {
    if (!canManageProduct($conn, $user_id, $id_producto)) {
        $_SESSION['message'] = 'No tienes permiso para actualizar este producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    $nombre = $_POST['nombre'] ?? '';
    $caracteristica = $_POST['caracteristica'] ?? '';
    $precio = isset($_POST['precio']) ? (float) $_POST['precio'] : 0;
    $stock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
    $id_categoria = isset($_POST['id_categoria']) ? (int) $_POST['id_categoria'] : 0;
    $id_almacen = isset($_POST['id_almacen']) ? (int) $_POST['id_almacen'] : 0;
    $fecha_actualizacion = date("Y-m-d H:i:s");

    $stmtUpdate = $conn->prepare("UPDATE producto SET nombre = ?, caracteristica = ?, precio = ?, stock = ?, id_categoria = ?, fecha_actualizacion = ? WHERE id_producto = ?");
    if (!$stmtUpdate) {
        $_SESSION['message'] = 'No se pudo actualizar el producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    $stmtUpdate->bind_param("ssdiisi", $nombre, $caracteristica, $precio, $stock, $id_categoria, $fecha_actualizacion, $id_producto);
    $stmtUpdate->execute();

    if ($id_almacen > 0) {
        $stmtEsta = $conn->prepare("INSERT INTO esta (id_producto, id_almacen) VALUES (?, ?) ON DUPLICATE KEY UPDATE id_almacen = VALUES(id_almacen)");
        if (!$stmtEsta) {
            $_SESSION['message'] = 'No se pudo actualizar el almacén del producto';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php");
            exit();
        }
        $stmtEsta->bind_param("ii", $id_producto, $id_almacen);
        $stmtEsta->execute();
    } else {
        $stmtEstaDelete = $conn->prepare("DELETE FROM esta WHERE id_producto = ?");
        if ($stmtEstaDelete) {
            $stmtEstaDelete->bind_param("i", $id_producto);
            $stmtEstaDelete->execute();
        }
    }

    $_SESSION['message'] = 'Producto actualizado correctamente';
    $_SESSION['message_type'] = 'success';
    header("Location: index.php");
    exit();
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_producto=<?php echo htmlspecialchars($id_producto); ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>" required>
                        <textarea name="caracteristica" class="form-control" required><?php echo htmlspecialchars($caracteristica); ?></textarea>
                        <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo htmlspecialchars($precio); ?>" required>
                        <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>

                        <select name="id_categoria" class="form-control" required>
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $query_categoria = "SELECT id_categoria, nombre_categoria FROM categoria";
                            $result_categoria = mysqli_query($conn, $query_categoria);
                            while ($categoria = mysqli_fetch_assoc($result_categoria)) {
                                $selected = ((int) $id_categoria === (int) $categoria['id_categoria']) ? "selected" : "";
                                echo "<option value='{$categoria['id_categoria']}' $selected>{$categoria['nombre_categoria']}</option>";
                            }
                            ?>
                        </select>

                        <select name="id_almacen" class="form-control">
                            <option value="">Selecciona un almacén</option>
                            <?php
                            $query_almacen = "SELECT id_almacen, nombre FROM almacen";
                            $result_almacen = mysqli_query($conn, $query_almacen);
                            while ($almacen = mysqli_fetch_assoc($result_almacen)) {
                                $selected = ((int) $id_almacen === (int) $almacen['id_almacen']) ? "selected" : "";
                                echo "<option value='{$almacen['id_almacen']}' $selected>{$almacen['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button class="btn btn-success" name="actualizar">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>