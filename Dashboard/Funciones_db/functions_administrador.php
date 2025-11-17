<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";  // Cambiar según tu configuración de MySQL
$password = "";      // Cambiar según tu configuración de MySQL
$database = "bdproduc_artesanales"; // Cambia este valor al nombre de tu base de datos

// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar si la conexión ha fallado
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// **Función para mostrar usuarios en el dashboard del administrador**
function mostrarUsuarios() {
    global $conn;  // Utilizamos la variable de conexión global

    // Consulta para obtener todos los usuarios
    $sql = "SELECT * FROM USUARIO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Usuarios</h2>";
        echo "<table>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estado</th>
                </tr>";
        // Imprimir los datos de cada usuario en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]." ".$row["apellido"]."</td>
                    <td>".$row["correo"]."</td>
                    <td>".$row["estado"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios registrados.</p>";
    }
}

//** Funcion para mostrar almacenes en el dashboard del administrador */
function mostrarAlmacen() {
    global $conn;

    // Consulta para obtener todas los almacenes 
    $sql = "SELECT * FROM ALMACEN";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Almacén</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Almacén</th>
                    <th>Fecha registro</th>
                </tr>";
        // Imprimir los datos de cada almacen en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["fecha_registro"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay almacenes registrados.</p>";
    }
}


//** Funcion para mostrar departamentos en el dashboard del administrador */
function mostrarDepartamentos() {
    global $conn;

    // Consulta para obtener todas las comunidades
    $sql = "SELECT * FROM DEPARTAMENTO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Departamentos</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Departamento</th>
                    <th>Capital</th>
                    <th>Superficie</th>
                </tr>";
        // Imprimir los datos de cada departamento en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre_departamento"]."</td>
                    <td>".$row["capital"]."</td>
                    <td>".$row["superficie"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay comunidades registradas.</p>";
    }
}


// **Función para mostrar comunidades en el dashboard del administrador**
function mostrarComunidades() {
    global $conn;

    // Consulta para obtener todas las comunidades
    $sql = "SELECT * FROM COMUNIDAD";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Comunidades</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Comunidad</th>
                    <th>Departamento</th>
                    <th>Número de Habitantes</th>
                </tr>";
        // Imprimir los datos de cada comunidad en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["nombre_departamento"]."</td>
                    <td>".$row["nro_habitantes"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay comunidades registradas.</p>";
    }
}



// **Función para mostrar empresas en el dashboard del administrador**
function mostrarEmpresaD() {
    global $conn;

    // Consulta para obtener todos los empresas
    $sql = "SELECT * FROM EMPRESA";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Empresas</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Empresa</th>
                    <th>Dirección</th>
                </tr>";
        // Imprimir los datos de cada producto en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["direccion"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay empresas registrados.</p>";
    }
}



// **Función para mostrar categorias en el dashboard del administrador**
function mostrarCategoriaP() {
    global $conn;

    // Consulta para obtener todos las categorias
    $sql = "SELECT * FROM CATEGORIA";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Categorías</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Categoría</th>
                    <th>Descripción</th>

                </tr>";
        // Imprimir los datos de cada categoria en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre_categoria"]."</td>
                    <td>".$row["descripcion"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay categorias registrados.</p>";
    }
}


// **Función para mostrar productos en el dashboard del administrador**
function mostrarProductos() {
    global $conn;

    // Consulta para obtener todos los productos
    $sql = "SELECT * FROM PRODUCTO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Productos</h2>";
        echo "<table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripcion</th>
                    <th>Stock</th>
                </tr>";
        // Imprimir los datos de cada producto en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["precio"]."</td>
                    <td>".$row["caracteristica"]."</td>
                    <td>".$row["stock"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay productos registrados.</p>";
    }
}


// **Función para mostrar reportes en el dashboard del administrador**
function mostrarReportes() {
    // Aquí puedes agregar lógica para generar reportes como ventas totales, pedidos completados, etc.
    echo "<h2>Reportes de Ventas</h2>";
    echo "<p>Esta sección estará dedicada a la generación de reportes de ventas y análisis de datos.</p>";
}

// **Función para mostrar pedidos en el dashboard del administrador**
function mostrarPedidos() {
    global $conn;

    $sql = "SELECT * FROM PEDIDO_CARRITO";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Pedidos</h2>";
        echo "<table>
                <tr>
                    <th>ID Pedido_Carrito</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_pedido_carrito"]."</td>
                    <td>".$row["cantidad"]."</td>
                    <td>".$row["estado_pedido"]."</td>
                    <td>".$row["fecha_pedido"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay pedidos registrados.</p>";
    }
}

