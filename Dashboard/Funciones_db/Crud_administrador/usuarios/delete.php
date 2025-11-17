<?php
include("../includes/db.php");

if (isset($_GET['id_usuario'])) {
    $id_usuario = (int) $_GET['id_usuario'];

    mysqli_begin_transaction($conn);

    try {
        $result = mysqli_query($conn, "SELECT rol FROM usuario WHERE id_usuario = $id_usuario");

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $rol = $row['rol'];
            mysqli_free_result($result);

            if ($rol === 'administrador') {
                mysqli_query($conn, "DELETE FROM administrador WHERE id_administrador = $id_usuario");
            } elseif ($rol === 'comprador') {
                mysqli_query($conn, "DELETE FROM comprador WHERE id_comprador = $id_usuario");
            } elseif ($rol === 'vendedor') {
                mysqli_query($conn, "DELETE FROM comunario WHERE id_comunario = $id_usuario");
            } elseif ($rol === 'delivery') {
                mysqli_query($conn, "DELETE FROM delivery WHERE id_delivery = $id_usuario");
            }

            mysqli_query($conn, "DELETE FROM usuario WHERE id_usuario = $id_usuario");
            mysqli_commit($conn);

            $_SESSION['message'] = 'Usuario eliminado correctamente';
            $_SESSION['message_type'] = 'danger';
        } else {
            if ($result) {
                mysqli_free_result($result);
            }
            mysqli_rollback($conn);
            $_SESSION['message'] = 'Usuario no encontrado';
            $_SESSION['message_type'] = 'danger';
        }
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);

        if ((int) $e->getCode() === 1451) {
            $_SESSION['message'] = 'Este usuario no se puede eliminar porque ya hizo compras o tiene relacion con otras partes del sistema.';
            $_SESSION['message_type'] = 'warning';
        } else {
            throw $e;
        }
    }

    header("Location: index.php");
    exit;
}
?>
