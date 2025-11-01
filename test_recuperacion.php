<?php
/**
 * Script de prueba para diagnosticar problemas con la recuperación de contraseña
 * Accede a este archivo directamente en el navegador para ver qué está pasando
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico de Recuperación de Contraseña</h2>";

// 1. Verificar conexión a BD
echo "<h3>1. Verificando conexión a base de datos...</h3>";
require_once 'db.php';
if ($conn) {
    echo "✅ Conexión exitosa<br>";
} else {
    echo "❌ Error de conexión: " . mysqli_connect_error() . "<br>";
    exit;
}

// 2. Verificar si existe la tabla
echo "<h3>2. Verificando tabla 'recuperacion_password'...</h3>";
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'recuperacion_password'");
if ($check_table && mysqli_num_rows($check_table) > 0) {
    echo "✅ La tabla existe<br>";
} else {
    echo "❌ La tabla NO existe. Por favor ejecuta crear_tabla_recuperacion.sql<br>";
}

// 3. Verificar configuración de email
echo "<h3>3. Verificando configuración de email...</h3>";
require_once 'config/email_config.php';
echo "SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : 'NO DEFINIDO') . "<br>";
echo "SMTP_USERNAME: " . (defined('SMTP_USERNAME') ? SMTP_USERNAME : 'NO DEFINIDO') . "<br>";
echo "SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? (SMTP_PASSWORD != 'tu_contraseña_aplicacion' ? 'CONFIGURADO (***)' : 'NO CONFIGURADO') : 'NO DEFINIDO') . "<br>";
echo "APP_URL: " . (defined('APP_URL') ? APP_URL : 'NO DEFINIDO') . "<br>";

// 4. Verificar función enviarEmail
echo "<h3>4. Verificando función enviarEmail...</h3>";
if (function_exists('enviarEmail')) {
    echo "✅ La función enviarEmail existe<br>";
} else {
    echo "❌ La función enviarEmail NO existe<br>";
}

// 5. Verificar si hay usuarios en la BD
echo "<h3>5. Verificando usuarios en la base de datos...</h3>";
$query_users = "SELECT COUNT(*) as total FROM usuario";
$result_users = mysqli_query($conn, $query_users);
if ($result_users) {
    $row = mysqli_fetch_assoc($result_users);
    echo "✅ Total de usuarios: " . $row['total'] . "<br>";
    
    // Mostrar algunos emails de ejemplo
    $query_emails = "SELECT correo FROM usuario LIMIT 5";
    $result_emails = mysqli_query($conn, $query_emails);
    if ($result_emails && mysqli_num_rows($result_emails) > 0) {
        echo "Emails de ejemplo en la BD:<br>";
        echo "<ul>";
        while ($email_row = mysqli_fetch_assoc($result_emails)) {
            echo "<li>" . htmlspecialchars($email_row['correo']) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "❌ Error al consultar usuarios: " . mysqli_error($conn) . "<br>";
}

// 6. Prueba de conexión SMTP (si está disponible)
echo "<h3>6. Verificando configuración SMTP...</h3>";
if (defined('SMTP_HOST') && defined('SMTP_PORT')) {
    $connection = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ Se puede conectar a " . SMTP_HOST . ":" . SMTP_PORT . "<br>";
        fclose($connection);
    } else {
        echo "⚠️ No se puede conectar a " . SMTP_HOST . ":" . SMTP_PORT . " (Error: $errstr)<br>";
        echo "Esto puede ser normal si tu servidor no permite conexiones salientes o si el puerto está bloqueado.<br>";
    }
} else {
    echo "❌ SMTP_HOST o SMTP_PORT no están definidos<br>";
}

echo "<hr>";
echo "<h3>✅ Diagnóstico completo</h3>";
echo "<p>Si ves algún ❌, ese es el problema que debes solucionar.</p>";
echo "<p>Si todo está ✅, el problema puede estar en el envío del correo. Verifica los logs de error de PHP.</p>";

?>