function mostrarPagosPendientes() {
    global $conn;

    $sql = "SELECT p.id_pago, p.monto, p.fecha_pago, p.tipo_pago, p.estado_pago, p.comprobante_pago, GROUP_CONCAT(DISTINCT prod.nombre ORDER BY prod.nombre SEPARATOR ', ') AS productos FROM pago p LEFT JOIN pedido_carrito pc ON p.id_pago = pc.id_pago LEFT JOIN producto prod ON pc.id_producto = prod.id_producto WHERE p.estado_pago = 'pendiente' GROUP BY p.id_pago, p.monto, p.fecha_pago, p.tipo_pago, p.estado_pago, p.comprobante_pago ORDER BY p.fecha_pago DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<h2>Solicitudes de Pago Pendientes</h2>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>
                <thead>
                    <tr>
                        <th>Producto(s)</th>
                        <th>Monto</th>
                        <th>Fecha de Pago</th>
                        <th>Tipo de Pago</th>
                        <th>Estado</th>
                        <th>Comprobante</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            $productos = $row['productos'] ? $row['productos'] : 'Sin productos asociados';
            $rutaComprobante = $row['comprobante_pago'] ? '../' . ltrim($row['comprobante_pago'], '/') : '';
            $botonDescarga = $rutaComprobante && file_exists(__DIR__ . '/../../' . ltrim($row['comprobante_pago'], '/'))
                ? "<a class='btn btn-link btn-sm' href='" . htmlspecialchars($rutaComprobante) . "' download>Descargar</a>"
                : 'Sin comprobante';
            echo "<tr>
                    <td>" . htmlspecialchars($productos) . "</td>
                    <td>" . number_format((float) $row['monto'], 2) . "</td>
                    <td>" . htmlspecialchars($row['fecha_pago']) . "</td>
                    <td>" . htmlspecialchars($row['tipo_pago']) . "</td>
                    <td>
                        <form method='POST' class='d-flex align-items-center gap-2'>
                            <input type='hidden' name='id_pago' value='" . (int) $row['id_pago'] . "'>
                            <select name='estado_pago' class='form-select form-select-sm'>
                                <option value='pendiente'" . ($row['estado_pago'] === 'pendiente' ? " selected" : "") . ">Pendiente</option>
                                <option value='completado'" . ($row['estado_pago'] === 'completado' ? " selected" : "") . ">Completado</option>
                            </select>
                            <button type='submit' name='actualizar_estado_pago' class='btn btn-primary btn-sm'>Actualizar</button>
                        </form>
                    </td>
                    <td>" . $botonDescarga . "</td>
                </tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<p>No hay pagos pendientes.</p>";
    }
}

function mostrarEntregas() {
    global $conn;

    $sql = "SELECT pc.id_pedido_carrito, pc.estado_pedido, prod.nombre AS nombre_producto, u.calle, u.nro, u.latitud, u.longitud FROM pedido_carrito pc LEFT JOIN ubicacion u ON pc.id_ubicacion = u.id_ubicacion LEFT JOIN producto prod ON pc.id_producto = prod.id_producto WHERE pc.estado_pedido = 'pendiente' ORDER BY pc.fecha_pedido DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<h2>Entregas</h2>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Calle</th>
                        <th>Número</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            $producto = $row['nombre_producto'] ? $row['nombre_producto'] : 'Producto no disponible';
            $calle = $row['calle'] ? $row['calle'] : 'Sin calle';
            $numero = isset($row['nro']) ? $row['nro'] : 'N/A';
            $latitud = $row['latitud'];
            $longitud = $row['longitud'];
            $tieneCoordenadas = $latitud !== null && $longitud !== null;
            $urlMapa = $tieneCoordenadas ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($latitud . ',' . $longitud) : '';
            $enlaceMapa = $tieneCoordenadas ? "<a href='" . htmlspecialchars($urlMapa) . "' target='_blank' rel='noopener noreferrer'>Ver en mapa</a>" : 'Sin coordenadas';
            $estadoActual = $row['estado_pedido'] ? $row['estado_pedido'] : 'pendiente';
            echo "<tr>
                    <td>" . htmlspecialchars($producto) . "</td>
                    <td>" . htmlspecialchars($calle) . "</td>
                    <td>" . htmlspecialchars($numero) . "</td>
                    <td>" . $enlaceMapa . "</td>
                    <td>
                        <form method='POST' class='d-flex align-items-center gap-2'>
                            <input type='hidden' name='id_pedido' value='" . (int) $row['id_pedido_carrito'] . "'>
                            <select name='estado_pedido' class='form-select form-select-sm'>
                                <option value='pendiente'" . ($estadoActual === 'pendiente' ? " selected" : "") . ">Pendiente</option>
                                <option value='completado'" . ($estadoActual === 'completado' ? " selected" : "") . ">Completado</option>
                                <option value='cancelado'" . ($estadoActual === 'cancelado' ? " selected" : "") . ">Cancelado</option>
                            </select>
                            <button type='submit' name='actualizar_estado_entrega' class='btn btn-primary btn-sm'>Actualizar</button>
                        </form>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<p>No hay entregas pendientes.</p>";
    }
}

