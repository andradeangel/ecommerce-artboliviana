<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "bdproduc_artesanales";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// **Función para mostrar los pedidos asignados al delivery**
function mostrarPedidosAsignados() {
    global $conn;

    // Consulta para obtener los pedidos asignados a este delivery
    // En este caso, se supone que el ID del delivery está almacenado en la sesión
    //$id_delivery = $_SESSION['id_delivery'];
    $id_delivery = 1; 

    $sql = "SELECT P.id_identrega_delivery, P.fecha_entrega, P.estado_entrega, C.nombre, C.apellido 
            FROM ENTREGA_DELIVERY P
            JOIN DELIVERY D ON P.id_delivery = D.id_delivery
            JOIN PEDIDO_CARRITO PD ON P.id_pedido_carrito = PD.id_pedido_carrito
            JOIN USUARIO C ON PD.id_comprador = C.id_usuario
            WHERE D.id_delivery = '$id_delivery' AND P.estado_entrega = 'pendiente'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Pedidos Asignados</h2>";
        echo "<table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha Pedido</th>
                    <th>Estado</th>
                    <th>Cliente</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_pedido_carrito"]."</td>
                    <td>".$row["fecha_pedido"]."</td>
                    <td>".$row["estado_pedido"]."</td>
                    <td>".$row["nombre"]." ".$row["apellido"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tienes pedidos asignados en este momento.</p>";
    }
}

// **Función para mostrar el historial de entregas realizadas**
function mostrarHistorialEntregas() {
    global $conn;

    //$id_delivery = $_SESSION['id_delivery']; // ID del delivery actual
    $id_delivery = 1;
    $sql = "SELECT E.id_identrega_delivery, E.fecha_entrega, PD.estado_pedido, E.fecha_entrega, C.nombre, C.apellido
            FROM ENTREGA_DELIVERY E
            JOIN DELIVERY D ON E.id_delivery = D.id_delivery
            JOIN PEDIDO_CARRITO PD ON E.id_pedido_carrito = PD.id_pedido_carrito
            JOIN USUARIO C ON PD.id_comprador = C.id_usuario
            WHERE D.id_delivery = '$id_delivery' AND E.estado_entrega = 'entregado'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Historial de Entregas</h2>";
        echo "<table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Datos Entrega</th>
                    <th>Fecha Entrega</th>
                    <th>Cliente</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_entrega_delivery"]."</td>
                    <td>".$row["datos_entrega"]."</td>
                    <td>".$row["fecha_entrega"]."</td>
                    <td>".$row["nombre"]." ".$row["apellido"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tienes entregas completadas.</p>";
    }
}

// **Función para mostrar el perfil del delivery**
function mostrarPerfilDelivery() {
    global $conn;

    //$id_delivery = $_SESSION['id_delivery']; // ID del delivery actual
    $id_delivery = 1;

    $sql = "SELECT U.nombre, U.apellido, U.correo, U.telefono
            FROM USUARIO U
            JOIN DELIVERY D ON U.id_usuario = D.id_delivery
            WHERE D.id_delivery = '$id_delivery'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Perfil del Delivery</h2>";
        while($row = $result->fetch_assoc()) {
            echo "<p><strong>Nombre:</strong> ".$row["nombre"]." ".$row["apellido"]."</p>";
            echo "<p><strong>Correo:</strong> ".$row["correo"]."</p>";
            echo "<p><strong>Teléfono:</strong> ".$row["telefono"]."</p>";
        }
    } else {
        echo "<p>Error al cargar el perfil.</p>";
    }
}
?>
