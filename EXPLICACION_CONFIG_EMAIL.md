# Explicación de la Configuración de Email

## 🤔 ¿Por qué necesito configurar email si cada usuario tiene su propio email?

Buena pregunta. Te explico la diferencia:

### 📧 Email del Sistema (el que debes configurar)

Este es **UNA SOLA cuenta de email** que usará tu aplicación para **ENVIAR** correos a los usuarios.

**Piénsalo como el "remitente" de todos los correos que envía tu aplicación.**

- **Ejemplo:** Crea una cuenta como `artesaniaboliviana@gmail.com`
- Esta cuenta será la que aparezca como "De: ArtesaníaBoliviana"
- Esta cuenta enviará correos A los emails de tus usuarios

### 👥 Emails de los Usuarios (no necesitas configurarlos)

Estos son los emails que **ya están registrados** en tu base de datos en la tabla `usuario`.

- **Ejemplo:** `maria@gmail.com`, `juan@hotmail.com`, `pedro@yahoo.com`
- Estos usuarios recibirán correos EN SU EMAIL PERSONAL
- Pero los correos serán ENVIADOS desde la cuenta del sistema

## 📊 Diagrama del Flujo

```
Usuario (maria@gmail.com) solicita recuperación de contraseña
    ↓
Tu aplicación usa la cuenta del SISTEMA (artesaniaboliviana@gmail.com)
    ↓
El sistema envía un correo desde artesaniaboliviana@gmail.com
    ↓
El correo llega a maria@gmail.com (email del usuario)
```

## 🔧 Configuración Paso a Paso

### Opción 1: Usar Gmail (Recomendado para desarrollo)

1. **Crea una cuenta de Gmail para tu aplicación:**
   - Ve a Gmail y crea una cuenta nueva (ej: `artesaniaboliviana@gmail.com`)
   - O usa una cuenta que ya tengas

2. **Activa la verificación en 2 pasos:**
   - Ve a tu cuenta de Google → Seguridad
   - Activa "Verificación en 2 pasos"

3. **Genera una Contraseña de Aplicación:**
   - En la misma sección de Seguridad
   - Busca "Contraseñas de aplicaciones"
   - Selecciona "Correo" y "Otro (personalizado)"
   - Escribe "ArtesaníaBoliviana"
   - Copia la contraseña de 16 caracteres que te da

4. **Configura en `config/email_config.php`:**
   ```php
   define('SMTP_USERNAME', 'artesaniaboliviana@gmail.com');  // Tu cuenta del sistema
   define('SMTP_PASSWORD', 'abcd efgh ijkl mnop');  // La contraseña de aplicación (16 caracteres sin espacios)
   define('SMTP_FROM_EMAIL', 'artesaniaboliviana@gmail.com');  // Mismo email
   ```

### Opción 2: Usar mail() de PHP (Más simple pero menos confiable)

Si solo quieres probar rápido sin configurar SMTP, el sistema usará `mail()` automáticamente. 
Pero esto puede no funcionar en todos los servidores y los correos pueden ir a spam.

Solo asegúrate de que tu servidor tenga `mail()` habilitado.

## ✅ Resumen

- **Email del Sistema** = Una cuenta que TÚ creas/configuras para que tu app envíe correos
- **Emails de Usuarios** = Ya están en tu base de datos, no necesitas configurarlos
- **El sistema envía desde:** tu_email_del_sistema@gmail.com
- **Los usuarios reciben en:** su_email_personal@gmail.com (el que registraron)

¿Tiene sentido ahora? 😊