function mostrarFormularioQR() {
    $rutaPublica = '../img/qr_code.png';
    $rutaFisica = dirname(dirname(__DIR__)) . '/img/qr_code.png';
    clearstatcache(true, $rutaFisica);
    $version = file_exists($rutaFisica) ? filemtime($rutaFisica) : time();
    if (isset($_SESSION['qr_version']) && $_SESSION['qr_version'] > $version) {
        $version = (int) $_SESSION['qr_version'];
    }
    echo "<h2>Actualizar Código QR</h2>";
    echo "<div class='card shadow-sm'><div class='card-body'>";
    echo "<p class='mb-3'>Carga una imagen PNG para reemplazar el código QR actual.</p>";
    echo "<div class='mb-4'><img src='" . htmlspecialchars($rutaPublica . '?v=' . $version) . "' alt='Código QR actual' class='img-fluid' style='max-width: 240px;'></div>";
    echo "<form method='POST' enctype='multipart/form-data' class='d-flex flex-column gap-3'>";
    echo "<div><input type='file' name='qr_imagen' accept='image/png' class='form-control' required></div>";
    echo "<button type='submit' name='actualizar_qr' class='btn btn-primary align-self-start'>Actualizar QR</button>";
    echo "</form>";
    echo "</div></div>";
}

function actualizarEstadoPedido($idPedido, $estadoPedido) {
    global $conn;

    $estadosPermitidos = ['pendiente', 'completado', 'cancelado'];
    if (!in_array($estadoPedido, $estadosPermitidos, true)) {
        return false;
    }

    $stmtPedido = $conn->prepare("SELECT estado_pedido, cantidad, id_producto FROM pedido_carrito WHERE id_pedido_carrito = ?");
    if (!$stmtPedido) {
        return false;
    }

    $stmtPedido->bind_param("i", $idPedido);
    if (!$stmtPedido->execute()) {
        $stmtPedido->close();
        return false;
    }

    $stmtPedido->bind_result($estadoActual, $cantidad, $idProducto);
    if (!$stmtPedido->fetch()) {
        $stmtPedido->close();
        return false;
    }
    $stmtPedido->close();

    $cantidad = max((int) $cantidad, 0);
    $idProducto = (int) $idProducto;

    if ($idProducto <= 0) {
        return false;
    }

    if (!$conn->begin_transaction()) {
        return false;
    }

    try {
        if ($estadoPedido === 'completado' && $estadoActual !== 'completado' && $cantidad > 0) {
            $stmtStock = $conn->prepare("UPDATE producto SET stock = GREATEST(stock - ?, 0) WHERE id_producto = ?");
            if (!$stmtStock) {
                $conn->rollback();
                return false;
            }
            $stmtStock->bind_param("ii", $cantidad, $idProducto);
            if (!$stmtStock->execute()) {
                $stmtStock->close();
                $conn->rollback();
                return false;
            }
            $stmtStock->close();
        }

        $stmtActualizar = $conn->prepare("UPDATE pedido_carrito SET estado_pedido = ? WHERE id_pedido_carrito = ?");
        if (!$stmtActualizar) {
            $conn->rollback();
            return false;
        }
        $stmtActualizar->bind_param("si", $estadoPedido, $idPedido);
        if (!$stmtActualizar->execute()) {
            $stmtActualizar->close();
            $conn->rollback();
            return false;
        }
        $stmtActualizar->close();

        $conn->commit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        return false;
    }

    return true;
}

function actualizarEstadoPago($idPago, $estadoPago) {
    global $conn;

    if ($estadoPago !== 'pendiente' && $estadoPago !== 'completado') {
        return false;
    }

    $stmt = $conn->prepare("UPDATE pago SET estado_pago = ? WHERE id_pago = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("si", $estadoPago, $idPago);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}
?>
