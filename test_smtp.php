<?php
/**
 * Script para probar la conexión SMTP directamente
 * Accede a este archivo en el navegador para ver qué está pasando
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/email_config.php';

echo "<h2>Prueba de Conexión SMTP</h2>";
echo "<pre>";

echo "Configuración:\n";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_USERNAME: " . SMTP_USERNAME . "\n";
echo "SMTP_PASSWORD: " . (strlen(SMTP_PASSWORD) > 0 ? str_repeat('*', strlen(SMTP_PASSWORD)) . " (longitud: " . strlen(SMTP_PASSWORD) . ")" : "NO CONFIGURADO") . "\n";
echo "SMTP_ENCRYPTION: " . SMTP_ENCRYPTION . "\n";
echo "\n";

echo "=== Paso 1: Conectando al servidor ===\n";
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$smtp = @stream_socket_client(
    SMTP_HOST . ':' . SMTP_PORT,
    $errno,
    $errstr,
    10,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$smtp) {
    die("❌ ERROR: No se pudo conectar - $errstr ($errno)\n");
}
echo "✅ Conectado exitosamente\n\n";

echo "=== Paso 2: Respuesta inicial ===\n";
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '220') {
    die("❌ ERROR: Respuesta inesperada\n");
}
echo "✅ OK\n\n";

echo "=== Paso 3: EHLO ===\n";
fputs($smtp, "EHLO " . gethostname() . "\r\n");
while ($line = fgets($smtp, 515)) {
    echo "Servidor: " . trim($line) . "\n";
    if (substr($line, 3, 1) == ' ') break;
}
echo "✅ OK\n\n";

echo "=== Paso 4: STARTTLS ===\n";
fputs($smtp, "STARTTLS\r\n");
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '220') {
    die("❌ ERROR: STARTTLS falló\n");
}
echo "✅ OK\n\n";

echo "=== Paso 5: Habilitando TLS ===\n";
$crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
    $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
}
if (!@stream_socket_enable_crypto($smtp, true, $crypto_method)) {
    die("❌ ERROR: No se pudo habilitar TLS\n");
}
echo "✅ TLS habilitado\n\n";

echo "=== Paso 6: EHLO después de TLS ===\n";
fputs($smtp, "EHLO " . gethostname() . "\r\n");
while ($line = fgets($smtp, 515)) {
    echo "Servidor: " . trim($line) . "\n";
    if (substr($line, 3, 1) == ' ') break;
}
echo "✅ OK\n\n";

echo "=== Paso 7: AUTH LOGIN ===\n";
fputs($smtp, "AUTH LOGIN\r\n");
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '334') {
    die("❌ ERROR: AUTH LOGIN falló\n");
}
echo "✅ OK\n\n";

echo "=== Paso 8: Enviando Username ===\n";
$username_b64 = base64_encode(SMTP_USERNAME);
fputs($smtp, $username_b64 . "\r\n");
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '334') {
    die("❌ ERROR: Username rechazado\n");
}
echo "✅ Username aceptado\n\n";

echo "=== Paso 9: Enviando Password ===\n";
$password_clean = trim(str_replace(' ', '', SMTP_PASSWORD));
echo "Password limpio (longitud): " . strlen($password_clean) . " caracteres\n";
$password_b64 = base64_encode($password_clean);
fputs($smtp, $password_b64 . "\r\n");
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '235') {
    echo "\n❌ ERROR: Autenticación falló\n";
    echo "Posibles causas:\n";
    echo "1. La contraseña de aplicación es incorrecta\n";
    echo "2. La contraseña tiene espacios (debe estar sin espacios)\n";
    echo "3. La verificación en 2 pasos no está activada en Gmail\n";
    echo "4. La contraseña de aplicación fue revocada\n";
    fclose($smtp);
    exit;
}
echo "✅ Autenticación exitosa!\n\n";

echo "=== Paso 10: MAIL FROM ===\n";
fputs($smtp, "MAIL FROM: <" . SMTP_FROM_EMAIL . ">\r\n");
$response = fgets($smtp, 515);
echo "Servidor: " . trim($response) . "\n";
if (substr($response, 0, 3) !== '250') {
    die("❌ ERROR: MAIL FROM falló\n");
}
echo "✅ OK\n\n";

fputs($smtp, "QUIT\r\n");
fclose($smtp);

echo "✅✅✅ ¡Conexión SMTP exitosa! ✅✅✅\n";
echo "\nLa configuración es correcta. Si aún no funciona, el problema puede estar en el envío del mensaje.\n";
echo "</pre>";
?>

