<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<main class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
                    unset($_SESSION['message']);
                    if (isset($_SESSION['message_type'])) {
                        unset($_SESSION['message_type']);
                    }
                }
                ?>

            <div class="card card-body">
                <form action="save.php" method="POST">
                    <div class="form-group">
                        <p><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></p>
                        <p><input type="text" name="apellido" class="form-control" placeholder="Apellido" required></p>
                        <p><input type="email" name="correo" class="form-control" placeholder="Correo Electrónico" required></p>
                        <p><input type="password" name="contraseña" class="form-control" placeholder="Contraseña" required></p>
                        <p><input type="date" name="fecha_naci" class="form-control" placeholder="Fecha de Nacimiento" required></p>
                        <p><input type="tel" name="telefono" class="form-control" placeholder="Teléfono" required></p>

                        <p>
                            <select name="rol" class="form-control" id="rol" onchange="showFields(this.value)" required>
                                <option value="" disabled selected>Selecciona un rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="comprador">Comprador</option>
                            </select>
                        </p>

                        <div id="comunidadField" style="display:none;">
                            <select name="id_comunidad" id="id_comunidad" class="form-control">
                                <option value="" disabled selected>Selecciona una comunidad</option>
                                <?php
                                $query_comunidad = "SELECT id_comunidad, nombre FROM comunidad";
                                $result_comunidad = mysqli_query($conn, $query_comunidad);
                                while ($comunidad = mysqli_fetch_assoc($result_comunidad)) {
                                    echo "<option value='{$comunidad['id_comunidad']}'>{$comunidad['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div id="deliveryField" style="display:none;">
                            <select name="id_empresa" id="id_empresa" class="form-control">
                                <option value="" disabled selected>Selecciona una empresa de delivery</option>
                                <?php
                                $query_empresa = "SELECT id_empresa, nombre FROM empresa";
                                $result_empresa = mysqli_query($conn, $query_empresa);
                                while ($empresa = mysqli_fetch_assoc($result_empresa)) {
                                    echo "<option value='{$empresa['id_empresa']}'>{$empresa['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-success btn block" name="save" value="Enviar">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Fecha de Registro</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $delivery_exists = false;
                    $check_delivery = mysqli_query($conn, "SHOW TABLES LIKE 'delivery'");
                    if ($check_delivery) {
                        $delivery_exists = mysqli_num_rows($check_delivery) > 0;
                        mysqli_free_result($check_delivery);
                    }

                    $select_fields = "SELECT u.*, c.nombre AS nombre_comunidad";
                    if ($delivery_exists) {
                        $select_fields .= ", e.nombre AS nombre_empresa";
                    } else {
                        $select_fields .= ", NULL AS nombre_empresa";
                    }

                    $query = $select_fields . "
                              FROM usuario u
                              LEFT JOIN comunario co ON u.id_usuario = co.id_comunario
                              LEFT JOIN comunidad c ON co.id_comunidad = c.id_comunidad";

                    if ($delivery_exists) {
                        $query .= "
                              LEFT JOIN delivery d ON u.id_usuario = d.id_delivery
                              LEFT JOIN empresa e ON d.id_empresa = e.id_empresa";
                    }

                    $result_usuario = mysqli_query($conn, $query);

                    if ($result_usuario && mysqli_num_rows($result_usuario) > 0) {
                        while ($row = mysqli_fetch_assoc($result_usuario)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['apellido']; ?></td>
                            <td><?php echo $row['correo']; ?></td>
                            <td><?php echo $row['fecha_naci']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['rol']; ?></td>
                            <td><?php echo $row['fecha_registro']; ?></td>
                            <td>
                                <a href="edit.php?id_usuario=<?php echo $row['id_usuario']; ?>" class="btn btn-secondary">Editar</a>
                                <a href="delete.php?id_usuario=<?php echo $row['id_usuario']; ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php }
                        mysqli_free_result($result_usuario);
                    } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function showFields(value) {
        var comunidadField = document.getElementById('comunidadField');
        var comunidadSelect = document.getElementById('id_comunidad');
        var deliveryField = document.getElementById('deliveryField');
        var empresaSelect = document.getElementById('id_empresa');

        if (comunidadField) {
            if (value === 'vendedor') {
                comunidadField.style.display = 'block';
                if (comunidadSelect) {
                    comunidadSelect.required = true;
                }
            } else {
                comunidadField.style.display = 'none';
                if (comunidadSelect) {
                    comunidadSelect.required = false;
                    comunidadSelect.value = '';
                }
            }
        }

        if (deliveryField) {
            if (value === 'delivery') {
                deliveryField.style.display = 'block';
                if (empresaSelect) {
                    empresaSelect.required = true;
                }
            } else {
                deliveryField.style.display = 'none';
                if (empresaSelect) {
                    empresaSelect.required = false;
                    empresaSelect.value = '';
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var rolSelect = document.getElementById('rol');
        if (rolSelect) {
            showFields(rolSelect.value);
        }
    });
</script>

<?php include('../includes/footer.php'); ?>
