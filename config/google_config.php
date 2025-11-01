<?php
/**
 * Configuración de Google OAuth 2.0
 * 
 * INSTRUCCIONES PARA OBTENER CREDENCIALES:
 * 
 * 1. Ve a: https://console.cloud.google.com/
 * 2. Crea un nuevo proyecto o selecciona uno existente
 * 3. Ve a "APIs y servicios" > "Credenciales"
 * 4. Haz clic en "Crear credenciales" > "ID de cliente de OAuth"
 * 5. Selecciona "Aplicación web"
 * 6. Configura:
 *    - Orígenes autorizados de JavaScript: http://localhost, http://localhost/tu-proyecto
 *    - URIs de redireccionamiento autorizadas: http://localhost/tu-proyecto/google_login.php
 * 7. Copia el CLIENT_ID y reemplázalo abajo
 */

// ⚠️ IMPORTANTE: Reemplaza este CLIENT_ID con el tuyo de Google Cloud Console
define('483219139081-2fqjpmji0tr9m7djadpf9n5p64n21slo', '483219139081-2fqjpmji0tr9m7djadpf9n5p64n21slo.apps.googleusercontent.com');

// URLs permitidas para redirección después del login
define('ALLOWED_REDIRECT_URLS', [
    'index.php',
    'productos-artesania-bolivia.php',
    'carrito.php',
    'Dashboard/Dashboard_comprador.php'
]);

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
?>