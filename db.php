<?php
// Conexión a la base de datos MySQL
$servername = "localhost"; // Cambia si es necesario
$username = "root"; // Usuario de la BD
$password = ""; // Contraseña de la BD
$database = "bdproduc_artesanales"; // Nombre de tu base de datos

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
