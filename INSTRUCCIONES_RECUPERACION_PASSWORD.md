# Instrucciones para Configurar Recuperación de Contraseña

## 📋 Resumen del Sistema

Se ha implementado un sistema completo de recuperación de contraseña que incluye:
- ✅ Tabla de base de datos para tokens de recuperación
- ✅ Página para solicitar recuperación (`forgot_password.php`)
- ✅ Página para restablecer contraseña (`reset_password.php`)
- ✅ Configuración de email (`config/email_config.php`)
- ✅ Enlace actualizado en el modal de login

## 🔧 Pasos de Instalación

### 1. Crear la Tabla en la Base de Datos

Ejecuta el script SQL que se encuentra en `crear_tabla_recuperacion.sql`:

```bash
# Opción 1: Desde phpMyAdmin
# Abre phpMyAdmin, selecciona tu base de datos 'bdproduc_artesanales' y ejecuta el contenido de crear_tabla_recuperacion.sql

# Opción 2: Desde línea de comandos
mysql -u root -p bdproduc_artesanales < crear_tabla_recuperacion.sql
```

### 2. Configurar el Envío de Emails

Edita el archivo `config/email_config.php` y configura los siguientes valores:

#### Opción A: Usando Gmail (Recomendado para desarrollo)

1. **Crea una Contraseña de Aplicación en Google:**
   - Ve a tu cuenta de Google → Seguridad
   - Activa la verificación en 2 pasos (si no está activada)
   - Ve a "Contraseñas de aplicaciones"
   - Genera una nueva contraseña para "Correo" y "Otro (personalizado)" → escribe "ArtesaníaBoliviana"
   - Copia la contraseña generada (16 caracteres)

2. **Configura en `email_config.php`:**
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_PORT', 587);
   define('SMTP_USERNAME', 'tu_email@gmail.com');
   define('SMTP_PASSWORD', 'la_contraseña_de_aplicación_que_copiaste');
   define('SMTP_ENCRYPTION', 'tls');
   define('SMTP_FROM_EMAIL', 'tu_email@gmail.com');
   define('SMTP_FROM_NAME', 'ArtesaníaBoliviana');
   ```

#### Opción B: Usando mail() nativo de PHP (Más simple, menos confiable)

El sistema detectará automáticamente si no está instalado PHPMailer y usará `mail()`. Sin embargo, esto puede no funcionar en todos los servidores y los emails pueden ir a spam.

#### Opción C: Instalar PHPMailer (Recomendado para producción)

```bash
composer require phpmailer/phpmailer
```

### 3. Configurar la URL de la Aplicación

En `config/email_config.php`, actualiza la constante `APP_URL`:

```php
// Para desarrollo local:
define('APP_URL', 'http://localhost');

// Para producción:
define('APP_URL', 'https://tudominio.com');
```

## 🔒 Seguridad Implementada

- ✅ Tokens únicos y aleatorios (64 caracteres hexadecimales)
- ✅ Tokens con expiración (24 horas por defecto)
- ✅ Tokens de un solo uso (se marcan como usados después de resetear)
- ✅ Validación de contraseñas (mínimo 8 caracteres)
- ✅ Hash seguro de contraseñas usando `password_hash()`
- ✅ Limpieza automática de tokens antiguos
- ✅ No revela si un email existe o no (por seguridad)

## 📧 Flujo del Sistema

1. **Usuario solicita recuperación:**
   - Ingresa su email en `forgot_password.php`
   - El sistema genera un token único
   - Se envía un email con el enlace de recuperación

2. **Usuario recibe el email:**
   - El email contiene un enlace tipo: `reset_password.php?token=XXXXX`
   - El enlace expira en 24 horas

3. **Usuario restablece contraseña:**
   - Hace clic en el enlace del email
   - Ingresa su nueva contraseña (dos veces para confirmar)
   - El token se marca como usado
   - La contraseña se actualiza en la base de datos

## 🧪 Pruebas

1. **Probar el flujo completo:**
   - Ve a `index.php` → Modal de login → "¿Olvidaste tu contraseña?"
   - Ingresa un email válido de tu base de datos
   - Revisa tu correo (y carpeta de spam)
   - Haz clic en el enlace
   - Cambia tu contraseña

2. **Probar validaciones:**
   - Intentar usar un token expirado
   - Intentar usar un token usado
   - Ingresar contraseñas que no coinciden
   - Ingresar contraseña muy corta

## ⚠️ Solución de Problemas

### El email no se envía:
- Verifica la configuración SMTP en `email_config.php`
- Verifica que las credenciales sean correctas
- Revisa los logs de error de PHP
- Si usas Gmail, asegúrate de usar una "Contraseña de Aplicación", no tu contraseña normal

### Error: "Table 'recuperacion_password' doesn't exist":
- Ejecuta el script SQL `crear_tabla_recuperacion.sql`

### El enlace no funciona:
- Verifica que `APP_URL` esté configurado correctamente
- Asegúrate de que la ruta sea accesible desde internet (en producción)

### Los emails van a spam:
- Configura SPF, DKIM y DMARC en tu dominio (producción)
- Usa un servicio profesional de email como SendGrid, Mailgun, o Amazon SES

## 📝 Personalización

### Cambiar tiempo de expiración:
En `config/email_config.php`:
```php
define('TOKEN_EXPIRATION_HOURS', 24); // Cambia a las horas que desees
```

### Personalizar el diseño del email:
Edita el HTML dentro de `forgot_password.php` en la variable `$body`.

## 🔗 Archivos Creados/Modificados

- ✅ `crear_tabla_recuperacion.sql` - Script para crear la tabla
- ✅ `config/email_config.php` - Configuración de email
- ✅ `forgot_password.php` - Página de solicitud de recuperación
- ✅ `reset_password.php` - Página para restablecer contraseña
- ✅ `index.php` - Actualizado con enlace a recuperación

¡Listo! El sistema de recuperación de contraseña está completamente implementado.

