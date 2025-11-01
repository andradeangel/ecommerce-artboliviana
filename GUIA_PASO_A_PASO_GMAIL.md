# 📧 Guía Paso a Paso: Configurar Gmail para Enviar Correos

Esta guía te ayudará a configurar tu cuenta Gmail (`andradefoldine.10@gmail.com`) para que tu aplicación pueda enviar correos.

---

## 🔐 Paso 1: Activar Verificación en 2 Pasos

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. En el menú lateral izquierdo, haz clic en **"Seguridad"** (Security)
3. Busca la sección **"Acceso a Google"** (How you sign in to Google)
4. Busca **"Verificación en 2 pasos"** (2-Step Verification)
5. Si está **desactivada**, haz clic en ella
6. Sigue las instrucciones para activarla:
   - Google te pedirá tu contraseña
   - Te pedirá un número de teléfono para recibir códigos
   - Te enviará un código SMS o llamada
   - Ingresa el código para confirmar
   - Haz clic en **"Activar"**

✅ **IMPORTANTE:** Debes tener la verificación en 2 pasos ACTIVADA para poder crear contraseñas de aplicación.

---

## 🔑 Paso 2: Generar Contraseña de Aplicación

Una vez que tengas la verificación en 2 pasos activada, sigue estos pasos:

### 2.1. Ir a la sección de Contraseñas de Aplicaciones

1. En la misma página de **"Seguridad"** de Google
2. Busca la sección **"Acceso a Google"** (How you sign in to Google)
3. Ahora deberías ver una nueva opción que dice: **"Contraseñas de aplicaciones"** (App passwords)
   - ⚠️ **NOTA:** Esta opción SOLO aparece después de activar la verificación en 2 pasos
   - Si no la ves, espera unos minutos y recarga la página

4. Haz clic en **"Contraseñas de aplicaciones"** (App passwords)

### 2.2. Crear la Contraseña de Aplicación

Ahora te aparecerá una pantalla para crear una contraseña de aplicación:

1. **Selecciona la aplicación:**
   - En el menú desplegable, busca y selecciona **"Correo"** (Mail)
   - Si no encuentras "Correo", selecciona **"Otro (Personalizado)"** (Other - Custom name)

2. **Selecciona el dispositivo:**
   - Selecciona **"Otro (Personalizado)"** (Other - Custom name) si no aparece tu dispositivo

3. **Escribe un nombre:**
   - Escribe: **"ArtesaníaBoliviana"** o el nombre que quieras para identificar esta contraseña
   - Este nombre solo es para que tú sepas para qué es esta contraseña

4. **Haz clic en "Generar"** (Generate)

### 2.3. Copiar la Contraseña

Google te mostrará una **contraseña de 16 caracteres** que se verá así:

```
abcd efgh ijkl mnop
```

⚠️ **IMPORTANTE:**
- Esta contraseña SOLO se muestra UNA VEZ
- **CÓPIALA INMEDIATAMENTE** antes de cerrar la ventana
- La necesitarás en el siguiente paso

---

## ⚙️ Paso 3: Configurar en tu Aplicación

Ahora que tienes la contraseña de aplicación, configúrala en tu archivo:

1. Abre el archivo: `config/email_config.php`

2. Busca estas líneas y actualízalas:

```php
define('SMTP_USERNAME', 'andradefoldine.10@gmail.com');  // Ya está configurado ✅
define('SMTP_PASSWORD', 'ABCD EFGH IJKL MNOP');  // 👈 AQUÍ Pega tu contraseña de aplicación
```

3. **IMPORTANTE:** Cuando pegues la contraseña, **quita los espacios**:

```php
// ❌ INCORRECTO (con espacios):
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop');

// ✅ CORRECTO (sin espacios):
define('SMTP_PASSWORD', 'abcdefghijklmnop');
```

4. Guarda el archivo

---

## 🎯 Ejemplo Completo del Archivo Configurado

Tu archivo `config/email_config.php` debería verse así:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'andradefoldine.10@gmail.com');  // Tu email
define('SMTP_PASSWORD', 'abcdefghijklmnop');  // Contraseña de aplicación SIN espacios
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'andradefoldine.10@gmail.com');
define('SMTP_FROM_NAME', 'ArtesaníaBoliviana');
```

---

## ✅ Paso 4: Probar que Funciona

1. Ve a tu aplicación
2. Haz clic en "Iniciar Sesión"
3. Haz clic en "¿Olvidaste tu contraseña?"
4. Ingresa un email que esté en tu base de datos
5. Haz clic en "Enviar Enlace de Recuperación"
6. Revisa el correo del usuario (y la carpeta de spam si no aparece)

---

## ❓ Solución de Problemas

### Problema 1: No veo "Contraseñas de aplicaciones"
- **Solución:** Asegúrate de que la verificación en 2 pasos esté ACTIVADA y espera unos minutos

### Problema 2: Me dice "Error al enviar email"
- **Solución:** 
  - Verifica que la contraseña de aplicación esté SIN espacios
  - Verifica que el email sea correcto: `andradefoldine.10@gmail.com`
  - Verifica que la verificación en 2 pasos esté activada

### Problema 3: Los correos van a spam
- **Solución:** Esto es normal cuando se envía desde una cuenta nueva. Los usuarios deben revisar su carpeta de spam

### Problema 4: Olvidé copiar la contraseña
- **Solución:** Debes crear una nueva contraseña de aplicación. La anterior ya no se puede ver

---

## 📝 Resumen Rápido

1. ✅ Activa verificación en 2 pasos en Google
2. ✅ Ve a "Contraseñas de aplicaciones"
3. ✅ Selecciona "Correo" o "Otro (Personalizado)"
4. ✅ Escribe "ArtesaníaBoliviana" como nombre
5. ✅ Genera y COPIA la contraseña de 16 caracteres
6. ✅ Pégala en `config/email_config.php` SIN espacios
7. ✅ Guarda y prueba

---

¿Necesitas ayuda con algún paso específico? 😊


